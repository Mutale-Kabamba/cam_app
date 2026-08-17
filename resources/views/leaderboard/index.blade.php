@extends('layouts.app')

@section('title', 'Festival Championship Leaderboard & Results')

@section('hero')
<div class="page-hero-eyebrow">🏆 Official Standings</div>
<div style="display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
    <div>
        <h2>Festival Championship Leaderboard</h2>
        <p>Real-time aggregated points and published results across all competition categories.</p>
    </div>
    <a href="{{ route('leaderboard.big_screen', ['category_id' => $selectedCategory]) }}" class="btn btn-primary" target="_blank" style="flex-shrink: 0;">
        📺 Big Screen Projection
    </a>
</div>
@endsection

@section('content')

{{-- Category Tabs --}}
<div class="glass-card" style="margin-bottom: 1.5rem; padding: 1rem 1.25rem;">
    <div style="display: flex; gap: 0.45rem; overflow-x: auto; padding-bottom: 0.1rem; flex-wrap: wrap;">
        <a href="{{ route('leaderboard.index') }}"
           class="btn btn-sm {{ !$selectedCategory ? 'btn-primary' : 'btn-secondary' }}"
           style="white-space: nowrap;">
            🎖️ Overall Championship
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('leaderboard.index', ['category_id' => $cat->id]) }}"
               class="btn btn-sm {{ $selectedCategory == $cat->id ? 'btn-primary' : 'btn-secondary' }}"
               style="white-space: nowrap;">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>
</div>

@if($selectedCategory && $categoryResults)
    @php $activeCat = $categories->firstWhere('id', $selectedCategory); @endphp

    {{-- Category Results --}}
    <div class="glass-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-card); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h3 style="font-family: var(--font-display); font-size: 1.3rem; font-weight: 800; color: #fff;">
                    {{ $activeCat?->name }} — Official Results
                </h3>
                <p style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.2rem;">
                    Max marks: <strong style="color: #fff;">{{ $activeCat?->max_raw_score }} pts</strong>
                    &bull; Stage allocation: <strong style="color: #fff;">{{ $activeCat?->allocated_minutes }} mins</strong>
                </p>
            </div>
            <span class="badge badge-completed" style="padding: 0.4rem 0.85rem;">✓ Published</span>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 90px;">Rank</th>
                        <th>Parish</th>
                        <th>Deanery</th>
                        <th style="text-align: center;">Adj. Average</th>
                        <th style="text-align: center; color: #f87171;">Time Penalty</th>
                        <th style="text-align: right;">Final Score</th>
                        <th style="text-align: right; color: #10b981;">Points</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categoryResults as $index => $res)
                        <tr class="{{ $index === 0 ? 'rank-1' : ($index === 1 ? 'rank-2' : ($index === 2 ? 'rank-3' : '')) }}">
                            <td>
                                @if($res->rank === 1 || $index === 0)
                                    <span class="badge badge-gold">🥇 1st</span>
                                @elseif($res->rank === 2 || $index === 1)
                                    <span class="badge badge-silver">🥈 2nd</span>
                                @elseif($res->rank === 3 || $index === 2)
                                    <span class="badge badge-bronze">🥉 3rd</span>
                                @else
                                    <strong style="color: var(--text-muted); font-size: 1rem; padding-left: 0.35rem;">#{{ $res->rank ?? ($index + 1) }}</strong>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 700; font-size: 0.95rem; color: #fff;">{{ $res->parish->name }}</div>
                                <div style="font-size: 0.72rem; color: #f59e0b; margin-top: 0.1rem;">{{ $res->parish->code }}</div>
                            </td>
                            <td>
                                <span style="color: var(--text-sub); font-size: 0.85rem;">{{ $res->parish->deanery }}</span>
                            </td>
                            <td style="text-align: center;">
                                <strong style="color: #38bdf8; font-family: var(--font-display); font-size: 1rem;">{{ $res->adjudicators_average }}</strong>
                            </td>
                            <td style="text-align: center;">
                                <span style="color: {{ $res->time_penalty > 0 ? '#f87171' : 'var(--text-muted)' }}; font-weight: 700;">
                                    {{ $res->time_penalty > 0 ? '−' . $res->time_penalty . ' pts' : '0' }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <span style="font-family: var(--font-display); font-size: 1.25rem; font-weight: 900; color: #f59e0b;">
                                    {{ $res->final_score }}
                                </span>
                                <span style="font-size: 0.72rem; color: var(--text-muted);">/ {{ $activeCat?->max_raw_score }}</span>
                            </td>
                            <td style="text-align: right;">
                                <strong style="color: #10b981; font-family: var(--font-display); font-size: 1rem;">
                                    +{{ $res->championship_points }} pts
                                </strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-icon">⚖️</div>
                                    <h3>Results Pending Finalization</h3>
                                    <p>Official results for {{ $activeCat?->name }} have not been published yet.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@else

    {{-- Overall Championship --}}
    <div class="glass-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-card); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
            <h3 style="font-family: var(--font-display); font-size: 1.3rem; font-weight: 800; color: #fff;">
                🎖️ Overall Parish Championship Standings
            </h3>
            <span style="font-size: 0.75rem; color: var(--text-muted);">Aggregated across all categories</span>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 90px;">Rank</th>
                        <th>Parish</th>
                        <th>Deanery</th>
                        <th style="text-align: center;">Categories</th>
                        <th style="text-align: center;">Avg Score</th>
                        <th style="text-align: right; color: #f59e0b;">Championship Pts</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($standings as $index => $item)
                        <tr class="{{ $index === 0 ? 'rank-1' : ($index === 1 ? 'rank-2' : ($index === 2 ? 'rank-3' : '')) }}">
                            <td>
                                @if($index === 0)
                                    <span class="badge badge-gold">🥇 1st</span>
                                @elseif($index === 1)
                                    <span class="badge badge-silver">🥈 2nd</span>
                                @elseif($index === 2)
                                    <span class="badge badge-bronze">🥉 3rd</span>
                                @else
                                    <strong style="color: var(--text-muted); font-size: 1rem; padding-left: 0.35rem;">#{{ $index + 1 }}</strong>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 700; font-size: 0.95rem; color: #fff;">{{ $item['parish']->name }}</div>
                                <div style="font-size: 0.72rem; color: #f59e0b; margin-top: 0.1rem;">{{ $item['parish']->code }}</div>
                            </td>
                            <td>
                                <span style="color: var(--text-sub); font-size: 0.85rem;">{{ $item['parish']->deanery ?? 'Livingstone' }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span style="font-weight: 700; color: var(--text-sub);">{{ $item['categories_participated'] }}</span>
                                <span style="color: var(--text-muted); font-size: 0.78rem;">/ {{ $categories->count() }}</span>
                            </td>
                            <td style="text-align: center;">
                                <strong style="color: #38bdf8; font-family: var(--font-display);">{{ $item['average_score'] }}%</strong>
                            </td>
                            <td style="text-align: right;">
                                <span style="font-family: var(--font-display); font-size: 1.3rem; font-weight: 900; color: #f59e0b;">
                                    {{ $item['total_points'] }}
                                    <span style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted);">pts</span>
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon">🏆</div>
                                    <h3>No Standings Recorded Yet</h3>
                                    <p>Parish championship standings will appear here automatically once official results are consolidated by the admin team.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

@endsection
