<?php

use App\Http\Controllers\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider within a group
| which is assigned the "api" middleware group.
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Schedule API
|--------------------------------------------------------------------------
*/

Route::prefix('schedules')->group(function () {
    /**
     * Search for available bus schedules.
     *
     * @queryParam origin integer required The origin terminal ID. Example: 1
     * @queryParam destination integer required The destination terminal ID. Example: 5
     * @queryParam date string required The departure date in Y-m-d format. Example: 2026-02-05
     * @queryParam operator integer optional Filter by bus operator ID. Example: 1
     */
    Route::get('/', function (Request $request) {
        $query = \App\Models\Schedule::with(['route.originTerminal', 'route.destinationTerminal', 'busOperator', 'bus.busClass'])
            ->whereHas('route', function ($q) use ($request) {
                $q->where('origin_terminal_id', $request->origin)
                  ->where('destination_terminal_id', $request->destination);
            })
            ->whereDate('departure_time', $request->date)
            ->where('available_seats', '>', 0)
            ->where('status', 'scheduled')
            ->orderBy('departure_time');

        if ($request->operator) {
            $query->where('bus_operator_id', $request->operator);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    })->name('api.schedules.search');

    /**
     * Get schedule details with booked seats.
     *
     * @urlParam id integer required The schedule ID. Example: 1
     */
    Route::get('/{schedule}', function (\App\Models\Schedule $schedule) {
        $schedule->load(['route.originTerminal.city', 'route.destinationTerminal.city', 'busOperator', 'bus.busClass']);

        $bookedSeats = \App\Models\Ticket::where('schedule_id', $schedule->id)
            ->whereIn('status', ['pending', 'confirmed', 'used'])
            ->pluck('seat_number')
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'schedule' => $schedule,
                'booked_seats' => $bookedSeats,
                'available_seats' => $schedule->available_seats,
            ],
        ]);
    })->name('api.schedules.show');
});

/*
|--------------------------------------------------------------------------
| Ticket Verification API
|--------------------------------------------------------------------------
*/

Route::prefix('tickets')->group(function () {
    /**
     * Verify a ticket by booking code.
     *
     * This endpoint is used by terminal verifiers to validate passenger tickets.
     *
     * @bodyParam booking_code string required The unique booking code. Example: ABCD1234
     */
    Route::post('/verify', [VerificationController::class, 'apiVerify'])
        ->name('api.tickets.verify');
});

/*
|--------------------------------------------------------------------------
| Terminal & Operator Lists
|--------------------------------------------------------------------------
*/

Route::get('/terminals', function () {
    return response()->json([
        'success' => true,
        'data' => \App\Models\Terminal::with('city.province')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(),
    ]);
})->name('api.terminals.index');

/**
 * Get list of all active bus operators.
 */
Route::get('/operators', function () {
    return response()->json([
        'success' => true,
        'data' => \App\Models\BusOperator::where('is_active', true)
            ->orderBy('name')
            ->get(),
    ]);
})->name('api.operators.index');
