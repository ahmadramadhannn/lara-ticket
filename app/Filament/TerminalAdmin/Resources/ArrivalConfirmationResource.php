<?php

namespace App\Filament\TerminalAdmin\Resources;

use App\Filament\TerminalAdmin\Resources\ArrivalConfirmationResource\Pages;
use App\Models\Route;
use App\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ArrivalConfirmationResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Arrival Confirmations';

    protected static ?string $modelLabel = 'Arrival Confirmation';

    protected static ?string $pluralModelLabel = 'Arrival Confirmations';

    protected static ?string $navigationGroup = 'Terminal Operations';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        
        // Get terminal IDs where user can confirm arrivals
        $terminalIds = $user->assignedTerminals()
            ->wherePivot('can_confirm_arrivals', true)
            ->pluck('terminals.id')
            ->toArray();
        
        // Get routes where destination is in user's terminals
        $routeIds = Route::whereIn('destination_terminal_id', $terminalIds)
            ->pluck('id')
            ->toArray();
        
        return parent::getEloquentQuery()
            ->whereIn('route_id', $routeIds)
            ->where('status', '!=', 'cancelled');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Schedule Details')
                    ->schema([
                        Forms\Components\Placeholder::make('route')
                            ->content(fn (Schedule $record): string => $record->route->route_name),
                        Forms\Components\Placeholder::make('bus')
                            ->content(fn (Schedule $record): string => $record->bus->registration_number),
                        Forms\Components\Placeholder::make('operator')
                            ->content(fn (Schedule $record): string => $record->busOperator->name),
                        Forms\Components\Placeholder::make('departure')
                            ->content(fn (Schedule $record): string => $record->departure_time->format('d M Y H:i')),
                        Forms\Components\Placeholder::make('arrival')
                            ->content(fn (Schedule $record): string => $record->arrival_time->format('d M Y H:i')),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Confirmation')
                    ->schema([
                        Forms\Components\Select::make('arrival_status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'rejected' => 'Rejected',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('confirmation_notes')
                            ->label('Notes')
                            ->placeholder('Optional notes about the arrival...')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('route.route_name')
                    ->label('Route')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('busOperator.name')
                    ->label('Operator')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bus.registration_number')
                    ->label('Bus')
                    ->searchable(),
                Tables\Columns\TextColumn::make('departure_time')
                    ->label('Departure')
                    ->dateTime('d M H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('arrival_time')
                    ->label('Expected Arrival')
                    ->dateTime('d M H:i')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Trip Status')
                    ->colors([
                        'primary' => 'scheduled',
                        'warning' => 'departed',
                        'success' => 'arrived',
                    ]),
                Tables\Columns\BadgeColumn::make('arrival_status')
                    ->label('Confirmation')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('confirmed_at')
                    ->label('Confirmed At')
                    ->dateTime('d M H:i')
                    ->placeholder('-'),
            ])
            ->defaultSort('arrival_time', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('arrival_status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending'),
                Tables\Filters\Filter::make('today')
                    ->query(fn (Builder $query) => $query->whereDate('arrival_time', today()))
                    ->label('Arriving Today')
                    ->toggle()
                    ->default(true),
                Tables\Filters\Filter::make('departed_only')
                    ->query(fn (Builder $query) => $query->whereIn('status', ['departed', 'arrived']))
                    ->label('In Transit / Arrived Only')
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Confirm Arrival')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Confirmation Notes')
                            ->placeholder('Optional notes...')
                            ->rows(2),
                    ])
                    ->visible(fn (Schedule $record) => $record->arrival_status === 'pending')
                    ->action(function (Schedule $record, array $data) {
                        $record->confirmArrival(Auth::user(), $data['notes'] ?? null);
                    }),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Arrival')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Rejection Reason')
                            ->placeholder('Please provide a reason for rejection...')
                            ->required()
                            ->rows(2),
                    ])
                    ->visible(fn (Schedule $record) => $record->arrival_status === 'pending')
                    ->action(function (Schedule $record, array $data) {
                        $record->rejectArrival(Auth::user(), $data['notes']);
                    }),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulk_confirm')
                    ->label('Confirm Selected')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            if ($record->arrival_status === 'pending') {
                                $record->confirmArrival(Auth::user());
                            }
                        }
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArrivalConfirmations::route('/'),
        ];
    }
}
