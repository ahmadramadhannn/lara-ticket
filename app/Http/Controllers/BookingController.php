<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingController extends Controller
{
    /**
     * Show booking form for a schedule.
     */
    public function create(Request $request, Schedule $schedule)
    {
        $request->validate([
            'seat' => 'required|string',
        ]);

        $schedule->load([
            'route.originTerminal.city',
            'route.destinationTerminal.city',
            'bus.busClass',
            'busOperator',
        ]);

        $seat = $request->seat;

        // Check if seat is available
        $isBooked = $schedule->tickets()
            ->where('seat_number', $seat)
            ->whereIn('status', ['confirmed', 'pending'])
            ->exists();

        if ($isBooked) {
            return back()->with('error', 'Kursi sudah dipesan. Silakan pilih kursi lain.');
        }

        return view('booking.create', compact('schedule', 'seat'));
    }

    /**
     * Store a new booking.
     */
    public function store(Request $request, Schedule $schedule)
    {
        $request->validate([
            'seat' => 'required|string',
            'passenger_name' => 'required|string|max:255',
            'passenger_id_number' => 'nullable|string|max:50',
        ]);

        $user = $request->user();
        $seat = $request->seat;

        // Use database transaction
        return DB::transaction(function () use ($request, $schedule, $user, $seat) {
            // Lock the schedule row
            $schedule = Schedule::lockForUpdate()->find($schedule->id);

            // Double-check seat availability
            $isBooked = $schedule->tickets()
                ->where('seat_number', $seat)
                ->whereIn('status', ['confirmed', 'pending'])
                ->exists();

            if ($isBooked) {
                return back()->with('error', 'Kursi sudah dipesan. Silakan pilih kursi lain.');
            }

            // Check available seats
            if ($schedule->available_seats <= 0) {
                return back()->with('error', 'Tidak ada kursi tersedia untuk jadwal ini.');
            }

            // Create ticket
            $ticket = Ticket::create([
                'user_id' => $user->id,
                'schedule_id' => $schedule->id,
                'seat_number' => $seat,
                'passenger_name' => $request->passenger_name,
                'passenger_id_number' => $request->passenger_id_number,
                'price' => $schedule->base_price,
                'status' => 'pending',
            ]);

            // Decrease available seats
            $schedule->decrement('available_seats');

            // Create payment
            $payment = Payment::create([
                'user_id' => $user->id,
                'payable_type' => Ticket::class,
                'payable_id' => $ticket->id,
                'amount' => $schedule->base_price,
                'status' => 'pending',
            ]);

            return redirect()->route('payments.show', $payment)
                ->with('success', 'Pemesanan berhasil! Silakan selesaikan pembayaran.');
        });
    }
}
