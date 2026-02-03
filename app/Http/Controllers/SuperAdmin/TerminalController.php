<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Terminal;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TerminalController extends Controller
{
    /**
     * Display a listing of terminals.
     */
    public function index(Request $request): View
    {
        $query = Terminal::with('city.province');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Filter by province
        if ($request->filled('province')) {
            $query->whereHas('city.province', function ($q) use ($request) {
                $q->where('id', $request->province);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $terminals = $query->orderBy('name')->paginate(15)->withQueryString();
        $provinces = Province::orderBy('name')->get();

        return view('super-admin.terminals.index', compact('terminals', 'provinces'));
    }

    /**
     * Show the form for creating a new terminal.
     */
    public function create(): View
    {
        $provinces = Province::with('cities')->orderBy('name')->get();
        return view('super-admin.terminals.create', compact('provinces'));
    }

    /**
     * Store a newly created terminal.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:terminals,code',
            'city_id' => 'required|exists:cities,id',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Terminal::create($validated);

        return redirect()->route('super-admin.terminals.index')
            ->with('success', __('Terminal created successfully.'));
    }

    /**
     * Show the form for editing the specified terminal.
     */
    public function edit(Terminal $terminal): View
    {
        $provinces = Province::with('cities')->orderBy('name')->get();
        $terminal->load('city.province');
        return view('super-admin.terminals.edit', compact('terminal', 'provinces'));
    }

    /**
     * Update the specified terminal.
     */
    public function update(Request $request, Terminal $terminal): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:terminals,code,' . $terminal->id,
            'city_id' => 'required|exists:cities,id',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $terminal->update($validated);

        return redirect()->route('super-admin.terminals.index')
            ->with('success', __('Terminal updated successfully.'));
    }

    /**
     * Remove the specified terminal.
     */
    public function destroy(Terminal $terminal): RedirectResponse
    {
        // Check if terminal has routes
        if ($terminal->originRoutes()->exists() || $terminal->destinationRoutes()->exists()) {
            return back()->with('error', __('Cannot delete terminal with existing routes.'));
        }

        $terminal->delete();

        return redirect()->route('super-admin.terminals.index')
            ->with('success', __('Terminal deleted successfully.'));
    }
}
