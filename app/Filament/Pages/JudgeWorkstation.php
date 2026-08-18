<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\Parish;
use App\Models\ScheduleItem;
use App\Models\AdjudicationScore;
use App\Models\ConsolidatedResult;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class JudgeWorkstation extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationLabel = 'Judge Workstation';

    protected static ?string $title = '⚖️ Official Adjudicator Workstation';

    protected static ?string $navigationGroup = 'Judging & Results';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.judge-workstation';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && $user->isJudge() && !$user->isAdmin();
    }

    public string $activeJudge = 'Judge 1';
    public ?int $selectedCategoryId = null;
    public ?int $scoringParishId = null;
    public bool $showScoreModal = false;

    // Modal Form State
    public array $criteriaScores = [];
    public ?string $conductorName = '';
    public ?string $directorProducer = '';
    public ?string $composerAuthor = '';
    public ?string $languageUsed = '';
    public ?int $participantCount = null;
    public ?string $itemTitle = '';
    public ?string $comments = '';
    public bool $isDisqualified = false;

    // Choir 4 Prescribed Songs State
    public array $songTitles = [
        'social_song' => '',
        'kyrie' => '',
        'gloria' => '',
        'thanksgiving' => '',
    ];

    public array $songsPresented = [
        'social_song' => true,
        'kyrie' => true,
        'gloria' => true,
        'thanksgiving' => true,
    ];

    // Traditional Dance 3 Dances & Compliance State
    public array $traditionalDances = [
        1 => ['dance' => '', 'tribe' => '', 'province' => ''],
        2 => ['dance' => '', 'tribe' => '', 'province' => ''],
        3 => ['dance' => '', 'tribe' => '', 'province' => ''],
    ];

    public int $missingDancesCount = 0; // -10 per missing dance
    public int $repeatedProvincesCount = 0; // -5 for each repeated province
    public bool $failedIdentifyDances = false; // -5 marks
    public bool $usedMasqueradeVinyau = false; // Disqualification!

    // Universal Time Penalty Deduction
    public int $timePenaltyDeduction = 0; // 0, 2, 5, 10, 15 marks

    public function mount(): void
    {
        $user = auth()->user();
        $this->activeJudge = $user ? $user->getJudgeName() : 'Judge 1';

        $firstCat = Category::orderBy('id')->first();
        $this->selectedCategoryId = request()->query('category_id', $firstCat?->id);
    }

    public function selectCategory(int $categoryId): void
    {
        $this->selectedCategoryId = $categoryId;
        $this->showScoreModal = false;
    }

    public function switchJudge(string $judgeName): void
    {
        if ($this->isAdmin && in_array($judgeName, ['Judge 1', 'Judge 2', 'Judge 3'])) {
            $this->activeJudge = $judgeName;
        }
    }

    public function isChoirCategory(): bool
    {
        $category = Category::find($this->selectedCategoryId);
        if (!$category) {
            return false;
        }
        return str_contains(strtolower($category->slug ?? ''), 'choir')
            || str_contains(strtolower($category->name ?? ''), 'choir');
    }

    public function isSelfComposedCategory(): bool
    {
        $category = Category::find($this->selectedCategoryId);
        if (!$category) {
            return false;
        }
        return str_contains(strtolower($category->slug ?? ''), 'self-composed')
            || str_contains(strtolower($category->name ?? ''), 'self-composed')
            || str_contains(strtolower($category->slug ?? ''), 'self composed')
            || str_contains(strtolower($category->name ?? ''), 'self composed');
    }

    public function isPoetryCategory(): bool
    {
        $category = Category::find($this->selectedCategoryId);
        if (!$category) {
            return false;
        }
        return str_contains(strtolower($category->slug ?? ''), 'poetry')
            || str_contains(strtolower($category->name ?? ''), 'poetry');
    }

    public function isTraditionalDanceCategory(): bool
    {
        $category = Category::find($this->selectedCategoryId);
        if (!$category) {
            return false;
        }
        return str_contains(strtolower($category->slug ?? ''), 'traditional-dance')
            || str_contains(strtolower($category->name ?? ''), 'traditional-dance')
            || str_contains(strtolower($category->slug ?? ''), 'traditional dance')
            || str_contains(strtolower($category->name ?? ''), 'traditional dance');
    }

    public function isDramaCategory(): bool
    {
        $category = Category::find($this->selectedCategoryId);
        if (!$category) {
            return false;
        }
        return str_contains(strtolower($category->slug ?? ''), 'drama')
            || str_contains(strtolower($category->name ?? ''), 'drama');
    }

    public function getOmittedSongsCount(): int
    {
        if (!$this->isChoirCategory()) {
            return 0;
        }
        $count = 0;
        foreach (['social_song', 'kyrie', 'gloria', 'thanksgiving'] as $sKey) {
            if (empty($this->songsPresented[$sKey])) {
                $count++;
            }
        }
        return $count;
    }

    public function getOmissionPenalty(): float
    {
        return $this->getOmittedSongsCount() * 25.0;
    }

    public function getTraditionalDanceDeductions(): float
    {
        if (!$this->isTraditionalDanceCategory()) {
            return 0;
        }
        $deductions = 0;
        $deductions += intval($this->missingDancesCount) * 10.0;
        $deductions += intval($this->repeatedProvincesCount) * 5.0;
        if ($this->failedIdentifyDances) {
            $deductions += 5.0;
        }
        return floatval($deductions);
    }

    public function calculateRubricSubtotal(): float
    {
        $total = 0;
        foreach ($this->criteriaScores as $score) {
            if (is_numeric($score)) {
                $total += floatval($score);
            }
        }
        return round($total, 2);
    }

    public function calculateTotal(): float
    {
        $rubric = $this->calculateRubricSubtotal();
        if ($this->isChoirCategory()) {
            $omittedCount = $this->getOmittedSongsCount();
            $maxAllowed = max(0, 100 - ($omittedCount * 25));
            $penalty = $this->getOmissionPenalty() + floatval($this->timePenaltyDeduction);
            return round(max(0, min($maxAllowed, $rubric - $penalty)), 2);
        }

        if ($this->isTraditionalDanceCategory()) {
            if ($this->usedMasqueradeVinyau || $this->isDisqualified) {
                return 0.0;
            }
            $deductions = $this->getTraditionalDanceDeductions() + floatval($this->timePenaltyDeduction);
            return round(max(0, $rubric - $deductions), 2);
        }

        if ($this->timePenaltyDeduction > 0) {
            return round(max(0, $rubric - floatval($this->timePenaltyDeduction)), 2);
        }

        return round($rubric, 2);
    }

    public function openScoreModal(int $parishId): void
    {
        $this->scoringParishId = $parishId;
        $category = Category::find($this->selectedCategoryId);
        if (!$category) {
            return;
        }

        $existing = AdjudicationScore::where('category_id', $this->selectedCategoryId)
            ->where('parish_id', $parishId)
            ->where('adjudicator_name', $this->activeJudge)
            ->first();

        // Initialise criteria scores
        $this->criteriaScores = [];
        $criteria = $category->judging_criteria ?? [];
        foreach ($criteria as $index => $criterion) {
            $key = $criterion['no'] ?? ($index + 1);
            $this->criteriaScores[$key] = $existing?->criteria_scores[$key] ?? '';
        }

        $this->conductorName = $existing?->conductor_name ?? '';
        $this->directorProducer = $existing?->director_producer ?? '';
        $this->composerAuthor = $existing?->composer_author ?? '';
        $this->languageUsed = $existing?->language_used ?? '';
        $this->participantCount = $existing?->participant_count;
        $this->itemTitle = $existing?->item_title ?? '';
        $this->comments = $existing?->comments ?? '';
        $this->isDisqualified = (bool) ($existing?->is_disqualified ?? false);

        // Load 4 Prescribed Songs
        $songData = $existing?->song_titles_breakdown ?? [];
        $this->songTitles = [
            'social_song' => $songData['social_song']['title'] ?? ($songData['social_song'] ?? ''),
            'kyrie' => $songData['kyrie']['title'] ?? ($songData['kyrie'] ?? ''),
            'gloria' => $songData['gloria']['title'] ?? ($songData['gloria'] ?? ''),
            'thanksgiving' => $songData['thanksgiving']['title'] ?? ($songData['thanksgiving'] ?? ''),
        ];
        $this->songsPresented = [
            'social_song' => isset($songData['social_song']['presented']) ? (bool)$songData['social_song']['presented'] : true,
            'kyrie' => isset($songData['kyrie']['presented']) ? (bool)$songData['kyrie']['presented'] : true,
            'gloria' => isset($songData['gloria']['presented']) ? (bool)$songData['gloria']['presented'] : true,
            'thanksgiving' => isset($songData['thanksgiving']['presented']) ? (bool)$songData['thanksgiving']['presented'] : true,
        ];

        // Load Traditional Dance 3 Dances & Compliance
        if (isset($songData['traditional_dances'])) {
            $this->traditionalDances = [
                1 => [
                    'dance' => $songData['traditional_dances'][1]['dance'] ?? '',
                    'tribe' => $songData['traditional_dances'][1]['tribe'] ?? '',
                    'province' => $songData['traditional_dances'][1]['province'] ?? '',
                ],
                2 => [
                    'dance' => $songData['traditional_dances'][2]['dance'] ?? '',
                    'tribe' => $songData['traditional_dances'][2]['tribe'] ?? '',
                    'province' => $songData['traditional_dances'][2]['province'] ?? '',
                ],
                3 => [
                    'dance' => $songData['traditional_dances'][3]['dance'] ?? '',
                    'tribe' => $songData['traditional_dances'][3]['tribe'] ?? '',
                    'province' => $songData['traditional_dances'][3]['province'] ?? '',
                ],
            ];
            $this->missingDancesCount = intval($songData['missing_dances_count'] ?? 0);
            $this->repeatedProvincesCount = intval($songData['repeated_provinces_count'] ?? 0);
            $this->failedIdentifyDances = (bool)($songData['failed_identify_dances'] ?? false);
            $this->usedMasqueradeVinyau = (bool)($songData['used_masquerade_vinyau'] ?? false);
        } else {
            $this->traditionalDances = [
                1 => ['dance' => '', 'tribe' => '', 'province' => ''],
                2 => ['dance' => '', 'tribe' => '', 'province' => ''],
                3 => ['dance' => '', 'tribe' => '', 'province' => ''],
            ];
            $this->missingDancesCount = 0;
            $this->repeatedProvincesCount = 0;
            $this->failedIdentifyDances = false;
            $this->usedMasqueradeVinyau = false;
        }

        $this->timePenaltyDeduction = intval($songData['time_penalty_deduction'] ?? 0);

        $this->showScoreModal = true;
    }

    public function closeScoreModal(): void
    {
        $this->showScoreModal = false;
        $this->scoringParishId = null;
    }

    public function saveScore(): void
    {
        if (!$this->selectedCategoryId || !$this->scoringParishId) {
            return;
        }

        $category = Category::findOrFail($this->selectedCategoryId);
        $parish = Parish::findOrFail($this->scoringParishId);

        $criteria = $category->judging_criteria ?? [];
        $cleanedScores = [];
        $totalRaw = 0;

        if (!empty($criteria)) {
            foreach ($criteria as $index => $criterion) {
                $key = $criterion['no'] ?? ($index + 1);
                $maxPts = floatval($criterion['possible_score'] ?? 100);
                $entered = floatval($this->criteriaScores[$key] ?? 0);

                if ($entered < 0) {
                    $entered = 0;
                } elseif ($entered > $maxPts) {
                    $entered = $maxPts;
                }

                $cleanedScores[$key] = $entered;
                $totalRaw += $entered;
            }
        } else {
            foreach ($this->criteriaScores as $k => $score) {
                if (is_numeric($score)) {
                    $val = floatval($score);
                    $cleanedScores[$k] = $val;
                    $totalRaw += $val;
                }
            }
        }

        $isChoir = $this->isChoirCategory();
        $isTraditionalDance = $this->isTraditionalDanceCategory();
        $isDrama = $this->isDramaCategory();

        $omittedCount = $isChoir ? $this->getOmittedSongsCount() : 0;
        $omissionPenalty = $omittedCount * 25.0;

        $danceDeductions = $isTraditionalDance ? $this->getTraditionalDanceDeductions() : 0;

        if ($this->usedMasqueradeVinyau) {
            $this->isDisqualified = true;
        }

        if ($isChoir) {
            $maxPossible = max(0, 100 - $omissionPenalty);
            $finalRawScore = max(0, min($maxPossible, $totalRaw - $omissionPenalty - floatval($this->timePenaltyDeduction)));
        } elseif ($isTraditionalDance) {
            $finalRawScore = max(0, $totalRaw - $danceDeductions - floatval($this->timePenaltyDeduction));
            $maxPossible = 100;
        } elseif ($isDrama) {
            $finalRawScore = max(0, $totalRaw - floatval($this->timePenaltyDeduction));
            $maxPossible = $category->max_raw_score > 0 ? $category->max_raw_score : 120;
        } else {
            $maxPossible = $category->max_raw_score > 0 ? $category->max_raw_score : 100;
            $finalRawScore = max(0, $totalRaw - floatval($this->timePenaltyDeduction));
        }

        $maxDenominator = $category->max_raw_score > 0 ? $category->max_raw_score : ($isDrama ? 120 : 100);
        $normalized = round(($finalRawScore / $maxDenominator) * 100, 2);

        if ($this->isDisqualified) {
            $finalRawScore = 0;
            $normalized = 0;
        }

        $songBreakdown = null;
        if ($isChoir) {
            $songBreakdown = [
                'social_song' => ['title' => $this->songTitles['social_song'] ?? '', 'presented' => (bool)($this->songsPresented['social_song'] ?? true)],
                'kyrie' => ['title' => $this->songTitles['kyrie'] ?? '', 'presented' => (bool)($this->songsPresented['kyrie'] ?? true)],
                'gloria' => ['title' => $this->songTitles['gloria'] ?? '', 'presented' => (bool)($this->songsPresented['gloria'] ?? true)],
                'thanksgiving' => ['title' => $this->songTitles['thanksgiving'] ?? '', 'presented' => (bool)($this->songsPresented['thanksgiving'] ?? true)],
                'omitted_songs_count' => $omittedCount,
                'omission_penalty' => $omissionPenalty,
                'time_penalty_deduction' => $this->timePenaltyDeduction,
            ];
        } elseif ($isTraditionalDance) {
            $songBreakdown = [
                'traditional_dances' => $this->traditionalDances,
                'missing_dances_count' => $this->missingDancesCount,
                'repeated_provinces_count' => $this->repeatedProvincesCount,
                'failed_identify_dances' => $this->failedIdentifyDances,
                'used_masquerade_vinyau' => $this->usedMasqueradeVinyau,
                'compliance_deductions_total' => $danceDeductions,
                'time_penalty_deduction' => $this->timePenaltyDeduction,
            ];
        } elseif ($isDrama) {
            $songBreakdown = [
                'play_title' => $this->itemTitle,
                'playwright' => $this->composerAuthor,
                'director' => $this->directorProducer,
                'language_used' => $this->languageUsed,
                'participant_count' => $this->participantCount,
                'time_penalty_deduction' => $this->timePenaltyDeduction,
            ];
        }

        $resolvedTitle = !empty($this->itemTitle)
            ? $this->itemTitle
            : ($isChoir ? 'Choral Presentation (4 Prescribed Songs)' : ($isTraditionalDance ? 'Traditional Dance Presentation (3 Provinces)' : ($isDrama ? 'Dramatic Production' : '')));

        AdjudicationScore::updateOrCreate(
            [
                'category_id' => $category->id,
                'parish_id' => $parish->id,
                'adjudicator_name' => $this->activeJudge,
            ],
            [
                'conductor_name' => $this->conductorName,
                'director_producer' => $this->directorProducer,
                'composer_author' => $this->composerAuthor,
                'language_used' => $this->languageUsed,
                'participant_count' => $this->participantCount,
                'item_title' => $resolvedTitle,
                'song_titles_breakdown' => $songBreakdown,
                'criteria_scores' => $cleanedScores,
                'raw_total_score' => $finalRawScore,
                'normalized_score' => $normalized,
                'comments' => $this->comments,
                'is_disqualified' => $this->isDisqualified,
            ]
        );

        // Update Consolidated Average & Results
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

        Notification::make()
            ->title('Score Submitted & Locked')
            ->body("{$parish->name} evaluated by {$this->activeJudge}: {$finalRawScore}/100 pts." . ($omittedCount > 0 ? " ({$omittedCount} song(s) omitted, -{$omissionPenalty} pts penalty)" : ''))
            ->success()
            ->send();

        $this->closeScoreModal();
    }

    public function getViewData(): array
    {
        $categories = Category::all();
        $activeCategory = Category::find($this->selectedCategoryId) ?? $categories->first();

        $allParishes = Parish::orderBy('name')->get();
        $totalParishes = $allParishes->count();

        $hasAnyParticipationSet = $allParishes->contains(function ($p) {
            return !empty($p->participating_categories) && is_array($p->participating_categories);
        });
        $hasAnyScheduleSet = ScheduleItem::whereNotNull('category_id')->whereNotNull('parish_id')->exists();

        $scheduledParishIds = [];
        $categorySchedules = [];
        if ($this->selectedCategoryId) {
            $categorySchedules = ScheduleItem::where('category_id', $this->selectedCategoryId)
                ->whereNotNull('parish_id')
                ->get()
                ->keyBy('parish_id');

            $scheduledParishIds = $categorySchedules->keys()->toArray();
        }

        if ($this->selectedCategoryId && ($hasAnyParticipationSet || $hasAnyScheduleSet)) {
            $parishes = $allParishes->filter(function (Parish $p) use ($scheduledParishIds) {
                // 1. Is it specifically scheduled for this category in the timetable?
                if (in_array($p->id, $scheduledParishIds)) {
                    return true;
                }
                // 2. Is this category ticked in its participating_categories?
                if (!empty($p->participating_categories) && is_array($p->participating_categories)) {
                    return in_array($this->selectedCategoryId, $p->participating_categories)
                        || in_array((string)$this->selectedCategoryId, $p->participating_categories)
                        || in_array((int)$this->selectedCategoryId, $p->participating_categories);
                }
                return false;
            })->values();
        } else {
            // If zero participation or schedules are configured yet, display all parishes as default fallback
            $parishes = $allParishes;
        }

        $scores = AdjudicationScore::where('category_id', $this->selectedCategoryId)
            ->where('adjudicator_name', $this->activeJudge)
            ->get()
            ->keyBy('parish_id');

        $liveSchedule = ScheduleItem::with(['parish', 'category'])
            ->where('status', 'in_progress')
            ->first();

        $scoringParish = $this->scoringParishId ? Parish::find($this->scoringParishId) : null;

        // Augment categories with their individual participant counts for the tab badges
        $categoriesWithCounts = $categories->map(function ($cat) use ($allParishes, $hasAnyParticipationSet, $hasAnyScheduleSet) {
            $catScheduledIds = ScheduleItem::where('category_id', $cat->id)
                ->whereNotNull('parish_id')
                ->pluck('parish_id')
                ->toArray();

            if ($hasAnyParticipationSet || $hasAnyScheduleSet) {
                $targetCount = $allParishes->filter(function (Parish $p) use ($cat, $catScheduledIds) {
                    if (in_array($p->id, $catScheduledIds)) {
                        return true;
                    }
                    if (!empty($p->participating_categories) && is_array($p->participating_categories)) {
                        return in_array($cat->id, $p->participating_categories)
                            || in_array((string)$cat->id, $p->participating_categories)
                            || in_array((int)$cat->id, $p->participating_categories);
                    }
                    return false;
                })->count();
            } else {
                $targetCount = $allParishes->count();
            }

            $cat->participant_count = $targetCount;
            return $cat;
        });

        return [
            'categories' => $categoriesWithCounts,
            'activeCategory' => $activeCategory,
            'parishes' => $parishes,
            'scores' => $scores,
            'liveSchedule' => $liveSchedule,
            'categorySchedules' => $categorySchedules,
            'scoringParish' => $scoringParish,
            'judges' => ['Judge 1', 'Judge 2', 'Judge 3'],
        ];
    }
}
