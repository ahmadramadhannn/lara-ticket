<?php

namespace App\Filament\SuperAdmin\Resources\RouteResource\Pages;

use App\Filament\SuperAdmin\Resources\RouteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRoute extends CreateRecord
{
    protected static string $resource = RouteResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
