<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\UserResource\Pages;
use App\Filament\SuperAdmin\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationGroup = 'User Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Details')
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
                            ->maxLength(255),
                        Forms\Components\Select::make('user_status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'pending' => 'Pending',
                            ])
                            ->default('active')
                            ->required(),
                        Forms\Components\Select::make('role')
                            ->options([
                                'super_admin' => 'Super Admin',
                                'company_admin' => 'Company Admin',
                                'terminal_admin' => 'Terminal Admin',
                                'buyer' => 'Buyer',
                            ])
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => \Illuminate\Support\Facades\Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Assignments')
                    ->description('Assign user to specific operator or terminal (for Admins)')
                    ->schema([
                        Forms\Components\Select::make('bus_operator_id')
                            ->label('Bus Operator')
                            ->relationship('busOperator', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get) => in_array($get('role'), ['company_admin', 'terminal_admin'])),
                        Forms\Components\Select::make('terminal_id')
                            ->label('Primary Terminal')
                            ->relationship('terminal', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get) => $get('role') === 'terminal_admin'),
                    ])
                    ->columns(2)
                    ->visible(fn (Forms\Get $get) => in_array($get('role'), ['company_admin', 'terminal_admin'])),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('role')
                    ->colors([
                        'danger' => 'super_admin',
                        'warning' => 'company_admin',
                        'info' => 'terminal_admin',
                        'gray' => 'buyer',
                    ]),
                Tables\Columns\TextColumn::make('busOperator.name')
                    ->label('Operator')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\BadgeColumn::make('user_status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'pending',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'company_admin' => 'Company Admin',
                        'terminal_admin' => 'Terminal Admin',
                        'buyer' => 'Buyer',
                    ]),
                Tables\Filters\SelectFilter::make('user_status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'pending' => 'Pending',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function (User $record, array $data) {
                        // Log user updates
                        \App\Models\ActivityLog::log(
                            action: 'updated',
                            subjectType: 'User',
                            subjectId: $record->id,
                            newValues: $data,
                            description: "Updated user: {$record->name} ({$record->email})"
                        );
                    }),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Tables\Actions\DeleteAction $action, User $record) {
                        // Safety Guard #1: Can't delete yourself
                        if ($record->id === \Illuminate\Support\Facades\Auth::id()) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Cannot delete yourself')
                                ->body('You cannot delete your own account while logged in.')
                                ->persistent()
                                ->send();
                            $action->cancel();
                            return;
                        }
                        
                        // Safety Guard #2: Can't delete the last active super admin
                        if ($record->isSuperAdmin()) {
                            $activeAdminCount = User::where('role', 'super_admin')
                                ->where('user_status', 'active')
                                ->count();
                            
                            if ($activeAdminCount <= 1) {
                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title('Cannot delete the last super admin')
                                    ->body('At least one active super admin must exist in the system.')
                                    ->persistent()
                                    ->send();
                                $action->cancel();
                                return;
                            }
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn (User $record) => 
                        $record->isSuperAdmin() 
                            ? '⚠️ Delete Super Admin?' 
                            : 'Delete User?'
                    )
                    ->modalDescription(fn (User $record) =>
                        $record->isSuperAdmin()
                            ? "You are about to delete a super admin account. This action cannot be undone and will remove all access for {$record->name}."
                            : "Are you sure you want to delete {$record->name}? This action cannot be undone."
                    )
                    ->modalSubmitActionLabel('Yes, Delete')
                    ->successNotificationTitle('User deleted successfully')
                    ->after(function (User $record) {
                        // Log deletion
                        \App\Models\ActivityLog::log(
                            action: 'deleted',
                            subjectType: 'User',
                            subjectId: $record->id,
                            oldValues: [
                                'name' => $record->name,
                                'email' => $record->email,
                                'role' => $record->role,
                            ],
                            description: "Deleted user: {$record->name} ({$record->email}) with role {$record->role}"
                        );
                    }),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
