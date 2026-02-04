<?php

namespace App\Filament\SuperAdmin\Resources\TerminalResource\Pages;

use App\Filament\SuperAdmin\Resources\TerminalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTerminal extends EditRecord
{
    protected static string $resource = TerminalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
