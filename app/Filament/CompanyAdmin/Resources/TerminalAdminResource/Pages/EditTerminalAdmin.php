<?php

namespace App\Filament\CompanyAdmin\Resources\TerminalAdminResource\Pages;

use App\Filament\CompanyAdmin\Resources\TerminalAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTerminalAdmin extends EditRecord
{
    protected static string $resource = TerminalAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
