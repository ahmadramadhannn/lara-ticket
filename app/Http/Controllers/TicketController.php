<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display user's tickets.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $upcomingTickets = Ticket::forUser($user->id)
            ->confirmed()
            ->upcoming()
            ->with([
                'schedule.route.originTerminal.city',
                'schedule.route.destinationTerminal.city',
                'schedule.busOperator',
                'schedule.bus.busClass',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $pastTickets = Ticket::forUser($user->id)
            ->whereIn('status', ['used', 'expired'])
            ->past()
            ->with([
                'schedule.route.originTerminal.city',
                'schedule.route.destinationTerminal.city',
                'schedule.busOperator',
            ])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('tickets.index', compact('upcomingTickets', 'pastTickets'));
    }

    /**
     * Display a ticket.
     */
    public function show(Ticket $ticket)
    {
        // Authorize: ticket must belong to current user or user is admin/verifier
        $user = request()->user();
        if ($ticket->user_id !== $user->id && !$user->isAdmin() && !$user->isVerifier()) {
            abort(403);
        }

        $ticket->load([
            'schedule.route.originTerminal.city.province',
            'schedule.route.destinationTerminal.city.province',
            'schedule.busOperator',
            'schedule.bus.busClass',
            'payment',
            'user',
        ]);

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Download ticket as PDF (placeholder).
     */
    public function download(Ticket $ticket)
    {
        if ($ticket->user_id !== request()->user()->id) {
            abort(403);
        }

        // For now, just redirect to show page
        // In production, generate PDF
        return redirect()->route('tickets.show', $ticket)
            ->with('info', 'Fitur download PDF akan segera hadir.');
    }
}
