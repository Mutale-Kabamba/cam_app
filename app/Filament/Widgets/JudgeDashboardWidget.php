<?php

namespace App\Filament\Widgets;

use App\Models\Category;
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
        $totalCategories = Category::count();

        return [
            'isJudge' => $isJudge,
            'judgeName' => $judgeName,
            'liveSchedule' => $liveSchedule,
            'scoresCount' => $scoresCount,
            'totalCategories' => $totalCategories,
        ];
    }
}
