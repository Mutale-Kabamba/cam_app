<?php

namespace App\Filament\Imports;

use App\Models\Parish;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ParishImporter extends Importer
{
    protected static ?string $model = Parish::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Parish Name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255'])
                ->exampleHeader('Parish Name'),

            ImportColumn::make('contact_person')
                ->label('Contact Person')
                ->rules(['nullable', 'string', 'max:255'])
                ->exampleHeader('Contact Person'),

            ImportColumn::make('contact_phone')
                ->label('Contact Phone')
                ->rules(['nullable', 'string', 'max:50'])
                ->exampleHeader('Contact Phone'),

            ImportColumn::make('contact_email')
                ->label('Contact Email')
                ->rules(['nullable', 'email', 'max:255'])
                ->exampleHeader('Contact Email'),

            ImportColumn::make('male_count')
                ->label('Male Participants')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:0'])
                ->fillRecordUsing(function (Parish $record, $state) {
                    $record->male_count = intval($state ?? 0);
                })
                ->exampleHeader('Male Participants'),

            ImportColumn::make('female_count')
                ->label('Female Participants')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:0'])
                ->fillRecordUsing(function (Parish $record, $state) {
                    $record->female_count = intval($state ?? 0);
                })
                ->exampleHeader('Female Participants'),
        ];
    }

    public function resolveRecord(): ?Parish
    {
        // Update existing parish by name, or create new
        return Parish::firstOrNew([
            'name' => $this->data['name'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Parish import complete. ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed — download the failure report to review errors.';
        }

        return $body;
    }
}
