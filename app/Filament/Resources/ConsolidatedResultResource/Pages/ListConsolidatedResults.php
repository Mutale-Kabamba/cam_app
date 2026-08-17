<?php

namespace App\Filament\Resources\ConsolidatedResultResource\Pages;

use App\Filament\Exports\ConsolidatedResultExporter;
use App\Filament\Resources\ConsolidatedResultResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListConsolidatedResults extends ListRecords
{
    protected static string $resource = ConsolidatedResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->label('Export Standings')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->exporter(ConsolidatedResultExporter::class),

            Actions\CreateAction::make(),
        ];
    }
}
