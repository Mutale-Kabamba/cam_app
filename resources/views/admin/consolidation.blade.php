@extends('layouts.app')

@section('title', 'Admin - 3-Judge Consolidation Hub')

@section('content')
<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.5rem; color: #8b5cf6; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">
            <span><a href="{{ route('admin.index') }}" style="color: #94a3b8; text-decoration: none;">⚙️ Admin</a> &bull; Scoring Aggregator</span>
        </div>
        <h2 style="font-family: var(--font-display); font-size: 2.2rem; font-weight: 800; margin-bottom: 0.25rem; letter-spacing: -0.02em;">
            📊 3-Judge Adjudication Consolidation Hub
        </h2>
        <p style="color: var(--text-muted);">Review scorecards from Judge 1, 2, and 3, apply timekeeper penalty marks, and publish finalized results.</p>
    </div>
    @if($activeCategory)
        <div>
            <form method="POST" action="{{ route('admin.consolidation.finalize') }}" onsubmit="return confirm('Publish finalized standings for {{ $activeCategory->name }} to the Live Leaderboard & Big Screen?')">
                @csrf
                <input type="hidden" name="category_id" value="{{ $activeCategory->id }}">
                <button type="submit" class="btn btn-primary" style="font-weight: 800; font-size: 1rem; padding: 0.75rem 1.5rem;">
                    🚀 Finalize & Publish to Leaderboard
                </button>
            </form>
        </div>
    @endif
</div>

<!-- Category Selector Pills -->
<div class="glass-card" style="margin-bottom: 1.5rem; padding: 1rem;">
    <div style="display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.25rem;">
        @foreach($categories as $cat)
            <a href="{{ route('admin.consolidation', ['category_id' => $cat->id]) }}" 
               class="btn btn-sm {{ $selectedCategoryId == $cat->id ? 'btn-primary' : 'btn-secondary' }}" 
               style="white-space: nowrap; font-weight: 700;">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>
</div>

@if($activeCategory)
    <div class="glass-card" style="margin-bottom: 1.5rem; border-color: rgba(245, 158, 11, 0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span class="badge badge-gold">Active Category</span>
                <h3 style="font-family: var(--font-display); font-size: 1.4rem; font-weight: 800; color: #fff; margin-top: 0.35rem;">
                    {{ $activeCategory->name }}
                </h3>
                <p style="color: var(--text-muted); font-size: 0.85rem;">
                    Max Possible Score: <strong>{{ $activeCategory->max_raw_score }} pts</strong> &bull; Stage Allocation: <strong>{{ $activeCategory->allocated_minutes }} mins</strong>
                </p>
            </div>
            <div style="display: flex; gap: 1.5rem; text-align: right;">
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Judge Scoring Mode</div>
                    <strong style="color: #38bdf8; font-size: 1.1rem;">3 Judges Average</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- 3-Judge Consolidation Matrix -->
    <div class="glass-card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;">Code</th>
                        <th>Parish</th>
                        <th style="text-align: center; color: #c4b5fd;">Judge 1</th>
                        <th style="text-align: center; color: #c4b5fd;">Judge 2</th>
                        <th style="text-align: center; color: #c4b5fd;">Judge 3</th>
                        <th style="text-align: center; color: #38bdf8;">3-Judge Avg</th>
                        <th style="text-align: center; color: #f87171; width: 140px;">Time Penalty</th>
                        <th style="text-align: right; color: #f59e0b; width: 110px;">Final Score</th>
                        <th style="text-align: center; width: 90px;">Rank</th>
                        <th style="text-align: center; width: 100px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($matrix as $row)
                        <tr>
                            <td>
                                <strong style="color: #f59e0b; font-family: var(--font-display);">{{ $row['parish']->code }}</strong>
                            </td>
                            <td>
                                <strong style="color: #fff;">{{ $row['parish']->name }}</strong>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $row['parish']->deanery }}</div>
                            </td>
                            
                            <!-- Judge 1 -->
                            <td style="text-align: center;">
                                @if($row['judge_scores']['Judge 1'] !== null)
                                    <span style="font-weight: 700; color: #c4b5fd;">{{ $row['judge_scores']['Judge 1'] }}</span>
                                @else
                                    <span style="color: rgba(255,255,255,0.2);">-</span>
                                @endif
                            </td>

                            <!-- Judge 2 -->
                            <td style="text-align: center;">
                                @if($row['judge_scores']['Judge 2'] !== null)
                                    <span style="font-weight: 700; color: #c4b5fd;">{{ $row['judge_scores']['Judge 2'] }}</span>
                                @else
                                    <span style="color: rgba(255,255,255,0.2);">-</span>
                                @endif
                            </td>

                            <!-- Judge 3 -->
                            <td style="text-align: center;">
                                @if($row['judge_scores']['Judge 3'] !== null)
                                    <span style="font-weight: 700; color: #c4b5fd;">{{ $row['judge_scores']['Judge 3'] }}</span>
                                @else
                                    <span style="color: rgba(255,255,255,0.2);">-</span>
                                @endif
                            </td>

                            <!-- 3-Judge Average -->
                            <td style="text-align: center;">
                                <strong style="color: #38bdf8; font-size: 1.05rem;">
                                    {{ $row['average'] }}
                                </strong>
                                <div style="font-size: 0.7rem; color: var(--text-muted);">({{ $row['submitted_count'] }}/3 judges)</div>
                            </td>

                            <!-- Time Penalty Input -->
                            <td style="text-align: center;">
                                <form method="POST" action="{{ route('admin.consolidation.time_penalty') }}" style="display: flex; align-items: center; justify-content: center; gap: 0.3rem;">
                                    @csrf
                                    <input type="hidden" name="category_id" value="{{ $activeCategory->id }}">
                                    <input type="hidden" name="parish_id" value="{{ $row['parish']->id }}">
                                    <input type="number" name="time_penalty" value="{{ $row['time_penalty'] }}" step="1" min="0" max="50" style="width: 55px; padding: 0.25rem; font-size: 0.85rem; text-align: center; border-color: rgba(239,68,68,0.4);" title="-2, -5, -10, -15 mark deduction">
                                    <button type="submit" class="btn btn-sm btn-secondary" style="padding: 0.25rem 0.45rem;" title="Save Penalty">💾</button>
                                </form>
                            </td>

                            <!-- Final Score -->
                            <td style="text-align: right;">
                                <span style="font-family: var(--font-display); font-size: 1.25rem; font-weight: 800; color: #f59e0b;">
                                    {{ $row['final_score'] }}
                                </span>
                            </td>

                            <!-- Rank -->
                            <td style="text-align: center;">
                                @if($row['is_finalized'] && $row['rank'])
                                    @if($row['rank'] == 1)
                                        <span class="badge badge-gold">🥇 1st</span>
                                    @elseif($row['rank'] == 2)
                                        <span class="badge badge-silver">🥈 2nd</span>
                                    @elseif($row['rank'] == 3)
                                        <span class="badge badge-bronze">🥉 3rd</span>
                                    @else
                                        <strong style="color: var(--text-muted);">#{{ $row['rank'] }}</strong>
                                    @endif
                                @else
                                    <span style="color: rgba(255,255,255,0.2);">-</span>
                                @endif
                            </td>

                            <!-- Finalized Status -->
                            <td style="text-align: center;">
                                @if($row['is_finalized'])
                                    <span class="badge badge-completed">Published</span>
                                @else
                                    <span class="badge badge-scheduled">Draft</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
