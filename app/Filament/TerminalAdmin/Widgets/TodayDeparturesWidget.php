<?php

namespace App\Filament\TerminalAdmin\Widgets;

use App\Models\Route;
use App\Models\Schedule;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TodayDeparturesWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = "Today's Departures";

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $terminalIds = $user->assignedTerminals()->pluck('terminals.id')->toArray();
        
        $routeIds = Route::whereIn('origin_terminal_id', $terminalIds)->pluck('id')->toArray();

        return $table
            ->query(
                Schedule::query()
                    ->whereIn('route_id', $routeIds)
                    ->whereDate('departure_time', today())
                    ->orderBy('departure_time')
            )
            ->columns([
                Tables\Columns\TextColumn::make('departure_time')
                    ->label('Time')
                    ->time('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('route.destinationTerminal.name')
                    ->label('Destination'),
                Tables\Columns\TextColumn::make('bus.registration_number')
                    ->label('Bus'),
                Tables\Columns\TextColumn::make('available_seats')
                    ->label('Seats'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'scheduled',
                        'warning' => 'departed',
                        'success' => 'arrived',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->paginated(false);
    }
}
