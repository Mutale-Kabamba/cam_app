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

        $this->showScoreModal = true;
    }

    public function closeScoreModal(): void
    {
        $this->showScoreModal = false;
        $this->scoringParishId = null;
    }

    public function calculateTotal(): float
    {
        $total = 0;
        foreach ($this->criteriaScores as $score) {
            if (is_numeric($score)) {
                $total += floatval($score);
            }
        }
        return round($total, 2);
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

        $maxPossible = $category->max_raw_score > 0 ? $category->max_raw_score : 100;
        $normalized = round(($totalRaw / $maxPossible) * 100, 2);

        if ($this->isDisqualified) {
            $totalRaw = 0;
            $normalized = 0;
        }

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
                'item_title' => $this->itemTitle,
                'criteria_scores' => $cleanedScores,
                'raw_total_score' => $totalRaw,
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
            ->body("{$parish->name} evaluated by {$this->activeJudge}: {$totalRaw}/{$maxPossible} pts.")
            ->success()
            ->send();

        $this->closeScoreModal();
    }

    public function getViewData(): array
    {
        $categories = Category::all();
        $activeCategory = Category::find($this->selectedCategoryId) ?? $categories->first();

        $scheduledParishIds = [];
        if ($this->selectedCategoryId) {
            $scheduledParishIds = ScheduleItem::where('category_id', $this->selectedCategoryId)
                ->whereNotNull('parish_id')
                ->pluck('parish_id')
                ->toArray();
        }

        if (!empty($scheduledParishIds)) {
            $parishes = Parish::whereIn('id', $scheduledParishIds)->orderBy('name')->get();
        } else {
            $parishes = Parish::orderBy('name')->get();
        }

        $scores = AdjudicationScore::where('category_id', $this->selectedCategoryId)
            ->where('adjudicator_name', $this->activeJudge)
            ->get()
            ->keyBy('parish_id');

        $liveSchedule = ScheduleItem::with(['parish', 'category'])
            ->where('status', 'in_progress')
            ->first();

        $scoringParish = $this->scoringParishId ? Parish::find($this->scoringParishId) : null;

        return [
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'parishes' => $parishes,
            'scores' => $scores,
            'liveSchedule' => $liveSchedule,
            'scoringParish' => $scoringParish,
            'judges' => ['Judge 1', 'Judge 2', 'Judge 3'],
        ];
    }
}
