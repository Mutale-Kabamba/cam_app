<?php

namespace App\Filament\Widgets;

use App\Models\AdjudicationScore;
use App\Models\Category;
use App\Models\Parish;
use App\Models\ScheduleItem;
use Filament\Widgets\Widget;

class JudgeDashboardWidget extends Widget
{
    protected static string $view = 'filament.widgets.judge-dashboard-widget';

    protected static ?int $sort = -2;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && method_exists($user, 'isJudge') && $user->isJudge() && !$user->isAdmin();
    }

    public function getViewData(): array
    {
        $user = auth()->user();
        $isJudge = $user && method_exists($user, 'isJudge') && $user->isJudge();
        $judgeName = $isJudge && method_exists($user, 'getJudgeName') ? $user->getJudgeName() : 'Judge 1';

        $liveSchedule = ScheduleItem::with(['parish', 'category'])
            ->where('status', 'in_progress')
            ->first();

        // Total assessments by this judge
        $scoresCount = AdjudicationScore::where('adjudicator_name', $judgeName)->count();
        
        // Eager-load score counts grouped by category to eliminate N+1 queries
        $scoresByCategory = AdjudicationScore::where('adjudicator_name', $judgeName)
            ->selectRaw('category_id, count(*) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $allParishes = Parish::all(['id', 'participating_categories']);
        $totalParishes = $allParishes->count();

        $allScheduleItems = ScheduleItem::whereNotNull('category_id')
            ->whereNotNull('parish_id')
            ->get(['category_id', 'parish_id']);

        $hasAnyParticipationSet = $allParishes->contains(fn ($p) => !empty($p->participating_categories) && is_array($p->participating_categories));
        $hasAnyScheduleSet = $allScheduleItems->isNotEmpty();

        $categories = Category::orderBy('id')->get()->map(function ($cat) use ($judgeName, $allParishes, $totalParishes, $hasAnyParticipationSet, $hasAnyScheduleSet, $allScheduleItems, $scoresByCategory) {
            $scheduledParishIds = $allScheduleItems->where('category_id', $cat->id)->pluck('parish_id')->unique()->toArray();

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

            $assessedCount = $scoresByCategory->get($cat->id, 0);

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
            'totalCategories' => $categories->count(),
            'totalParishes' => $totalParishes,
            'categories' => $categories,
        ];
    }
}