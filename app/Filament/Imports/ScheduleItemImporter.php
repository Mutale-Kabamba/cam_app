<?php

namespace App\Filament\Imports;

use App\Models\Category;
use App\Models\Parish;
use App\Models\ScheduleItem;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ScheduleItemImporter extends Importer
{
    protected static ?string $model = ScheduleItem::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('event_date')
                ->label('Event Date')
                ->requiredMapping()
                ->rules(['required', 'date'])
                ->exampleHeader('Event Date'),

            ImportColumn::make('day_name')
                ->label('Day Name')
                ->requiredMapping()
                ->rules(['required', 'string', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'])
                ->exampleHeader('Day Name'),

            ImportColumn::make('scheduled_start_time')
                ->label('Start Time (HH:MM)')
                ->requiredMapping()
                ->rules(['required', 'string'])
                ->exampleHeader('Start Time'),

            ImportColumn::make('scheduled_end_time')
                ->label('End Time (HH:MM)')
                ->requiredMapping()
                ->rules(['required', 'string'])
                ->exampleHeader('End Time'),

            ImportColumn::make('venue')
                ->label('Venue')
                ->rules(['nullable', 'string', 'max:255'])
                ->exampleHeader('Venue'),

            ImportColumn::make('activity_title')
                ->label('Activity Title')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:500'])
                ->exampleHeader('Activity Title'),

            ImportColumn::make('category_id')
                ->label('Category (name or slug)')
                ->fillRecordUsing(function (ScheduleItem $record, $state) {
                    if (blank($state)) {
                        $record->category_id = null;
                        return;
                    }
                    $category = Category::where('name', $state)
                        ->orWhere('slug', $state)
                        ->first();
                    $record->category_id = $category?->id;
                })
                ->rules(['nullable', 'string'])
                ->exampleHeader('Category'),

            ImportColumn::make('parish_id')
                ->label('Parish (name or code)')
                ->fillRecordUsing(function (ScheduleItem $record, $state) {
                    if (blank($state)) {
                        $record->parish_id = null;
                        return;
                    }
                    $parish = Parish::where('name', $state)
                        ->orWhere('code', $state)
                        ->first();
                    $record->parish_id = $parish?->id;
                })
                ->rules(['nullable', 'string'])
                ->exampleHeader('Parish'),

            ImportColumn::make('performance_order')
                ->label('Performance Order')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:1'])
                ->fillRecordUsing(function (ScheduleItem $record, $state) {
                    $record->performance_order = filled($state) ? intval($state) : null;
                })
                ->exampleHeader('Performance Order'),

            ImportColumn::make('status')
                ->label('Status')
                ->rules(['nullable', 'string', 'in:scheduled,in_progress,completed'])
                ->fillRecordUsing(function (ScheduleItem $record, $state) {
                    $record->status = filled($state) ? $state : 'scheduled';
                })
                ->exampleHeader('Status'),
        ];
    }

    public function resolveRecord(): ?ScheduleItem
    {
        // Always create new schedule items (timetable slots are not uniquely keyed by a single field)
        return new ScheduleItem();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Timetable import complete. '
            . number_format($import->successful_rows) . ' '
            . str('item')->plural($import->successful_rows) . ' imported successfully.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' '
                . str('row')->plural($failedRowsCount) . ' failed — download the failure report to review errors.';
        }

        return $body;
    }
}
