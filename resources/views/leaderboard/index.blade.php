@extends('layouts.app')

@section('title', 'Festival Championship Leaderboard & Results')

@section('content')
<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.5rem; color: #f59e0b; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">
            <span>🏆 Catholic Diocese of Livingstone &bull; Official Standings</span>
        </div>
        <h2 style="font-family: var(--font-display); font-size: 2.2rem; font-weight: 800; margin-bottom: 0.25rem; letter-spacing: -0.02em;">
            Festival Championship Leaderboard
        </h2>
        <p style="color: var(--text-muted);">Real-time aggregated points and published results across all competition categories.</p>
    </div>
    <div>
        <a href="{{ route('leaderboard.big_screen', ['category_id' => $selectedCategory]) }}" class="btn btn-primary" target="_blank">
            📺 Launch Big Screen Projection
        </a>
    </div>
</div>

<!-- Category Tabs -->
<div class="glass-card" style="margin-bottom: 1.5rem; padding: 1rem;">
    <div style="display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.25rem;">
        <a href="{{ route('leaderboard.index') }}" 
           class="btn btn-sm {{ !$selectedCategory ? 'btn-primary' : 'btn-secondary' }}" 
           style="white-space: nowrap; font-weight: 700;">
            🏆 Overall Championship
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('leaderboard.index', ['category_id' => $cat->id]) }}" 
               class="btn btn-sm {{ $selectedCategory == $cat->id ? 'btn-primary' : 'btn-secondary' }}" 
               style="white-space: nowrap; font-weight: 700;">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>
</div>

@if($selectedCategory && $categoryResults)
    @php
        $activeCat = $categories->firstWhere('id', $selectedCategory);
    @endphp
    <!-- Category Specific Results -->
    <div class="glass-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-card); padding-bottom: 0.75rem;">
            <div>
                <h3 style="font-family: var(--font-display); font-size: 1.4rem; font-weight: 800; color: #fff;">
                    {{ $activeCat?->name }} Official Results
                </h3>
                <p style="color: var(--text-muted); font-size: 0.85rem;">
                    Max Marks: <strong>{{ $activeCat?->max_raw_score }} pts</strong> &bull; Stage Allocation: <strong>{{ $activeCat?->allocated_minutes }} mins</strong>
                </p>
            </div>
            <div>
                <span class="badge badge-completed">Published Results</span>
            </div>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px;">Rank</th>
                        <th>Parish</th>
                        <th>Deanery</th>
                        <th style="text-align: center;">Adjudication Avg</th>
                        <th style="text-align: center; color: #f87171;">Time Penalty</th>
                        <th style="text-align: right; color: #f59e0b;">Final Score</th>
                        <th style="text-align: right;">Points Awarded</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categoryResults as $index => $res)
                        <tr style="{{ $index === 0 ? 'background: rgba(245, 158, 11, 0.08);' : '' }}">
                            <td>
                                @if($res->rank === 1 || $index === 0)
                                    <span class="badge badge-gold">🥇 1st</span>
                                @elseif($res->rank === 2 || $index === 1)
                                    <span class="badge badge-silver">🥈 2nd</span>
                                @elseif($res->rank === 3 || $index === 2)
                                    <span class="badge badge-bronze">🥉 3rd</span>
                                @else
                                    <strong style="color: var(--text-muted); font-size: 1rem; padding-left: 0.5rem;">#{{ $res->rank ?? ($index + 1) }}</strong>
                                @endif
                            </td>
                            <td>
                                <strong style="color: #fff; font-size: 1.05rem;">{{ $res->parish->name }}</strong>
                                <span style="color: #f59e0b; font-size: 0.8rem; margin-left: 0.4rem;">({{ $res->parish->code }})</span>
                            </td>
                            <td>
                                <span style="color: #cbd5e1;">{{ $res->parish->deanery }}</span>
                            </td>
                            <td style="text-align: center;">
                                <strong style="color: #38bdf8;">{{ $res->adjudicators_average }}</strong>
                            </td>
                            <td style="text-align: center; color: #f87171;">
                                {{ $res->time_penalty > 0 ? '-' . $res->time_penalty . ' pts' : '0' }}
                            </td>
                            <td style="text-align: right;">
                                <span style="font-family: var(--font-display); font-size: 1.3rem; font-weight: 800; color: #f59e0b;">
                                    {{ $res->final_score }} <span style="font-size: 0.8rem; color: var(--text-muted);">/ {{ $activeCat?->max_raw_score }}</span>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <strong style="color: #10b981; font-size: 1.1rem;">
                                    +{{ $res->championship_points }} pts
                                </strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                Official results for this category have not been finalized yet. Check back soon!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@else
    <!-- Overall Championship Standings -->
    <div class="glass-card">
        <h3 style="font-family: var(--font-display); font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem; color: #fff;">
            🎖️ Overall Deanery & Parish Championship Standings
        </h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px;">Rank</th>
                        <th>Parish</th>
                        <th>Deanery</th>
                        <th>Categories Entered</th>
                        <th>Avg Score</th>
                        <th style="text-align: right;">Championship Points</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($standings as $index => $item)
                        <tr style="{{ $index === 0 ? 'background: rgba(245, 158, 11, 0.08);' : '' }}">
                            <td>
                                @if($index === 0)
                                    <span class="badge badge-gold">🥇 1st</span>
                                @elseif($index === 1)
                                    <span class="badge badge-silver">🥈 2nd</span>
                                @elseif($index === 2)
                                    <span class="badge badge-bronze">🥉 3rd</span>
                                @else
                                    <strong style="color: var(--text-muted); font-size: 1rem; padding-left: 0.5rem;">#{{ $index + 1 }}</strong>
                                @endif
                            </td>
                            <td>
                                <strong style="color: #fff; font-size: 1.05rem;">{{ $item['parish']->name }}</strong>
                                <span style="color: #f59e0b; font-size: 0.8rem; margin-left: 0.4rem;">({{ $item['parish']->code }})</span>
                            </td>
                            <td>
                                <span style="color: #cbd5e1;">{{ $item['parish']->deanery ?? 'Livingstone' }}</span>
                            </td>
                            <td>
                                <span>{{ $item['categories_participated'] }} of 8</span>
                            </td>
                            <td>
                                <strong style="color: #38bdf8;">{{ $item['average_score'] }}%</strong>
                            </td>
                            <td style="text-align: right;">
                                <span style="font-family: var(--font-display); font-size: 1.3rem; font-weight: 800; color: #f59e0b;">
                                    {{ $item['total_points'] }} pts
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                No parish points recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
