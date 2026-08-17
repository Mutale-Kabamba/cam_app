<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Exports\CategoryExporter;
use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->label('Export Rubric Sheet')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->exporter(CategoryExporter::class),

            Actions\CreateAction::make(),
        ];
    }
}
