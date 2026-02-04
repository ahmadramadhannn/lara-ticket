<?php

namespace App\Filament\SuperAdmin\Resources\BusOperatorResource\Pages;

use App\Filament\SuperAdmin\Resources\BusOperatorResource;
use Filament\Resources\Pages\ListRecords;

class ListBusOperators extends ListRecords
{
    protected static string $resource = BusOperatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
