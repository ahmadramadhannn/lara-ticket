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
        // Define popular routes
        $routeDefinitions = [
            // Jakarta routes
            ['origin' => 'JKT-KLD', 'destination' => 'BDG-CCH', 'distance' => 150, 'duration' => 180],
            ['origin' => 'JKT-KLD', 'destination' => 'SMG-TRB', 'distance' => 450, 'duration' => 420],
            ['origin' => 'JKT-KLD', 'destination' => 'SLO-TRT', 'distance' => 550, 'duration' => 480],
            ['origin' => 'JKT-KLD', 'destination' => 'SBY-PRB', 'distance' => 780, 'duration' => 720],
            ['origin' => 'JKT-KLD', 'destination' => 'YGY-GWN', 'distance' => 520, 'duration' => 480],

            ['origin' => 'JKT-LBB', 'destination' => 'BDG-LWP', 'distance' => 140, 'duration' => 165],
            ['origin' => 'JKT-PLG', 'destination' => 'SMG-TRB', 'distance' => 440, 'duration' => 400],

            // Bandung routes
            ['origin' => 'BDG-CCH', 'destination' => 'SMG-TRB', 'distance' => 350, 'duration' => 300],
            ['origin' => 'BDG-CCH', 'destination' => 'SBY-PRB', 'distance' => 650, 'duration' => 600],
            ['origin' => 'BDG-LWP', 'destination' => 'TSM-IDH', 'distance' => 120, 'duration' => 150],

            // Semarang routes
            ['origin' => 'SMG-TRB', 'destination' => 'SLO-TRT', 'distance' => 100, 'duration' => 90],
            ['origin' => 'SMG-TRB', 'destination' => 'SBY-PRB', 'distance' => 350, 'duration' => 300],
            ['origin' => 'SMG-TRB', 'destination' => 'YGY-GWN', 'distance' => 130, 'duration' => 120],

            // Solo routes
            ['origin' => 'SLO-TRT', 'destination' => 'SBY-PRB', 'distance' => 260, 'duration' => 240],
            ['origin' => 'SLO-TRT', 'destination' => 'MLG-ARJ', 'distance' => 200, 'duration' => 210],

            // Surabaya - Bali (long distance)
            ['origin' => 'SBY-PRB', 'destination' => 'DPS-UBG', 'distance' => 400, 'duration' => 660],

            // Return routes (reverse direction)
            ['origin' => 'BDG-CCH', 'destination' => 'JKT-KLD', 'distance' => 150, 'duration' => 180],
            ['origin' => 'SMG-TRB', 'destination' => 'JKT-KLD', 'distance' => 450, 'duration' => 420],
            ['origin' => 'SBY-PRB', 'destination' => 'JKT-KLD', 'distance' => 780, 'duration' => 720],
            ['origin' => 'SBY-PRB', 'destination' => 'SMG-TRB', 'distance' => 350, 'duration' => 300],
            ['origin' => 'YGY-GWN', 'destination' => 'JKT-KLD', 'distance' => 520, 'duration' => 480],
        ];

        // Create routes
        $routes = [];
        foreach ($routeDefinitions as $def) {
            $origin = Terminal::where('code', $def['origin'])->first();
            $destination = Terminal::where('code', $def['destination'])->first();

            if ($origin && $destination) {
                $route = Route::create([
                    'origin_terminal_id' => $origin->id,
                    'destination_terminal_id' => $destination->id,
                    'distance_km' => $def['distance'],
                    'estimated_duration_minutes' => $def['duration'],
                    'is_active' => true,
                ]);
                $routes[$def['origin'] . '->' . $def['destination']] = $route;
            }
        }

        // Create schedules for the next 14 days
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

        // Departure times for schedules
        $departureTimes = [
            '06:00', '07:30', '09:00', '10:30', '12:00',
            '14:00', '16:00', '18:00', '20:00', '21:30', '23:00',
        ];

        foreach ($routes as $route) {
            // Assign 2-4 operators per route randomly
            $routeOperators = $operators->random(rand(2, min(4, $operators->count())));

            foreach ($routeOperators as $operator) {
                // Each operator has 2-4 departures per day on this route
                $operatorDepartures = collect($departureTimes)->random(rand(2, 4))->sort()->values();

                // Select buses for this operator (different classes)
                $operatorBuses = $operator->buses->shuffle()->take(rand(2, 3));

                for ($day = 0; $day < 14; $day++) {
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

                        Schedule::create([
                            'route_id' => $route->id,
                            'bus_id' => $bus->id,
                            'bus_operator_id' => $operator->id,
                            'departure_time' => $departureTime,
                            'arrival_time' => $arrivalTime,
                            'base_price' => $basePrice,
                            'available_seats' => $bus->total_seats,
                            'status' => 'scheduled',
                        ]);
                    }
                }
            }
        }
    }
}
