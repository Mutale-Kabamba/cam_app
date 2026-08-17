<?php

namespace App\Filament\Resources\AdjudicationScoreResource\Pages;

use App\Filament\Exports\AdjudicationScoreExporter;
use App\Filament\Resources\AdjudicationScoreResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListAdjudicationScores extends ListRecords
{
    protected static string $resource = AdjudicationScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->label('Export Scores')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->exporter(AdjudicationScoreExporter::class),

            Actions\CreateAction::make(),
        ];
    }
}
