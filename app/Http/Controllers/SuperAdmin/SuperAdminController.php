<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BusOperator;
use App\Models\Terminal;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    /**
     * Show the super admin dashboard.
     */
    public function dashboard(): View
    {
        $stats = [
            'pending_registrations' => BusOperator::pending()->count(),
            'approved_operators' => BusOperator::approved()->count(),
            'total_terminals' => Terminal::count(),
            'total_users' => User::count(),
        ];

        $recentPending = BusOperator::pending()
            ->with('submittedBy')
            ->latest()
            ->take(5)
            ->get();

        return view('super-admin.dashboard', compact('stats', 'recentPending'));
    }

    /**
     * Show the list of pending registrations.
     */
    public function registrations(): View
    {
        $pendingOperators = BusOperator::pending()
            ->with('submittedBy')
            ->latest()
            ->paginate(15);

        return view('super-admin.registrations.index', compact('pendingOperators'));
    }

    /**
     * Show details of a pending registration.
     */
    public function showRegistration(BusOperator $operator): View
    {
        $operator->load('submittedBy');
        
        return view('super-admin.registrations.show', compact('operator'));
    }

    /**
     * Approve an operator registration.
     */
    public function approve(BusOperator $operator): RedirectResponse
    {
        // Update the bus operator status
        $operator->update([
            'approval_status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'is_active' => true,
        ]);

        // Activate the operator's user account
        if ($operator->submittedBy) {
            $operator->submittedBy->update([
                'user_status' => 'active',
            ]);
        }

        return redirect()->route('super-admin.registrations')
            ->with('success', "PO '{$operator->name}' berhasil disetujui.");
    }

    /**
     * Reject an operator registration.
     */
    public function reject(Request $request, BusOperator $operator): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        // Update the bus operator status
        $operator->update([
            'approval_status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Suspend the operator's user account
        if ($operator->submittedBy) {
            $operator->submittedBy->update([
                'user_status' => 'suspended',
            ]);
        }

        return redirect()->route('super-admin.registrations')
            ->with('success', "PO '{$operator->name}' ditolak.");
    }

    /**
     * Show the list of all approved operators.
     */
    public function operators(): View
    {
        $operators = BusOperator::approved()
            ->with('submittedBy')
            ->withCount('buses')
            ->latest()
            ->paginate(15);

        return view('super-admin.operators.index', compact('operators'));
    }
}
