@extends('layouts.app')

@section('title', 'Official Score Sheet - ' . $parish->name . ' (' . $activeJudge . ')')

@section('content')
<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 700; color: #a78bfa; margin-bottom: 0.25rem;">
            <a href="{{ route('judge.index', ['judge' => $activeJudge, 'category_id' => $category->id]) }}" style="color: #94a3b8; text-decoration: none;">&larr; Back to {{ $activeJudge }} Workstation</a>
            <span>&bull;</span>
            <span>Catholic Diocese of Livingstone Youth Ministry</span>
        </div>
        <h2 style="font-family: var(--font-display); font-size: 2.2rem; font-weight: 800; color: #fff; letter-spacing: -0.02em;">
            {{ strtoupper($category->name) }} ADJUDICATION FORM
        </h2>
        <p style="color: #f59e0b; font-weight: 600; font-size: 0.9rem;">
            THEME: “A YEAR OF WORKING TOGETHER IN DEEPER PASTORAL CARE”
        </p>
    </div>

    <!-- Active Judge Indicator (Automatic) -->
    <div style="display: flex; align-items: center; gap: 0.75rem;">
        <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Adjudicator:</span>
        <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1.5px solid #f59e0b; font-size: 1rem; padding: 0.45rem 1.1rem; font-weight: 800;">
            ⚖️ {{ $activeJudge }}
        </span>
    </div>
</div>

<!-- Parish Header Banner -->
<div class="glass-card" style="margin-bottom: 1.75rem; border-color: rgba(245, 158, 11, 0.3); background: linear-gradient(135deg, rgba(245, 158, 11, 0.08), rgba(23, 31, 50, 0.9));">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 58px; height: 58px; border-radius: 14px; background: #f59e0b; color: #0b0f19; display: flex; align-items: center; justify-content: center; font-family: var(--font-display); font-size: 1.6rem; font-weight: 900;">
                {{ $parish->code }}
            </div>
            <div>
                <h3 style="font-family: var(--font-display); font-size: 1.65rem; font-weight: 800; color: #fff;">
                    {{ $parish->name }}
                </h3>
                <p style="color: #cbd5e1; font-size: 0.95rem;">
                    {{ $parish->deanery }} &bull; Contingent: {{ $parish->camp_contingent_count }} campers
                </p>
            </div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Allocated Time</div>
            <strong style="color: #38bdf8; font-size: 1.3rem;">{{ $category->allocated_minutes > 0 ? $category->allocated_minutes . ' mins on stage' : 'Oral Quiz' }}</strong>
            <div style="font-size: 0.75rem; color: var(--text-muted);">Max: {{ $category->max_raw_score }} Marks</div>
        </div>
    </div>
</div>

<!-- Official Adjudication Form -->
<form method="POST" action="{{ route('judge.submit') }}" id="scoresheet-form">
    @csrf
    <input type="hidden" name="category_id" value="{{ $category->id }}">
    <input type="hidden" name="parish_id" value="{{ $parish->id }}">
    <input type="hidden" name="adjudicator_name" value="{{ $activeJudge }}">

    @php
        $isChoir = $category->slug === 'choir' || str_contains(strtolower($category->name), 'choir');
        $isSelfComposed = $category->slug === 'self-composed' || str_contains(strtolower($category->name), 'self-composed');
        $isPoetry = $category->slug === 'poetry' || str_contains(strtolower($category->name), 'poetry');
        $songBreakdown = $existingScore?->song_titles_breakdown ?? [];
    @endphp

    <!-- Category Specific Identification Header -->
    <div class="glass-card" style="margin-bottom: 1.75rem;">
        <h4 style="font-family: var(--font-display); font-size: 1.15rem; font-weight: 700; color: #fff; margin-bottom: 1rem; border-bottom: 1px solid var(--border-card); padding-bottom: 0.5rem;">
            📋 Presentation Information
        </h4>

        @if($isChoir)
            <!-- Choir Specific Form Header -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Conductor</label>
                    <input type="text" name="conductor_name" value="{{ old('conductor_name', $existingScore?->conductor_name) }}" placeholder="e.g. John Banda" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Number of Participants</label>
                    <input type="number" name="participant_count" value="{{ old('participant_count', $existingScore?->participant_count) }}" placeholder="Unlimited allowed" style="width: 100%;">
                </div>
            </div>

            <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid var(--border-card); border-radius: 10px; padding: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 800; color: #f59e0b; text-transform: uppercase; margin-bottom: 0.75rem;">
                    🎵 4 Songs Category & Titles Breakdown
                </label>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">1. Social Song</label>
                        <input type="text" name="song_titles_breakdown[social_song]" value="{{ $songBreakdown['social_song'] ?? '' }}" placeholder="Title of Social Song" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">2. Kyrie</label>
                        <input type="text" name="song_titles_breakdown[kyrie]" value="{{ $songBreakdown['kyrie'] ?? '' }}" placeholder="Title of Kyrie" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">3. Gloria</label>
                        <input type="text" name="song_titles_breakdown[gloria]" value="{{ $songBreakdown['gloria'] ?? '' }}" placeholder="Title of Gloria" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">4. Thanksgiving</label>
                        <input type="text" name="song_titles_breakdown[thanksgiving]" value="{{ $songBreakdown['thanksgiving'] ?? '' }}" placeholder="Title of Thanksgiving" style="width: 100%;">
                    </div>
                </div>
            </div>

        @elseif($isSelfComposed)
            <!-- Self-Composed Specific Form Header -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                <div style="grid-column: 1 / -1;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Title of Song</label>
                    <input type="text" name="item_title" value="{{ old('item_title', $existingScore?->item_title) }}" placeholder="e.g. Tukopano mwa Pastoral Care" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Composer(s)</label>
                    <input type="text" name="composer_author" value="{{ old('composer_author', $existingScore?->composer_author) }}" placeholder="Composer Name(s)" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Director</label>
                    <input type="text" name="director_producer" value="{{ old('director_producer', $existingScore?->director_producer) }}" placeholder="Director Name" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Language Used</label>
                    <input type="text" name="language_used" value="{{ old('language_used', $existingScore?->language_used) }}" placeholder="e.g. Lozi, Tonga, English" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Number of Participants</label>
                    <input type="number" name="participant_count" value="{{ old('participant_count', $existingScore?->participant_count) }}" placeholder="Headcount" style="width: 100%;">
                </div>
            </div>

        @elseif($isPoetry)
            <!-- Poetry Specific Form Header -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                <div style="grid-column: 1 / -1;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Title of Poem</label>
                    <input type="text" name="item_title" value="{{ old('item_title', $existingScore?->item_title) }}" placeholder="e.g. Walking Together in Pastoral Care" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Composer(s) / Author</label>
                    <input type="text" name="composer_author" value="{{ old('composer_author', $existingScore?->composer_author) }}" placeholder="Author Name" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Producer / Director</label>
                    <input type="text" name="director_producer" value="{{ old('director_producer', $existingScore?->director_producer) }}" placeholder="Director Name" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Language Used</label>
                    <input type="text" name="language_used" value="{{ old('language_used', $existingScore?->language_used) }}" placeholder="e.g. English, Lozi" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Number of Participants (Max 6)</label>
                    <input type="number" name="participant_count" value="{{ old('participant_count', $existingScore?->participant_count) }}" placeholder="Max 6 on stage" max="6" style="width: 100%;">
                </div>
            </div>

        @else
            <!-- General Header -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Presenter / Leader / Director</label>
                    <input type="text" name="conductor_name" value="{{ old('conductor_name', $existingScore?->conductor_name) }}" placeholder="Leader name" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Number of Participants</label>
                    <input type="number" name="participant_count" value="{{ old('participant_count', $existingScore?->participant_count) }}" placeholder="Headcount" style="width: 100%;">
                </div>
                <div style="grid-column: 1 / -1;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Item Title / Details</label>
                    <input type="text" name="item_title" value="{{ old('item_title', $existingScore?->item_title) }}" placeholder="Performance title or quiz team details" style="width: 100%;">
                </div>
            </div>
        @endif
    </div>

    <!-- Criteria Rubric Marks Grid -->
    <div class="glass-card" style="margin-bottom: 1.75rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-card); padding-bottom: 0.75rem;">
            <div>
                <h4 style="font-family: var(--font-display); font-size: 1.2rem; font-weight: 800; color: #fff;">
                    ⚖️ Adjudication Criteria
                </h4>
                <p style="color: var(--text-muted); font-size: 0.8rem;">Evaluate each criterion item below according to the official Diocesan guidelines.</p>
            </div>
            <div style="text-align: right; background: rgba(15, 23, 42, 0.8); padding: 0.5rem 1.25rem; border-radius: 10px; border: 1px solid var(--border-card);">
                <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Total Marks Awarded:</span>
                <div style="font-family: var(--font-display); font-size: 2rem; font-weight: 900; color: #f59e0b;" id="live-total-display">
                    {{ $existingScore ? $existingScore->raw_total_score : 0 }} / {{ $category->max_raw_score }}
                </div>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @php
                $criteria = $category->judging_criteria ?? [];
                $savedScores = $existingScore?->criteria_scores ?? [];
            @endphp

            @forelse($criteria as $index => $crit)
                @php
                    $critName = $crit['criterion'] ?? ($crit['name'] ?? ('Criterion ' . ($index + 1)));
                    $critMax = $crit['possible_score'] ?? ($crit['max_score'] ?? 10);
                    $critDesc = $crit['description'] ?? ($crit['desc'] ?? '');
                    $currentVal = $savedScores[$critName] ?? ($savedScores[$index] ?? 0);
                @endphp
                <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid var(--border-card); border-radius: 12px; padding: 1.1rem 1.3rem; display: flex; justify-content: space-between; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 250px;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 0.8rem; font-weight: 800; color: #f59e0b;">#{{ $index + 1 }}</span>
                            <strong style="color: #fff; font-size: 1.05rem;">{{ $critName }}</strong>
                        </div>
                        @if($critDesc)
                            <p style="color: var(--text-muted); font-size: 0.83rem; margin-top: 0.25rem; line-height: 1.4;">
                                {{ $critDesc }}
                            </p>
                        @endif
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="text-align: right;">
                            <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Possible Score</span>
                            <div style="font-weight: 700; color: #cbd5e1;">{{ $critMax }} pts</div>
                        </div>
                        <input type="number" 
                               name="criteria_scores[{{ $critName }}]" 
                               class="criterion-input"
                               data-max="{{ $critMax }}"
                               value="{{ $currentVal }}" 
                               min="0" 
                               max="{{ $critMax }}" 
                               step="0.5" 
                               required 
                               style="width: 90px; font-size: 1.3rem; font-weight: 800; text-align: center; color: #f59e0b; padding: 0.5rem;"
                               oninput="calculateRunningTotal()">
                    </div>
                </div>
            @empty
                <p style="color: var(--text-muted);">No criteria rubric defined.</p>
            @endforelse
        </div>
    </div>

    <!-- Judge Comments & Feedback -->
    <div class="glass-card" style="margin-bottom: 1.75rem;">
        <h4 style="font-family: var(--font-display); font-size: 1.1rem; font-weight: 700; color: #fff; margin-bottom: 0.5rem;">
            COMMENTS
        </h4>
        <p style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.75rem;">
            Provide constructive adjudicator comments and observations:
        </p>
        <textarea name="comments" rows="4" placeholder="Enter your qualitative assessment and recommendations..." style="width: 100%; font-size: 0.95rem; line-height: 1.5;">{{ old('comments', $existingScore?->comments) }}</textarea>

        <div style="margin-top: 1rem; display: flex; align-items: center; gap: 0.6rem; padding-top: 0.75rem; border-top: 1px solid var(--border-card);">
            <input type="checkbox" id="is_disqualified" name="is_disqualified" value="1" {{ ($existingScore?->is_disqualified) ? 'checked' : '' }}>
            <label for="is_disqualified" style="font-size: 0.85rem; font-weight: 700; color: #f87171;">
                Disqualify this performance (severe rule violation)
            </label>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="glass-card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; position: sticky; bottom: 1rem; background: rgba(11, 15, 25, 0.95); border: 2px solid var(--primary);">
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted);">Adjudicator:</div>
            <strong style="color: #fff; font-size: 1.1rem;">{{ $activeJudge }} &bull; {{ $parish->name }}</strong>
        </div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <a href="{{ route('judge.index', ['judge' => $activeJudge, 'category_id' => $category->id]) }}" class="btn btn-secondary">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary" style="font-size: 1.05rem; font-weight: 800; padding: 0.75rem 2rem;">
                💾 Save & Submit Official Scorecard
            </button>
        </div>
    </div>
</form>

<script>
function calculateRunningTotal() {
    const inputs = document.querySelectorAll('.criterion-input');
    let total = 0;
    inputs.forEach(input => {
        const val = parseFloat(input.value) || 0;
        total += val;
    });

    const maxTotal = {{ $category->max_raw_score }};
    document.getElementById('live-total-display').innerText = total.toFixed(1) + ' / ' + maxTotal;
}

document.addEventListener('DOMContentLoaded', function() {
    calculateRunningTotal();
});
</script>
@endsection
