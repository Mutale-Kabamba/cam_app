<?php

namespace App\Filament\Resources\AdjudicationScoreResource\Pages;

use App\Filament\Resources\AdjudicationScoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdjudicationScores extends ListRecords
{
    protected static string $resource = AdjudicationScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
