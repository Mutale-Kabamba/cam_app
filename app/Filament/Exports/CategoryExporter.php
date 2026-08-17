<?php

namespace App\Filament\Exports;

use App\Models\Category;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CategoryExporter extends Exporter
{
    protected static ?string $model = Category::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label('Category Name'),
            ExportColumn::make('type')
                ->label('Type')
                ->formatStateUsing(fn ($state) => match($state) {
                    'stage_performance' => 'Stage Performance',
                    'quiz_written' => 'Quiz / Written',
                    default => $state,
                }),
            ExportColumn::make('theme')
                ->label('Theme'),
            ExportColumn::make('description')
                ->label('Description'),
            ExportColumn::make('allocated_minutes')
                ->label('Stage Time (mins)'),
            ExportColumn::make('prep_minutes')
                ->label('Prep Time (mins)'),
            ExportColumn::make('max_raw_score')
                ->label('Max Score (pts)'),
            ExportColumn::make('rules')
                ->label('Rules')
                ->formatStateUsing(fn ($state) => is_array($state)
                    ? implode(' | ', $state)
                    : $state),
            ExportColumn::make('judging_criteria')
                ->label('Judging Criteria')
                ->formatStateUsing(fn ($state) => is_array($state)
                    ? implode(' | ', array_map(
                        fn ($c) => "#{$c['no']} {$c['criterion']} ({$c['possible_score']}pts)",
                        $state
                    ))
                    : $state),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Categories & rubrics export complete. ' . number_format($export->successful_rows) . ' categories exported.';
    }
}
