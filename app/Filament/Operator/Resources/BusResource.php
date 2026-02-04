<?php

namespace App\Filament\Operator\Resources;

use App\Filament\Operator\Resources\BusResource\Pages;
use App\Models\Bus;
use App\Models\BusClass;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BusResource extends Resource
{
    protected static ?string $model = Bus::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Fleet Management';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        // Scope to current operator's buses only
        $user = Auth::user();
        
        return parent::getEloquentQuery()
            ->where('bus_operator_id', $user->bus_operator_id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bus Details')
                    ->schema([
                        Forms\Components\Hidden::make('bus_operator_id')
                            ->default(fn () => Auth::user()->bus_operator_id),
                        Forms\Components\TextInput::make('registration_number')
                            ->label('Registration Number (Plat Nomor)')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('bus_class_id')
                            ->label('Bus Class')
                            ->options(BusClass::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('total_seats')
                            ->label('Total Seats')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Seat Layout')
                    ->schema([
                        Forms\Components\KeyValue::make('seat_layout')
                            ->label('Seat Layout Configuration')
                            ->keyLabel('Row')
                            ->valueLabel('Seats per row')
                            ->addActionLabel('Add Row')
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('registration_number')
                    ->label('Registration')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('busClass.name')
                    ->label('Class')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_seats')
                    ->label('Seats')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('schedules_count')
                    ->label('Schedules')
                    ->counts('schedules')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('bus_class_id')
                    ->label('Class')
                    ->options(BusClass::all()->pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBuses::route('/'),
            'create' => Pages\CreateBus::route('/create'),
            'edit' => Pages\EditBus::route('/{record}/edit'),
        ];
    }
}
