<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Display payment details.
     */
    public function show(Payment $payment)
    {
        // Authorize: payment must belong to current user
        if ($payment->user_id !== request()->user()->id) {
            abort(403);
        }

        $payment->load('payable.schedule.route.originTerminal.city', 'payable.schedule.route.destinationTerminal.city', 'payable.schedule.busOperator');

        return view('payments.show', compact('payment'));
    }

    /**
     * Process payment (mock implementation).
     */
    public function process(Request $request, Payment $payment)
    {
        // Authorize
        if ($payment->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($payment->isPaid()) {
            return back()->with('info', 'Pembayaran sudah selesai.');
        }

        $request->validate([
            'method' => 'required|in:bank_transfer,e_wallet,credit_card',
        ]);

        // Mock payment processing
        $payment->markAsPaid($request->method, [
            'processed_at' => now()->toIso8601String(),
            'mock' => true,
        ]);

        // Update ticket status to confirmed
        if ($payment->payable instanceof Ticket) {
            $payment->payable->update(['status' => 'confirmed']);

            // Generate QR code for ticket
            $this->generateTicketQrCode($payment->payable);
        }

        return redirect()->route('tickets.show', $payment->payable)
            ->with('success', 'Pembayaran berhasil! Tiket Anda sudah dikonfirmasi.');
    }

    /**
     * Generate QR code for ticket.
     */
    protected function generateTicketQrCode(Ticket $ticket): void
    {
        $qrData = json_encode([
            'id' => $ticket->id,
            'code' => $ticket->booking_code,
            'schedule' => $ticket->schedule_id,
            'seat' => $ticket->seat_number,
            'passenger' => $ticket->passenger_name,
        ]);

        // Store QR code path (we'll generate actual QR later with package)
        $ticket->update([
            'qr_code' => "qr/{$ticket->booking_code}.png",
        ]);
    }

    /**
     * Show receipt.
     */
    public function receipt(Payment $payment)
    {
        if ($payment->user_id !== request()->user()->id && !request()->user()->isAdmin()) {
            abort(403);
        }

        if (!$payment->isPaid()) {
            return back()->with('error', 'Pembayaran belum selesai.');
        }

        $payment->load(
            'user',
            'payable.schedule.route.originTerminal.city',
            'payable.schedule.route.destinationTerminal.city',
            'payable.schedule.busOperator',
            'payable.schedule.bus.busClass'
        );

        return view('payments.receipt', compact('payment'));
    }
}
