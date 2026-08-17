<?php

namespace App\Filament\Widgets;

use App\Models\Parish;
use App\Models\Category;
use App\Models\ScheduleItem;
use App\Models\AdjudicationScore;
use App\Models\ConsolidatedResult;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FestivalStatsOverview extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function getStats(): array
    {
        $totalParishes = Parish::count();
        $checkedIn = Parish::where('camp_checked_in', true)->count();
        $totalCampers = Parish::sum('camp_contingent_count');
        $activeCampers = Parish::where('camp_checked_in', true)->sum('camp_contingent_count');

        $totalSchedules = ScheduleItem::count();
        $completedSchedules = ScheduleItem::where('status', 'completed')->count();
        $onStageSchedules = ScheduleItem::where('status', 'in_progress')->count();

        $finalizedCats = ConsolidatedResult::where('is_finalized', true)->distinct('category_id')->count('category_id');

        return [
            Stat::make('Parishes Checked-In', "{$checkedIn} / {$totalParishes}")
                ->description("{$activeCampers} of {$totalCampers} campers in camp")
                ->descriptionIcon('heroicon-m-building-library')
                ->color('success'),

            Stat::make('Performances On Stage', $onStageSchedules)
                ->description("{$completedSchedules} of {$totalSchedules} activities finished")
                ->descriptionIcon('heroicon-m-play')
                ->color('danger'),

            Stat::make('Adjudication Scorecards', AdjudicationScore::count())
                ->description('Marks submitted by 3 Judges')
                ->descriptionIcon('heroicon-m-scale')
                ->color('warning'),

            Stat::make('Finalized Categories', "{$finalizedCats} / 8")
                ->description('Published to Live Leaderboard')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('info'),
        ];
    }
}
