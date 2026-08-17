<?php

namespace App\Filament\Resources\AdjudicationScoreResource\Pages;

use App\Filament\Resources\AdjudicationScoreResource;
use App\Models\AdjudicationScore;
use App\Models\ConsolidatedResult;
use Filament\Resources\Pages\CreateRecord;

class CreateAdjudicationScore extends CreateRecord
{
    protected static string $resource = AdjudicationScoreResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        if ($user && $user->isJudge()) {
            $data['adjudicator_name'] = $user->getJudgeName();
        }

        return $data;
    }

    protected function afterCreate(): void
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
