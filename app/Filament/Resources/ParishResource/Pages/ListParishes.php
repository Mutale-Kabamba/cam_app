<?php

namespace App\Filament\Resources\ParishResource\Pages;

use App\Filament\Exports\ParishExporter;
use App\Filament\Imports\ParishImporter;
use App\Filament\Resources\ParishResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListParishes extends ListRecords
{
    protected static string $resource = ParishResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->label('Import Parishes')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->importer(ParishImporter::class),

            ExportAction::make()
                ->label('Export Parishes')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->exporter(ParishExporter::class),

            Actions\CreateAction::make(),
        ];
    }
}
