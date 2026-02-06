<?php


namespace App\Filament\SuperAdmin\Resources\UserResource\Pages;

use App\Filament\SuperAdmin\Resources\UserResource;
use App\Models\ActivityLog;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        ActivityLog::log(
            action: 'created',
            subjectType: 'User',
            subjectId: $this->record->id,
            newValues: [
                'name' => $this->record->name,
                'email' => $this->record->email,
                'role' => $this->record->role,
            ],
            description: "Created new user: {$this->record->name} ({$this->record->email}) with role {$this->record->role}"
        );
    }
}
