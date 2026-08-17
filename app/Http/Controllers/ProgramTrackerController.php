<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScheduleItem;
use App\Models\Category;
use App\Models\Parish;

class ProgramTrackerController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $parishes = Parish::orderBy('name')->get();
        
        $availableDays = ScheduleItem::whereNotNull('day_name')
            ->distinct()
            ->pluck('day_name')
            ->toArray();

        $selectedCategory = $request->query('category_id');
        $selectedDay = $request->query('day_name');

        $query = ScheduleItem::with(['parish', 'category'])
            ->orderBy('event_date')
            ->orderBy('scheduled_start_time');

        if ($selectedDay) {
            $query->where('day_name', $selectedDay);
        }

        if ($selectedCategory) {
            $query->where('category_id', $selectedCategory);
        }

        $scheduleItems = $query->get();

        $stats = [
            'total_categories' => Category::count(),
            'completed' => ScheduleItem::where('status', 'completed')->count(),
            'in_progress' => ScheduleItem::where('status', 'in_progress')->count(),
            'upcoming' => ScheduleItem::where('status', 'scheduled')->count(),
        ];

        return view('program.index', compact('scheduleItems', 'categories', 'parishes', 'availableDays', 'selectedCategory', 'selectedDay', 'stats'));
    }
}
