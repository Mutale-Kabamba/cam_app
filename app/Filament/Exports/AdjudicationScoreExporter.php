<?php

namespace App\Filament\Exports;

use App\Models\AdjudicationScore;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class AdjudicationScoreExporter extends Exporter
{
    protected static ?string $model = AdjudicationScore::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('adjudicator_name')
                ->label('Judge'),
            ExportColumn::make('category.name')
                ->label('Category'),
            ExportColumn::make('parish.name')
                ->label('Parish'),
            ExportColumn::make('total_raw_score')
                ->label('Total Raw Score'),
            ExportColumn::make('final_percentage')
                ->label('Final Percentage (%)'),
            ExportColumn::make('notes')
                ->label('Judge Notes'),
            ExportColumn::make('created_at')
                ->label('Submitted At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Adjudication scores export complete. ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed.';
        }

        return $body;
    }
}
