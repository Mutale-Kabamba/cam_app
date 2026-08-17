<?php

namespace App\Filament\Resources\ScheduleItemResource\Pages;

use App\Filament\Resources\ScheduleItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateScheduleItem extends CreateRecord
{
    protected static string $resource = ScheduleItemResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
