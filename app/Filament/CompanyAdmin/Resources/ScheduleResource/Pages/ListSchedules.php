<?php

namespace App\Filament\CompanyAdmin\Resources\ScheduleResource\Pages;

use App\Filament\CompanyAdmin\Resources\ScheduleResource;
use Filament\Resources\Pages\ListRecords;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
