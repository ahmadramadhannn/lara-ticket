<?php

namespace App\Filament\TerminalAdmin\Resources;

use App\Filament\TerminalAdmin\Resources\ScheduleResource\Pages;
use App\Models\Bus;
use App\Models\Route;
use App\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Terminal Operations';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        
        // Get all terminal IDs the user is assigned to
        $terminalIds = $user->assignedTerminals()->pluck('terminals.id')->toArray();
        
        // Get routes where origin OR destination is in user's terminals
        $routeIds = Route::where(function ($query) use ($terminalIds) {
            $query->whereIn('origin_terminal_id', $terminalIds)
                  ->orWhereIn('destination_terminal_id', $terminalIds);
        })->pluck('id')->toArray();
        
        $query = parent::getEloquentQuery()
            ->whereIn('route_id', $routeIds);

        // If user is linked to a specific bus operator, only show their schedules
        if ($user->bus_operator_id) {
            $query->where('bus_operator_id', $user->bus_operator_id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $terminalIds = $user->assignedTerminals()->pluck('terminals.id')->toArray();
        
        // Get routes originating from user's terminals (for creating schedules)
        $originRoutes = Route::whereIn('origin_terminal_id', $terminalIds)
            ->where('is_active', true)
            ->get()
            ->mapWithKeys(fn ($r) => [$r->id => $r->route_name]);

        return $form
            ->schema([
                Forms\Components\Section::make('Schedule Details')
                    ->schema([
                        Forms\Components\Select::make('route_id')
                            ->label('Route')
                            ->options($originRoutes)
                            ->searchable()
                            ->required()
                            ->disabled(fn ($record) => $record !== null), // Can't change route on edit
                        Forms\Components\Select::make('bus_id')
                            ->label('Bus')
                            ->relationship('bus', 'registration_number', function (Builder $query) use ($user) {
                                // If user is linked to an operator, only show their buses
                                if ($user->bus_operator_id) {
                                    $query->where('bus_operator_id', $user->bus_operator_id);
                                }
                                return $query;
                            })
                            ->searchable()
                            ->required()
                            ->disabled(fn ($record) => $record !== null),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Time & Pricing')
                    ->schema([
                        Forms\Components\DateTimePicker::make('departure_time')
                            ->label('Departure Time')
                            ->required()
                            ->minDate(now()),
                        Forms\Components\DateTimePicker::make('arrival_time')
                            ->label('Arrival Time')
                            ->required()
                            ->afterOrEqual('departure_time'),
                        Forms\Components\TextInput::make('base_price')
                            ->label('Base Price (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->minValue(0),
                        Forms\Components\TextInput::make('available_seats')
                            ->label('Available Seats')
                            ->numeric()
                            ->required()
                            ->minValue(0),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'scheduled' => 'Scheduled',
                                'departed' => 'Departed',
                                'arrived' => 'Arrived',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('scheduled')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = Auth::user();
        $terminalIds = $user->assignedTerminals()->pluck('terminals.id')->toArray();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('route.route_name')
                    ->label('Route')
                    ->searchable(['originTerminal.name', 'destinationTerminal.name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('bus.registration_number')
                    ->label('Bus')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('busOperator.name')
                    ->label('Operator')
                    ->sortable(),
                Tables\Columns\TextColumn::make('departure_time')
                    ->label('Departure')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('arrival_time')
                    ->label('Arrival')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('available_seats')
                    ->label('Seats')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'scheduled',
                        'warning' => 'departed',
                        'success' => 'arrived',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('direction')
                    ->label('Direction')
                    ->getStateUsing(function (Schedule $record) use ($terminalIds) {
                        $originId = $record->route->origin_terminal_id;
                        return in_array($originId, $terminalIds) ? '🚌 Departing' : '📍 Arriving';
                    }),
            ])
            ->defaultSort('departure_time', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'departed' => 'Departed',
                        'arrived' => 'Arrived',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\Filter::make('departing')
                    ->query(function (Builder $query) use ($terminalIds) {
                        $query->whereHas('route', fn ($q) => $q->whereIn('origin_terminal_id', $terminalIds));
                    })
                    ->label('Departing from my terminal')
                    ->toggle(),
                Tables\Filters\Filter::make('arriving')
                    ->query(function (Builder $query) use ($terminalIds) {
                        $query->whereHas('route', fn ($q) => $q->whereIn('destination_terminal_id', $terminalIds));
                    })
                    ->label('Arriving at my terminal')
                    ->toggle(),
                Tables\Filters\Filter::make('today')
                    ->query(fn (Builder $query) => $query->whereDate('departure_time', today()))
                    ->label('Today Only')
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(function (Schedule $record) use ($terminalIds) {
                        // Can only edit if schedule originates from user's terminal
                        return in_array($record->route->origin_terminal_id, $terminalIds);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->visible(function (Schedule $record) use ($terminalIds) {
                         return in_array($record->route->origin_terminal_id, $terminalIds);
                    }),
                Tables\Actions\Action::make('mark_departed')
                    ->icon('heroicon-o-truck')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Schedule $record) => $record->status === 'scheduled')
                    ->action(fn (Schedule $record) => $record->update(['status' => 'departed'])),
                Tables\Actions\Action::make('mark_arrived')
                    ->icon('heroicon-o-map-pin')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Schedule $record) => $record->status === 'departed')
                    ->action(fn (Schedule $record) => $record->update(['status' => 'arrived'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }
}
