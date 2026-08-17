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

    @if($scoringParish && $activeCategory)
        <!-- ========================================================= -->
        <!-- VIEW A: DEDICATED OFFICIAL ADJUDICATION SCORE SHEET VIEW  -->
        <!-- ========================================================= -->
        <div class="space-y-6">
            <!-- Top Navigation & Return Bar -->
            <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <button
                    type="button"
                    wire:click="closeScoreModal"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-gray-50 px-4 py-2 text-xs font-bold text-gray-700 transition hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    <span>←</span>
                    <span>Back to Category Roster</span>
                </button>

                <div class="flex items-center gap-2.5">
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-black uppercase tracking-wider text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                        {{ $activeJudge }}
                    </span>
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                        {{ $activeCategory->name }}
                    </span>
                </div>
            </div>

            <!-- Parish Header Card -->
            <div class="rounded-3xl border border-amber-500/30 bg-gradient-to-r from-amber-500/10 via-white to-white p-6 shadow-md dark:border-amber-500/20 dark:from-amber-950/40 dark:via-gray-900 dark:to-gray-900">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                            <span>⛪ Performance Adjudication</span>
                            <span>&bull;</span>
                            <span>{{ $scoringParish->deanery }}</span>
                        </div>
                        <h2 class="mt-1 text-2xl font-black text-gray-900 dark:text-white sm:text-3xl">
                            {{ $scoringParish->name }}
                        </h2>
                        <div class="mt-1 flex items-center gap-3 text-xs font-semibold text-gray-500 dark:text-gray-400">
                            <span>Parish Code: <strong class="text-gray-900 dark:text-white">{{ $scoringParish->code }}</strong></span>
                            <span>&bull;</span>
                            <span>Category: <strong class="text-amber-600 dark:text-amber-400">{{ $activeCategory->name }}</strong></span>
                        </div>
                    </div>

                    <!-- Live Total Marks Header Counter -->
                    <div class="rounded-2xl border border-amber-300 bg-amber-50 px-6 py-3 text-right dark:border-amber-500/30 dark:bg-amber-950/60">
                        <div class="text-[11px] font-black uppercase tracking-wider text-amber-700 dark:text-amber-400">Total Marks Computed</div>
                        <div class="font-mono text-3xl font-black text-amber-900 dark:text-amber-300">
                            {{ $this->calculateTotal() }} <span class="text-sm font-normal text-gray-500 dark:text-gray-400">/ {{ $activeCategory->max_raw_score ?? 100 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Metadata Fields -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <h3 class="mb-3 text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Performance Metadata</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Song / Presentation Title</label>
                        <input
                            type="text"
                            wire:model="itemTitle"
                            placeholder="e.g. Magnificat in C Major"
                            class="mt-1 w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2 text-sm text-gray-900 focus:border-amber-500 focus:bg-white focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900"
                        >
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Conductor / Director</label>
                        <input
                            type="text"
                            wire:model="conductorName"
                            placeholder="e.g. John Banda"
                            class="mt-1 w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2 text-sm text-gray-900 focus:border-amber-500 focus:bg-white focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900"
                        >
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Language Used</label>
                        <input
                            type="text"
                            wire:model="languageUsed"
                            placeholder="e.g. English / Lozi"
                            class="mt-1 w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2 text-sm text-gray-900 focus:border-amber-500 focus:bg-white focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900"
                        >
                    </div>
                </div>
            </div>

            <!-- Rubric Criteria Cards -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Official Rubric Assessment Criteria ({{ count($activeCategory->judging_criteria ?? []) }} Items)
                    </h3>
                    <div class="text-xs font-bold text-gray-500 dark:text-gray-400">
                        Max Possible: <strong class="text-amber-600 dark:text-amber-400">{{ $activeCategory->max_raw_score ?? 100 }} pts</strong>
                    </div>
                </div>

                @foreach($activeCategory->judging_criteria ?? [] as $index => $criterion)
                    @php
                        $cKey = $criterion['no'] ?? ($index + 1);
                        $maxScore = $criterion['possible_score'] ?? 10;
                    @endphp
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:border-amber-400 dark:border-gray-800 dark:bg-gray-900 dark:hover:border-amber-500/50">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="flex-1 min-w-[240px]">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-amber-100 text-xs font-black text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                                        {{ $cKey }}
                                    </span>
                                    <span class="text-base font-bold text-gray-900 dark:text-white">
                                        {{ $criterion['criterion'] }}
                                    </span>
                                    <span class="rounded-lg bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                        Max: {{ $maxScore }} pts
                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                    {{ $criterion['description'] }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <label class="text-xs font-bold text-gray-500 dark:text-gray-400">Awarded:</label>
                                <input
                                    type="number"
                                    step="0.5"
                                    min="0"
                                    max="{{ $maxScore }}"
                                    wire:model.live.debounce.250ms="criteriaScores.{{ $cKey }}"
                                    placeholder="0 - {{ $maxScore }}"
                                    class="w-28 rounded-xl border border-amber-400 bg-amber-50/50 px-3.5 py-2.5 text-center font-mono text-lg font-black text-amber-900 focus:border-amber-500 focus:bg-white focus:outline-none dark:border-amber-500/60 dark:bg-gray-950 dark:text-amber-300"
                                >
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Adjudicator Remarks & Comments -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <label class="text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    Adjudicator Comments & Constructive Critique
                </label>
                <textarea
                    wire:model="comments"
                    rows="3"
                    placeholder="Provide constructive feedback for the choir / performers on vocal control, staging, harmony, discipline..."
                    class="mt-2 w-full rounded-xl border border-gray-300 bg-gray-50 p-3.5 text-sm text-gray-900 focus:border-amber-500 focus:bg-white focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900"
                ></textarea>

                <div class="mt-4 flex items-center gap-2.5 rounded-xl border border-red-200 bg-red-50 p-3 dark:border-red-900/30 dark:bg-red-950/20">
                    <input
                        type="checkbox"
                        id="disqualified_chk"
                        wire:model="isDisqualified"
                        class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500"
                    >
                    <label for="disqualified_chk" class="text-xs font-bold text-red-700 dark:text-red-400 cursor-pointer">
                        Flag for Disqualification (Breach of Diocesan CAM Festival regulations)
                    </label>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="sticky bottom-4 z-20 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white/95 p-4 shadow-xl backdrop-blur-md dark:border-gray-800 dark:bg-gray-900/95">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400">Total Score:</span>
                    <span class="font-mono text-2xl font-black text-amber-600 dark:text-amber-400">
                        {{ $this->calculateTotal() }}
                    </span>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">/ {{ $activeCategory->max_raw_score ?? 100 }} pts</span>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        wire:click="closeScoreModal"
                        class="rounded-xl border border-gray-300 bg-gray-100 px-5 py-2.5 text-xs font-bold text-gray-700 transition hover:bg-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        wire:click="saveScore"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-2.5 text-xs font-black text-slate-950 shadow-md transition hover:from-amber-400 hover:to-amber-500 hover:scale-[1.02]"
                    >
                        <span>🔒 Save & Lock Official Score</span>
                    </button>
                </div>
            </div>
        </div>

    @else
        <!-- ========================================================= -->
        <!-- VIEW B: MAIN ROSTER VIEW & CATEGORY WORKSTATION           -->
        <!-- ========================================================= -->
        <div class="space-y-6">
            <!-- 1. Hero Header Banner -->
            <div class="rounded-3xl border border-amber-500/30 bg-gradient-to-r from-amber-500/15 via-white to-white p-6 shadow-md dark:border-amber-500/20 dark:from-amber-950/40 dark:via-gray-900 dark:to-gray-900">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-tr from-amber-400 to-amber-600 text-2xl font-black text-slate-950 shadow-md">
                            ⚖️
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-black uppercase tracking-wider text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                                    {{ $activeJudge }}
                                </span>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">&bull; Official Adjudicator Console</span>
                            </div>
                            <h1 class="mt-1 text-2xl font-black tracking-tight text-gray-900 dark:text-white sm:text-3xl">
                                Festival Adjudication & Scoring Console
                            </h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-gray-50 px-5 py-3 dark:border-gray-800 dark:bg-gray-800/80">
                        <div>
                            <div class="text-[11px] font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Category Progress</div>
                            <div class="text-lg font-black text-gray-900 dark:text-white">
                                {{ $scoredCount }} <span class="text-xs font-medium text-gray-500 dark:text-gray-400">/ {{ $totalParishes }} Parishes</span>
                            </div>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-xs font-black text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                            {{ $progressPct }}%
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Live Stage Alert -->
            @if($liveSchedule && $liveSchedule->parish)
                <div class="rounded-2xl border border-red-300 bg-red-50 p-4 dark:border-red-900/40 dark:bg-red-950/40">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="relative flex h-3.5 w-3.5">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex h-3.5 w-3.5 rounded-full bg-red-500"></span>
                            </span>
                            <div>
                                <div class="text-xs font-black uppercase tracking-wider text-red-700 dark:text-red-400">⚡ LIVE ON MAIN STAGE NOW</div>
                                <div class="text-base font-extrabold text-gray-900 dark:text-white">
                                    {{ $liveSchedule->activity_title }} &bull; <span class="text-amber-600 dark:text-amber-400">⛪ {{ $liveSchedule->parish->name }}</span>
                                </div>
                            </div>
                        </div>

                        @if($liveSchedule->category_id === $selectedCategoryId)
                            <button
                                type="button"
                                wire:click="openScoreModal({{ $liveSchedule->parish_id }})"
                                class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2 text-xs font-black text-white shadow transition hover:bg-red-700 hover:scale-[1.02]"
                            >
                                <span>Score Live Performance</span>
                                <span>➔</span>
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            <!-- 3. Category Selector Tabs -->
            <div class="space-y-2">
                <label class="text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Competition Categories</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($categories as $category)
                        @php
                            $catScoresCount = \App\Models\AdjudicationScore::where('category_id', $category->id)
                                ->where('adjudicator_name', $activeJudge)
                                ->count();
                        @endphp
                        <button
                            type="button"
                            wire:click="selectCategory({{ $category->id }})"
                            class="group flex items-center gap-2.5 rounded-2xl border px-4 py-2.5 text-sm font-bold transition {{ $selectedCategoryId === $category->id ? 'border-amber-500 bg-amber-50 text-amber-900 shadow-sm dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-500/40' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-700' }}"
                        >
                            <span>{{ $category->name }}</span>
                            <span class="rounded-full px-2 py-0.5 text-xs font-black {{ $selectedCategoryId === $category->id ? 'bg-amber-500 text-slate-950' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                                {{ $catScoresCount }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- 4. Category Roster Table -->
            @if($activeCategory)
                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="mb-5 flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 pb-4 dark:border-gray-800">
                        <div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white">
                                {{ $activeCategory->name }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $activeCategory->description ?? 'Official Catholic Association of Youth (CAM) Diocesan Competition Assessment.' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="rounded-xl bg-gray-100 px-3.5 py-1.5 text-xs font-bold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                Max Score: <strong class="text-amber-600 dark:text-amber-400">{{ $activeCategory->max_raw_score ?? 100 }} pts</strong>
                            </span>
                            <span class="rounded-xl bg-amber-50 px-3.5 py-1.5 text-xs font-bold text-amber-800 dark:bg-amber-950/40 dark:text-amber-300">
                                Progress: {{ $scores->count() }} / {{ $parishes->count() }} Assessed
                            </span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 text-xs font-black uppercase tracking-wider text-gray-500 dark:border-gray-800 dark:text-gray-400">
                                    <th class="py-3 px-4">Parish & Contingent</th>
                                    <th class="py-3 px-4">Deanery</th>
                                    <th class="py-3 px-4">Adjudication Status</th>
                                    <th class="py-3 px-4 text-center">Score Recorded</th>
                                    <th class="py-3 px-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse($parishes as $parish)
                                    @php
                                        $score = $scores->get($parish->id);
                                    @endphp
                                    <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td class="py-4 px-4">
                                            <div class="font-extrabold text-gray-900 dark:text-white text-base">
                                                {{ $parish->name }}
                                            </div>
                                            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-0.5">Code: {{ $parish->code }}</div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                                {{ $parish->deanery }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($score)
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-extrabold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400">
                                                    ✓ Assessed
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-xs font-extrabold text-blue-700 dark:bg-blue-950/60 dark:text-blue-400">
                                                    ⏳ Awaiting Score
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @if($score)
                                                <div class="font-mono text-lg font-black text-amber-600 dark:text-amber-400">
                                                    {{ $score->raw_total_score }} <span class="text-xs font-normal text-gray-400">/ {{ $activeCategory->max_raw_score ?? 100 }}</span>
                                                </div>
                                                @if($score->comments)
                                                    <div class="max-w-xs truncate text-xs text-gray-500 italic mt-0.5">"{{ $score->comments }}"</div>
                                                @endif
                                            @else
                                                <span class="text-xs font-bold text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-right">
                                            <button
                                                type="button"
                                                wire:click="openScoreModal({{ $parish->id }})"
                                                class="inline-flex items-center gap-1.5 rounded-xl border border-amber-300 bg-amber-50 px-4 py-2 text-xs font-bold text-amber-900 transition hover:bg-amber-500 hover:text-slate-950 dark:border-amber-500/40 dark:bg-amber-950/40 dark:text-amber-300 dark:hover:bg-amber-500 dark:hover:text-slate-950"
                                            >
                                                <span>{{ $score ? '✏️ Modify Marks' : '⚖️ Score Sheet' }}</span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-12 text-center text-gray-400 font-medium">
                                            No parishes found scheduled for this category.
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
