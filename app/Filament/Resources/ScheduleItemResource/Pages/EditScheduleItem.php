<?php

namespace App\Filament\Resources\ScheduleItemResource\Pages;

use App\Filament\Resources\ScheduleItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScheduleItem extends EditRecord
{
    protected static string $resource = ScheduleItemResource::class;

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
