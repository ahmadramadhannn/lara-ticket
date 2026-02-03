<?php

namespace Database\Seeders;

use App\Models\BusOperator;
use App\Models\BusClass;
use App\Models\Bus;
use Illuminate\Database\Seeder;

class BusOperatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create bus classes first
        $classes = [
            [
                'name' => 'Ekonomi',
                'amenities' => ['AC', 'Reclining Seat'],
            ],
            [
                'name' => 'Bisnis',
                'amenities' => ['AC', 'Reclining Seat', 'Leg Room', 'USB Charging'],
            ],
            [
                'name' => 'Eksekutif',
                'amenities' => ['AC', 'Reclining Seat', 'Leg Room', 'USB Charging', 'WiFi', 'Toilet'],
            ],
            [
                'name' => 'Super Eksekutif',
                'amenities' => ['AC', 'Fully Reclining Seat', 'Extra Leg Room', 'USB Charging', 'WiFi', 'Toilet', 'Selimut', 'Bantal', 'Snack'],
            ],
            [
                'name' => 'Sleeper',
                'amenities' => ['AC', 'Bed/Pod', 'USB Charging', 'WiFi', 'Toilet', 'Selimut', 'Bantal', 'Snack', 'Entertainment'],
            ],
        ];

        foreach ($classes as $classData) {
            BusClass::create($classData);
        }

        // Create bus operators
        $operators = [
            [
                'name' => 'Pahala Kencana',
                'code' => 'PK',
                'description' => 'Salah satu PO bus terbesar di Indonesia dengan armada modern.',
                'contact_phone' => '021-7891234',
                'contact_email' => 'info@pahalakencana.co.id',
            ],
            [
                'name' => 'Sinar Jaya',
                'code' => 'SJ',
                'description' => 'PO bus dengan jaringan luas di Pulau Jawa.',
                'contact_phone' => '021-5551234',
                'contact_email' => 'info@sinarjaya.co.id',
            ],
            [
                'name' => 'Harapan Jaya',
                'code' => 'HJ',
                'description' => 'PO bus premium dengan pelayanan terbaik.',
                'contact_phone' => '021-6661234',
                'contact_email' => 'info@harapanjaya.co.id',
            ],
            [
                'name' => 'Lorena',
                'code' => 'LR',
                'description' => 'PO bus legendaris dengan sejarah panjang.',
                'contact_phone' => '021-7771234',
                'contact_email' => 'info@lorena.co.id',
            ],
            [
                'name' => 'Rosalia Indah',
                'code' => 'RI',
                'description' => 'PO bus asal Solo dengan armada yang nyaman.',
                'contact_phone' => '0271-771234',
                'contact_email' => 'info@rosaliaindah.co.id',
            ],
            [
                'name' => 'Kramat Djati',
                'code' => 'KD',
                'description' => 'PO bus dengan layanan antar kota antar provinsi.',
                'contact_phone' => '021-8881234',
                'contact_email' => 'info@kramatdjati.co.id',
            ],
            [
                'name' => 'Gunung Harta',
                'code' => 'GH',
                'description' => 'PO bus dengan rute Jakarta-Bali.',
                'contact_phone' => '021-9991234',
                'contact_email' => 'info@gunungharta.co.id',
            ],
        ];

        $busClasses = BusClass::all();
        $ekonomi = $busClasses->where('name', 'Ekonomi')->first();
        $bisnis = $busClasses->where('name', 'Bisnis')->first();
        $eksekutif = $busClasses->where('name', 'Eksekutif')->first();
        $superEksekutif = $busClasses->where('name', 'Super Eksekutif')->first();
        $sleeper = $busClasses->where('name', 'Sleeper')->first();

        foreach ($operators as $operatorData) {
            $operator = BusOperator::create($operatorData);

            // Create buses for each operator
            $busConfigs = [
                ['class' => $ekonomi, 'seats' => 50, 'count' => 3],
                ['class' => $bisnis, 'seats' => 40, 'count' => 2],
                ['class' => $eksekutif, 'seats' => 32, 'count' => 2],
                ['class' => $superEksekutif, 'seats' => 24, 'count' => 1],
            ];

            // Some operators have sleeper buses
            if (in_array($operator->code, ['PK', 'HJ', 'RI'])) {
                $busConfigs[] = ['class' => $sleeper, 'seats' => 18, 'count' => 1];
            }

            $busNumber = 1;
            foreach ($busConfigs as $config) {
                for ($i = 0; $i < $config['count']; $i++) {
                    Bus::create([
                        'bus_operator_id' => $operator->id,
                        'bus_class_id' => $config['class']->id,
                        'registration_number' => sprintf('%s-%04d', $operator->code, $busNumber++),
                        'total_seats' => $config['seats'],
                        'seat_layout' => $this->generateSeatLayout($config['seats']),
                        'is_active' => true,
                    ]);
                }
            }
        }
    }

    private function generateSeatLayout(int $totalSeats): array
    {
        $layout = [];
        $row = 1;
        $seats = 0;
        $seatsPerRow = 4; // Typical bus layout: 2-2

        if ($totalSeats <= 18) {
            $seatsPerRow = 2; // Sleeper: 1-1
        } elseif ($totalSeats <= 24) {
            $seatsPerRow = 3; // Super Eksekutif: 2-1
        }

        while ($seats < $totalSeats) {
            $rowSeats = [];
            for ($col = 0; $col < $seatsPerRow && $seats < $totalSeats; $col++) {
                $seatLabel = $row . chr(65 + $col); // 1A, 1B, 1C, etc.
                $rowSeats[] = $seatLabel;
                $seats++;
            }
            $layout[] = $rowSeats;
            $row++;
        }

        return $layout;
    }
}
