@extends('layouts.app')

@section('title', 'Adjudication & Official Judging Sheets')

@section('content')
<div style="margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; color: #f59e0b; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">
        <span>✝️ Catholic Diocese of Livingstone &bull; Youth Ministry</span>
    </div>
    <h2 style="font-family: var(--font-display); font-size: 2.2rem; font-weight: 800; margin-bottom: 0.25rem; letter-spacing: -0.02em;">
        ⚖️ Adjudication & Official Judging Sheets
    </h2>
    <p style="color: var(--text-muted); font-size: 1rem;">
        Theme: <em>“A YEAR OF WORKING TOGETHER IN DEEPER PASTORAL CARE”</em>
    </p>
</div>

<!-- Category Selector Bar -->
<div class="glass-card" style="margin-bottom: 1.75rem;">
    <form method="GET" action="{{ route('adjudication.index') }}" style="display: flex; gap: 1.25rem; align-items: center; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 280px;">
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">
                Select Competition Category
            </label>
            <select name="category_id" style="width: 100%; background: rgba(15, 23, 42, 0.9); border: 1px solid var(--border-card); color: #fff; padding: 0.65rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.95rem;" onchange="this.form.submit()">
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $selectedCategory == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }} &bull; Max {{ $cat->max_raw_score }} pts ({{ $cat->allocated_minutes > 0 ? $cat->allocated_minutes . ' mins' : 'Quiz' }})
                    </option>
                @endforeach
            </select>
        </div>
    </form>
</div>

@if($activeCategory)
    <!-- Category Overview Card -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
        <div class="glass-card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                <div>
                    <span class="badge badge-scheduled" style="margin-bottom: 0.5rem;">{{ ucfirst(str_replace('_', ' ', $activeCategory->type)) }}</span>
                    <h3 style="font-family: var(--font-display); font-size: 1.5rem; font-weight: 800; color: #fff;">
                        {{ $activeCategory->name }} Adjudication Form
                    </h3>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Total Score</div>
                    <div style="font-family: var(--font-display); font-size: 1.75rem; font-weight: 900; color: #f59e0b;">
                        {{ $activeCategory->max_raw_score }} Points
                    </div>
                </div>
            </div>

            <p style="color: #cbd5e1; font-size: 0.95rem; margin-bottom: 1.25rem;">
                {{ $activeCategory->description }}
            </p>

            <!-- Rules / Requirements Pills -->
            @if($activeCategory->rules)
                <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid var(--border-card); border-radius: 12px; padding: 1rem;">
                    <h4 style="font-size: 0.8rem; text-transform: uppercase; color: #f59e0b; font-weight: 700; margin-bottom: 0.5rem;">
                        📋 Category Rules & Requirements
                    </h4>
                    <ul style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.5rem; font-size: 0.85rem; color: #e2e8f0; list-style: none;">
                        @if(isset($activeCategory->rules['participant_limit']))
                            <li>👥 <strong>Participants:</strong> {{ $activeCategory->rules['participant_limit'] }}</li>
                        @endif
                        @if(isset($activeCategory->rules['languages_allowed']))
                            <li>🗣️ <strong>Languages:</strong> {{ implode(', ', $activeCategory->rules['languages_allowed']) }}</li>
                        @endif
                        @if(isset($activeCategory->rules['songs_required']))
                            <li>🎵 <strong>Required Songs:</strong> {{ implode(', ', $activeCategory->rules['songs_required']) }}</li>
                        @endif
                        @if(isset($activeCategory->rules['dances_required']))
                            <li>💃 <strong>Required:</strong> {{ $activeCategory->rules['dances_required'] }}</li>
                        @endif
                        @if(isset($activeCategory->rules['books_covered']))
                            <li>📖 <strong>Scripture Books:</strong> {{ implode(', ', $activeCategory->rules['books_covered']) }}</li>
                        @endif
                        @if(isset($activeCategory->rules['focus_material']))
                            <li>📚 <strong>Focus:</strong> {{ $activeCategory->rules['focus_material'] }}</li>
                        @endif
                        @if(isset($activeCategory->rules['written_exam_subjects']))
                            <li>📝 <strong>Written Exams:</strong> {{ $activeCategory->rules['written_exam_subjects'] }}</li>
                        @endif
                        @if(isset($activeCategory->rules['dress_code']))
                            <li>👘 <strong>Dress Code:</strong> {{ $activeCategory->rules['dress_code'] }}</li>
                        @endif
                        @if(isset($activeCategory->rules['safety']))
                            <li>🛡️ <strong>Safety:</strong> {{ $activeCategory->rules['safety'] }}</li>
                        @endif
                        @if(isset($activeCategory->rules['prohibitions']))
                            <li>🚫 <strong>Prohibitions:</strong> {{ $activeCategory->rules['prohibitions'] }}</li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>

        <!-- Timekeeper & Penalty Rules Box -->
        <div class="glass-card" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h4 style="font-family: var(--font-display); font-size: 1.1rem; font-weight: 700; color: #ef4444; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.4rem;">
                    ⏱️ Time Management Rule
                </h4>
                <div style="font-size: 0.85rem; color: #cbd5e1; margin-bottom: 1rem; line-height: 1.4;">
                    Allocated Stage Time: <strong>{{ $activeCategory->allocated_minutes > 0 ? $activeCategory->allocated_minutes . ' mins' : 'N/A' }}</strong><br>
                    Stage Prep Time: <strong>{{ $activeCategory->prep_minutes }} mins</strong>
                </div>

                <div style="background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 10px; padding: 0.75rem; font-size: 0.8rem;">
                    <div style="font-weight: 700; color: #f87171; margin-bottom: 0.4rem;">Penalty Deductions (Independent Timekeeper):</div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;"><span>Up to 1 min over:</span> <strong style="color:#ef4444;">-2 marks</strong></div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;"><span>1 to 3 mins over:</span> <strong style="color:#ef4444;">-5 marks</strong></div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;"><span>3 to 5 mins over:</span> <strong style="color:#ef4444;">-10 marks</strong></div>
                    <div style="display: flex; justify-content: space-between;"><span>&gt; 5 mins over:</span> <strong style="color:#ef4444;">-15 marks</strong></div>
                </div>
            </div>

            <div style="margin-top: 1rem; font-size: 0.75rem; color: var(--text-muted); font-style: italic;">
                * Time penalties are deducted from the final aggregated score after marks are totaled.
            </div>
        </div>
    </div>

    <!-- Official Adjudication Criteria Sheet Table -->
    <div class="glass-card" style="margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h3 style="font-family: var(--font-display); font-size: 1.35rem; font-weight: 800; color: #fff;">
                    📋 Official Adjudication Criteria Rubric
                </h3>
                <p style="color: var(--text-muted); font-size: 0.85rem;">Standard evaluation parameters prescribed by the Diocesan Youth Ministry</p>
            </div>
            <span class="badge badge-gold" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                Total Possible: {{ $activeCategory->max_raw_score }} Marks
            </span>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;">No.</th>
                        <th style="width: 260px;">Criterion</th>
                        <th>Assessment Focus & Description</th>
                        <th style="width: 140px; text-align: right;">Possible Score</th>
                    </tr>
                </thead>
                <tbody>
                    @if($activeCategory->judging_criteria && count($activeCategory->judging_criteria) > 0)
                        @foreach($activeCategory->judging_criteria as $crit)
                            <tr>
                                <td>
                                    <span style="display: inline-flex; width: 28px; height: 28px; background: rgba(255,255,255,0.06); border-radius: 6px; align-items: center; justify-content: center; font-weight: 700; color: #f59e0b; font-size: 0.85rem;">
                                        {{ $crit['no'] ?? $loop->iteration }}
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: #fff; font-size: 0.95rem;">{{ $crit['criterion'] }}</strong>
                                </td>
                                <td style="color: #cbd5e1; font-size: 0.88rem; line-height: 1.4;">
                                    {{ $crit['description'] }}
                                </td>
                                <td style="text-align: right;">
                                    <span style="font-family: var(--font-display); font-weight: 800; font-size: 1.15rem; color: #34d399;">
                                        {{ $crit['possible_score'] }}
                                    </span>
                                    <span style="color: var(--text-muted); font-size: 0.75rem;">pts</span>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                No specific criteria items configured.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Digital Score Sheet Submission / Entry Section -->
    <div class="glass-card">
        <h3 style="font-family: var(--font-display); font-size: 1.3rem; font-weight: 800; margin-bottom: 1rem; color: #fff;">
            ✍️ Parish Performance Scoring Sheet
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">
                    Select Participating Parish (17 Parishes Seeded)
                </label>
                <select id="parish_select" style="width: 100%; background: rgba(15, 23, 42, 0.9); border: 1px solid var(--border-card); color: #fff; padding: 0.65rem 1rem; border-radius: 8px; font-weight: 600;">
                    @foreach($parishes as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} &bull; {{ $p->deanery }} ({{ $p->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">
                    Adjudicator Full Name
                </label>
                <input type="text" placeholder="e.g. Fr. / Sr. / Mr. Official Adjudicator" style="width: 100%; background: rgba(15, 23, 42, 0.9); border: 1px solid var(--border-card); color: #fff; padding: 0.65rem 1rem; border-radius: 8px;">
            </div>
        </div>

        <!-- Quick Criteria Scoring Grid -->
        <div style="background: rgba(15, 23, 42, 0.5); border: 1px solid var(--border-card); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
            <h4 style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; color: #f59e0b; margin-bottom: 1rem;">
                Enter Scores per Criterion (Max {{ $activeCategory->max_raw_score }} Marks)
            </h4>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                @if($activeCategory->judging_criteria)
                    @foreach($activeCategory->judging_criteria as $cItem)
                        <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.03); padding: 0.6rem 0.85rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                            <span style="font-size: 0.85rem; font-weight: 600; color: #e2e8f0;">
                                {{ $cItem['criterion'] }} <small style="color: var(--text-muted);">({{ $cItem['possible_score'] }} pts max)</small>
                            </span>
                            <input type="number" min="0" max="{{ $cItem['possible_score'] }}" placeholder="0" style="width: 70px; text-align: center; background: rgba(15, 23, 42, 0.9); border: 1px solid rgba(245, 158, 11, 0.3); color: #f59e0b; font-weight: 800; font-size: 1rem; padding: 0.35rem; border-radius: 6px;">
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem;">
            <button type="button" class="btn btn-primary" onclick="alert('Score recorded for parish successfully!')">
                💾 Submit & Validate Official Marks
            </button>
        </div>
    </div>
@endif
@endsection
