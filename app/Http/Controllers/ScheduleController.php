<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Terminal;
use App\Models\BusOperator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display the schedule search page (landing page).
     */
    public function index()
    {
        $terminals = Terminal::with('city.province')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->groupBy('city.province.name');

        $operators = BusOperator::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('schedules.index', compact('terminals', 'operators'));
    }

    /**
     * Search for available schedules.
     */
    public function search(Request $request)
    {
        $request->validate([
            'origin' => 'required|exists:terminals,id',
            'destination' => 'required|exists:terminals,id|different:origin',
            'date' => 'required|date|after_or_equal:today',
            'operator' => 'nullable|exists:bus_operators,id',
        ]);

        $origin = Terminal::with('city')->find($request->origin);
        $destination = Terminal::with('city')->find($request->destination);
        $date = Carbon::parse($request->date);

        $query = Schedule::with([
            'route.originTerminal.city',
            'route.destinationTerminal.city',
            'bus.busClass',
            'busOperator',
        ])
            ->available()
            ->fromTerminal($request->origin)
            ->toTerminal($request->destination)
            ->forDate($date);

        if ($request->filled('operator')) {
            $query->byOperator($request->operator);
        }

        $schedules = $query->orderBy('departure_time')->get();

        $terminals = Terminal::with('city.province')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->groupBy('city.province.name');

        $operators = BusOperator::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('schedules.search', compact(
            'schedules',
            'origin',
            'destination',
            'date',
            'terminals',
            'operators'
        ));
    }

    /**
     * Show schedule details.
     */
    public function show(Schedule $schedule)
    {
        $schedule->load([
            'route.originTerminal.city.province',
            'route.destinationTerminal.city.province',
            'bus.busClass',
            'busOperator',
            'tickets' => function ($query) {
                $query->whereIn('status', ['confirmed', 'pending']);
            },
        ]);

        // Get booked seats
        $bookedSeats = $schedule->tickets->pluck('seat_number')->toArray();

        // Get seat layout from bus
        $seatLayout = $schedule->bus->seat_layout ?? [];

        return view('schedules.show', compact('schedule', 'bookedSeats', 'seatLayout'));
    }
}
