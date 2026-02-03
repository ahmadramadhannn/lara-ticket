<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Show the verification form.
     */
    public function index()
    {
        return view('verification.index');
    }

    /**
     * Verify a ticket by booking code.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'booking_code' => 'required|string|max:20',
        ]);

        $bookingCode = strtoupper(trim($request->booking_code));

        $ticket = Ticket::where('booking_code', $bookingCode)
            ->with([
                'schedule.route.originTerminal.city',
                'schedule.route.destinationTerminal.city',
                'schedule.busOperator',
                'schedule.bus.busClass',
                'user',
            ])
            ->first();

        if (!$ticket) {
            return back()->with('error', 'Tiket tidak ditemukan.');
        }

        // Check if ticket is valid
        $validationResult = $this->validateTicket($ticket);

        return view('verification.result', [
            'ticket' => $ticket,
            'validation' => $validationResult,
        ]);
    }

    /**
     * Mark ticket as used.
     */
    public function markUsed(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        if ($ticket->status !== 'confirmed') {
            return back()->with('error', 'Tiket tidak dapat digunakan. Status: ' . $ticket->status);
        }

        // Only verifier or admin can mark as used
        if (!$user->isVerifier() && !$user->isAdmin()) {
            abort(403);
        }

        $ticket->update([
            'status' => 'used',
            'verified_at' => now(),
            'verified_by' => $user->id,
        ]);

        return back()->with('success', 'Tiket berhasil divalidasi! Penumpang dapat naik bus.');
    }

    /**
     * Validate ticket and return validation result.
     */
    protected function validateTicket(Ticket $ticket): array
    {
        $schedule = $ticket->schedule;
        $now = Carbon::now();
        $departureTime = Carbon::parse($schedule->departure_time);

        // Status checks
        if ($ticket->status === 'used') {
            return [
                'valid' => false,
                'status' => 'used',
                'message' => 'Tiket sudah digunakan.',
                'type' => 'error',
            ];
        }

        if ($ticket->status === 'cancelled') {
            return [
                'valid' => false,
                'status' => 'cancelled',
                'message' => 'Tiket sudah dibatalkan.',
                'type' => 'error',
            ];
        }

        if ($ticket->status === 'expired') {
            return [
                'valid' => false,
                'status' => 'expired',
                'message' => 'Tiket sudah kadaluarsa.',
                'type' => 'error',
            ];
        }

        if ($ticket->status === 'pending') {
            return [
                'valid' => false,
                'status' => 'pending',
                'message' => 'Tiket belum dibayar.',
                'type' => 'warning',
            ];
        }

        // Date checks
        if ($departureTime->isPast()) {
            return [
                'valid' => false,
                'status' => 'departed',
                'message' => 'Bus sudah berangkat pada ' . $departureTime->format('d M Y H:i'),
                'type' => 'error',
            ];
        }

        if (!$departureTime->isToday()) {
            return [
                'valid' => false,
                'status' => 'not_today',
                'message' => 'Tiket berlaku untuk tanggal ' . $departureTime->format('d M Y'),
                'type' => 'warning',
            ];
        }

        // Schedule status check
        if ($schedule->status === 'cancelled') {
            return [
                'valid' => false,
                'status' => 'schedule_cancelled',
                'message' => 'Jadwal keberangkatan dibatalkan.',
                'type' => 'error',
            ];
        }

        // All checks passed
        return [
            'valid' => true,
            'status' => 'confirmed',
            'message' => 'Tiket valid! Penumpang dapat naik bus.',
            'type' => 'success',
        ];
    }

    /**
     * API endpoint for mobile verification.
     */
    public function apiVerify(Request $request)
    {
        $request->validate([
            'booking_code' => 'required|string|max:20',
        ]);

        $bookingCode = strtoupper(trim($request->booking_code));

        $ticket = Ticket::where('booking_code', $bookingCode)
            ->with([
                'schedule.route.originTerminal.city',
                'schedule.route.destinationTerminal.city',
                'schedule.busOperator',
            ])
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan.',
            ], 404);
        }

        $validationResult = $this->validateTicket($ticket);

        return response()->json([
            'success' => true,
            'ticket' => [
                'booking_code' => $ticket->booking_code,
                'passenger_name' => $ticket->passenger_name,
                'seat_number' => $ticket->seat_number,
                'status' => $ticket->status,
                'departure' => $ticket->schedule->departure_time->format('Y-m-d H:i'),
                'origin' => $ticket->schedule->route->originTerminal->name,
                'destination' => $ticket->schedule->route->destinationTerminal->name,
                'operator' => $ticket->schedule->busOperator->name,
            ],
            'validation' => $validationResult,
        ]);
    }
}
