<?php

namespace App\Filament\Exports;

use App\Models\ConsolidatedResult;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ConsolidatedResultExporter extends Exporter
{
    protected static ?string $model = ConsolidatedResult::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('category.name')
                ->label('Category'),
            ExportColumn::make('parish.name')
                ->label('Parish'),
            ExportColumn::make('adjudicators_average')
                ->label('3-Judge Average'),
            ExportColumn::make('time_penalty')
                ->label('Time Penalty'),
            ExportColumn::make('final_score')
                ->label('Final Score'),
            ExportColumn::make('rank')
                ->label('Rank'),
            ExportColumn::make('championship_points')
                ->label('Championship Points'),
            ExportColumn::make('is_finalized')
                ->label('Published')
                ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Consolidated standings export complete. ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed.';
        }

        return $body;
    }
}
