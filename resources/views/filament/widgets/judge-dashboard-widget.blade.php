<x-filament-widgets::widget>
    @php
        $data = $this->getViewData();
        $isJudge = $data['isJudge'];
        $judgeName = $data['judgeName'];
        $liveSchedule = $data['liveSchedule'];
        $scoresCount = $data['scoresCount'];
        $totalCategories = $data['totalCategories'];
        $progressPct = $totalCategories > 0 ? min(100, round(($scoresCount / $totalCategories) * 100)) : 0;
    @endphp

    {{-- Live Stage Alert --}}
    @if($liveSchedule && $liveSchedule->parish)
        <x-filament::section class="mb-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="relative flex h-3 w-3">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-danger-400 opacity-75"></span>
                        <span class="relative inline-flex h-3 w-3 rounded-full bg-danger-500"></span>
                    </span>
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-danger-600 dark:text-danger-400">⚡ Live on Stage Now</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $liveSchedule->activity_title }}
                            &bull; <span class="text-primary-600 dark:text-primary-400">{{ $liveSchedule->parish->name }}</span>
                        </p>
                    </div>
                </div>
                <x-filament::button
                    color="danger"
                    size="sm"
                    tag="a"
                    href="{{ \App\Filament\Pages\JudgeWorkstation::getUrl(['category_id' => $liveSchedule->category_id]) }}"
                >
                    Score Live Performance →
                </x-filament::button>
            </div>
        </x-filament::section>
    @endif

    {{-- Hero Section --}}
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::badge color="warning">{{ $judgeName }}</x-filament::badge>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Official Adjudicator Console</span>
            </div>
        </x-slot>

        <x-slot name="description">
            Submit criteria marks, score live performances on stage, and review locked rubric scorecards.
        </x-slot>

        <x-slot name="headerEnd">
            <x-filament::button
                tag="a"
                href="{{ \App\Filament\Pages\JudgeWorkstation::getUrl() }}"
                icon="heroicon-m-scale"
            >
                Open Workstation
            </x-filament::button>
        </x-slot>

        {{-- Progress Bar --}}
        <div class="mb-2 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
            <span class="font-semibold">Scoring Progress</span>
            <span>{{ $scoresCount }} of {{ $totalCategories }} categories submitted</span>
        </div>
        <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-white/10">
            <div
                class="h-full rounded-full bg-primary-500 transition-all duration-700"
                style="width: {{ $progressPct }}%"
            ></div>
        </div>
    </x-filament::section>

    {{-- Stats Grid --}}
    <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4">

        <x-filament::section compact>
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-primary-50 dark:bg-primary-950/30">
                    <x-filament::icon icon="heroicon-m-user" class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Assigned Identity</p>
                    <p class="text-base font-black text-primary-600 dark:text-primary-400">{{ $judgeName }}</p>
                    <p class="text-xs text-gray-400">Official Adjudicator</p>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section compact>
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-success-50 dark:bg-success-950/30">
                    <x-filament::icon icon="heroicon-m-check-badge" class="h-5 w-5 text-success-600 dark:text-success-400" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Evaluations Submitted</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white leading-none">{{ $scoresCount }}</p>
                    <p class="text-xs text-gray-400">scorecards locked</p>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section compact>
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-info-50 dark:bg-info-950/30">
                    <x-filament::icon icon="heroicon-m-trophy" class="h-5 w-5 text-info-600 dark:text-info-400" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Competition Categories</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white leading-none">{{ $totalCategories }}</p>
                    <p class="text-xs text-gray-400">competition events</p>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section compact>
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl
                    {{ $liveSchedule ? 'bg-danger-50 dark:bg-danger-950/30' : 'bg-gray-100 dark:bg-white/10' }}">
                    <x-filament::icon
                        icon="{{ $liveSchedule ? 'heroicon-m-musical-note' : 'heroicon-m-pause-circle' }}"
                        class="h-5 w-5 {{ $liveSchedule ? 'text-danger-600 dark:text-danger-400' : 'text-gray-400' }}"
                    />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Stage Status</p>
                    <p class="text-sm font-bold {{ $liveSchedule ? 'text-danger-600 dark:text-danger-400' : 'text-gray-500 dark:text-gray-400' }}">
                        {{ $liveSchedule ? '● Performance LIVE' : 'Intermission' }}
                    </p>
                    <p class="text-xs text-gray-400 truncate">
                        {{ $liveSchedule?->parish?->name ?? 'No active performance' }}
                    </p>
                </div>
            </div>
        </x-filament::section>

    </div>

</x-filament-widgets::widget>
