<?php

namespace App\Filament\Operator\Resources\BusResource\Pages;

use App\Filament\Operator\Resources\BusResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBus extends CreateRecord
{
    protected static string $resource = BusResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
