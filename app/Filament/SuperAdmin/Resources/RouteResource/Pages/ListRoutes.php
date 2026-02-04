<?php

namespace App\Filament\SuperAdmin\Resources\RouteResource\Pages;

use App\Filament\SuperAdmin\Resources\RouteResource;
use Filament\Resources\Pages\ListRecords;

class ListRoutes extends ListRecords
{
    protected static string $resource = RouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
