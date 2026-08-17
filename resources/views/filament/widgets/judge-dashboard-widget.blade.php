<x-filament-widgets::widget>
    @php
        $data = $this->getViewData();
        $isJudge = $data['isJudge'];
        $judgeName = $data['judgeName'];
        $liveSchedule = $data['liveSchedule'];
        $scoresCount = $data['scoresCount'];
        $totalCategories = $data['totalCategories'];
        $totalParishes = $data['totalParishes'] ?? 0;
        $categories = $data['categories'] ?? collect();
        $progressPct = $totalCategories > 0 ? min(100, round(($scoresCount / $totalCategories) * 100)) : 0;
    @endphp

    <div class="space-y-6">

        {{-- 1. Live Stage Alert (If stage is active) --}}
        @if($liveSchedule && $liveSchedule->parish)
            <div class="relative overflow-hidden rounded-2xl border border-danger-500/30 bg-danger-500/10 p-4 shadow-sm dark:border-danger-500/20 dark:bg-danger-950/40">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <span class="relative flex h-3.5 w-3.5 flex-shrink-0">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-danger-400 opacity-75"></span>
                            <span class="relative inline-flex h-3.5 w-3.5 rounded-full bg-danger-500"></span>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-black uppercase tracking-wider text-danger-700 dark:text-danger-400">
                                    ⚡ Live on Stage Now
                                </span>
                                @if($liveSchedule->category)
                                    <span class="rounded-md bg-danger-500/20 px-2 py-0.5 text-[10px] font-bold text-danger-800 dark:text-danger-300">
                                        {{ $liveSchedule->category->name }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">
                                {{ $liveSchedule->activity_title }}
                                &bull; <span class="text-primary-600 dark:text-primary-400 font-semibold">{{ $liveSchedule->parish->name }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">({{ $liveSchedule->parish->deanery }})</span>
                            </p>
                        </div>
                    </div>
                    <x-filament::button
                        color="danger"
                        size="md"
                        tag="a"
                        icon="heroicon-m-scale"
                        href="{{ \App\Filament\Pages\JudgeWorkstation::getUrl(['category_id' => $liveSchedule->category_id]) }}"
                    >
                        Score Live Performance →
                    </x-filament::button>
                </div>
            </div>
        @endif

        {{-- 2. Hero Section: Adjudicator Identity & Master CTA --}}
        <x-filament::section class="relative overflow-hidden">
            <x-slot name="heading">
                <div class="flex flex-wrap items-center gap-2.5">
                    <x-filament::badge color="warning" size="lg" icon="heroicon-m-scale">
                        {{ $judgeName }}
                    </x-filament::badge>
                    <span class="text-base font-bold text-gray-900 dark:text-white">
                        Official Adjudicator Console
                    </span>
                </div>
            </x-slot>

            <x-slot name="description">
                Catholic Diocese of Livingstone &bull; CAM Festival 2026 Adjudication System. Score live performances on stage, evaluate rubrics, and lock official results.
            </x-slot>

            <x-slot name="headerEnd">
                <x-filament::button
                    tag="a"
                    href="{{ \App\Filament\Pages\JudgeWorkstation::getUrl() }}"
                    icon="heroicon-m-arrow-top-right-on-square"
                    size="lg"
                >
                    Launch Workstation
                </x-filament::button>
            </x-slot>

            <div class="pt-2">
                <div class="mb-2 flex items-center justify-between text-xs font-semibold text-gray-600 dark:text-gray-400">
                    <span class="flex items-center gap-1.5">
                        <x-filament::icon icon="heroicon-m-chart-bar" class="h-4 w-4 text-primary-500" />
                        Overall Scoring Completion
                    </span>
                    <span class="font-mono text-sm font-bold text-primary-600 dark:text-primary-400">
                        {{ $scoresCount }} scorecards submitted ({{ $progressPct }}%)
                    </span>
                </div>
                <div class="h-2.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-white/10">
                    <div
                        class="h-full rounded-full bg-primary-500 transition-all duration-700"
                        style="width: {{ $progressPct }}%"
                    ></div>
                </div>
            </div>
        </x-filament::section>

        {{-- 3. 4-Column Stat Cards Grid --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">

            {{-- Stat 1: Assigned Judge --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center gap-3.5">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950/40 dark:text-primary-400">
                        <x-filament::icon icon="heroicon-m-user" class="h-6 w-6" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Assigned Identity</p>
                        <p class="truncate text-lg font-black text-primary-600 dark:text-primary-400">{{ $judgeName }}</p>
                        <p class="text-xs text-gray-400">Official Adjudicator</p>
                    </div>
                </div>
            </div>

            {{-- Stat 2: Evaluations Submitted --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center gap-3.5">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-success-50 text-success-600 dark:bg-success-950/40 dark:text-success-400">
                        <x-filament::icon icon="heroicon-m-clipboard-document-check" class="h-6 w-6" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Evaluations Submitted</p>
                        <p class="font-mono text-2xl font-black text-gray-900 dark:text-white leading-none my-0.5">{{ $scoresCount }}</p>
                        <p class="text-xs text-gray-400">scorecards locked</p>
                    </div>
                </div>
            </div>

            {{-- Stat 3: Competition Categories --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center gap-3.5">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-info-50 text-info-600 dark:bg-info-950/40 dark:text-info-400">
                        <x-filament::icon icon="heroicon-m-trophy" class="h-6 w-6" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Competition Categories</p>
                        <p class="font-mono text-2xl font-black text-gray-900 dark:text-white leading-none my-0.5">{{ $totalCategories }}</p>
                        <p class="text-xs text-gray-400">competition events</p>
                    </div>
                </div>
            </div>

            {{-- Stat 4: Stage Status --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center gap-3.5">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl {{ $liveSchedule ? 'bg-danger-50 text-danger-600 dark:bg-danger-950/40 dark:text-danger-400' : 'bg-gray-100 text-gray-400 dark:bg-white/10 dark:text-gray-400' }}">
                        <x-filament::icon icon="{{ $liveSchedule ? 'heroicon-m-sparkles' : 'heroicon-m-pause-circle' }}" class="h-6 w-6" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Stage Status</p>
                        <p class="text-sm font-bold truncate {{ $liveSchedule ? 'text-danger-600 dark:text-danger-400' : 'text-gray-600 dark:text-gray-400' }}">
                            {{ $liveSchedule ? '● Performance LIVE' : 'Intermission' }}
                        </p>
                        <p class="truncate text-xs text-gray-400" title="{{ $liveSchedule?->parish?->name ?? 'No active performance' }}">
                            {{ $liveSchedule?->parish?->name ?? 'No active performance' }}
                        </p>
                    </div>
                </div>
            </div>

        </div>

        {{-- 4. Category Roster & Direct Adjudication Cards --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-m-squares-2x2" class="h-5 w-5 text-primary-500" />
                    <span>Festival Competition Categories</span>
                </div>
            </x-slot>
            <x-slot name="description">
                Select a category to begin adjudicating, scoring criteria, and submitting official marks.
            </x-slot>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem;" class="pt-2">
                @foreach($categories as $category)
                    <div class="group relative flex flex-col justify-between rounded-xl border border-gray-200 bg-white p-4 transition-all duration-200 hover:border-primary-400 hover:shadow-md dark:border-white/10 dark:bg-gray-900/60 dark:hover:border-primary-500">
                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <h4 class="font-bold text-gray-900 group-hover:text-primary-600 dark:text-white dark:group-hover:text-primary-400">
                                    {{ $category->name }}
                                </h4>
                                <x-filament::badge color="warning" size="sm">
                                    Max {{ $category->max_raw_score ?? 100 }} pts
                                </x-filament::badge>
                            </div>
                            <p class="mt-1 line-clamp-2 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                {{ $category->description ?? 'Official category judging and rubric assessment.' }}
                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-gray-100 dark:border-white/5">
                            <div class="mb-1.5 flex items-center justify-between text-xs">
                                <span class="text-gray-500 dark:text-gray-400 font-medium">Scored</span>
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ $category->assessed_count }} / {{ $category->scheduled_count }} Parishes
                                </span>
                            </div>
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-white/10 mb-3">
                                <div
                                    class="h-full rounded-full bg-primary-500 transition-all duration-500"
                                    style="width: {{ $category->progress_pct }}%"
                                ></div>
                            </div>
                            <x-filament::button
                                size="sm"
                                color="{{ $category->progress_pct === 100 ? 'gray' : 'primary' }}"
                                icon="heroicon-m-scale"
                                tag="a"
                                href="{{ \App\Filament\Pages\JudgeWorkstation::getUrl(['category_id' => $category->id]) }}"
                                class="w-full justify-center"
                            >
                                {{ $category->progress_pct === 100 ? 'Review Scores' : 'Adjudicate Category' }} →
                            </x-filament::button>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

    </div>
</x-filament-widgets::widget>
