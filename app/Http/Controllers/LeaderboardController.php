<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Parish;
use App\Models\ConsolidatedResult;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $selectedCategory = $request->query('category_id');

        $parishes = Parish::with(['consolidatedResults.category'])->get();

        // Calculate overall championship points per parish
        $standings = $parishes->map(function ($parish) {
            $totalPoints = $parish->consolidatedResults->sum('championship_points');
            $avgScore = $parish->consolidatedResults->avg('final_score') ?? 0;
            return [
                'parish' => $parish,
                'total_points' => $totalPoints,
                'average_score' => round($avgScore, 2),
                'categories_participated' => $parish->consolidatedResults->count(),
            ];
        })->sortByDesc('total_points')->values();

        $categoryResults = null;
        if ($selectedCategory) {
            $categoryResults = ConsolidatedResult::with('parish')
                ->where('category_id', $selectedCategory)
                ->orderByDesc('final_score')
                ->get();
        }

        return view('leaderboard.index', compact('categories', 'standings', 'selectedCategory', 'categoryResults'));
    }

    public function bigScreen(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $selectedCategory = $request->query('category_id');
        $activeCategory = null;
        $categoryResults = null;

        $parishes = Parish::with(['consolidatedResults.category'])->get();

        $standings = $parishes->map(function ($parish) {
            return [
                'parish' => $parish,
                'total_points' => $parish->consolidatedResults->sum('championship_points'),
                'average_score' => round($parish->consolidatedResults->avg('final_score') ?? 0, 2),
                'categories_participated' => $parish->consolidatedResults->count(),
            ];
        })->sortByDesc('total_points')->values();

        if ($selectedCategory) {
            $activeCategory = Category::find($selectedCategory);
            $resultsMap = ConsolidatedResult::with('parish')
                ->where('category_id', $selectedCategory)
                ->get()
                ->keyBy('parish_id');

            // Build list of all parishes with their scores or pending status for this category
            $categoryResults = $parishes->map(function ($parish) use ($resultsMap) {
                $res = $resultsMap->get($parish->id);
                return [
                    'parish' => $parish,
                    'final_score' => $res ? $res->final_score : 0,
                    'adjudicators_average' => $res ? $res->adjudicators_average : 0,
                    'time_penalty' => $res ? $res->time_penalty : 0,
                    'championship_points' => $res ? $res->championship_points : 0,
                    'is_finalized' => $res ? $res->is_finalized : false,
                ];
            })->sortByDesc('final_score')->values();
        }

        return view('leaderboard.big_screen', compact('categories', 'standings', 'selectedCategory', 'activeCategory', 'categoryResults'));
    }
}
