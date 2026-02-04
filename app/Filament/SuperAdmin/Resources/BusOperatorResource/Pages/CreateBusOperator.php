<?php

namespace App\Filament\SuperAdmin\Resources\BusOperatorResource\Pages;

use App\Filament\SuperAdmin\Resources\BusOperatorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBusOperator extends CreateRecord
{
    protected static string $resource = BusOperatorResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
