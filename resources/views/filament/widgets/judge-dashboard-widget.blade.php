<x-filament-widgets::widget>
    @php
        $data = $this->getViewData();
        $isJudge = $data['isJudge'];
        $judgeName = $data['judgeName'];
        $liveSchedule = $data['liveSchedule'];
        $scoresCount = $data['scoresCount'];
        $totalCategories = $data['totalCategories'];
    @endphp

    <div class="relative overflow-hidden rounded-3xl border border-amber-500/30 bg-gradient-to-r from-amber-500/10 via-white to-white p-6 shadow-md dark:border-amber-500/20 dark:from-amber-950/40 dark:via-gray-900 dark:to-gray-900">
        <div class="flex flex-wrap items-center justify-between gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-black uppercase tracking-wider text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                        ⚖️ {{ $judgeName }} Console
                    </span>
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">&bull; CAM Festival Official Adjudication</span>
                </div>
                <h2 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white sm:text-3xl">
                    Adjudicator Scoring Workstation
                </h2>
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">
                    Submit criteria marks, score live performances on stage, and review locked rubric scorecards.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a
                    href="{{ \App\Filament\Pages\JudgeWorkstation::getUrl() }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-5 py-3 text-xs font-black text-slate-950 shadow-md transition hover:from-amber-400 hover:to-amber-500 hover:scale-[1.02]"
                >
                    <span>⚖️ Launch Judge Workstation</span>
                    <span>➔</span>
                </a>

                @if($liveSchedule && $liveSchedule->parish)
                    <a
                        href="{{ \App\Filament\Pages\JudgeWorkstation::getUrl(['category_id' => $liveSchedule->category_id]) }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-xs font-bold text-red-700 shadow transition hover:bg-red-100 dark:border-red-900/40 dark:bg-red-950/40 dark:text-red-300"
                    >
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                        </span>
                        <span>Score LIVE: {{ $liveSchedule->parish->name }}</span>
                    </a>
                @endif
            </div>
        </div>

        <div class="mt-5 grid grid-cols-2 gap-4 border-t border-gray-200/80 pt-4 dark:border-gray-800 sm:grid-cols-4">
            <div>
                <div class="text-[11px] font-bold uppercase text-gray-500 dark:text-gray-400">Assigned Identity</div>
                <div class="text-base font-extrabold text-amber-600 dark:text-amber-400">{{ $judgeName }}</div>
            </div>
            <div>
                <div class="text-[11px] font-bold uppercase text-gray-500 dark:text-gray-400">Evaluations Submitted</div>
                <div class="text-base font-extrabold text-gray-900 dark:text-white">{{ $scoresCount }}</div>
            </div>
            <div>
                <div class="text-[11px] font-bold uppercase text-gray-500 dark:text-gray-400">Competition Categories</div>
                <div class="text-base font-extrabold text-gray-900 dark:text-white">{{ $totalCategories }} Events</div>
            </div>
            <div>
                <div class="text-[11px] font-bold uppercase text-gray-500 dark:text-gray-400">Stage Status</div>
                <div class="text-base font-extrabold {{ $liveSchedule ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                    {{ $liveSchedule ? '● Performance LIVE' : '⏳ Intermission' }}
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
