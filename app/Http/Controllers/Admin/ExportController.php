<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdjudicationScore;
use App\Models\Category;
use App\Models\ConsolidatedResult;
use App\Models\Parish;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    /**
     * Master festival report — all data combined into one structured CSV.
     */
    public function masterReport(): Response
    {
        $this->authorizeAdmin();

        $lines = [];

        // ── Section 1: Parish Registrations
        $lines[] = ['=== SECTION 1: PARISH REGISTRATIONS ==='];
        $lines[] = ['Parish Name', 'Contact Person', 'Contact Phone', 'Contact Email', 'Male', 'Female', 'Total Contingent', 'Checked In'];

        Parish::orderBy('name')->each(function (Parish $p) use (&$lines) {
            $lines[] = [
                $p->name,
                $p->contact_person ?? '',
                $p->contact_phone ?? '',
                $p->contact_email ?? '',
                $p->male_count ?? 0,
                $p->female_count ?? 0,
                $p->camp_contingent_count ?? 0,
                $p->camp_checked_in ? 'Yes' : 'No',
            ];
        });

        $lines[] = [];

        // ── Section 2: Consolidated Standings (per category)
        $lines[] = ['=== SECTION 2: CONSOLIDATED STANDINGS ==='];
        $lines[] = ['Category', 'Parish', '3-Judge Average', 'Time Penalty', 'Final Score', 'Rank', 'Championship Points', 'Published'];

        ConsolidatedResult::with(['category', 'parish'])
            ->orderBy('category_id')
            ->orderByDesc('final_score')
            ->each(function (ConsolidatedResult $r) use (&$lines) {
                $lines[] = [
                    $r->category?->name ?? '',
                    $r->parish?->name ?? '',
                    $r->adjudicators_average,
                    $r->time_penalty,
                    $r->final_score,
                    $r->rank ?? '',
                    $r->championship_points,
                    $r->is_finalized ? 'Yes' : 'No',
                ];
            });

        $lines[] = [];

        // ── Section 3: Overall Championship Points Totals
        $lines[] = ['=== SECTION 3: OVERALL CHAMPIONSHIP LEADERBOARD ==='];
        $lines[] = ['Rank', 'Parish', 'Total Championship Points', 'Events Contested'];

        $totals = ConsolidatedResult::where('is_finalized', true)
            ->selectRaw('parish_id, SUM(championship_points) as total_points, COUNT(*) as events_count')
            ->groupBy('parish_id')
            ->orderByDesc('total_points')
            ->with('parish')
            ->get();

        foreach ($totals as $rank => $row) {
            $lines[] = [
                $rank + 1,
                $row->parish?->name ?? '',
                $row->total_points,
                $row->events_count,
            ];
        }

        $lines[] = [];

        // ── Section 4: Adjudication Scores
        $lines[] = ['=== SECTION 4: ADJUDICATION SCORES (ALL JUDGES) ==='];
        $lines[] = ['Judge', 'Category', 'Parish', 'Total Raw Score', 'Final %', 'Submitted At'];

        AdjudicationScore::with(['category', 'parish'])
            ->orderBy('category_id')
            ->orderBy('parish_id')
            ->each(function (AdjudicationScore $s) use (&$lines) {
                $lines[] = [
                    $s->adjudicator_name,
                    $s->category?->name ?? '',
                    $s->parish?->name ?? '',
                    $s->total_raw_score ?? '',
                    $s->final_percentage ?? '',
                    $s->created_at?->format('Y-m-d H:i'),
                ];
            });

        return $this->streamCsv(
            $lines,
            'cam-festival-2026-master-report-' . now()->format('Ymd-His') . '.csv'
        );
    }

    /**
     * Download a blank CSV template for importing parishes.
     */
    public function parishImportTemplate(): Response
    {
        $this->authorizeAdmin();

        $lines = [
            ['Parish Name', 'Contact Person', 'Contact Phone', 'Contact Email', 'Male Participants', 'Female Participants'],
            ["St. Theresa's Cathedral", 'John Doe', '+260 97 1234567', 'john@cathedral.zm', '25', '30'],
            ['Christ the King Parish', 'Jane Smith', '+260 96 7654321', 'jane@ctk.zm', '18', '22'],
        ];

        return $this->streamCsv($lines, 'parish-import-template.csv');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Admin access required.');
    }

    private function streamCsv(array $rows, string $filename): Response
    {
        $handle = fopen('php://temp', 'r+');

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}
