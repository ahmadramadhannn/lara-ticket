<?php

namespace App\Filament\CompanyAdmin\Resources;

use App\Filament\CompanyAdmin\Resources\TerminalAdminResource\Pages;
use App\Models\Terminal;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TerminalAdminResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Terminal Admins';

    protected static ?string $modelLabel = 'Terminal Admin';

    protected static ?string $pluralModelLabel = 'Terminal Admins';

    protected static ?string $navigationGroup = 'Team Management';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        
        // Get terminal admins that belong to the same company (invited by this company's admins)
        return parent::getEloquentQuery()
            ->where('role', 'terminal_admin')
            ->where('bus_operator_id', $user->bus_operator_id);
    }

    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $operatorId = $user->bus_operator_id;

        return $form
            ->schema([
                Forms\Components\Section::make('Account Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\Hidden::make('role')
                            ->default('terminal_admin'),
                        Forms\Components\Hidden::make('bus_operator_id')
                            ->default($operatorId),
                        Forms\Components\Hidden::make('invited_by')
                            ->default($user->id),
                        Forms\Components\Select::make('user_status')
                            ->options([
                                'active' => 'Active',
                                'pending' => 'Pending',
                                'suspended' => 'Suspended',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Primary Terminal')
                    ->schema([
                        Forms\Components\Select::make('terminal_id')
                            ->label('Primary Terminal')
                            ->options(Terminal::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->helperText('The main terminal this admin will manage'),
                    ]),

                Forms\Components\Section::make('Terminal Assignments')
                    ->schema([
                        Forms\Components\Repeater::make('terminalAssignments')
                            ->relationship('assignedTerminals')
                            ->schema([
                                Forms\Components\Select::make('terminal_id')
                                    ->label('Terminal')
                                    ->options(Terminal::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                Forms\Components\Select::make('assignment_type')
                                    ->options([
                                        'primary' => 'Primary',
                                        'backup' => 'Backup',
                                    ])
                                    ->default('primary')
                                    ->required(),
                                Forms\Components\Toggle::make('can_manage_schedules')
                                    ->label('Manage Schedules')
                                    ->default(true),
                                Forms\Components\Toggle::make('can_verify_tickets')
                                    ->label('Verify Tickets')
                                    ->default(true),
                                Forms\Components\Toggle::make('can_confirm_arrivals')
                                    ->label('Confirm Arrivals')
                                    ->default(true),
                            ])
                            ->columns(5)
                            ->defaultItems(1)
                            ->addActionLabel('Add Terminal Assignment')
                            ->itemLabel(fn (array $state): ?string => 
                                Terminal::find($state['terminal_id'])?->name ?? 'New Assignment'
                            ),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('primaryTerminal.name')
                    ->label('Primary Terminal')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('assignedTerminals_count')
                    ->counts('assignedTerminals')
                    ->label('Terminals'),
                Tables\Columns\BadgeColumn::make('user_status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending',
                        'danger' => 'suspended',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_status')
                    ->options([
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'suspended' => 'Suspended',
                    ]),
                Tables\Filters\SelectFilter::make('terminal_id')
                    ->label('Primary Terminal')
                    ->options(Terminal::where('is_active', true)->pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('suspend')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (User $record) => $record->user_status === 'active')
                    ->action(fn (User $record) => $record->update(['user_status' => 'suspended'])),
                Tables\Actions\Action::make('activate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (User $record) => $record->user_status !== 'active')
                    ->action(fn (User $record) => $record->update(['user_status' => 'active'])),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTerminalAdmins::route('/'),
            'create' => Pages\CreateTerminalAdmin::route('/create'),
            'edit' => Pages\EditTerminalAdmin::route('/{record}/edit'),
        ];
    }
}
