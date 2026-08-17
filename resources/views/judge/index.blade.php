@extends('layouts.app')

@section('title', 'Judge Workstation - ' . $activeJudge)

@section('content')
<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.5rem; color: #a78bfa; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">
            <span>⚖️ Official Adjudication Workstation</span>
        </div>
        <h2 style="font-family: var(--font-display); font-size: 2.2rem; font-weight: 800; margin-bottom: 0.25rem; letter-spacing: -0.02em;">
            Adjudication Portal: <span style="color: #f59e0b;">{{ $activeJudge }}</span>
        </h2>
        <p style="color: var(--text-muted);">Select category and parish to record marks, qualitative remarks, and evaluate performances against official Diocesan rubrics.</p>
    </div>

    <!-- Judge Switcher / Identity Badge -->
    @if($isAdmin ?? false)
        <div style="display: flex; align-items: center; gap: 0.5rem; background: rgba(15, 23, 42, 0.8); padding: 0.4rem; border-radius: 12px; border: 1px solid var(--border-card);">
            <span style="font-size: 0.75rem; font-weight: 700; color: #93c5fd; text-transform: uppercase; padding: 0 0.5rem;">Admin Switch:</span>
            @foreach($judges as $j)
                <a href="{{ route('judge.index', ['judge' => $j, 'category_id' => $selectedCategoryId]) }}" 
                   class="btn btn-sm {{ $activeJudge == $j ? 'btn-primary' : 'btn-secondary' }}"
                   style="font-weight: 700;">
                    {{ $j }}
                </a>
            @endforeach
        </div>
    @else
        <div style="display: flex; align-items: center; gap: 0.5rem; background: rgba(245, 158, 11, 0.1); padding: 0.5rem 1rem; border-radius: 12px; border: 1.5px solid rgba(245, 158, 11, 0.4);">
            <span style="font-size: 0.85rem; font-weight: 800; color: #f59e0b;">
                ⚖️ Official Adjudicator: {{ $activeJudge }}
            </span>
        </div>
    @endif
</div>

<!-- Category Selector Pills -->
<div class="glass-card" style="margin-bottom: 1.5rem; padding: 1rem;">
    <div style="display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.25rem;">
        @foreach($categories as $cat)
            <a href="{{ route('judge.index', ['judge' => $activeJudge, 'category_id' => $cat->id]) }}" 
               class="btn btn-sm {{ $selectedCategoryId == $cat->id ? 'btn-primary' : 'btn-secondary' }}" 
               style="white-space: nowrap; font-weight: 700;">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>
</div>

@if($activeCategory)
    <!-- Category Overview Banner -->
    <div class="glass-card" style="margin-bottom: 1.5rem; border-color: rgba(139, 92, 246, 0.3); background: linear-gradient(135deg, rgba(139, 92, 246, 0.08), rgba(23, 31, 50, 0.8));">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span class="badge" style="background: rgba(139, 92, 246, 0.2); color: #c4b5fd; border: 1px solid rgba(139, 92, 246, 0.4);">
                    {{ $activeJudge }} Active Workspace
                </span>
                <h3 style="font-family: var(--font-display); font-size: 1.5rem; font-weight: 800; color: #fff; margin-top: 0.35rem;">
                    {{ $activeCategory->name }}
                </h3>
                <p style="color: var(--text-muted); font-size: 0.85rem;">
                    Max Marks: <strong>{{ $activeCategory->max_raw_score }} pts</strong> &bull; Stage Allocation: <strong>{{ $activeCategory->allocated_minutes > 0 ? $activeCategory->allocated_minutes . ' mins' : 'Quiz' }}</strong>
                </p>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem;">Your Scoring Progress</div>
                <div style="font-family: var(--font-display); font-size: 1.6rem; font-weight: 800; color: #f59e0b;">
                    {{ $scoredCount }} <span style="font-size: 1rem; color: var(--text-muted);">/ {{ $totalParishes }} Parishes Scored</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Parishes Adjudication List -->
    <div class="glass-card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 70px;">Code</th>
                        <th>Parish Name</th>
                        <th>Deanery</th>
                        <th style="text-align: center; width: 180px;">Your Assessment Status</th>
                        <th style="text-align: right; width: 140px;">Your Score</th>
                        <th style="text-align: right; width: 180px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parishStatusList as $item)
                        <tr>
                            <td>
                                <strong style="color: #f59e0b; font-family: var(--font-display); font-size: 1rem;">{{ $item['parish']->code }}</strong>
                            </td>
                            <td>
                                <div style="font-weight: 700; font-size: 1.05rem; color: #fff;">{{ $item['parish']->name }}</div>
                            </td>
                            <td>
                                <span style="color: #cbd5e1;">{{ $item['parish']->deanery }}</span>
                            </td>
                            <td style="text-align: center;">
                                @if($item['is_scored'])
                                    <span class="badge badge-completed">✓ Scored by {{ $activeJudge }}</span>
                                @else
                                    <span class="badge badge-scheduled">⏳ Pending Assessment</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                @if($item['is_scored'])
                                    <span style="font-family: var(--font-display); font-size: 1.3rem; font-weight: 800; color: #f59e0b;">
                                        {{ $item['raw_score'] }} <span style="font-size: 0.8rem; color: var(--text-muted);">/ {{ $activeCategory->max_raw_score }}</span>
                                    </span>
                                @else
                                    <span style="color: rgba(255,255,255,0.2);">-</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('judge.scoresheet', ['category' => $activeCategory->id, 'parish' => $item['parish']->id, 'judge' => $activeJudge]) }}" 
                                   class="btn btn-sm {{ $item['is_scored'] ? 'btn-secondary' : 'btn-primary' }}"
                                   style="font-weight: 700;">
                                    @if($item['is_scored'])
                                        ✏️ Edit Marks
                                    @else
                                        📝 Open Score Sheet
                                    @endif
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3.5rem 1rem; color: var(--text-muted);">
                                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">⛪</div>
                                <div style="font-size: 1.1rem; font-weight: 700; color: #cbd5e1; margin-bottom: 0.25rem;">No Parishes Found</div>
                                <p style="font-size: 0.9rem; max-width: 500px; margin: 0 auto; color: var(--text-muted);">
                                    No parishes are registered or scheduled for this category yet.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="glass-card" style="text-align: center; padding: 4rem 1rem;">
        <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">⚖️</div>
        <div style="font-size: 1.25rem; font-weight: 700; color: #fff; margin-bottom: 0.5rem;">No Competition Categories Configured</div>
        <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto;">
            Please create competition categories and judging criteria in the Admin panel to begin adjudication.
        </p>
    </div>
@endif
@endsection
