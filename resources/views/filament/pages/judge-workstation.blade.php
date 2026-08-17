<x-filament-panels::page>
    @php
        $viewData = $this->getViewData();
        $categories = $viewData['categories'];
        $activeCategory = $viewData['activeCategory'];
        $parishes = $viewData['parishes'];
        $scores = $viewData['scores'];
        $liveSchedule = $viewData['liveSchedule'];
        $scoringParish = $viewData['scoringParish'];
        $totalParishes = $parishes->count();
        $scoredCount = $scores->count();
        $progressPct = $totalParishes > 0 ? round(($scoredCount / $totalParishes) * 100) : 0;
    @endphp

    <style>
        /* ── All colours respect Filament's light/dark theme via Tailwind classes ── */
        /* We only add layout/animation helpers not in Tailwind by default */

        .ws-ping {
            animation: ws-ping 1.4s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
        @keyframes ws-ping {
            75%, 100% { transform: scale(2.2); opacity: 0; }
        }

        .ws-progress-ring svg { transform: rotate(-90deg); }
        .ws-ring-bg { fill: none; stroke-width: 4; }
        .ws-ring-val {
            fill: none;
            stroke-width: 4;
            stroke-linecap: round;
            transition: stroke-dashoffset 0.6s ease;
        }
        .ws-ring-text {
            position: absolute; inset: 0;
            display: flex; align-items: center; justify-content: center;
        }

        .ws-score-num-input {
            /* matches Filament input style */
            width: 6.5rem;
            text-align: center;
            font-weight: 800;
            font-size: 1.1rem;
        }

        .ws-criterion-card {
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .ws-criterion-card:focus-within {
            --tw-ring-color: rgb(245 158 11 / 0.2);
            --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
            border-color: rgb(245 158 11 / 0.5);
        }

        .ws-tab-btn {
            transition: all 0.15s ease;
        }
        .ws-tab-btn:hover {
            transform: translateY(-1px);
        }

        .ws-action-btn {
            transition: all 0.15s ease;
        }
        .ws-action-btn:hover {
            transform: translateY(-1px);
        }

        .ws-sticky-bar {
            position: sticky;
            bottom: 1rem;
            z-index: 20;
        }
    </style>

    @if($scoringParish && $activeCategory)
    {{-- ============================================================ --}}
    {{-- VIEW A: SCORE SHEET                                          --}}
    {{-- ============================================================ --}}
    <div class="space-y-5">

        {{-- Back bar --}}
        <div class="flex flex-wrap items-center justify-between gap-3
                    rounded-2xl border border-gray-200 bg-white px-4 py-3 shadow-sm
                    dark:border-white/10 dark:bg-gray-900">
            <button type="button" wire:click="closeScoreModal"
                class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50
                       px-4 py-2 text-xs font-bold text-gray-700 transition
                       hover:bg-gray-100 hover:border-gray-300
                       dark:border-white/10 dark:bg-white/5 dark:text-gray-200 dark:hover:bg-white/10">
                ← Back to Roster
            </button>
            <div class="flex items-center gap-2.5">
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-black uppercase tracking-wider text-amber-800
                             dark:bg-amber-900/40 dark:text-amber-300">
                    ⚖️ {{ $activeJudge }}
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ $activeCategory->name }}</span>
            </div>
        </div>

        {{-- Parish Hero --}}
        <div class="rounded-3xl border border-amber-500/30 bg-gradient-to-br from-amber-500/10 via-white to-white p-6 shadow-sm
                    dark:border-amber-500/20 dark:from-amber-950/40 dark:via-gray-900 dark:to-gray-900">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 flex-shrink-0 flex items-center justify-center rounded-2xl
                                bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950
                                text-lg font-black shadow-lg">
                        {{ $scoringParish->code ?? '⛪' }}
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400 mb-1">
                            {{ $scoringParish->deanery }} · Performance Adjudication
                        </p>
                        <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-gray-900 dark:text-white leading-tight">
                            {{ $scoringParish->name }}
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Category: <strong class="text-amber-600 dark:text-amber-400">{{ $activeCategory->name }}</strong>
                            · Max: <strong class="text-gray-800 dark:text-white">{{ $activeCategory->max_raw_score ?? 100 }} pts</strong>
                        </p>
                    </div>
                </div>

                {{-- Live total --}}
                <div class="rounded-2xl border border-amber-300 bg-amber-50 px-6 py-4 text-right
                            dark:border-amber-500/30 dark:bg-amber-950/50">
                    <div class="text-[10px] font-black uppercase tracking-widest text-amber-600 dark:text-amber-400 mb-1">Live Total</div>
                    <div class="font-mono text-3xl font-black text-amber-700 dark:text-amber-300 leading-none">
                        {{ $this->calculateTotal() }}
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">/ {{ $activeCategory->max_raw_score ?? 100 }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Presentation Info --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <h3 class="mb-4 text-xs font-black uppercase tracking-widest text-gray-500 dark:text-gray-400">
                📋 Presentation Information
            </h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="text-xs font-bold text-gray-600 dark:text-gray-300 mb-1 block">Song / Presentation Title</label>
                    <input type="text" wire:model="itemTitle" placeholder="e.g. Magnificat in C Major"
                        class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2 text-sm text-gray-900
                               focus:border-amber-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-400/20
                               dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-gray-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-600 dark:text-gray-300 mb-1 block">Conductor / Director</label>
                    <input type="text" wire:model="conductorName" placeholder="e.g. John Banda"
                        class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2 text-sm text-gray-900
                               focus:border-amber-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-400/20
                               dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-gray-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-600 dark:text-gray-300 mb-1 block">Language Used</label>
                    <input type="text" wire:model="languageUsed" placeholder="e.g. English / Lozi"
                        class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2 text-sm text-gray-900
                               focus:border-amber-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-400/20
                               dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-gray-500">
                </div>
            </div>
        </div>

        {{-- Rubric Criteria --}}
        <div class="space-y-2.5">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-black uppercase tracking-widest text-gray-500 dark:text-gray-400">
                    ⚖️ Assessment Rubric
                    <span class="ml-2 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-black text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                        {{ count($activeCategory->judging_criteria ?? []) }} criteria
                    </span>
                </h3>
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                    Max: <strong class="text-amber-600 dark:text-amber-400">{{ $activeCategory->max_raw_score ?? 100 }} pts</strong>
                </span>
            </div>

            @foreach($activeCategory->judging_criteria ?? [] as $index => $criterion)
                @php
                    $cKey = $criterion['no'] ?? ($index + 1);
                    $maxScore = $criterion['possible_score'] ?? 10;
                @endphp
                <div class="ws-criterion-card rounded-2xl border border-gray-200 bg-white p-4 shadow-sm
                            dark:border-white/10 dark:bg-gray-900">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-start gap-3 flex-1 min-w-[200px]">
                            <span class="mt-0.5 flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-xl
                                         bg-amber-100 text-xs font-black text-amber-800
                                         dark:bg-amber-900/40 dark:text-amber-300">
                                {{ $cKey }}
                            </span>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $criterion['criterion'] }}</span>
                                    <span class="rounded-lg bg-gray-100 px-2 py-0.5 text-[11px] font-semibold text-gray-500
                                                 dark:bg-white/10 dark:text-gray-400">
                                        Max {{ $maxScore }} pts
                                    </span>
                                </div>
                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                    {{ $criterion['description'] }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5 flex-shrink-0">
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400">Score:</label>
                            <input type="number" step="0.5" min="0" max="{{ $maxScore }}"
                                wire:model.live.debounce.250ms="criteriaScores.{{ $cKey }}"
                                placeholder="0–{{ $maxScore }}"
                                class="ws-score-num-input rounded-xl border-2 border-amber-300 bg-amber-50 px-3 py-2.5
                                       text-center font-mono font-black text-amber-800
                                       focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-400/20
                                       dark:border-amber-500/50 dark:bg-amber-950/40 dark:text-amber-300 dark:focus:border-amber-400">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Comments --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <label class="mb-2 block text-xs font-black uppercase tracking-widest text-gray-500 dark:text-gray-400">
                💬 Adjudicator Comments &amp; Constructive Critique
            </label>
            <textarea wire:model="comments" rows="3"
                placeholder="Provide constructive feedback on vocal control, staging, harmony, discipline, theme relevance…"
                class="w-full rounded-xl border border-gray-300 bg-gray-50 p-3.5 text-sm text-gray-900
                       focus:border-amber-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-400/20
                       dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-gray-500"></textarea>

            <div class="mt-3 flex items-center gap-2.5 rounded-xl border border-red-200 bg-red-50 p-3
                        dark:border-red-900/30 dark:bg-red-950/20">
                <input type="checkbox" id="disq_chk" wire:model="isDisqualified"
                    class="h-4 w-4 cursor-pointer rounded border-gray-300 accent-red-600">
                <label for="disq_chk" class="cursor-pointer text-xs font-bold text-red-700 dark:text-red-400">
                    🚫 Flag for Disqualification — Breach of CAM Festival regulations
                </label>
            </div>
        </div>

        {{-- Sticky Save Bar --}}
        <div class="ws-sticky-bar rounded-2xl border border-gray-200 bg-white/95 p-4 shadow-2xl backdrop-blur-md
                    dark:border-white/10 dark:bg-gray-900/95">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-baseline gap-2">
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400">Total Score:</span>
                    <span class="font-mono text-2xl font-black text-amber-600 dark:text-amber-400">
                        {{ $this->calculateTotal() }}
                    </span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">/ {{ $activeCategory->max_raw_score ?? 100 }} pts</span>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" wire:click="closeScoreModal"
                        class="ws-action-btn rounded-xl border border-gray-200 bg-gray-100 px-5 py-2.5 text-xs font-bold text-gray-600
                               hover:bg-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10">
                        Cancel
                    </button>
                    <button type="button" wire:click="saveScore"
                        class="ws-action-btn inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600
                               px-6 py-2.5 text-xs font-black text-slate-950 shadow-lg shadow-amber-500/30
                               hover:from-amber-400 hover:to-amber-500">
                        🔒 Save &amp; Lock Official Score
                    </button>
                </div>
            </div>
        </div>
    </div>

    @else
    {{-- ============================================================ --}}
    {{-- VIEW B: ROSTER DASHBOARD                                     --}}
    {{-- ============================================================ --}}
    <div class="space-y-5">

        {{-- Hero Header --}}
        <div class="rounded-3xl border border-amber-500/25 bg-gradient-to-br from-amber-500/10 via-white to-white p-6 shadow-sm
                    dark:border-amber-500/15 dark:from-amber-950/40 dark:via-gray-900 dark:to-gray-900">
            <div class="flex flex-wrap items-center justify-between gap-5">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl
                                bg-gradient-to-br from-amber-400 to-amber-600 text-2xl shadow-lg shadow-amber-500/30">
                        ⚖️
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="rounded-full bg-amber-100 px-3 py-0.5 text-xs font-black uppercase tracking-widest text-amber-800
                                         dark:bg-amber-900/40 dark:text-amber-300">
                                {{ $activeJudge }}
                            </span>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">• Official Adjudicator Console</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-gray-900 dark:text-white leading-tight">
                            Festival Adjudication &amp; Scoring Console
                        </h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Catholic Diocese of Livingstone · CAM Festival 2026</p>
                    </div>
                </div>

                {{-- Progress block --}}
                <div class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white px-5 py-3.5 shadow-sm
                            dark:border-white/10 dark:bg-gray-800/60">
                    {{-- SVG Ring --}}
                    <div class="ws-progress-ring relative h-14 w-14 flex-shrink-0">
                        <svg width="56" height="56" viewBox="0 0 56 56">
                            <circle class="ws-ring-bg stroke-gray-200 dark:stroke-white/10" cx="28" cy="28" r="23"/>
                            <circle class="ws-ring-val stroke-amber-500" cx="28" cy="28" r="23"
                                stroke-dasharray="{{ round(2 * M_PI * 23, 2) }}"
                                stroke-dashoffset="{{ round(2 * M_PI * 23 * (1 - $progressPct / 100), 2) }}"/>
                        </svg>
                        <div class="ws-ring-text text-[11px] font-black text-amber-600 dark:text-amber-400">
                            {{ $progressPct }}%
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-0.5">
                            Category Progress
                        </div>
                        <div class="text-xl font-black text-gray-900 dark:text-white leading-none">
                            {{ $scoredCount }}
                            <span class="text-sm font-medium text-gray-400 dark:text-gray-500">/ {{ $totalParishes }}</span>
                        </div>
                        <div class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">parishes assessed</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Live Alert --}}
        @if($liveSchedule && $liveSchedule->parish)
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 dark:border-red-900/40 dark:bg-red-950/30">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="relative flex h-3 w-3 flex-shrink-0">
                        <span class="ws-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex h-3 w-3 rounded-full bg-red-500"></span>
                    </span>
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-red-600 dark:text-red-400">
                            ⚡ Live on Stage Now
                        </div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white">
                            {{ $liveSchedule->activity_title }}
                            &bull; <span class="text-amber-600 dark:text-amber-400">⛪ {{ $liveSchedule->parish->name }}</span>
                        </div>
                    </div>
                </div>
                @if($liveSchedule->category_id === $selectedCategoryId)
                    <button type="button" wire:click="openScoreModal({{ $liveSchedule->parish_id }})"
                        class="ws-action-btn inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2 text-xs font-black text-white shadow-md hover:bg-red-700">
                        Score Live Performance →
                    </button>
                @endif
            </div>
        </div>
        @endif

        {{-- Category Tabs --}}
        <div class="space-y-2">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400">
                Competition Categories
            </p>
            <div class="flex flex-wrap gap-2">
                @foreach($categories as $category)
                    @php
                        $catScoresCount = \App\Models\AdjudicationScore::where('category_id', $category->id)
                            ->where('adjudicator_name', $activeJudge)
                            ->count();
                    @endphp
                    <button type="button"
                        wire:click="selectCategory({{ $category->id }})"
                        class="ws-tab-btn group flex items-center gap-2 rounded-2xl border px-4 py-2 text-sm font-bold
                               {{ $selectedCategoryId === $category->id
                                   ? 'border-amber-400 bg-amber-50 text-amber-900 shadow-sm dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-500/40'
                                   : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 dark:border-white/10 dark:bg-gray-900 dark:text-gray-300' }}">
                        <span>{{ $category->name }}</span>
                        <span class="rounded-full px-2 py-0.5 text-xs font-black
                                     {{ $selectedCategoryId === $category->id
                                         ? 'bg-amber-500 text-slate-950'
                                         : 'bg-gray-100 text-gray-500 dark:bg-white/10 dark:text-gray-400' }}">
                            {{ $catScoresCount }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Roster Table --}}
        @if($activeCategory)
        <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">

            {{-- Table header --}}
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 px-6 py-4 dark:border-white/10">
                <div>
                    <h3 class="text-lg font-black text-gray-900 dark:text-white">{{ $activeCategory->name }}</h3>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                        {{ $activeCategory->description ?? 'Official CAM Festival competition category.' }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-1.5 text-xs font-bold text-gray-600
                                 dark:border-white/10 dark:bg-white/5 dark:text-gray-300">
                        Max <strong class="text-amber-600 dark:text-amber-400">{{ $activeCategory->max_raw_score ?? 100 }} pts</strong>
                    </span>
                    <span class="rounded-xl border border-amber-200 bg-amber-50 px-3.5 py-1.5 text-xs font-bold text-amber-800
                                 dark:border-amber-500/30 dark:bg-amber-950/40 dark:text-amber-300">
                        {{ $scores->count() }} / {{ $parishes->count() }} Assessed
                    </span>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-[11px] font-black uppercase tracking-widest text-gray-400 dark:border-white/10">
                            <th class="px-5 py-3">Parish &amp; Contingent</th>
                            <th class="px-5 py-3">Deanery</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-center">Score</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                        @forelse($parishes as $parish)
                            @php $score = $scores->get($parish->id); @endphp
                            <tr class="transition hover:bg-gray-50/80 dark:hover:bg-white/[0.03]">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $parish->name }}</div>
                                    <div class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">Code: <strong class="text-amber-600 dark:text-amber-400">{{ $parish->code }}</strong></div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600
                                                 dark:bg-white/10 dark:text-gray-300">
                                        {{ $parish->deanery }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @if($score)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-extrabold text-emerald-700
                                                     dark:bg-emerald-950/50 dark:text-emerald-400">
                                            ✓ Assessed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-3 py-1 text-xs font-extrabold text-blue-700
                                                     dark:bg-blue-950/50 dark:text-blue-400">
                                            ⏳ Awaiting
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if($score)
                                        <div class="font-mono text-lg font-black text-amber-600 dark:text-amber-400">
                                            {{ $score->raw_total_score }}
                                            <span class="text-xs font-normal text-gray-400">/ {{ $activeCategory->max_raw_score ?? 100 }}</span>
                                        </div>
                                        @if($score->comments)
                                            <div class="mt-0.5 max-w-[180px] truncate text-xs italic text-gray-400">"{{ $score->comments }}"</div>
                                        @endif
                                    @else
                                        <span class="text-sm font-bold text-gray-300 dark:text-gray-600">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <button type="button"
                                        wire:click="openScoreModal({{ $parish->id }})"
                                        class="ws-action-btn inline-flex items-center gap-1.5 rounded-xl border px-4 py-2 text-xs font-bold
                                               {{ $score
                                                   ? 'border-emerald-300 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 dark:border-emerald-500/30 dark:bg-emerald-950/30 dark:text-emerald-400'
                                                   : 'border-amber-300 bg-amber-50 text-amber-900 hover:bg-amber-100 dark:border-amber-500/30 dark:bg-amber-950/30 dark:text-amber-300' }}">
                                        {{ $score ? '✏️ Edit Score' : '⚖️ Score Sheet' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-14 text-center">
                                    <div class="text-4xl mb-3">⛪</div>
                                    <div class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-1">No Parishes Scheduled</div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 max-w-sm mx-auto">
                                        No parishes have been assigned to this category yet. Assign them via the Admin panel.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
    @endif

</x-filament-panels::page>
