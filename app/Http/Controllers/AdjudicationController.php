<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Parish;
use App\Models\AdjudicationScore;

class AdjudicationController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $parishes = Parish::orderBy('name')->get();
        $selectedCategory = $request->query('category_id', $categories->first()?->id);

        $scores = AdjudicationScore::where('category_id', $selectedCategory)
            ->get();

        $activeCategory = Category::find($selectedCategory);

        return view('adjudication.index', compact('categories', 'parishes', 'selectedCategory', 'activeCategory', 'scores'));
    }
}
