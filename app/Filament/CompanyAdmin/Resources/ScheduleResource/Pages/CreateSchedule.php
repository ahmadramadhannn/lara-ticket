<?php

namespace App\Filament\CompanyAdmin\Resources\ScheduleResource\Pages;

use App\Filament\CompanyAdmin\Resources\ScheduleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
