<?php

namespace App\Filament\Operator\Resources;

use App\Filament\Operator\Resources\ScheduleResource\Pages;
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

    protected static ?string $navigationGroup = 'Fleet Management';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        // Scope to current operator's schedules only
        $user = Auth::user();
        
        return parent::getEloquentQuery()
            ->where('bus_operator_id', $user->bus_operator_id);
    }

    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $operatorId = $user->bus_operator_id;

        return $form
            ->schema([
                Forms\Components\Section::make('Schedule Details')
                    ->schema([
                        Forms\Components\Hidden::make('bus_operator_id')
                            ->default($operatorId),
                        Forms\Components\Select::make('route_id')
                            ->label('Route')
                            ->options(Route::where('is_active', true)->get()->mapWithKeys(fn ($r) => [$r->id => $r->route_name]))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('bus_id')
                            ->label('Bus')
                            ->options(Bus::where('bus_operator_id', $operatorId)->where('is_active', true)->get()->mapWithKeys(fn ($b) => [$b->id => $b->registration_number . ' (' . $b->busClass?->name . ')']))
                            ->searchable()
                            ->required(),
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
                Tables\Columns\TextColumn::make('departure_time')
                    ->label('Departure')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('arrival_time')
                    ->label('Arrival')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('base_price')
                    ->label('Price')
                    ->money('IDR')
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
                Tables\Filters\Filter::make('upcoming')
                    ->query(fn (Builder $query) => $query->where('departure_time', '>', now()))
                    ->label('Upcoming Only')
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('cancel')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Schedule $record) => $record->status === 'scheduled')
                    ->action(fn (Schedule $record) => $record->update(['status' => 'cancelled'])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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
