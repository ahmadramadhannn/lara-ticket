<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SampleBookingSeeder extends Seeder
{
    /**
     * Seed sample tickets and payments for demo users.
     */
    public function run(): void
    {
        $buyers = User::where('role', 'buyer')->get();
        $schedules = Schedule::with('route', 'bus')
            ->where('departure_time', '>=', now())
            ->where('available_seats', '>', 0)
            ->inRandomOrder()
            ->limit(30)
            ->get();

        if ($buyers->isEmpty() || $schedules->isEmpty()) {
            $this->command->warn('No buyers or schedules available for sample bookings.');
            return;
        }

        $passengerNames = [
            'Ahmad Rizki', 'Siti Nurhaliza', 'Budi Santoso', 'Dewi Kartika',
            'Eko Prasetyo', 'Fitri Handayani', 'Gunawan Wibowo', 'Henny Susanti',
            'Irfan Hakim', 'Julia Perez', 'Kurniawan Dwi', 'Lina Marlina',
            'Muhammad Fadli', 'Nadia Saphira', 'Oscar Lawalata', 'Putri Ayu',
            'Qori Sandioriva', 'Rudi Hartono', 'Sri Mulyani', 'Taufik Hidayat',
        ];

        $ticketCount = 0;

        foreach ($buyers as $buyer) {
            // Each buyer gets 3-5 tickets
            $numTickets = rand(3, 5);

            for ($i = 0; $i < $numTickets && $schedules->isNotEmpty(); $i++) {
                $schedule = $schedules->random();

                if ($schedule->available_seats < 1) {
                    continue;
                }

                // Get a random available seat
                $bookedSeats = Ticket::where('schedule_id', $schedule->id)
                    ->whereIn('status', ['pending', 'confirmed', 'used'])
                    ->pluck('seat_number')
                    ->toArray();

                $allSeats = range(1, $schedule->bus->total_seats);
                $availableSeats = array_diff($allSeats, $bookedSeats);

                if (empty($availableSeats)) {
                    continue;
                }

                $seatNumber = array_values($availableSeats)[array_rand($availableSeats)];
                $passengerName = $passengerNames[array_rand($passengerNames)];

                // Determine ticket status based on departure time
                $status = 'confirmed';
                $verifiedAt = null;
                $verifiedBy = null;

                if ($schedule->departure_time < now()) {
                    // Past schedules - mark some as used
                    $status = rand(0, 1) ? 'used' : 'expired';
                    if ($status === 'used') {
                        $verifiedAt = $schedule->departure_time->addMinutes(rand(5, 30));
                        $verifiedBy = User::where('role', 'verifier')->inRandomOrder()->first()?->id;
                    }
                }

                // Create ticket
                $ticket = Ticket::create([
                    'user_id' => $buyer->id,
                    'schedule_id' => $schedule->id,
                    'booking_code' => strtoupper(Str::random(8)),
                    'seat_number' => $seatNumber,
                    'passenger_name' => $passengerName,
                    'passenger_id_number' => '32' . rand(10, 99) . rand(1000000000, 9999999999),
                    'price' => $schedule->base_price,
                    'status' => $status,
                    'verified_at' => $verifiedAt,
                    'verified_by' => $verifiedBy,
                ]);

                // Create payment for confirmed/used tickets
                if (in_array($status, ['confirmed', 'used'])) {
                    $paymentMethods = ['bank_transfer', 'e_wallet', 'credit_card'];

                    Payment::create([
                        'user_id' => $buyer->id,
                        'payable_type' => Ticket::class,
                        'payable_id' => $ticket->id,
                        'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                        'amount' => $schedule->base_price,
                        'method' => $paymentMethods[array_rand($paymentMethods)],
                        'status' => 'paid',
                        'paid_at' => $ticket->created_at,
                        'metadata' => json_encode([
                            'transaction_id' => 'TXN-' . strtoupper(Str::random(10)),
                            'payment_gateway' => 'MockPay',
                        ]),
                    ]);
                }

                // Update available seats
                $schedule->decrement('available_seats');

                $ticketCount++;
            }
        }

        $this->command->info("Created {$ticketCount} sample tickets with payments.");
    }
}
