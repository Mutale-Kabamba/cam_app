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
        $categories = Category::all();
        $parishes = Parish::orderBy('name')->get();
        
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $currentDayName = now()->format('l');
        $defaultDay = in_array($currentDayName, $days) ? $currentDayName : 'Monday';

        $selectedCategory = $request->query('category_id');
        $selectedDay = $request->query('day_name', $defaultDay);

        $query = ScheduleItem::with(['parish', 'category'])
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

        return view('program.index', compact('scheduleItems', 'categories', 'parishes', 'selectedCategory', 'selectedDay', 'stats'));
    }
}
