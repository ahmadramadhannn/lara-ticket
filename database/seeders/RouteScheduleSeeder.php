<?php

namespace Database\Seeders;

use App\Models\Terminal;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Bus;
use App\Models\BusOperator;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RouteScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define popular routes - comprehensive coverage
        $routeDefinitions = [
            // === Jakarta routes ===
            // From Terminal Kalideres (JKT-KLD)
            ['origin' => 'JKT-KLD', 'destination' => 'BDG-CCH', 'distance' => 150, 'duration' => 180],
            ['origin' => 'JKT-KLD', 'destination' => 'BDG-LWP', 'distance' => 140, 'duration' => 170],
            ['origin' => 'JKT-KLD', 'destination' => 'SMG-TRB', 'distance' => 450, 'duration' => 420],
            ['origin' => 'JKT-KLD', 'destination' => 'SLO-TRT', 'distance' => 550, 'duration' => 480],
            ['origin' => 'JKT-KLD', 'destination' => 'SBY-PRB', 'distance' => 780, 'duration' => 720],
            ['origin' => 'JKT-KLD', 'destination' => 'YGY-GWN', 'distance' => 520, 'duration' => 480],
            ['origin' => 'JKT-KLD', 'destination' => 'MLG-ARJ', 'distance' => 850, 'duration' => 780],

            // From Terminal Lebak Bulus (JKT-LBB)
            ['origin' => 'JKT-LBB', 'destination' => 'BDG-CCH', 'distance' => 145, 'duration' => 175],
            ['origin' => 'JKT-LBB', 'destination' => 'BDG-LWP', 'distance' => 140, 'duration' => 165],
            ['origin' => 'JKT-LBB', 'destination' => 'BGR-BRN', 'distance' => 50, 'duration' => 60],
            ['origin' => 'JKT-LBB', 'destination' => 'SMG-TRB', 'distance' => 445, 'duration' => 410],

            // From Terminal Pulo Gebang (JKT-PLG)
            ['origin' => 'JKT-PLG', 'destination' => 'SMG-TRB', 'distance' => 440, 'duration' => 400],
            ['origin' => 'JKT-PLG', 'destination' => 'SBY-PRB', 'distance' => 775, 'duration' => 710],
            ['origin' => 'JKT-PLG', 'destination' => 'BKS-TMN', 'distance' => 20, 'duration' => 30],

            // From Terminal Kampung Rambutan (JKT-KPR)
            ['origin' => 'JKT-KPR', 'destination' => 'BGR-BRN', 'distance' => 40, 'duration' => 50],
            ['origin' => 'JKT-KPR', 'destination' => 'BDG-CCH', 'distance' => 160, 'duration' => 190],

            // === Bandung routes ===
            ['origin' => 'BDG-CCH', 'destination' => 'JKT-KLD', 'distance' => 150, 'duration' => 180],
            ['origin' => 'BDG-CCH', 'destination' => 'JKT-LBB', 'distance' => 145, 'duration' => 175],
            ['origin' => 'BDG-CCH', 'destination' => 'SMG-TRB', 'distance' => 350, 'duration' => 300],
            ['origin' => 'BDG-CCH', 'destination' => 'SBY-PRB', 'distance' => 650, 'duration' => 600],
            ['origin' => 'BDG-CCH', 'destination' => 'YGY-GWN', 'distance' => 400, 'duration' => 360],

            ['origin' => 'BDG-LWP', 'destination' => 'JKT-KLD', 'distance' => 140, 'duration' => 170],
            ['origin' => 'BDG-LWP', 'destination' => 'TSM-IDH', 'distance' => 120, 'duration' => 150],
            ['origin' => 'BDG-LWP', 'destination' => 'SMG-TRB', 'distance' => 355, 'duration' => 305],

            // === Bekasi routes ===
            ['origin' => 'BKS-TMN', 'destination' => 'SMG-TRB', 'distance' => 435, 'duration' => 395],
            ['origin' => 'BKS-TMN', 'destination' => 'SBY-PRB', 'distance' => 770, 'duration' => 705],
            ['origin' => 'BKS-TMN', 'destination' => 'BDG-CCH', 'distance' => 120, 'duration' => 150],

            // === Bogor routes ===
            ['origin' => 'BGR-BRN', 'destination' => 'BDG-CCH', 'distance' => 100, 'duration' => 120],
            ['origin' => 'BGR-BRN', 'destination' => 'JKT-LBB', 'distance' => 50, 'duration' => 60],
            ['origin' => 'BGR-BRN', 'destination' => 'JKT-KPR', 'distance' => 40, 'duration' => 50],

            // === Tasikmalaya routes ===
            ['origin' => 'TSM-IDH', 'destination' => 'BDG-LWP', 'distance' => 120, 'duration' => 150],
            ['origin' => 'TSM-IDH', 'destination' => 'JKT-KLD', 'distance' => 260, 'duration' => 300],

            // === Semarang routes ===
            ['origin' => 'SMG-TRB', 'destination' => 'JKT-KLD', 'distance' => 450, 'duration' => 420],
            ['origin' => 'SMG-TRB', 'destination' => 'BDG-CCH', 'distance' => 350, 'duration' => 300],
            ['origin' => 'SMG-TRB', 'destination' => 'SLO-TRT', 'distance' => 100, 'duration' => 90],
            ['origin' => 'SMG-TRB', 'destination' => 'SBY-PRB', 'distance' => 350, 'duration' => 300],
            ['origin' => 'SMG-TRB', 'destination' => 'YGY-GWN', 'distance' => 130, 'duration' => 120],
            ['origin' => 'SMG-TRB', 'destination' => 'MLG-ARJ', 'distance' => 400, 'duration' => 360],

            ['origin' => 'SMG-MKG', 'destination' => 'JKT-KLD', 'distance' => 445, 'duration' => 415],
            ['origin' => 'SMG-MKG', 'destination' => 'SBY-PRB', 'distance' => 355, 'duration' => 305],

            // === Solo routes ===
            ['origin' => 'SLO-TRT', 'destination' => 'JKT-KLD', 'distance' => 550, 'duration' => 480],
            ['origin' => 'SLO-TRT', 'destination' => 'SMG-TRB', 'distance' => 100, 'duration' => 90],
            ['origin' => 'SLO-TRT', 'destination' => 'SBY-PRB', 'distance' => 260, 'duration' => 240],
            ['origin' => 'SLO-TRT', 'destination' => 'MLG-ARJ', 'distance' => 200, 'duration' => 210],
            ['origin' => 'SLO-TRT', 'destination' => 'YGY-GWN', 'distance' => 65, 'duration' => 75],

            // === Purwokerto routes ===
            ['origin' => 'PWK-BLP', 'destination' => 'JKT-KLD', 'distance' => 350, 'duration' => 320],
            ['origin' => 'PWK-BLP', 'destination' => 'SMG-TRB', 'distance' => 180, 'duration' => 160],
            ['origin' => 'PWK-BLP', 'destination' => 'YGY-GWN', 'distance' => 150, 'duration' => 140],

            // === Cilacap routes ===
            ['origin' => 'CLP-MOS', 'destination' => 'JKT-KLD', 'distance' => 380, 'duration' => 350],
            ['origin' => 'CLP-MOS', 'destination' => 'YGY-GWN', 'distance' => 180, 'duration' => 170],

            // === Surabaya routes ===
            ['origin' => 'SBY-PRB', 'destination' => 'JKT-KLD', 'distance' => 780, 'duration' => 720],
            ['origin' => 'SBY-PRB', 'destination' => 'SMG-TRB', 'distance' => 350, 'duration' => 300],
            ['origin' => 'SBY-PRB', 'destination' => 'SLO-TRT', 'distance' => 260, 'duration' => 240],
            ['origin' => 'SBY-PRB', 'destination' => 'MLG-ARJ', 'distance' => 100, 'duration' => 120],
            ['origin' => 'SBY-PRB', 'destination' => 'YGY-GWN', 'distance' => 330, 'duration' => 300],
            ['origin' => 'SBY-PRB', 'destination' => 'DPS-UBG', 'distance' => 400, 'duration' => 660],
            ['origin' => 'SBY-PRB', 'destination' => 'BDG-CCH', 'distance' => 650, 'duration' => 600],

            // === Malang routes ===
            ['origin' => 'MLG-ARJ', 'destination' => 'SBY-PRB', 'distance' => 100, 'duration' => 120],
            ['origin' => 'MLG-ARJ', 'destination' => 'SLO-TRT', 'distance' => 200, 'duration' => 210],
            ['origin' => 'MLG-ARJ', 'destination' => 'JKT-KLD', 'distance' => 850, 'duration' => 780],

            // === Kediri routes ===
            ['origin' => 'KDR-TMN', 'destination' => 'SBY-PRB', 'distance' => 130, 'duration' => 150],
            ['origin' => 'KDR-TMN', 'destination' => 'MLG-ARJ', 'distance' => 80, 'duration' => 100],
            ['origin' => 'KDR-TMN', 'destination' => 'SLO-TRT', 'distance' => 180, 'duration' => 200],

            // === Yogyakarta routes ===
            ['origin' => 'YGY-GWN', 'destination' => 'JKT-KLD', 'distance' => 520, 'duration' => 480],
            ['origin' => 'YGY-GWN', 'destination' => 'SMG-TRB', 'distance' => 130, 'duration' => 120],
            ['origin' => 'YGY-GWN', 'destination' => 'SLO-TRT', 'distance' => 65, 'duration' => 75],
            ['origin' => 'YGY-GWN', 'destination' => 'SBY-PRB', 'distance' => 330, 'duration' => 300],
            ['origin' => 'YGY-GWN', 'destination' => 'BDG-CCH', 'distance' => 400, 'duration' => 360],

            // === Bali routes ===
            ['origin' => 'DPS-UBG', 'destination' => 'SBY-PRB', 'distance' => 400, 'duration' => 660],
            ['origin' => 'DPS-MWI', 'destination' => 'SBY-PRB', 'distance' => 410, 'duration' => 670],
        ];

        // Create routes
        $routes = [];
        foreach ($routeDefinitions as $def) {
            $origin = Terminal::where('code', $def['origin'])->first();
            $destination = Terminal::where('code', $def['destination'])->first();

            if ($origin && $destination) {
                $route = Route::firstOrCreate(
                    [
                        'origin_terminal_id' => $origin->id,
                        'destination_terminal_id' => $destination->id,
                    ],
                    [
                        'distance_km' => $def['distance'],
                        'estimated_duration_minutes' => $def['duration'],
                        'is_active' => true,
                    ]
                );
                $routes[$def['origin'] . '->' . $def['destination']] = $route;
            }
        }

        // Create schedules for the next 30 days (extended from 14)
        $operators = BusOperator::with('buses.busClass')->get();
        $today = Carbon::today();

        // Base prices per class (per 100km)
        $pricePerKm = [
            'Ekonomi' => 300,
            'Bisnis' => 450,
            'Eksekutif' => 600,
            'Super Eksekutif' => 850,
            'Sleeper' => 1200,
        ];

        // Departure times for schedules - comprehensive coverage
        $departureTimes = [
            '05:00', '06:00', '07:00', '07:30', '08:00', '09:00', '10:00', '10:30',
            '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00',
            '19:00', '20:00', '21:00', '21:30', '22:00', '23:00', '23:30',
        ];

        foreach ($routes as $route) {
            // Assign 2-5 operators per route randomly
            $routeOperators = $operators->random(rand(2, min(5, $operators->count())));

            foreach ($routeOperators as $operator) {
                // Each operator has 3-6 departures per day on this route
                $operatorDepartures = collect($departureTimes)->random(rand(3, 6))->sort()->values();

                // Select buses for this operator (different classes)
                $operatorBuses = $operator->buses->shuffle()->take(rand(2, 4));

                // 30 days of schedules
                for ($day = 0; $day < 30; $day++) {
                    $departureDate = $today->copy()->addDays($day);

                    foreach ($operatorDepartures as $index => $time) {
                        $bus = $operatorBuses[$index % count($operatorBuses)];
                        $className = $bus->busClass->name;

                        $departureTime = Carbon::parse($departureDate->format('Y-m-d') . ' ' . $time);
                        $arrivalTime = $departureTime->copy()->addMinutes($route->estimated_duration_minutes);

                        // Calculate price based on distance and class
                        $basePrice = ($route->distance_km / 100) * ($pricePerKm[$className] ?? 400);
                        // Round to nearest 5000
                        $basePrice = round($basePrice / 5000) * 5000;
                        // Minimum price
                        $basePrice = max($basePrice, 25000);

                        // Weekend surcharge
                        if ($departureDate->isWeekend()) {
                            $basePrice *= 1.15;
                            $basePrice = round($basePrice / 5000) * 5000;
                        }

                        Schedule::firstOrCreate(
                            [
                                'route_id' => $route->id,
                                'bus_id' => $bus->id,
                                'bus_operator_id' => $operator->id,
                                'departure_time' => $departureTime,
                            ],
                            [
                                'arrival_time' => $arrivalTime,
                                'base_price' => $basePrice,
                                'available_seats' => $bus->total_seats,
                                'status' => 'scheduled',
                            ]
                        );
                    }
                }
            }
        }
    }
}
