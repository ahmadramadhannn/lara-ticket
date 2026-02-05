<?php

namespace App\Filament\TerminalAdmin\Resources\ArrivalConfirmationResource\Pages;

use App\Filament\TerminalAdmin\Resources\ArrivalConfirmationResource;
use Filament\Resources\Pages\ListRecords;

class ListArrivalConfirmations extends ListRecords
{
    protected static string $resource = ArrivalConfirmationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
