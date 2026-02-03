<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Route;
use App\Models\Bus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    /**
     * Display a listing of operator's schedules.
     */
    public function index(Request $request): View
    {
        $operator = auth()->user()->busOperator;

        $query = Schedule::with(['route.originTerminal', 'route.destinationTerminal', 'bus.busClass'])
            ->where('bus_operator_id', $operator->id);

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('departure_time', $request->date);
        } else {
            // Default to upcoming schedules
            $query->where('departure_time', '>=', now());
        }

        // Filter by route
        if ($request->filled('route')) {
            $query->where('route_id', $request->route);
        }

        // Filter by bus
        if ($request->filled('bus')) {
            $query->where('bus_id', $request->bus);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $schedules = $query->orderBy('departure_time')->paginate(20)->withQueryString();

        $routes = Route::with(['originTerminal', 'destinationTerminal'])
            ->where('is_active', true)
            ->get();

        $buses = Bus::where('bus_operator_id', $operator->id)->orderBy('name')->get();

        return view('operator.schedules.index', compact('schedules', 'routes', 'buses', 'operator'));
    }

    /**
     * Show the form for creating a new schedule.
     */
    public function create(): View
    {
        $operator = auth()->user()->busOperator;

        $routes = Route::with(['originTerminal.city', 'destinationTerminal.city'])
            ->where('is_active', true)
            ->get()
            ->map(function ($route) {
                $route->display_name = $route->originTerminal->name . ' → ' . $route->destinationTerminal->name;
                return $route;
            });

        $buses = Bus::with('busClass')
            ->where('bus_operator_id', $operator->id)
            ->orderBy('name')
            ->get();

        return view('operator.schedules.create', compact('operator', 'routes', 'buses'));
    }

    /**
     * Store a newly created schedule.
     */
    public function store(Request $request): RedirectResponse
    {
        $operator = auth()->user()->busOperator;

        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'bus_id' => 'required|exists:buses,id',
            'departure_date' => 'required|date|after_or_equal:today',
            'departure_time' => 'required|date_format:H:i',
            'base_price' => 'required|numeric|min:1000',
        ]);

        // Verify bus belongs to operator
        $bus = Bus::where('id', $validated['bus_id'])
            ->where('bus_operator_id', $operator->id)
            ->firstOrFail();

        $route = Route::findOrFail($validated['route_id']);

        $departureTime = Carbon::parse($validated['departure_date'] . ' ' . $validated['departure_time']);
        $arrivalTime = $departureTime->copy()->addMinutes($route->estimated_duration_minutes);

        // Check for duplicate schedule
        $exists = Schedule::where('bus_id', $bus->id)
            ->where('departure_time', $departureTime)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', __('This bus already has a schedule at this time.'));
        }

        Schedule::create([
            'route_id' => $route->id,
            'bus_id' => $bus->id,
            'bus_operator_id' => $operator->id,
            'departure_time' => $departureTime,
            'arrival_time' => $arrivalTime,
            'base_price' => $validated['base_price'],
            'available_seats' => $bus->total_seats,
            'status' => 'scheduled',
        ]);

        return redirect()->route('operator.schedules.index')
            ->with('success', __('Schedule created successfully.'));
    }

    /**
     * Show the form for editing the specified schedule.
     */
    public function edit(Schedule $schedule): View
    {
        $operator = auth()->user()->busOperator;

        // Ensure schedule belongs to operator
        if ($schedule->bus_operator_id !== $operator->id) {
            abort(403);
        }

        $schedule->load(['route.originTerminal', 'route.destinationTerminal', 'bus']);

        $routes = Route::with(['originTerminal.city', 'destinationTerminal.city'])
            ->where('is_active', true)
            ->get()
            ->map(function ($route) {
                $route->display_name = $route->originTerminal->name . ' → ' . $route->destinationTerminal->name;
                return $route;
            });

        $buses = Bus::with('busClass')
            ->where('bus_operator_id', $operator->id)
            ->orderBy('name')
            ->get();

        return view('operator.schedules.edit', compact('schedule', 'operator', 'routes', 'buses'));
    }

    /**
     * Update the specified schedule.
     */
    public function update(Request $request, Schedule $schedule): RedirectResponse
    {
        $operator = auth()->user()->busOperator;

        // Ensure schedule belongs to operator
        if ($schedule->bus_operator_id !== $operator->id) {
            abort(403);
        }

        // Prevent editing past or started schedules
        if ($schedule->departure_time < now()) {
            return back()->with('error', __('Cannot edit past schedules.'));
        }

        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'bus_id' => 'required|exists:buses,id',
            'departure_date' => 'required|date|after_or_equal:today',
            'departure_time' => 'required|date_format:H:i',
            'base_price' => 'required|numeric|min:1000',
            'status' => 'required|in:scheduled,cancelled',
        ]);

        // Verify bus belongs to operator
        $bus = Bus::where('id', $validated['bus_id'])
            ->where('bus_operator_id', $operator->id)
            ->firstOrFail();

        $route = Route::findOrFail($validated['route_id']);

        $departureTime = Carbon::parse($validated['departure_date'] . ' ' . $validated['departure_time']);
        $arrivalTime = $departureTime->copy()->addMinutes($route->estimated_duration_minutes);

        $schedule->update([
            'route_id' => $route->id,
            'bus_id' => $bus->id,
            'departure_time' => $departureTime,
            'arrival_time' => $arrivalTime,
            'base_price' => $validated['base_price'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('operator.schedules.index')
            ->with('success', __('Schedule updated successfully.'));
    }

    /**
     * Remove the specified schedule.
     */
    public function destroy(Schedule $schedule): RedirectResponse
    {
        $operator = auth()->user()->busOperator;

        // Ensure schedule belongs to operator
        if ($schedule->bus_operator_id !== $operator->id) {
            abort(403);
        }

        // Check if schedule has tickets
        if ($schedule->tickets()->whereIn('status', ['confirmed', 'pending'])->exists()) {
            return back()->with('error', __('Cannot delete schedule with active tickets.'));
        }

        $schedule->delete();

        return redirect()->route('operator.schedules.index')
            ->with('success', __('Schedule deleted successfully.'));
    }

    /**
     * Bulk create schedules for multiple days.
     */
    public function bulkCreate(Request $request): RedirectResponse
    {
        $operator = auth()->user()->busOperator;

        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'bus_id' => 'required|exists:buses,id',
            'departure_time' => 'required|date_format:H:i',
            'base_price' => 'required|numeric|min:1000',
            'date_from' => 'required|date|after_or_equal:today',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        // Verify bus belongs to operator
        $bus = Bus::where('id', $validated['bus_id'])
            ->where('bus_operator_id', $operator->id)
            ->firstOrFail();

        $route = Route::findOrFail($validated['route_id']);

        $startDate = Carbon::parse($validated['date_from']);
        $endDate = Carbon::parse($validated['date_to']);
        $created = 0;
        $skipped = 0;

        while ($startDate <= $endDate) {
            $departureTime = Carbon::parse($startDate->format('Y-m-d') . ' ' . $validated['departure_time']);
            $arrivalTime = $departureTime->copy()->addMinutes($route->estimated_duration_minutes);

            // Check for existing schedule
            $exists = Schedule::where('bus_id', $bus->id)
                ->where('departure_time', $departureTime)
                ->exists();

            if (!$exists) {
                Schedule::create([
                    'route_id' => $route->id,
                    'bus_id' => $bus->id,
                    'bus_operator_id' => $operator->id,
                    'departure_time' => $departureTime,
                    'arrival_time' => $arrivalTime,
                    'base_price' => $validated['base_price'],
                    'available_seats' => $bus->total_seats,
                    'status' => 'scheduled',
                ]);
                $created++;
            } else {
                $skipped++;
            }

            $startDate->addDay();
        }

        $message = __(':created schedules created.', ['created' => $created]);
        if ($skipped > 0) {
            $message .= ' ' . __(':skipped skipped (already exist).', ['skipped' => $skipped]);
        }

        return redirect()->route('operator.schedules.index')
            ->with('success', $message);
    }
}
