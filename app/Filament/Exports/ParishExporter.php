<?php

namespace App\Filament\Exports;

use App\Models\Parish;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ParishExporter extends Exporter
{
    protected static ?string $model = Parish::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label('Parish Name'),
            ExportColumn::make('contact_person')
                ->label('Contact Person'),
            ExportColumn::make('contact_phone')
                ->label('Contact Phone'),
            ExportColumn::make('contact_email')
                ->label('Contact Email'),
            ExportColumn::make('male_count')
                ->label('Male Participants'),
            ExportColumn::make('female_count')
                ->label('Female Participants'),
            ExportColumn::make('camp_contingent_count')
                ->label('Total Contingent'),
            ExportColumn::make('created_at')
                ->label('Registered At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Parish registrations export complete. ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
