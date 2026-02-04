<?php

namespace App\Filament\Operator\Resources\ScheduleResource\Pages;

use App\Filament\Operator\Resources\ScheduleResource;
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
