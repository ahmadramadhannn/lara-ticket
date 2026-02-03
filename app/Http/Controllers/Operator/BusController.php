<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\BusClass;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BusController extends Controller
{
    /**
     * Display a listing of operator's buses.
     */
    public function index(Request $request): View
    {
        $operator = auth()->user()->busOperator;

        $query = Bus::with('busClass')
            ->where('bus_operator_id', $operator->id);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Filter by class
        if ($request->filled('class')) {
            $query->where('bus_class_id', $request->class);
        }

        $buses = $query->orderBy('name')->paginate(15)->withQueryString();
        $busClasses = BusClass::orderBy('name')->get();

        return view('operator.buses.index', compact('buses', 'busClasses', 'operator'));
    }

    /**
     * Show the form for creating a new bus.
     */
    public function create(): View
    {
        $operator = auth()->user()->busOperator;
        $busClasses = BusClass::orderBy('name')->get();

        return view('operator.buses.create', compact('operator', 'busClasses'));
    }

    /**
     * Store a newly created bus.
     */
    public function store(Request $request): RedirectResponse
    {
        $operator = auth()->user()->busOperator;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:20|unique:buses,registration_number',
            'bus_class_id' => 'required|exists:bus_classes,id',
            'total_seats' => 'required|integer|min:1|max:100',
            'seat_layout' => 'nullable|array',
        ]);

        $validated['bus_operator_id'] = $operator->id;

        // Generate default seat layout if not provided
        if (empty($validated['seat_layout'])) {
            $validated['seat_layout'] = $this->generateDefaultSeatLayout($validated['total_seats']);
        }

        Bus::create($validated);

        return redirect()->route('operator.buses.index')
            ->with('success', __('Bus registered successfully.'));
    }

    /**
     * Show the form for editing the specified bus.
     */
    public function edit(Bus $bus): View
    {
        $operator = auth()->user()->busOperator;

        // Ensure bus belongs to operator
        if ($bus->bus_operator_id !== $operator->id) {
            abort(403);
        }

        $busClasses = BusClass::orderBy('name')->get();

        return view('operator.buses.edit', compact('bus', 'operator', 'busClasses'));
    }

    /**
     * Update the specified bus.
     */
    public function update(Request $request, Bus $bus): RedirectResponse
    {
        $operator = auth()->user()->busOperator;

        // Ensure bus belongs to operator
        if ($bus->bus_operator_id !== $operator->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:20|unique:buses,registration_number,' . $bus->id,
            'bus_class_id' => 'required|exists:bus_classes,id',
            'total_seats' => 'required|integer|min:1|max:100',
        ]);

        $bus->update($validated);

        return redirect()->route('operator.buses.index')
            ->with('success', __('Bus updated successfully.'));
    }

    /**
     * Remove the specified bus.
     */
    public function destroy(Bus $bus): RedirectResponse
    {
        $operator = auth()->user()->busOperator;

        // Ensure bus belongs to operator
        if ($bus->bus_operator_id !== $operator->id) {
            abort(403);
        }

        // Check if bus has schedules
        if ($bus->schedules()->exists()) {
            return back()->with('error', __('Cannot delete bus with existing schedules.'));
        }

        $bus->delete();

        return redirect()->route('operator.buses.index')
            ->with('success', __('Bus deleted successfully.'));
    }

    /**
     * Generate a default seat layout.
     */
    private function generateDefaultSeatLayout(int $totalSeats): array
    {
        $layout = [];
        $seatsPerRow = 4; // Default 2-2 configuration

        for ($i = 1; $i <= $totalSeats; $i++) {
            $layout[] = [
                'number' => $i,
                'row' => ceil($i / $seatsPerRow),
                'position' => (($i - 1) % $seatsPerRow) + 1,
            ];
        }

        return $layout;
    }
}
