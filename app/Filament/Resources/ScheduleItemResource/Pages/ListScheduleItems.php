<?php

namespace App\Filament\Resources\ScheduleItemResource\Pages;

use App\Filament\Resources\ScheduleItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScheduleItems extends ListRecords
{
    protected static string $resource = ScheduleItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
