<?php

namespace App\Filament\SuperAdmin\Resources\BusOperatorResource\Pages;

use App\Filament\SuperAdmin\Resources\BusOperatorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBusOperator extends EditRecord
{
    protected static string $resource = BusOperatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
