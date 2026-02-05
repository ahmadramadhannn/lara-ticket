<?php

namespace App\Filament\CompanyAdmin\Resources\TerminalAdminResource\Pages;

use App\Filament\CompanyAdmin\Resources\TerminalAdminResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListTerminalAdmins extends ListRecords
{
    protected static string $resource = TerminalAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
