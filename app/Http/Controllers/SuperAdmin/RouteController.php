<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Terminal;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RouteController extends Controller
{
    /**
     * Display a listing of routes.
     */
    public function index(Request $request): View
    {
        $query = Route::with(['originTerminal.city.province', 'destinationTerminal.city.province']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('originTerminal', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })->orWhereHas('destinationTerminal', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter by origin terminal
        if ($request->filled('origin')) {
            $query->where('origin_terminal_id', $request->origin);
        }

        // Filter by destination terminal
        if ($request->filled('destination')) {
            $query->where('destination_terminal_id', $request->destination);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $routes = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $terminals = Terminal::where('is_active', true)->orderBy('name')->get();

        return view('super-admin.routes.index', compact('routes', 'terminals'));
    }

    /**
     * Show the form for creating a new route.
     */
    public function create(): View
    {
        $terminals = Terminal::with('city.province')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->groupBy('city.province.name');

        return view('super-admin.routes.create', compact('terminals'));
    }

    /**
     * Store a newly created route.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'origin_terminal_id' => 'required|exists:terminals,id',
            'destination_terminal_id' => 'required|exists:terminals,id|different:origin_terminal_id',
            'distance_km' => 'required|numeric|min:1|max:9999',
            'estimated_duration_minutes' => 'required|integer|min:1|max:2880',
            'is_active' => 'boolean',
        ]);

        // Check for duplicate route
        $exists = Route::where('origin_terminal_id', $validated['origin_terminal_id'])
            ->where('destination_terminal_id', $validated['destination_terminal_id'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', __('This route already exists.'));
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        Route::create($validated);

        return redirect()->route('super-admin.routes.index')
            ->with('success', __('Route created successfully.'));
    }

    /**
     * Show the form for editing the specified route.
     */
    public function edit(Route $route): View
    {
        $terminals = Terminal::with('city.province')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->groupBy('city.province.name');

        $route->load(['originTerminal', 'destinationTerminal']);

        return view('super-admin.routes.edit', compact('route', 'terminals'));
    }

    /**
     * Update the specified route.
     */
    public function update(Request $request, Route $route): RedirectResponse
    {
        $validated = $request->validate([
            'origin_terminal_id' => 'required|exists:terminals,id',
            'destination_terminal_id' => 'required|exists:terminals,id|different:origin_terminal_id',
            'distance_km' => 'required|numeric|min:1|max:9999',
            'estimated_duration_minutes' => 'required|integer|min:1|max:2880',
            'is_active' => 'boolean',
        ]);

        // Check for duplicate route (excluding current)
        $exists = Route::where('origin_terminal_id', $validated['origin_terminal_id'])
            ->where('destination_terminal_id', $validated['destination_terminal_id'])
            ->where('id', '!=', $route->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', __('This route already exists.'));
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $route->update($validated);

        return redirect()->route('super-admin.routes.index')
            ->with('success', __('Route updated successfully.'));
    }

    /**
     * Remove the specified route.
     */
    public function destroy(Route $route): RedirectResponse
    {
        // Check if route has schedules
        if ($route->schedules()->exists()) {
            return back()->with('error', __('Cannot delete route with existing schedules.'));
        }

        $route->delete();

        return redirect()->route('super-admin.routes.index')
            ->with('success', __('Route deleted successfully.'));
    }
}
