<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Parish;
use App\Models\ScheduleItem;
use App\Models\AdjudicationScore;
use App\Models\ConsolidatedResult;

class JudgeController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to access the Judge Portal.');
        }

        if (!$user->isJudge() && !$user->isAdmin()) {
            return redirect()->route('program.index')->with('error', 'Unauthorized access: Official Judge or Admin credentials required.');
        }

        $judges = ['Judge 1', 'Judge 2', 'Judge 3'];

        // Automatic judge identity based on logged-in user or admin switcher
        if ($user->isJudge()) {
            $activeJudge = $user->getJudgeName();
            $isAdmin = false;
        } else {
            $isAdmin = true;
            $activeJudge = $request->query('judge', 'Judge 1');
            if (!in_array($activeJudge, $judges)) {
                $activeJudge = 'Judge 1';
            }
        }

        $categories = Category::all();
        $selectedCategoryId = $request->query('category_id', $categories->first()?->id);
        $activeCategory = Category::find($selectedCategoryId);

        // Filter only parishes that belong to / are scheduled in this selected category
        $scheduledParishIds = ScheduleItem::where('category_id', $selectedCategoryId)
            ->whereNotNull('parish_id')
            ->pluck('parish_id')
            ->toArray();

        if (!empty($scheduledParishIds)) {
            $parishes = Parish::whereIn('id', $scheduledParishIds)->orderBy('name')->get();
        } else {
            $parishes = Parish::orderBy('name')->get();
        }

        // Get all scores submitted by this active judge for this category
        $judgeScores = AdjudicationScore::where('category_id', $selectedCategoryId)
            ->where('adjudicator_name', $activeJudge)
            ->get()
            ->keyBy('parish_id');

        $parishStatusList = $parishes->map(function ($parish) use ($judgeScores) {
            $score = $judgeScores->get($parish->id);
            return [
                'parish' => $parish,
                'score' => $score,
                'is_scored' => $score !== null,
                'raw_score' => $score ? $score->raw_total_score : null,
                'comments' => $score ? $score->comments : null,
            ];
        });

        $scoredCount = $judgeScores->count();
        $totalParishes = $parishes->count();

        return view('judge.index', compact(
            'judges', 'activeJudge', 'isAdmin', 'categories', 'activeCategory',
            'selectedCategoryId', 'parishStatusList', 'scoredCount', 'totalParishes'
        ));
    }

    public function scoreSheet(Request $request, Category $category, Parish $parish)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to access the Judge Portal.');
        }

        if (!$user->isJudge() && !$user->isAdmin()) {
            return redirect()->route('program.index')->with('error', 'Unauthorized access: Official Judge or Admin credentials required.');
        }

        $judges = ['Judge 1', 'Judge 2', 'Judge 3'];

        if ($user->isJudge()) {
            $activeJudge = $user->getJudgeName();
            $isAdmin = false;
        } else {
            $isAdmin = true;
            $activeJudge = $request->query('judge', 'Judge 1');
            if (!in_array($activeJudge, $judges)) {
                $activeJudge = 'Judge 1';
            }
        }

        // Fetch existing score if already assessed
        $existingScore = AdjudicationScore::where('category_id', $category->id)
            ->where('parish_id', $parish->id)
            ->where('adjudicator_name', $activeJudge)
            ->first();

        return view('judge.scoresheet', compact('category', 'parish', 'activeJudge', 'isAdmin', 'existingScore', 'judges'));
    }

    public function submitScore(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to access the Judge Portal.');
        }

        if (!$user->isJudge() && !$user->isAdmin()) {
            return redirect()->route('program.index')->with('error', 'Unauthorized access.');
        }

        if ($user->isJudge()) {
            $adjudicatorName = $user->getJudgeName();
        } else {
            $adjudicatorName = $request->input('adjudicator_name', 'Judge 1');
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'parish_id' => 'required|exists:parishes,id',
            'conductor_name' => 'nullable|string',
            'director_producer' => 'nullable|string',
            'composer_author' => 'nullable|string',
            'language_used' => 'nullable|string',
            'participant_count' => 'nullable|integer',
            'item_title' => 'nullable|string',
            'song_titles_breakdown' => 'nullable|array',
            'criteria_scores' => 'required|array',
            'comments' => 'nullable|string',
            'is_disqualified' => 'nullable|boolean',
        ]);

        $category = Category::findOrFail($validated['category_id']);
        $parish = Parish::findOrFail($validated['parish_id']);

        // Sum criteria scores
        $totalRaw = 0;
        $criteriaScores = [];

        foreach ($validated['criteria_scores'] as $critKey => $scoreVal) {
            $val = floatval($scoreVal);
            $criteriaScores[$critKey] = $val;
            $totalRaw += $val;
        }

        // Calculate normalized score (out of 100)
        $maxPossible = $category->max_raw_score > 0 ? $category->max_raw_score : 100;
        $normalized = round(($totalRaw / $maxPossible) * 100, 2);

        $isDisqualified = $request->has('is_disqualified') && $request->is_disqualified;
        if ($isDisqualified) {
            $totalRaw = 0;
            $normalized = 0;
        }

        AdjudicationScore::updateOrCreate(
            [
                'category_id' => $category->id,
                'parish_id' => $parish->id,
                'adjudicator_name' => $adjudicatorName,
            ],
            [
                'conductor_name' => $validated['conductor_name'] ?? null,
                'director_producer' => $validated['director_producer'] ?? null,
                'composer_author' => $validated['composer_author'] ?? null,
                'language_used' => $validated['language_used'] ?? null,
                'participant_count' => $validated['participant_count'] ?? null,
                'item_title' => $validated['item_title'] ?? null,
                'song_titles_breakdown' => $validated['song_titles_breakdown'] ?? null,
                'criteria_scores' => $criteriaScores,
                'raw_total_score' => $totalRaw,
                'normalized_score' => $normalized,
                'comments' => $validated['comments'] ?? null,
                'is_disqualified' => $isDisqualified,
            ]
        );

        // Update Consolidated average
        $allScores = AdjudicationScore::where('category_id', $category->id)
            ->where('parish_id', $parish->id)
            ->get();

        $avg = $allScores->count() > 0 ? round($allScores->avg('raw_total_score'), 2) : 0;
        $existingConsolidated = ConsolidatedResult::where('category_id', $category->id)
            ->where('parish_id', $parish->id)
            ->first();

        $timePenalty = $existingConsolidated ? $existingConsolidated->time_penalty : 0;
        $finalScore = max(0, $avg - $timePenalty);

        ConsolidatedResult::updateOrCreate(
            ['category_id' => $category->id, 'parish_id' => $parish->id],
            [
                'adjudicators_average' => $avg,
                'time_penalty' => $timePenalty,
                'final_score' => $finalScore,
            ]
        );

        return redirect()->route('judge.index', [
            'judge' => $adjudicatorName,
            'category_id' => $category->id,
        ])->with('success', "Marks recorded successfully for {$parish->name} by {$adjudicatorName}. Total: {$totalRaw}/{$maxPossible} pts.");
    }
}
