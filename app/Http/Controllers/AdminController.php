<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parish;
use App\Models\Category;
use App\Models\ScheduleItem;
use App\Models\AdjudicationScore;
use App\Models\ConsolidatedResult;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_parishes' => Parish::count(),
            'checked_in_parishes' => Parish::where('camp_checked_in', true)->count(),
            'total_contingent' => Parish::sum('camp_contingent_count'),
            'checked_in_contingent' => Parish::where('camp_checked_in', true)->sum('camp_contingent_count'),
            'total_categories' => Category::count(),
            'total_schedules' => ScheduleItem::count(),
            'completed_schedules' => ScheduleItem::where('status', 'completed')->count(),
            'in_progress_schedules' => ScheduleItem::where('status', 'in_progress')->count(),
            'total_adjudications' => AdjudicationScore::count(),
            'finalized_categories' => ConsolidatedResult::where('is_finalized', true)->distinct('category_id')->count('category_id'),
        ];

        $categories = Category::all();
        $recentSchedules = ScheduleItem::with(['parish', 'category'])->orderBy('event_date')->orderBy('scheduled_start_time')->take(8)->get();
        $parishes = Parish::orderBy('name')->get();

        return view('admin.index', compact('stats', 'categories', 'recentSchedules', 'parishes'));
    }

    // -------------------------------------------------------------
    // Timetable & Program Management
    // -------------------------------------------------------------
    public function program(Request $request)
    {
        $categories = Category::all();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $currentDayName = now()->format('l');
        $defaultDay = in_array($currentDayName, $days) ? $currentDayName : 'Monday';

        $selectedDay = $request->query('day_name', $defaultDay);
        $selectedCategory = $request->query('category_id');

        $query = ScheduleItem::with(['parish', 'category'])->orderBy('scheduled_start_time');

        if ($selectedDay) {
            $query->where('day_name', $selectedDay);
        }
        if ($selectedCategory) {
            $query->where('category_id', $selectedCategory);
        }

        $scheduleItems = $query->get();

        return view('admin.program', compact('scheduleItems', 'categories', 'parishes', 'selectedDay', 'selectedCategory'));
    }

    public function updateScheduleStatus(Request $request, ScheduleItem $item)
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed',
            'time_penalty_marks' => 'nullable|integer|min:0|max:50',
            'actual_duration_seconds' => 'nullable|integer|min:0',
            'scheduled_start_time' => 'nullable',
            'scheduled_end_time' => 'nullable',
            'venue' => 'nullable|string',
            'performance_order' => 'nullable|integer',
        ]);

        $item->update($validated);

        return back()->with('success', "Schedule item updated successfully.");
    }

    public function storeScheduleItem(Request $request)
    {
        $validated = $request->validate([
            'event_date' => 'required|date',
            'day_name' => 'required|string',
            'scheduled_start_time' => 'required',
            'scheduled_end_time' => 'required',
            'venue' => 'required|string',
            'activity_title' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'parish_id' => 'nullable|exists:parishes,id',
            'performance_order' => 'nullable|integer',
            'status' => 'required|in:scheduled,in_progress,completed',
        ]);

        ScheduleItem::create($validated);

        return back()->with('success', 'New schedule activity added successfully.');
    }

    public function deleteScheduleItem(ScheduleItem $item)
    {
        $item->delete();
        return back()->with('success', 'Schedule activity removed.');
    }

    // -------------------------------------------------------------
    // Parishes & Contingents Management
    // -------------------------------------------------------------
    public function parishes(Request $request)
    {
        $search = $request->query('search');
        $deanery = $request->query('deanery');

        $query = Parish::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('patron_matron_name', 'like', "%{$search}%");
            });
        }

        if ($deanery) {
            $query->where('deanery', $deanery);
        }

        $parishes = $query->orderBy('name')->get();
        $deaneries = Parish::select('deanery')->distinct()->whereNotNull('deanery')->pluck('deanery');

        return view('admin.parishes', compact('parishes', 'deaneries', 'search', 'deanery'));
    }

    public function toggleCheckIn(Parish $parish)
    {
        $parish->camp_checked_in = !$parish->camp_checked_in;
        $parish->save();

        $statusText = $parish->camp_checked_in ? 'Checked In' : 'Pending';
        return back()->with('success', "{$parish->name} is now marked as {$statusText}.");
    }

    public function updateParish(Request $request, Parish $parish)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|unique:parishes,code,' . $parish->id,
            'deanery' => 'required|string',
            'patron_matron_name' => 'nullable|string',
            'patron_contact' => 'nullable|string',
            'camp_contingent_count' => 'required|integer|min:0',
            'camp_checked_in' => 'nullable|boolean',
        ]);

        $validated['camp_checked_in'] = $request->has('camp_checked_in');

        $parish->update($validated);

        return back()->with('success', "Parish {$parish->name} updated successfully.");
    }

    public function storeParish(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:parishes,name',
            'code' => 'required|string|unique:parishes,code',
            'deanery' => 'required|string',
            'patron_matron_name' => 'nullable|string',
            'patron_contact' => 'nullable|string',
            'camp_contingent_count' => 'required|integer|min:0',
        ]);

        Parish::create($validated);

        return back()->with('success', "Parish {$validated['name']} registered successfully.");
    }

    // -------------------------------------------------------------
    // Adjudication Consolidation Hub (3 Judges & Timekeeper Penalty)
    // -------------------------------------------------------------
    public function consolidation(Request $request)
    {
        $categories = Category::all();
        $selectedCategoryId = $request->query('category_id', $categories->first()?->id);
        $activeCategory = Category::find($selectedCategoryId);

        $parishes = Parish::orderBy('name')->get();

        // Fetch scores for this category grouped by parish
        $scoresGrouped = AdjudicationScore::where('category_id', $selectedCategoryId)
            ->get()
            ->groupBy('parish_id');

        $consolidatedResults = ConsolidatedResult::where('category_id', $selectedCategoryId)
            ->get()
            ->keyBy('parish_id');

        // Build 3-Judge comparison matrix for all 17 parishes
        $judgesList = ['Judge 1', 'Judge 2', 'Judge 3'];
        $matrix = $parishes->map(function ($parish) use ($scoresGrouped, $consolidatedResults, $judgesList) {
            $pScores = $scoresGrouped->get($parish->id, collect());
            
            $jScores = [];
            foreach ($judgesList as $jName) {
                $found = $pScores->firstWhere('adjudicator_name', $jName);
                $jScores[$jName] = $found ? $found->raw_total_score : null;
            }

            $submittedCount = $pScores->count();
            $avg = $submittedCount > 0 ? round($pScores->avg('raw_total_score'), 2) : 0;
            
            $consolidated = $consolidatedResults->get($parish->id);

            return [
                'parish' => $parish,
                'judge_scores' => $jScores,
                'submitted_count' => $submittedCount,
                'average' => $avg,
                'time_penalty' => $consolidated ? $consolidated->time_penalty : 0,
                'final_score' => $consolidated ? $consolidated->final_score : max(0, $avg - ($consolidated ? $consolidated->time_penalty : 0)),
                'rank' => $consolidated ? $consolidated->rank : null,
                'championship_points' => $consolidated ? $consolidated->championship_points : 0,
                'is_finalized' => $consolidated ? $consolidated->is_finalized : false,
            ];
        });

        return view('admin.consolidation', compact('categories', 'activeCategory', 'selectedCategoryId', 'matrix', 'judgesList'));
    }

    public function updateTimePenalty(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'parish_id' => 'required|exists:parishes,id',
            'time_penalty' => 'required|numeric|min:0|max:50',
        ]);

        $scores = AdjudicationScore::where('category_id', $validated['category_id'])
            ->where('parish_id', $validated['parish_id'])
            ->get();

        $avg = $scores->count() > 0 ? round($scores->avg('raw_total_score'), 2) : 0;
        $finalScore = max(0, $avg - $validated['time_penalty']);

        ConsolidatedResult::updateOrCreate(
            ['category_id' => $validated['category_id'], 'parish_id' => $validated['parish_id']],
            [
                'adjudicators_average' => $avg,
                'time_penalty' => $validated['time_penalty'],
                'final_score' => $finalScore,
            ]
        );

        return back()->with('success', 'Timekeeper penalty updated and score adjusted.');
    }

    public function finalizeCategory(Request $request)
    {
        $categoryId = $request->input('category_id');
        $category = Category::findOrFail($categoryId);

        $parishes = Parish::all();
        $scoresGrouped = AdjudicationScore::where('category_id', $categoryId)->get()->groupBy('parish_id');

        $computedResults = [];
        foreach ($parishes as $parish) {
            $pScores = $scoresGrouped->get($parish->id, collect());
            $avg = $pScores->count() > 0 ? round($pScores->avg('raw_total_score'), 2) : 0;

            $existing = ConsolidatedResult::where('category_id', $categoryId)->where('parish_id', $parish->id)->first();
            $timePenalty = $existing ? $existing->time_penalty : 0;
            $finalScore = max(0, $avg - $timePenalty);

            $computedResults[] = [
                'parish_id' => $parish->id,
                'category_id' => $categoryId,
                'adjudicators_average' => $avg,
                'time_penalty' => $timePenalty,
                'final_score' => $finalScore,
            ];
        }

        // Sort by final score descending to assign rank and championship points
        usort($computedResults, fn($a, $b) => $b['final_score'] <=> $a['final_score']);

        $pointsDistribution = [1 => 10, 2 => 8, 3 => 6, 4 => 5, 5 => 4, 6 => 3, 7 => 2];

        foreach ($computedResults as $index => $resData) {
            $rank = $index + 1;
            $points = $resData['final_score'] > 0 ? ($pointsDistribution[$rank] ?? 1) : 0;

            ConsolidatedResult::updateOrCreate(
                ['category_id' => $categoryId, 'parish_id' => $resData['parish_id']],
                [
                    'adjudicators_average' => $resData['adjudicators_average'],
                    'time_penalty' => $resData['time_penalty'],
                    'final_score' => $resData['final_score'],
                    'rank' => $rank,
                    'championship_points' => $points,
                    'is_finalized' => true,
                ]
            );
        }

        return back()->with('success', "Category '{$category->name}' has been finalized and published to the Live Leaderboard & Big Screen!");
    }
}
