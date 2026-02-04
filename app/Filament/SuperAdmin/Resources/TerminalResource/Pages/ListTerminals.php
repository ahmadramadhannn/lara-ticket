<?php

namespace App\Filament\SuperAdmin\Resources\TerminalResource\Pages;

use App\Filament\SuperAdmin\Resources\TerminalResource;
use Filament\Resources\Pages\ListRecords;

class ListTerminals extends ListRecords
{
    protected static string $resource = TerminalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
