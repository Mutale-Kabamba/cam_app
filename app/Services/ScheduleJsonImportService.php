<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Parish;
use App\Models\ScheduleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ScheduleJsonImportService
{
    /**
     * Import schedule items from a JSON string or array.
     *
     * @param string|array $jsonInput
     * @param string $mode 'append', 'replace', or 'upsert'
     * @return array ['success' => bool, 'imported' => int, 'updated' => int, 'errors' => array, 'message' => string]
     */
    public function import(string|array $jsonInput, string $mode = 'append'): array
    {
        $data = is_array($jsonInput) ? $jsonInput : json_decode($jsonInput, true);

        if (json_last_error() !== JSON_ERROR_NONE && !is_array($jsonInput)) {
            return [
                'success' => false,
                'imported' => 0,
                'updated' => 0,
                'errors' => ['Invalid JSON format: ' . json_last_error_msg()],
                'message' => 'Failed to parse JSON string.',
            ];
        }

        // Unwrap if root key is 'schedule', 'items', or 'timetable'
        if (isset($data['schedule']) && is_array($data['schedule'])) {
            $data = $data['schedule'];
        } elseif (isset($data['items']) && is_array($data['items'])) {
            $data = $data['items'];
        } elseif (isset($data['timetable']) && is_array($data['timetable'])) {
            $data = $data['timetable'];
        }

        if (!is_array($data) || empty($data)) {
            return [
                'success' => false,
                'imported' => 0,
                'updated' => 0,
                'errors' => ['No schedule items found in the JSON payload.'],
                'message' => 'JSON payload is empty or not an array of items.',
            ];
        }

        $importedCount = 0;
        $updatedCount = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            if ($mode === 'replace') {
                ScheduleItem::truncate();
            }

            // Cache categories and parishes for fast lookup
            $categoriesBySlug = Category::all()->keyBy('slug');
            $categoriesByName = Category::all()->keyBy(fn ($c) => strtolower($c->name));
            $categoriesById = Category::all()->keyBy('id');

            $parishesByCode = Parish::all()->keyBy(fn ($p) => strtoupper($p->code));
            $parishesByName = Parish::all()->keyBy(fn ($p) => strtolower($p->name));
            $parishesById = Parish::all()->keyBy('id');

            foreach ($data as $index => $item) {
                $rowNum = $index + 1;

                if (!is_array($item)) {
                    $errors[] = "Item #{$rowNum}: Entry is not a valid JSON object.";
                    continue;
                }

                // Resolve event_date
                $rawDate = $item['event_date'] ?? $item['date'] ?? null;
                if (!$rawDate) {
                    $errors[] = "Item #{$rowNum}: Missing 'event_date'.";
                    continue;
                }

                try {
                    $carbonDate = Carbon::parse($rawDate);
                    $eventDate = $carbonDate->format('Y-m-d');
                } catch (\Exception $e) {
                    $errors[] = "Item #{$rowNum}: Invalid date format '{$rawDate}'.";
                    continue;
                }

                // Resolve day_name
                $dayName = $item['day_name'] ?? $item['day'] ?? $carbonDate->format('l');

                // Resolve times
                $startTime = $item['scheduled_start_time'] ?? $item['start_time'] ?? $item['start'] ?? '08:00:00';
                $endTime = $item['scheduled_end_time'] ?? $item['end_time'] ?? $item['end'] ?? '09:00:00';

                // Standardize time strings (H:i or H:i:s)
                $startTime = strlen($startTime) === 5 ? $startTime . ':00' : $startTime;
                $endTime = strlen($endTime) === 5 ? $endTime . ':00' : $endTime;

                // Resolve venue & title
                $venue = $item['venue'] ?? 'Main Stage';
                $title = $item['activity_title'] ?? $item['title'] ?? $item['activity'] ?? 'Scheduled Activity';

                // Resolve Category ID
                $categoryId = null;
                if (!empty($item['category_id']) && $categoriesById->has($item['category_id'])) {
                    $categoryId = $item['category_id'];
                } elseif (!empty($item['category_slug']) && $categoriesBySlug->has($item['category_slug'])) {
                    $categoryId = $categoriesBySlug->get($item['category_slug'])->id;
                } elseif (!empty($item['category']) || !empty($item['category_name'])) {
                    $catQuery = strtolower(trim($item['category_name'] ?? $item['category']));
                    if ($categoriesByName->has($catQuery)) {
                        $categoryId = $categoriesByName->get($catQuery)->id;
                    } elseif ($categoriesBySlug->has($catQuery)) {
                        $categoryId = $categoriesBySlug->get($catQuery)->id;
                    }
                }

                // Resolve Parish ID
                $parishId = null;
                if (!empty($item['parish_id']) && $parishesById->has($item['parish_id'])) {
                    $parishId = $item['parish_id'];
                } elseif (!empty($item['parish_code']) && $parishesByCode->has(strtoupper($item['parish_code']))) {
                    $parishId = $parishesByCode->get(strtoupper($item['parish_code']))->id;
                } elseif (!empty($item['parish']) || !empty($item['parish_name'])) {
                    $parishQuery = strtolower(trim($item['parish_name'] ?? $item['parish']));
                    if ($parishesByName->has($parishQuery)) {
                        $parishId = $parishesByName->get($parishQuery)->id;
                    } elseif ($parishesByCode->has(strtoupper($parishQuery))) {
                        $parishId = $parishesByCode->get(strtoupper($parishQuery))->id;
                    }
                }

                $status = $item['status'] ?? 'scheduled';
                $validStatuses = ['scheduled', 'in_progress', 'completed'];
                if (!in_array($status, $validStatuses)) {
                    $status = 'scheduled';
                }

                $performanceOrder = isset($item['performance_order']) ? intval($item['performance_order']) : null;
                $timePenalty = isset($item['time_penalty_marks']) ? intval($item['time_penalty_marks']) : 0;
                $actualDuration = isset($item['actual_duration_seconds']) ? intval($item['actual_duration_seconds']) : 0;

                $attributes = [
                    'event_date' => $eventDate,
                    'day_name' => $dayName,
                    'scheduled_start_time' => $startTime,
                    'scheduled_end_time' => $endTime,
                    'venue' => $venue,
                    'activity_title' => $title,
                    'category_id' => $categoryId,
                    'parish_id' => $parishId,
                    'performance_order' => $performanceOrder,
                    'status' => $status,
                    'time_penalty_marks' => $timePenalty,
                    'actual_duration_seconds' => $actualDuration,
                ];

                if ($mode === 'upsert') {
                    $existing = ScheduleItem::where('event_date', $eventDate)
                        ->where('scheduled_start_time', $startTime)
                        ->where('venue', $venue)
                        ->first();

                    if ($existing) {
                        $existing->update($attributes);
                        $updatedCount++;
                    } else {
                        ScheduleItem::create($attributes);
                        $importedCount++;
                    }
                } else {
                    ScheduleItem::create($attributes);
                    $importedCount++;
                }
            }

            DB::commit();

            return [
                'success' => true,
                'imported' => $importedCount,
                'updated' => $updatedCount,
                'errors' => $errors,
                'message' => "Successfully processed JSON schedule: {$importedCount} created" . ($updatedCount > 0 ? ", {$updatedCount} updated" : '') . '.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'imported' => 0,
                'updated' => 0,
                'errors' => [$e->getMessage()],
                'message' => 'An error occurred during database transaction: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Generate a sample JSON template for timetable import.
     */
    public function getSampleJson(): array
    {
        return [
            'schedule' => [
                [
                    'event_date' => '2026-08-18',
                    'day_name' => 'Tuesday',
                    'scheduled_start_time' => '08:30:00',
                    'scheduled_end_time' => '10:00:00',
                    'venue' => 'Main Stage',
                    'activity_title' => 'Choir Music (Melody) Competition - Morning Session',
                    'category_slug' => 'choir-music-melody',
                    'parish_code' => 'STC',
                    'performance_order' => 1,
                    'status' => 'scheduled',
                ],
                [
                    'event_date' => '2026-08-18',
                    'day_name' => 'Tuesday',
                    'scheduled_start_time' => '10:15:00',
                    'scheduled_end_time' => '12:00:00',
                    'venue' => 'Main Stage',
                    'activity_title' => 'Self-Composed Song Presentation',
                    'category_slug' => 'self-composed-song',
                    'parish_code' => 'CTK',
                    'performance_order' => 2,
                    'status' => 'scheduled',
                ],
                [
                    'event_date' => '2026-08-19',
                    'day_name' => 'Wednesday',
                    'scheduled_start_time' => '14:00:00',
                    'scheduled_end_time' => '16:00:00',
                    'venue' => 'Cultural Arena',
                    'activity_title' => 'Traditional Dance (3 Provinces Presentation)',
                    'category_slug' => 'traditional-dance',
                    'parish_code' => 'HCP',
                    'performance_order' => 1,
                    'status' => 'scheduled',
                ],
                [
                    'event_date' => '2026-08-20',
                    'day_name' => 'Thursday',
                    'scheduled_start_time' => '09:00:00',
                    'scheduled_end_time' => '11:00:00',
                    'venue' => 'Main Auditorium',
                    'activity_title' => 'Drama Stage Competition',
                    'category_slug' => 'drama',
                    'parish_code' => 'KZP',
                    'performance_order' => 1,
                    'status' => 'scheduled',
                ],
            ],
        ];
    }
}
