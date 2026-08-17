<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parish;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $deanery = $request->query('deanery');

        // Only display parishes that have checked in
        $query = Parish::where('camp_checked_in', true);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('patron_matron_name', 'like', "%{$search}%");
            });
        }

        if ($deanery) {
            $query->where('deanery', $deanery);
        }

        $parishes = $query->orderBy('name')->get();
        $deaneries = Parish::where('camp_checked_in', true)->select('deanery')->distinct()->whereNotNull('deanery')->pluck('deanery');

        $stats = [
            'total_parishes' => Parish::count(),
            'checked_in' => Parish::where('camp_checked_in', true)->count(),
            'total_contingent' => Parish::sum('camp_contingent_count'),
            'checked_in_contingent' => Parish::where('camp_checked_in', true)->sum('camp_contingent_count'),
        ];

        return view('registration.index', compact('parishes', 'deaneries', 'search', 'deanery', 'stats'));
    }
}
