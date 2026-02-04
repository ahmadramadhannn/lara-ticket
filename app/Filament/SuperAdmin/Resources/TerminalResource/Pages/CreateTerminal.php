<?php

namespace App\Filament\SuperAdmin\Resources\TerminalResource\Pages;

use App\Filament\SuperAdmin\Resources\TerminalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTerminal extends CreateRecord
{
    protected static string $resource = TerminalResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
