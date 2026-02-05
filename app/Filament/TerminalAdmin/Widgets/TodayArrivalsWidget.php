<?php

namespace App\Filament\TerminalAdmin\Widgets;

use App\Models\Route;
use App\Models\Schedule;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class TodayArrivalsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = "Today's Arrivals";

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $terminalIds = $user->assignedTerminals()->pluck('terminals.id')->toArray();
        
        $routeIds = Route::whereIn('destination_terminal_id', $terminalIds)->pluck('id')->toArray();

        return $table
            ->query(
                Schedule::query()
                    ->whereIn('route_id', $routeIds)
                    ->whereDate('arrival_time', today())
                    ->orderBy('arrival_time')
            )
            ->columns([
                Tables\Columns\TextColumn::make('arrival_time')
                    ->label('ETA')
                    ->time('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('route.originTerminal.name')
                    ->label('From'),
                Tables\Columns\TextColumn::make('busOperator.name')
                    ->label('Operator'),
                Tables\Columns\TextColumn::make('bus.registration_number')
                    ->label('Bus'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'scheduled',
                        'warning' => 'departed',
                        'success' => 'arrived',
                    ]),
                Tables\Columns\BadgeColumn::make('arrival_status')
                    ->label('Confirmed')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'rejected',
                    ]),
            ])
            ->paginated(false);
    }
}
