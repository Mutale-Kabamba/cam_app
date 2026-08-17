<?php

namespace App\Filament\Resources\ConsolidatedResultResource\Pages;

use App\Filament\Resources\ConsolidatedResultResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConsolidatedResults extends ListRecords
{
    protected static string $resource = ConsolidatedResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
