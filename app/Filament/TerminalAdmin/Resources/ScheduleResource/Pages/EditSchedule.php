<?php

namespace App\Filament\TerminalAdmin\Resources\ScheduleResource\Pages;

use App\Filament\TerminalAdmin\Resources\ScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSchedule extends EditRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
