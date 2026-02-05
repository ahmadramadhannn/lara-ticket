<?php

namespace App\Filament\TerminalAdmin\Resources\ScheduleResource\Pages;

use App\Filament\TerminalAdmin\Resources\ScheduleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        
        // Get the bus to find the operator
        $bus = \App\Models\Bus::find($data['bus_id']);
        $data['bus_operator_id'] = $bus->bus_operator_id;
        
        return $data;
    }
}
