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

        @if($this->isChoirCategory())
            {{-- CHOIR MUSIC (MELODY) - 4 PRESCRIBED SONGS & AUDIT --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-3 mb-4 dark:border-white/5">
                    <x-filament::icon icon="heroicon-m-musical-note" class="h-5 w-5 text-primary-500" />
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Choir Music (Melody) &mdash; 4 Prescribed Songs</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Each compulsory song carries 25 marks (total 100). Any parish that fails to present one or more songs forfeits 25 marks per omitted song.</p>
                    </div>
                </div>

                <div class="space-y-4 pt-1">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Conductor</label>
                            <input
                                type="text"
                                wire:model="conductorName"
                                placeholder="e.g. John Banda"
                                class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Number of Participants (on stage)</label>
                            <input
                                type="number"
                                wire:model="participantCount"
                                placeholder="e.g. 28"
                                class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white"
                            />
                        </div>
                    </div>

                    {{-- 4 Prescribed Songs Table --}}
                    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-gray-50/50 p-3 dark:border-white/10 dark:bg-white/[0.02]">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-[10px] font-black uppercase tracking-wider text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-white/10">
                                    <th class="pb-2">Song Category</th>
                                    <th class="pb-2">Title of Song</th>
                                    <th class="pb-2 text-center">Presentation Status</th>
                                    <th class="pb-2 text-right">Marks Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200/60 dark:divide-white/5">
                                {{-- 1. Social Song --}}
                                <tr>
                                    <td class="py-2.5 font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                        <span>🎵</span> Social Song
                                    </td>
                                    <td class="py-2.5 pr-3">
                                        <input
                                            type="text"
                                            wire:model="songTitles.social_song"
                                            placeholder="Enter Social Song title…"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-primary-500 focus:outline-none dark:border-white/10 dark:bg-gray-900 dark:text-white"
                                        />
                                    </td>
                                    <td class="py-2.5 text-center">
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                wire:model.live="songsPresented.social_song"
                                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                            />
                                            <span class="font-bold {{ ($songsPresented['social_song'] ?? true) ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                                                {{ ($songsPresented['social_song'] ?? true) ? '✓ Presented' : '✗ Omitted' }}
                                            </span>
                                        </label>
                                    </td>
                                    <td class="py-2.5 text-right font-mono font-bold {{ ($songsPresented['social_song'] ?? true) ? 'text-gray-700 dark:text-gray-300' : 'text-danger-600 dark:text-danger-400' }}">
                                        {{ ($songsPresented['social_song'] ?? true) ? '25 pts' : 'Forfeited (-25)' }}
                                    </td>
                                </tr>

                                {{-- 2. Kyrie --}}
                                <tr>
                                    <td class="py-2.5 font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                        <span>🎵</span> Kyrie
                                    </td>
                                    <td class="py-2.5 pr-3">
                                        <input
                                            type="text"
                                            wire:model="songTitles.kyrie"
                                            placeholder="Enter Kyrie song title…"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-primary-500 focus:outline-none dark:border-white/10 dark:bg-gray-900 dark:text-white"
                                        />
                                    </td>
                                    <td class="py-2.5 text-center">
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                wire:model.live="songsPresented.kyrie"
                                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                            />
                                            <span class="font-bold {{ ($songsPresented['kyrie'] ?? true) ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                                                {{ ($songsPresented['kyrie'] ?? true) ? '✓ Presented' : '✗ Omitted' }}
                                            </span>
                                        </label>
                                    </td>
                                    <td class="py-2.5 text-right font-mono font-bold {{ ($songsPresented['kyrie'] ?? true) ? 'text-gray-700 dark:text-gray-300' : 'text-danger-600 dark:text-danger-400' }}">
                                        {{ ($songsPresented['kyrie'] ?? true) ? '25 pts' : 'Forfeited (-25)' }}
                                    </td>
                                </tr>

                                {{-- 3. Gloria --}}
                                <tr>
                                    <td class="py-2.5 font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                        <span>🎵</span> Gloria
                                    </td>
                                    <td class="py-2.5 pr-3">
                                        <input
                                            type="text"
                                            wire:model="songTitles.gloria"
                                            placeholder="Enter Gloria song title…"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-primary-500 focus:outline-none dark:border-white/10 dark:bg-gray-900 dark:text-white"
                                        />
                                    </td>
                                    <td class="py-2.5 text-center">
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                wire:model.live="songsPresented.gloria"
                                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                            />
                                            <span class="font-bold {{ ($songsPresented['gloria'] ?? true) ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                                                {{ ($songsPresented['gloria'] ?? true) ? '✓ Presented' : '✗ Omitted' }}
                                            </span>
                                        </label>
                                    </td>
                                    <td class="py-2.5 text-right font-mono font-bold {{ ($songsPresented['gloria'] ?? true) ? 'text-gray-700 dark:text-gray-300' : 'text-danger-600 dark:text-danger-400' }}">
                                        {{ ($songsPresented['gloria'] ?? true) ? '25 pts' : 'Forfeited (-25)' }}
                                    </td>
                                </tr>

                                {{-- 4. Thanksgiving --}}
                                <tr>
                                    <td class="py-2.5 font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                        <span>🎵</span> Thanksgiving
                                    </td>
                                    <td class="py-2.5 pr-3">
                                        <input
                                            type="text"
                                            wire:model="songTitles.thanksgiving"
                                            placeholder="Enter Thanksgiving song title…"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-primary-500 focus:outline-none dark:border-white/10 dark:bg-gray-900 dark:text-white"
                                        />
                                    </td>
                                    <td class="py-2.5 text-center">
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                wire:model.live="songsPresented.thanksgiving"
                                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                            />
                                            <span class="font-bold {{ ($songsPresented['thanksgiving'] ?? true) ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                                                {{ ($songsPresented['thanksgiving'] ?? true) ? '✓ Presented' : '✗ Omitted' }}
                                            </span>
                                        </label>
                                    </td>
                                    <td class="py-2.5 text-right font-mono font-bold {{ ($songsPresented['thanksgiving'] ?? true) ? 'text-gray-700 dark:text-gray-300' : 'text-danger-600 dark:text-danger-400' }}">
                                        {{ ($songsPresented['thanksgiving'] ?? true) ? '25 pts' : 'Forfeited (-25)' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if($this->getOmittedSongsCount() > 0)
                        <div class="rounded-xl border border-danger-300 bg-danger-50 p-3 text-xs text-danger-700 dark:border-danger-900/40 dark:bg-danger-950/30 dark:text-danger-400">
                            <strong>⚠️ Omission Forfeiture Active:</strong> {{ $this->getOmittedSongsCount() }} song(s) omitted. Maximum score reduced by <strong>-{{ $this->getOmissionPenalty() }} marks</strong> (Max eligible: {{ 100 - $this->getOmissionPenalty() }} pts).
                        </div>
                    @endif
                </div>
            </div>

        @elseif($this->isSelfComposedCategory())
            {{-- SELF-COMPOSED SONG PRESENTATION INFORMATION --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-3 mb-4 dark:border-white/5">
                    <x-filament::icon icon="heroicon-m-musical-note" class="h-5 w-5 text-primary-500" />
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Self-Composed Song &mdash; Presentation Information</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Original musical composition reflecting the CAM festival theme. Unlimited participants allowed; traditional attire must match the song's language.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 pt-1">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            Title of Song
                        </label>
                        <input
                            type="text"
                            wire:model="itemTitle"
                            placeholder="e.g. Tukopano mwa Pastoral Care"
                            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-gray-500"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            Composer(s)
                        </label>
                        <input
                            type="text"
                            wire:model="composerAuthor"
                            placeholder="e.g. Fr. Dominic Mwanza"
                            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-gray-500"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            Director
                        </label>
                        <input
                            type="text"
                            wire:model="directorProducer"
                            placeholder="e.g. Mrs. Agnes Phiri"
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
                            placeholder="e.g. Lozi / Tonga / English"
                            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-gray-500"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            Number of Participants (on stage)
                        </label>
                        <input
                            type="number"
                            wire:model="participantCount"
                            placeholder="e.g. 24"
                            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-gray-500"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            Duration of Performance
                        </label>
                        <div class="flex items-center h-[42px] px-3.5 rounded-xl border border-gray-200 bg-gray-100 text-xs font-bold text-gray-700 dark:border-white/10 dark:bg-white/5 dark:text-gray-300">
                            ⏱️ 10 Minutes Allocated
                        </div>
                    </div>
                </div>
            </div>

        @elseif($this->isPoetryCategory())
            {{-- POETRY PRESENTATION INFORMATION --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-3 mb-4 dark:border-white/5">
                    <x-filament::icon icon="heroicon-m-sparkles" class="h-5 w-5 text-primary-500" />
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Poetry &mdash; Presentation Information</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Poetic stage performance in line with the festival theme. Maximum 6 participants on stage; 15 minutes allocated.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 pt-1">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            Title of Poem
                        </label>
                        <input
                            type="text"
                            wire:model="itemTitle"
                            placeholder="e.g. Walking Together in Hope"
                            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-gray-500"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            Author / Poet
                        </label>
                        <input
                            type="text"
                            wire:model="composerAuthor"
                            placeholder="e.g. Name of Author/Poet"
                            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-gray-500"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            Director / Trainer
                        </label>
                        <input
                            type="text"
                            wire:model="directorProducer"
                            placeholder="e.g. Name of Director/Trainer"
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
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            Number of Participants (Max 6)
                        </label>
                        <input
                            type="number"
                            max="6"
                            wire:model="participantCount"
                            placeholder="e.g. 6"
                            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-gray-500"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            Duration of Performance
                        </label>
                        <div class="flex items-center h-[42px] px-3.5 rounded-xl border border-gray-200 bg-gray-100 text-xs font-bold text-gray-700 dark:border-white/10 dark:bg-white/5 dark:text-gray-300">
                            ⏱️ 15 Minutes Allocated
                        </div>
                    </div>
                </div>
            </div>

        @elseif($this->isTraditionalDanceCategory())
            {{-- TRADITIONAL DANCE PRESENTATION & 3-DANCE AUDIT --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-3 mb-4 dark:border-white/5">
                    <x-filament::icon icon="heroicon-m-user-group" class="h-5 w-5 text-primary-500" />
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Traditional Dance &mdash; 3 Dances &amp; Compliance Audit</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">3 distinct cultural dances from 3 different provinces of Zambia. Maximum 15 participants on stage; 20 minutes allocated.</p>
                    </div>
                </div>

                <div class="space-y-4 pt-1">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Director / Lead Choreographer</label>
                            <input
                                type="text"
                                wire:model="directorProducer"
                                placeholder="e.g. Mr. Clement Mutale"
                                class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Number of Participants (Max 15)</label>
                            <input
                                type="number"
                                max="15"
                                wire:model="participantCount"
                                placeholder="e.g. 15"
                                class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Duration of Performance</label>
                            <div class="flex items-center h-[38px] px-3.5 rounded-xl border border-gray-200 bg-gray-100 text-xs font-bold text-gray-700 dark:border-white/10 dark:bg-white/5 dark:text-gray-300">
                                ⏱️ 20 Minutes Allocated
                            </div>
                        </div>
                    </div>

                    {{-- 3 Dances Table --}}
                    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-gray-50/50 p-3 dark:border-white/10 dark:bg-white/[0.02]">
                        <div class="mb-2 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            3 Dances from 3 Different Provinces
                        </div>
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-[10px] font-black uppercase tracking-wider text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-white/10">
                                    <th class="pb-2 w-12">No.</th>
                                    <th class="pb-2">Dance Name</th>
                                    <th class="pb-2">Tribe</th>
                                    <th class="pb-2">Province Represented</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200/60 dark:divide-white/5">
                                {{-- Dance 1 --}}
                                <tr>
                                    <td class="py-2 font-bold text-gray-500 dark:text-gray-400">1</td>
                                    <td class="py-2 pr-2">
                                        <input
                                            type="text"
                                            wire:model="traditionalDances.1.dance"
                                            placeholder="e.g. Kayowe / Mooba / Silimba"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-primary-500 focus:outline-none dark:border-white/10 dark:bg-gray-900 dark:text-white"
                                        />
                                    </td>
                                    <td class="py-2 pr-2">
                                        <input
                                            type="text"
                                            wire:model="traditionalDances.1.tribe"
                                            placeholder="e.g. Tonga / Lozi / Luvale"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-primary-500 focus:outline-none dark:border-white/10 dark:bg-gray-900 dark:text-white"
                                        />
                                    </td>
                                    <td class="py-2">
                                        <input
                                            type="text"
                                            wire:model="traditionalDances.1.province"
                                            placeholder="e.g. Southern / Western / North-Western"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-primary-500 focus:outline-none dark:border-white/10 dark:bg-gray-900 dark:text-white"
                                        />
                                    </td>
                                </tr>

                                {{-- Dance 2 --}}
                                <tr>
                                    <td class="py-2 font-bold text-gray-500 dark:text-gray-400">2</td>
                                    <td class="py-2 pr-2">
                                        <input
                                            type="text"
                                            wire:model="traditionalDances.2.dance"
                                            placeholder="e.g. Kalela / Chingande"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-primary-500 focus:outline-none dark:border-white/10 dark:bg-gray-900 dark:text-white"
                                        />
                                    </td>
                                    <td class="py-2 pr-2">
                                        <input
                                            type="text"
                                            wire:model="traditionalDances.2.tribe"
                                            placeholder="e.g. Bemba / Chewa / Tumbuka"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-primary-500 focus:outline-none dark:border-white/10 dark:bg-gray-900 dark:text-white"
                                        />
                                    </td>
                                    <td class="py-2">
                                        <input
                                            type="text"
                                            wire:model="traditionalDances.2.province"
                                            placeholder="e.g. Luapula / Eastern / Northern"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-primary-500 focus:outline-none dark:border-white/10 dark:bg-gray-900 dark:text-white"
                                        />
                                    </td>
                                </tr>

                                {{-- Dance 3 --}}
                                <tr>
                                    <td class="py-2 font-bold text-gray-500 dark:text-gray-400">3</td>
                                    <td class="py-2 pr-2">
                                        <input
                                            type="text"
                                            wire:model="traditionalDances.3.dance"
                                            placeholder="e.g. Gule Wamkulu alternative / Vimbuza"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-primary-500 focus:outline-none dark:border-white/10 dark:bg-gray-900 dark:text-white"
                                        />
                                    </td>
                                    <td class="py-2 pr-2">
                                        <input
                                            type="text"
                                            wire:model="traditionalDances.3.tribe"
                                            placeholder="e.g. Ngoni / Kaonde / Lunda"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-primary-500 focus:outline-none dark:border-white/10 dark:bg-gray-900 dark:text-white"
                                        />
                                    </td>
                                    <td class="py-2">
                                        <input
                                            type="text"
                                            wire:model="traditionalDances.3.province"
                                            placeholder="e.g. Copperbelt / Central / Muchinga"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-primary-500 focus:outline-none dark:border-white/10 dark:bg-gray-900 dark:text-white"
                                        />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Compliance Check & Violation Deductions Card --}}
                    <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm dark:border-white/10 dark:bg-gray-900/60 space-y-3">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-2 dark:border-white/5">
                            <span class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                ⚖️ Compliance Checks &amp; Violation Deductions
                            </span>
                            @if($this->getTraditionalDanceDeductions() > 0)
                                <span class="rounded-full bg-danger-50 px-2.5 py-0.5 text-xs font-black text-danger-700 dark:bg-danger-950/50 dark:text-danger-400">
                                    -{{ $this->getTraditionalDanceDeductions() }} marks penalty
                                </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 text-xs">
                            {{-- Missing Dances --}}
                            <div class="flex items-center justify-between rounded-lg border border-gray-200 p-2.5 dark:border-white/10">
                                <div>
                                    <span class="font-bold text-gray-800 dark:text-gray-200">Missing Dances</span>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400">-10 marks per missing dance</p>
                                </div>
                                <select
                                    wire:model.live="missingDancesCount"
                                    class="rounded-lg border border-gray-300 bg-white px-2 py-1 text-xs font-bold text-gray-900 dark:border-white/10 dark:bg-gray-900 dark:text-white"
                                >
                                    <option value="0">0 Missing (All 3 Presented)</option>
                                    <option value="1">1 Missing (-10 marks)</option>
                                    <option value="2">2 Missing (-20 marks)</option>
                                    <option value="3">3 Missing (-30 marks)</option>
                                </select>
                            </div>

                            {{-- Repeated Provinces --}}
                            <div class="flex items-center justify-between rounded-lg border border-gray-200 p-2.5 dark:border-white/10">
                                <div>
                                    <span class="font-bold text-gray-800 dark:text-gray-200">Repeated Provinces</span>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400">-5 marks for each repeated province</p>
                                </div>
                                <select
                                    wire:model.live="repeatedProvincesCount"
                                    class="rounded-lg border border-gray-300 bg-white px-2 py-1 text-xs font-bold text-gray-900 dark:border-white/10 dark:bg-gray-900 dark:text-white"
                                >
                                    <option value="0">0 (3 Unique Provinces)</option>
                                    <option value="1">1 Repeated (-5 marks)</option>
                                    <option value="2">2 Repeated (-10 marks)</option>
                                </select>
                            </div>

                            {{-- Failed to Identify --}}
                            <div class="flex items-center gap-2 rounded-lg border border-gray-200 p-2.5 dark:border-white/10">
                                <input
                                    type="checkbox"
                                    id="fail_id_chk"
                                    wire:model.live="failedIdentifyDances"
                                    class="h-4 w-4 rounded border-gray-300 text-danger-600 focus:ring-danger-500"
                                />
                                <label for="fail_id_chk" class="cursor-pointer text-xs font-medium text-gray-700 dark:text-gray-300">
                                    <strong class="text-danger-600 dark:text-danger-400">Failed to Identify</strong> dances and provinces represented (-5 marks)
                                </label>
                            </div>

                            {{-- Masquerade / Vinyau --}}
                            <div class="flex items-center gap-2 rounded-lg border border-danger-200 bg-danger-50/50 p-2.5 dark:border-danger-900/40 dark:bg-danger-950/20">
                                <input
                                    type="checkbox"
                                    id="vinyau_chk"
                                    wire:model.live="usedMasqueradeVinyau"
                                    class="h-4 w-4 rounded border-danger-400 text-danger-600 focus:ring-danger-500"
                                />
                                <label for="vinyau_chk" class="cursor-pointer text-xs font-bold text-danger-700 dark:text-danger-400">
                                    🚫 Use of Masquerade (Vinyau) &mdash; Immediate Disqualification
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($this->isDramaCategory())
            {{-- DRAMA PRESENTATION & TIME MANAGEMENT AUDIT --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-3 mb-4 dark:border-white/5">
                    <x-filament::icon icon="heroicon-m-sparkles" class="h-5 w-5 text-primary-500" />
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Drama Adjudication Form &mdash; Production Details</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Stage dramatic production based on the CAM festival theme. 45 minutes allocated.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 pt-1">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Title of Play
                        </label>
                        <input
                            type="text"
                            wire:model="itemTitle"
                            placeholder="e.g. The Road to Emmaus / Walking in Pastoral Light"
                            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Playwright / Scriptwriter
                        </label>
                        <input
                            type="text"
                            wire:model="composerAuthor"
                            placeholder="e.g. Scriptwriter name"
                            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Director / Producer
                        </label>
                        <input
                            type="text"
                            wire:model="directorProducer"
                            placeholder="e.g. Stage Director name"
                            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Language(s) Used
                        </label>
                        <input
                            type="text"
                            wire:model="languageUsed"
                            placeholder="e.g. English, Lozi, Tonga, Bemba"
                            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Number of Cast / Participants
                        </label>
                        <input
                            type="number"
                            wire:model="participantCount"
                            placeholder="e.g. 12"
                            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 dark:border-white/10 dark:bg-white/5 dark:text-white"
                        />
                    </div>
                </div>

                {{-- Time Management & Timekeeper Penalties --}}
                <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50/70 p-3.5 dark:border-white/10 dark:bg-white/[0.02]">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-200/80 pb-2 dark:border-white/5">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                ⏱️ Time Management &amp; Timekeeper Rule
                            </span>
                            <span class="rounded-full bg-primary-100 px-2 py-0.5 text-[10px] font-black text-primary-800 dark:bg-primary-950/60 dark:text-primary-300">
                                45 Minutes Allocated
                            </span>
                        </div>
                        @if($timePenaltyDeduction > 0)
                            <span class="rounded-full bg-danger-50 px-2.5 py-0.5 text-xs font-black text-danger-700 dark:bg-danger-950/50 dark:text-danger-400">
                                -{{ $timePenaltyDeduction }} marks time penalty
                            </span>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 text-xs">
                        <div>
                            <label class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Timekeeper Overtime Assessment</label>
                            <select
                                wire:model.live="timePenaltyDeduction"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-bold text-gray-900 shadow-sm focus:border-primary-500 focus:outline-none dark:border-white/10 dark:bg-gray-900 dark:text-white"
                            >
                                <option value="0">✅ Within Allocated Time (0 penalty)</option>
                                <option value="2">⚠️ Up to 1 minute over (-2 marks)</option>
                                <option value="5">⚠️ 1 to 3 minutes over (-5 marks)</option>
                                <option value="10">⚠️ 3 to 5 minutes over (-10 marks)</option>
                                <option value="15">🚫 More than 5 minutes over (-15 marks)</option>
                            </select>
                        </div>
                        <div class="flex items-center text-[11px] text-gray-500 dark:text-gray-400">
                            Penalties are recorded independently by the designated Timekeeper and deducted from the final adjudicated score.
                        </div>
                    </div>
                </div>
            </div>

        @else
            {{-- STANDARD PRESENTATION INFORMATION (Other Categories) --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-3 mb-4 dark:border-white/5">
                    <x-filament::icon icon="heroicon-m-document-text" class="h-5 w-5 text-primary-500" />
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Presentation Information</h3>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 pt-1">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            Song / Item Title
                        </label>
                        <input
                            type="text"
                            wire:model="itemTitle"
                            placeholder="e.g. Presentation title"
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
                            placeholder="e.g. Director name"
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
            </div>
        @endif

        {{-- Rubric Criteria List --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4 dark:border-white/5">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-m-clipboard-document-list" class="h-5 w-5 text-primary-500" />
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Official Assessment Rubric</h3>
                </div>
                <x-filament::badge color="primary">
                    {{ count($activeCategory->judging_criteria ?? []) }} Criteria &bull; Max {{ $activeCategory->max_raw_score ?? 100 }} pts
                </x-filament::badge>
            </div>

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
        </div>

        {{-- Constructive Comments & Disqualification --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="flex items-center gap-2 border-b border-gray-100 pb-3 mb-4 dark:border-white/5">
                <x-filament::icon icon="heroicon-m-chat-bubble-bottom-center-text" class="h-5 w-5 text-primary-500" />
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Adjudicator Comments &amp; Feedback</h3>
            </div>

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
        </div>

        {{-- Floating Sticky Save Bar --}}
        <div class="ws-sticky-bar rounded-2xl border border-gray-200 bg-white/95 p-4 shadow-2xl backdrop-blur-md dark:border-white/10 dark:bg-gray-900/95">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-baseline gap-2">
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Marks Awarded:
                        </span>
                        <span class="font-mono text-xl font-bold text-gray-800 dark:text-gray-200">
                            {{ $this->calculateRubricSubtotal() }}
                        </span>
                    </div>

                    @if($this->isChoirCategory() && $this->getOmittedSongsCount() > 0)
                        <div class="flex items-baseline gap-1.5 rounded-lg bg-danger-50 px-2.5 py-1 text-danger-700 dark:bg-danger-950/40 dark:text-danger-400">
                            <span class="text-xs font-bold uppercase">Less Omission:</span>
                            <span class="font-mono text-sm font-black">-{{ $this->getOmissionPenalty() }}</span>
                        </div>
                    @elseif($this->isTraditionalDanceCategory() && $this->getTraditionalDanceDeductions() > 0)
                        <div class="flex items-baseline gap-1.5 rounded-lg bg-danger-50 px-2.5 py-1 text-danger-700 dark:bg-danger-950/40 dark:text-danger-400">
                            <span class="text-xs font-bold uppercase">Less Deductions:</span>
                            <span class="font-mono text-sm font-black">-{{ $this->getTraditionalDanceDeductions() }}</span>
                        </div>
                    @endif

                    @if($timePenaltyDeduction > 0)
                        <div class="flex items-baseline gap-1.5 rounded-lg bg-danger-50 px-2.5 py-1 text-danger-700 dark:bg-danger-950/40 dark:text-danger-400">
                            <span class="text-xs font-bold uppercase">Time Penalty:</span>
                            <span class="font-mono text-sm font-black">-{{ $timePenaltyDeduction }}</span>
                        </div>
                    @endif

                    <div class="flex items-baseline gap-2 border-l border-gray-200 pl-4 dark:border-white/10">
                        <span class="text-xs font-extrabold text-primary-700 dark:text-primary-400 uppercase tracking-wider">
                            Final Score:
                        </span>
                        <span class="font-mono text-3xl font-black text-primary-600 dark:text-primary-400">
                            {{ $this->calculateTotal() }}
                        </span>
                        <span class="text-xs font-semibold text-gray-400 dark:text-gray-500">
                            / {{ $activeCategory->max_raw_score ?? 100 }} pts
                        </span>
                    </div>
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
