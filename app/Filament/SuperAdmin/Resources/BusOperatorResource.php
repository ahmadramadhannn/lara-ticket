<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\BusOperatorResource\Pages;
use App\Models\BusOperator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class BusOperatorResource extends Resource
{
    protected static ?string $model = BusOperator::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Operators';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Bus Operators';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Operator Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(10)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('contact_phone')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('contact_email')
                            ->email()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('approval_status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact_email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_phone'),
                Tables\Columns\BadgeColumn::make('approval_status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('buses_count')
                    ->label('Buses')
                    ->counts('buses')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('approval_status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (BusOperator $record) => $record->isPending())
                    ->action(function (BusOperator $record) {
                        $record->update([
                            'approval_status' => 'approved',
                            'approved_by' => Auth::id(),
                            'approved_at' => now(),
                            'is_active' => true,
                        ]);

                        // Also activate the associated user
                        if ($record->submittedBy) {
                            $record->submittedBy->update(['user_status' => 'active']);
                        }

                        // Log the approval
                        \App\Models\ActivityLog::log(
                            action: 'approved',
                            subjectType: 'BusOperator',
                            subjectId: $record->id,
                            description: "Approved operator registration: {$record->name} ({$record->code})"
                        );

                        Notification::make()
                            ->title('Operator Approved')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (BusOperator $record) => $record->isPending())
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Reason for rejection')
                            ->required()
                            ->helperText('This reason will be recorded and can be viewed later.')
                            ->rows(4),
                    ])
                    ->action(function (BusOperator $record, array $data) {
                        $record->update([
                            'approval_status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'rejected_at' => now(),
                        ]);

                        // Suspend the associated user
                        if ($record->submittedBy) {
                            $record->submittedBy->update(['user_status' => 'suspended']);
                        }

                        // Log the rejection
                        \App\Models\ActivityLog::log(
                            action: 'rejected',
                            subjectType: 'BusOperator',
                            subjectId: $record->id,
                            description: "Rejected operator registration: {$record->name} ({$record->code}) - Reason: {$data['rejection_reason']}"
                        );

                        Notification::make()
                            ->title('Operator Rejected')
                            ->warning()
                            ->send();

                        // TODO: Send email notification to the submitter with rejection reason
                    }),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBusOperators::route('/'),
            'create' => Pages\CreateBusOperator::route('/create'),
            'edit' => Pages\EditBusOperator::route('/{record}/edit'),
        ];
    }
}
