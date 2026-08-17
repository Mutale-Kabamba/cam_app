<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Parish;
use App\Models\ScheduleItem;
use App\Models\AdjudicationScore;
use Filament\Widgets\Widget;

class JudgeDashboardWidget extends Widget
{
    protected static string $view = 'filament.widgets.judge-dashboard-widget';

    protected static ?int $sort = -2;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->isJudge() && !$user->isAdmin();
    }

    public function getViewData(): array
    {
        $user = auth()->user();
        $isJudge = $user && $user->isJudge();
        $judgeName = $isJudge ? $user->getJudgeName() : 'Judge 1';

        $liveSchedule = ScheduleItem::with(['parish', 'category'])
            ->where('status', 'in_progress')
            ->first();

        $scoresCount = AdjudicationScore::where('adjudicator_name', $judgeName)->count();
        $allParishes = Parish::all();
        $totalParishes = $allParishes->count();

        $hasAnyParticipationSet = $allParishes->contains(function ($p) {
            return !empty($p->participating_categories) && is_array($p->participating_categories);
        });
        $hasAnyScheduleSet = ScheduleItem::whereNotNull('category_id')->whereNotNull('parish_id')->exists();

        $categories = Category::orderBy('id')->get()->map(function ($cat) use ($judgeName, $allParishes, $totalParishes, $hasAnyParticipationSet, $hasAnyScheduleSet) {
            $scheduledParishIds = ScheduleItem::where('category_id', $cat->id)
                ->whereNotNull('parish_id')
                ->pluck('parish_id')
                ->toArray();

            if ($hasAnyParticipationSet || $hasAnyScheduleSet) {
                $targetCount = $allParishes->filter(function (Parish $p) use ($cat, $scheduledParishIds) {
                    if (in_array($p->id, $scheduledParishIds)) {
                        return true;
                    }
                    if (!empty($p->participating_categories) && is_array($p->participating_categories)) {
                        return in_array($cat->id, $p->participating_categories)
                            || in_array((string)$cat->id, $p->participating_categories)
                            || in_array((int)$cat->id, $p->participating_categories);
                    }
                    return false;
                })->count();
            } else {
                $targetCount = $totalParishes;
            }

            $assessedCount = AdjudicationScore::where('category_id', $cat->id)
                ->where('adjudicator_name', $judgeName)
                ->count();

            $cat->scheduled_count = $targetCount;
            $cat->assessed_count = $assessedCount;
            $cat->progress_pct = $targetCount > 0 ? min(100, round(($assessedCount / $targetCount) * 100)) : 0;

            return $cat;
        });

        return [
            'isJudge' => $isJudge,
            'judgeName' => $judgeName,
            'liveSchedule' => $liveSchedule,
            'scoresCount' => $scoresCount,
            'totalCategories' => $totalCategories,
            'totalParishes' => $totalParishes,
            'categories' => $categories,
        ];
    }
}
