<?php

namespace App\Filament\Resources\AdjudicationScoreResource\Pages;

use App\Filament\Resources\AdjudicationScoreResource;
use App\Models\AdjudicationScore;
use App\Models\ConsolidatedResult;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdjudicationScore extends EditRecord
{
    protected static string $resource = AdjudicationScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        $allScores = AdjudicationScore::where('category_id', $record->category_id)
            ->where('parish_id', $record->parish_id)
            ->get();

        $avg = $allScores->count() > 0 ? round($allScores->avg('raw_total_score'), 2) : 0;
        $existing = ConsolidatedResult::where('category_id', $record->category_id)
            ->where('parish_id', $record->parish_id)
            ->first();

        $penalty = $existing ? $existing->time_penalty : 0;
        $finalScore = max(0, $avg - $penalty);

        ConsolidatedResult::updateOrCreate(
            ['category_id' => $record->category_id, 'parish_id' => $record->parish_id],
            [
                'adjudicators_average' => $avg,
                'time_penalty' => $penalty,
                'final_score' => $finalScore,
            ]
        );
    }
}
