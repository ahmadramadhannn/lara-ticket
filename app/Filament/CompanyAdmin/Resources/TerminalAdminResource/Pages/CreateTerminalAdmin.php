<?php

namespace App\Filament\CompanyAdmin\Resources\TerminalAdminResource\Pages;

use App\Filament\CompanyAdmin\Resources\TerminalAdminResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateTerminalAdmin extends CreateRecord
{
    protected static string $resource = TerminalAdminResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        
        // Set required fields
        $data['role'] = 'terminal_admin';
        $data['bus_operator_id'] = $user->bus_operator_id;
        $data['invited_by'] = $user->id;
        
        // Generate a random password (user will reset it)
        $data['password'] = Hash::make(Str::random(16));
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Sync terminal assignments from the repeater
        // The relationship will be handled by Filament's form
        
        // TODO: Send email notification with password reset link
        // Mail::to($this->record->email)->send(new TerminalAdminInvitation($this->record));
    }
}
