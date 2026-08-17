<x-filament-panels::page>
    @php
        $viewData = $this->getViewData();
        $categories = $viewData['categories'];
        $activeCategory = $viewData['activeCategory'];
        $parishes = $viewData['parishes'];
        $scores = $viewData['scores'];
        $liveSchedule = $viewData['liveSchedule'];
        $categorySchedules = $viewData['categorySchedules'] ?? [];
        $scoringParish = $viewData['scoringParish'];
        $totalParishes = $parishes->count();
        $scoredCount = $scores->count();
        $progressPct = $totalParishes > 0 ? min(100, round(($scoredCount / $totalParishes) * 100)) : 0;
    @endphp

    <style>
        .ws-sticky-bar {
            position: sticky;
            bottom: 1.25rem;
            z-index: 30;
        }
        .ws-num-input::-webkit-inner-spin-button,
        .ws-num-input::-webkit-outer-spin-button {
            opacity: 1;
        }
    </style>

    @if($scoringParish && $activeCategory)
    {{-- ============================================================ --}}
    {{-- VIEW A: ADJUDICATOR RUBRIC SCORE SHEET                       --}}
    {{-- ============================================================ --}}
    <div class="space-y-6">

        {{-- Top Navigation & Context Bar --}}
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-gray-200 bg-white px-4 py-3 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <x-filament::button
                type="button"
                color="gray"
                size="sm"
                icon="heroicon-m-arrow-left"
                wire:click="closeScoreModal"
            >
                Back to Category Roster
            </x-filament::button>

            <div class="flex items-center gap-2.5">
                <x-filament::badge color="warning" size="md" icon="heroicon-m-scale">
                    {{ $activeJudge }}
                </x-filament::badge>
                <x-filament::badge color="gray" size="md">
                    {{ $activeCategory->name }}
                </x-filament::badge>
            </div>
        </div>

        {{-- Parish Performance Hero Header --}}
        <div class="relative overflow-hidden rounded-2xl border border-primary-500/30 bg-gradient-to-r from-primary-500/10 via-white to-white p-6 shadow-sm dark:border-primary-500/20 dark:from-primary-950/40 dark:via-gray-900 dark:to-gray-900">
            <div class="flex flex-wrap items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 text-xl font-black text-slate-950 shadow-lg shadow-amber-500/20">
                        {{ $scoringParish->code ?? '⛪' }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400">
                                {{ $scoringParish->deanery }}
                            </span>
                            <span class="text-xs text-gray-400">&bull;</span>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Performance Adjudication</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-gray-900 dark:text-white">
                            {{ $scoringParish->name }}
                        </h2>
                        <div class="flex items-center gap-2 mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <span>Category: <strong class="text-gray-900 dark:text-white">{{ $activeCategory->name }}</strong></span>
                            <span>&bull;</span>
                            <span>Max Scale: <strong class="text-primary-600 dark:text-primary-400">{{ $activeCategory->max_raw_score ?? 100 }} pts</strong></span>
                        </div>
                    </div>
                </div>

                {{-- Live Computed Total Score Box --}}
                <div class="flex items-center gap-4 rounded-2xl border border-amber-300 bg-amber-50/80 px-6 py-4 dark:border-amber-500/30 dark:bg-amber-950/40">
                    <div class="text-right">
                        <div class="text-[10px] font-black uppercase tracking-widest text-amber-700 dark:text-amber-300">
                            Current Total
                        </div>
                        <div class="font-mono text-3xl font-black text-amber-800 dark:text-amber-300 leading-tight">
                            {{ $this->calculateTotal() }}
                            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">/ {{ $activeCategory->max_raw_score ?? 100 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Presentation Information --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-m-document-text" class="h-5 w-5 text-primary-500" />
                    <span>Presentation Information</span>
                </div>
            </x-slot>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 pt-1">
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                        Song / Item Title
                    </label>
                    <input
                        type="text"
                        wire:model="itemTitle"
                        placeholder="e.g. Magnificat in C Major"
                        class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-gray-500"
                    />
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                        Conductor / Director
                    </label>
                    <input
                        type="text"
                        wire:model="conductorName"
                        placeholder="e.g. John Banda"
                        class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-gray-500"
                    />
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                        Language Used
                    </label>
                    <input
                        type="text"
                        wire:model="languageUsed"
                        placeholder="e.g. English / Lozi / Tonga"
                        class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-gray-500"
                    />
                </div>
            </div>
        </x-filament::section>

        {{-- Rubric Criteria List --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-m-clipboard-document-list" class="h-5 w-5 text-primary-500" />
                    <span>Official Assessment Rubric</span>
                </div>
            </x-slot>

            <x-slot name="headerEnd">
                <x-filament::badge color="primary">
                    {{ count($activeCategory->judging_criteria ?? []) }} Criteria &bull; Max {{ $activeCategory->max_raw_score ?? 100 }} pts
                </x-filament::badge>
            </x-slot>

            <div class="space-y-4 pt-1">
                @foreach($activeCategory->judging_criteria ?? [] as $index => $criterion)
                    @php
                        $cKey = $criterion['no'] ?? ($index + 1);
                        $maxScore = $criterion['possible_score'] ?? 10;
                    @endphp
                    <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-gray-200 bg-gray-50/50 p-4 transition-all duration-150 focus-within:border-primary-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-primary-500/20 dark:border-white/10 dark:bg-white/[0.02] dark:focus-within:bg-gray-900">
                        <div class="flex items-start gap-3.5 flex-1 min-w-[240px]">
                            <span class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl bg-primary-100 font-mono text-xs font-black text-primary-800 dark:bg-primary-950/60 dark:text-primary-300">
                                {{ $cKey }}
                            </span>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ $criterion['criterion'] }}
                                    </span>
                                    <span class="rounded-md bg-gray-200/70 px-2 py-0.5 text-[11px] font-bold text-gray-700 dark:bg-white/10 dark:text-gray-300">
                                        Max {{ $maxScore }} pts
                                    </span>
                                </div>
                                @if(!empty($criterion['description']))
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                        {{ $criterion['description'] }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Scoring Controls --}}
                        <div class="flex items-center gap-3 flex-shrink-0">
                            {{-- Quick-fill presets --}}
                            <div class="hidden sm:flex items-center gap-1">
                                <button
                                    type="button"
                                    wire:click="$set('criteriaScores.{{ $cKey }}', 0)"
                                    class="rounded-lg border border-gray-200 bg-white px-2 py-1 text-[11px] font-bold text-gray-500 hover:bg-gray-100 dark:border-white/10 dark:bg-white/5 dark:text-gray-400"
                                >
                                    0
                                </button>
                                <button
                                    type="button"
                                    wire:click="$set('criteriaScores.{{ $cKey }}', {{ round($maxScore / 2, 1) }})"
                                    class="rounded-lg border border-gray-200 bg-white px-2 py-1 text-[11px] font-bold text-gray-500 hover:bg-gray-100 dark:border-white/10 dark:bg-white/5 dark:text-gray-400"
                                >
                                    50%
                                </button>
                                <button
                                    type="button"
                                    wire:click="$set('criteriaScores.{{ $cKey }}', {{ $maxScore }})"
                                    class="rounded-lg border border-primary-200 bg-primary-50 px-2 py-1 text-[11px] font-bold text-primary-700 hover:bg-primary-100 dark:border-primary-500/30 dark:bg-primary-950/40 dark:text-primary-300"
                                >
                                    Max
                                </button>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <label class="text-xs font-bold text-gray-500 dark:text-gray-400">Mark:</label>
                                <input
                                    type="number"
                                    step="0.5"
                                    min="0"
                                    max="{{ $maxScore }}"
                                    wire:model.live.debounce.200ms="criteriaScores.{{ $cKey }}"
                                    placeholder="0–{{ $maxScore }}"
                                    class="ws-num-input w-24 rounded-xl border-2 border-primary-300 bg-white px-3 py-2 text-center font-mono text-base font-black text-primary-900 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-primary-500/50 dark:bg-gray-900 dark:text-primary-300"
                                />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        {{-- Constructive Comments & Disqualification --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-m-chat-bubble-bottom-center-text" class="h-5 w-5 text-primary-500" />
                    <span>Adjudicator Comments &amp; Feedback</span>
                </div>
            </x-slot>

            <div class="space-y-4 pt-1">
                <textarea
                    wire:model="comments"
                    rows="3"
                    placeholder="Provide constructive feedback on vocal harmony, staging discipline, dynamic control, tone quality, costume/presentation..."
                    class="w-full rounded-xl border border-gray-300 bg-gray-50 p-3.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-gray-500"
                ></textarea>

                <div class="flex items-center gap-3 rounded-xl border border-danger-300 bg-danger-50 p-3.5 dark:border-danger-900/40 dark:bg-danger-950/20">
                    <input
                        type="checkbox"
                        id="disq_chk"
                        wire:model="isDisqualified"
                        class="h-4 w-4 cursor-pointer rounded border-gray-300 text-danger-600 focus:ring-danger-500"
                    />
                    <label for="disq_chk" class="cursor-pointer text-xs font-bold text-danger-700 dark:text-danger-400">
                        🚫 Flag for Disqualification &mdash; Critical breach of CAM Festival rules/regulations
                    </label>
                </div>
            </div>
        </x-filament::section>

        {{-- Floating Sticky Save Bar --}}
        <div class="ws-sticky-bar rounded-2xl border border-gray-200 bg-white/95 p-4 shadow-2xl backdrop-blur-md dark:border-white/10 dark:bg-gray-900/95">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-baseline gap-2.5">
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Final Scorecard Total:
                    </span>
                    <span class="font-mono text-3xl font-black text-primary-600 dark:text-primary-400">
                        {{ $this->calculateTotal() }}
                    </span>
                    <span class="text-xs font-semibold text-gray-400 dark:text-gray-500">
                        / {{ $activeCategory->max_raw_score ?? 100 }} pts
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <x-filament::button
                        type="button"
                        color="gray"
                        size="md"
                        wire:click="closeScoreModal"
                    >
                        Cancel
                    </x-filament::button>

                    <x-filament::button
                        type="button"
                        color="primary"
                        size="md"
                        icon="heroicon-m-lock-closed"
                        wire:click="saveScore"
                        wire:loading.attr="disabled"
                    >
                        Save &amp; Lock Official Score
                    </x-filament::button>
                </div>
            </div>
        </div>
    </div>

    @else
    {{-- ============================================================ --}}
    {{-- VIEW B: CATEGORY ROSTER DASHBOARD                            --}}
    {{-- ============================================================ --}}
    <div class="space-y-6">

        {{-- Hero Header Section --}}
        <x-filament::section class="relative overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 text-3xl shadow-lg shadow-amber-500/20">
                        ⚖️
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <x-filament::badge color="warning" size="md">
                                {{ $activeJudge }}
                            </x-filament::badge>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                &bull; Official Adjudicator Console
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-gray-900 dark:text-white">
                            Festival Adjudication & Scoring Console
                        </h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Catholic Diocese of Livingstone &bull; CAM Festival 2026
                        </p>
                    </div>
                </div>

                {{-- Category Progress KPI Card --}}
                <div class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-gray-50/80 px-5 py-3.5 shadow-sm dark:border-white/10 dark:bg-white/5">
                    <div class="text-right">
                        <div class="text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-0.5">
                            Category Progress
                        </div>
                        <div class="font-mono text-2xl font-black text-primary-600 dark:text-primary-400 leading-none">
                            {{ $scoredCount }}
                            <span class="text-sm font-normal text-gray-400 dark:text-gray-500">/ {{ $totalParishes }}</span>
                        </div>
                        <div class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">
                            {{ $progressPct }}% parishes assessed
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- Live Alert (if a performance is active on stage) --}}
        @if($liveSchedule && $liveSchedule->parish)
            <div class="relative overflow-hidden rounded-2xl border border-danger-500/30 bg-danger-500/10 p-4 shadow-sm dark:border-danger-500/20 dark:bg-danger-950/40">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <span class="relative flex h-3.5 w-3.5 flex-shrink-0">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-danger-400 opacity-75"></span>
                            <span class="relative inline-flex h-3.5 w-3.5 rounded-full bg-danger-500"></span>
                        </span>
                        <div>
                            <div class="text-[10px] font-black uppercase tracking-wider text-danger-700 dark:text-danger-400">
                                ⚡ Live on Stage Now
                            </div>
                            <div class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">
                                {{ $liveSchedule->activity_title }}
                                &bull; <span class="text-primary-600 dark:text-primary-400">⛪ {{ $liveSchedule->parish->name }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">({{ $liveSchedule->parish->deanery }})</span>
                            </div>
                        </div>
                    </div>

                    @if($liveSchedule->category_id === $selectedCategoryId)
                        <x-filament::button
                            color="danger"
                            size="md"
                            icon="heroicon-m-scale"
                            wire:click="openScoreModal({{ $liveSchedule->parish_id }})"
                        >
                            Score Live Performance →
                        </x-filament::button>
                    @else
                        <x-filament::button
                            color="danger"
                            size="md"
                            icon="heroicon-m-arrow-right"
                            wire:click="selectCategory({{ $liveSchedule->category_id }})"
                        >
                            Switch to {{ $liveSchedule->category->name ?? 'Live Category' }} →
                        </x-filament::button>
                    @endif
                </div>
            </div>
        @endif

        {{-- Category Selection Tabs --}}
        <div>
            <p class="mb-2 text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">
                Competition Categories
            </p>
            <div class="flex flex-wrap gap-2">
                @foreach($categories as $category)
                    @php
                        $catScoresCount = \App\Models\AdjudicationScore::where('category_id', $category->id)
                            ->where('adjudicator_name', $activeJudge)
                            ->count();
                        $isActive = ($selectedCategoryId === $category->id);
                    @endphp
                    <button
                        type="button"
                        wire:click="selectCategory({{ $category->id }})"
                        class="flex items-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-bold transition-all duration-150
                            {{ $isActive
                                ? 'border-primary-500 bg-primary-500 text-white shadow-md shadow-primary-500/20 dark:border-primary-500 dark:bg-primary-500 dark:text-slate-950'
                                : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300 hover:bg-gray-50 dark:border-white/10 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-white/5' }}"
                    >
                        <span>{{ $category->name }}</span>
                        <span class="rounded-full px-2 py-0.5 text-xs font-black
                            {{ $isActive
                                ? 'bg-white/20 text-white dark:bg-slate-950/20 dark:text-slate-950'
                                : 'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-400' }}">
                            {{ $catScoresCount }} / {{ $category->participant_count ?? 0 }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Active Category Header & Parishes Roster --}}
        @if($activeCategory)
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-m-trophy" class="h-5 w-5 text-primary-500" />
                        <span class="text-lg font-bold">{{ $activeCategory->name }}</span>
                    </div>
                </x-slot>

                <x-slot name="description">
                    {{ $activeCategory->description ?? 'Official CAM Festival competition category and parish assessment roster.' }}
                </x-slot>

                <x-slot name="headerEnd">
                    <div class="flex items-center gap-2">
                        <x-filament::badge color="gray" size="md">
                            Max <strong class="text-primary-600 dark:text-primary-400">{{ $activeCategory->max_raw_score ?? 100 }} pts</strong>
                        </x-filament::badge>
                        <x-filament::badge color="{{ $scores->count() === $parishes->count() && $parishes->count() > 0 ? 'success' : 'primary' }}" size="md">
                            {{ $scores->count() }} / {{ $parishes->count() }} Assessed
                        </x-filament::badge>
                    </div>
                </x-slot>

                {{-- Table of Parishes --}}
                <div class="overflow-x-auto -mx-6 -mb-6 mt-3">
                    <table class="w-full text-left text-sm divide-y divide-gray-200 dark:divide-white/10">
                        <thead class="bg-gray-50/75 dark:bg-white/[0.02]">
                            <tr class="text-[11px] font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                <th class="px-6 py-3.5">Parish &amp; Contingent</th>
                                <th class="px-6 py-3.5">Deanery</th>
                                <th class="px-6 py-3.5">Status</th>
                                <th class="px-6 py-3.5 text-center">Score</th>
                                <th class="px-6 py-3.5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white dark:divide-white/5 dark:bg-gray-900">
                            @forelse($parishes as $parish)
                                @php
                                    $score = $scores->get($parish->id);
                                    $isCurrentlyOnStage = ($liveSchedule && $liveSchedule->parish_id === $parish->id && $liveSchedule->category_id === $selectedCategoryId);
                                @endphp
                                <tr class="transition hover:bg-gray-50/80 dark:hover:bg-white/[0.02] {{ $isCurrentlyOnStage ? 'bg-danger-50/40 dark:bg-danger-950/20' : '' }}">
                                    <td class="px-6 py-4">
                                        @php
                                            $itemSchedule = isset($categorySchedules[$parish->id]) ? $categorySchedules[$parish->id] : null;
                                        @endphp
                                        <div class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                            {{ $parish->name }}
                                            @if($isCurrentlyOnStage)
                                                <x-filament::badge color="danger" size="sm">
                                                    ⚡ ON STAGE
                                                </x-filament::badge>
                                            @elseif($itemSchedule && $itemSchedule->performance_order)
                                                <x-filament::badge color="gray" size="sm">
                                                    Order #{{ $itemSchedule->performance_order }}
                                                </x-filament::badge>
                                            @endif
                                        </div>
                                        <div class="mt-0.5 text-xs text-gray-400 dark:text-gray-500 flex items-center gap-2">
                                            <span>Code: <strong class="text-primary-600 dark:text-primary-400">{{ $parish->code }}</strong></span>
                                            @if($itemSchedule && $itemSchedule->scheduled_start_time)
                                                <span>&bull;</span>
                                                <span class="text-gray-500 dark:text-gray-400">⏱️ {{ \Carbon\Carbon::parse($itemSchedule->scheduled_start_time)->format('H:i') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <x-filament::badge color="gray" size="sm">
                                            {{ $parish->deanery }}
                                        </x-filament::badge>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($score)
                                            <x-filament::badge color="success" size="sm" icon="heroicon-m-check-badge">
                                                ✓ Assessed
                                            </x-filament::badge>
                                        @else
                                            <x-filament::badge color="gray" size="sm" icon="heroicon-m-clock">
                                                ⏳ Awaiting
                                            </x-filament::badge>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($score)
                                            <div class="font-mono text-base font-black text-primary-600 dark:text-primary-400">
                                                {{ $score->raw_total_score }}
                                                <span class="text-xs font-normal text-gray-400">/ {{ $activeCategory->max_raw_score ?? 100 }}</span>
                                            </div>
                                            @if($score->comments)
                                                <div class="mt-0.5 max-w-[200px] truncate text-xs italic text-gray-400 mx-auto" title="{{ $score->comments }}">
                                                    "{{ $score->comments }}"
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-sm font-bold text-gray-300 dark:text-gray-600">&mdash;</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <x-filament::button
                                            type="button"
                                            size="sm"
                                            color="{{ $score ? 'success' : 'primary' }}"
                                            icon="{{ $score ? 'heroicon-m-pencil-square' : 'heroicon-m-scale' }}"
                                            wire:click="openScoreModal({{ $parish->id }})"
                                        >
                                            {{ $score ? 'Edit Score' : 'Score Sheet' }}
                                        </x-filament::button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="text-4xl mb-2">⛪</div>
                                        <div class="text-sm font-bold text-gray-600 dark:text-gray-300">No Parishes Scheduled</div>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 max-w-sm mx-auto">
                                            No parishes have been assigned to this competition event yet.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif

    </div>
    @endif

</x-filament-panels::page>
