<?php
namespace App\Services;
use App\Models\Category;
use App\Models\AdjudicationScore;
use App\Models\ConsolidatedResult;
use App\Models\ScheduleItem;

class AdjudicationService {
    public function calculateTimePenalty(int $allocatedMin, int $actualSec): int {
        $over = $actualSec - ($allocatedMin * 60);
        if ($over <= 0) return 0;
        $m = $over / 60.0;
        if ($m <= 1.0) return 2;
        if ($m <= 3.0) return 5;
        if ($m <= 5.0) return 10;
        return 15;
    }
    public function consolidateCategoryResults(int $catId): void {
        $scores = AdjudicationScore::where('category_id', $catId)->where('is_disqualified', false)->get()->groupBy('parish_id');
        foreach ($scores as $pId => $list) {
            $avg = $list->avg('normalized_score');
            $item = ScheduleItem::where('category_id', $catId)->where('parish_id', $pId)->first();
            $penalty = $item ? $item->time_penalty_marks : 0;
            ConsolidatedResult::updateOrCreate(
                ['category_id' => $catId, 'parish_id' => $pId],
                ['adjudicators_average' => $avg, 'time_penalty' => $penalty, 'final_score' => max(0, $avg - $penalty), 'is_finalized' => true]
            );
        }
    }
}