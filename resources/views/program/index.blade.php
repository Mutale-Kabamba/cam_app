@extends('layouts.app')

@section('title', 'Festival Timetable & Live Tracker')

@section('hero')
<div class="page-hero-eyebrow">📅 Live Competition Schedule</div>
<div style="display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
    <div>
        <h2>Festival Timetable &amp; Schedule Tracker</h2>
        <p>Real-time performance order, venue assignments, and live stage status — updated instantly.</p>
    </div>
    <a href="{{ route('leaderboard.big_screen') }}" class="btn btn-primary" target="_blank" style="flex-shrink: 0;">
        📺 Launch Big Screen
    </a>
</div>
@endsection

@section('content')

{{-- Stats --}}
<div class="grid-stats animate-in">
    <div class="stat-card animate-in animate-in-delay-1" style="--stat-color: #f59e0b;">
        <div class="stat-icon" style="color: #f59e0b; background: rgba(245,158,11,0.12);">🏆</div>
        <div class="stat-content">
            <h4>Categories</h4>
            <div class="stat-val">{{ $stats['total_categories'] }}</div>
            <div class="stat-sub">competition events</div>
        </div>
    </div>
    <div class="stat-card animate-in animate-in-delay-2" style="--stat-color: #ef4444;">
        <div class="stat-icon" style="color: #ef4444; background: rgba(239,68,68,0.12);">⚡</div>
        <div class="stat-content">
            <h4>Live on Stage</h4>
            <div class="stat-val">{{ $stats['in_progress'] }}</div>
            <div class="stat-sub">performing now</div>
        </div>
    </div>
    <div class="stat-card animate-in animate-in-delay-3" style="--stat-color: #3b82f6;">
        <div class="stat-icon" style="color: #3b82f6; background: rgba(59,130,246,0.12);">⏳</div>
        <div class="stat-content">
            <h4>Upcoming</h4>
            <div class="stat-val">{{ $stats['upcoming'] }}</div>
            <div class="stat-sub">scheduled ahead</div>
        </div>
    </div>
    <div class="stat-card animate-in animate-in-delay-4" style="--stat-color: #10b981;">
        <div class="stat-icon" style="color: #10b981; background: rgba(16,185,129,0.12);">✅</div>
        <div class="stat-content">
            <h4>Completed</h4>
            <div class="stat-val">{{ $stats['completed'] }}</div>
            <div class="stat-sub">finished performances</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="glass-card" style="margin-bottom: 1.5rem; padding: 1.1rem 1.35rem;">
    <form method="GET" action="{{ route('program.index') }}" class="filter-bar">
        <div class="filter-group">
            <label>Day</label>
            <select name="day_name" onchange="this.form.submit()">
                <option value="">All Days</option>
                @php $daysList = !empty($availableDays) ? $availableDays : ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']; @endphp
                @foreach($daysList as $day)
                    <option value="{{ $day }}" {{ $selectedDay == $day ? 'selected' : '' }}>{{ $day }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label>Category</label>
            <select name="category_id" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $selectedCategory == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        @if($selectedCategory || $selectedDay)
            <div style="margin-top: auto;">
                <a href="{{ route('program.index') }}" class="btn btn-secondary btn-sm">✕ Reset</a>
            </div>
        @endif
    </form>
</div>

{{-- Schedule Table --}}
<div class="glass-card" style="padding: 0; overflow: hidden;">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>Time Slot</th>
                    <th>Venue</th>
                    <th>Parish / Activity</th>
                    <th>Category</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($scheduleItems as $item)
                    <tr class="{{ $item->status === 'live' || $item->status === 'in_progress' ? 'live-row' : '' }}">
                        <td>
                            <span style="display: inline-flex; width: 32px; height: 32px; background: var(--primary-dim); border-radius: 9px; align-items: center; justify-content: center; font-weight: 800; color: #f59e0b; font-family: var(--font-display);">
                                {{ $item->performance_order ?? '—' }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #fff; font-size: 0.92rem;">
                                {{ \Carbon\Carbon::parse($item->scheduled_start_time)->format('H:i') }}
                                <span style="color: var(--text-muted); font-weight: 500;">→</span>
                                {{ \Carbon\Carbon::parse($item->scheduled_end_time)->format('H:i') }}
                            </div>
                            <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.1rem;">{{ $item->day_name }}</div>
                        </td>
                        <td>
                            <span style="display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.82rem; color: var(--text-sub);">
                                📍 {{ $item->venue }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #fff; font-size: 0.92rem;">{{ $item->activity_title }}</div>
                            @if($item->parish)
                                <div style="font-size: 0.75rem; color: #f59e0b; font-weight: 600; margin-top: 0.12rem;">
                                    ⛪ {{ $item->parish->name }}
                                    <span style="color: var(--text-muted); font-weight: 500;">· {{ $item->parish->deanery }}</span>
                                </div>
                            @else
                                <div style="font-size: 0.7rem; color: #38bdf8; margin-top: 0.12rem;">Diocesan Assembly</div>
                            @endif
                        </td>
                        <td>
                            @if($item->category)
                                <span style="background: rgba(59,130,246,0.10); color: #60a5fa; border: 1px solid rgba(59,130,246,0.22); padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">
                                    {{ $item->category->name }}
                                </span>
                            @else
                                <span style="color: var(--text-muted); font-size: 0.78rem;">General</span>
                            @endif
                        </td>
                        <td>
                            @if($item->status === 'live' || $item->status === 'in_progress')
                                <span class="badge badge-live">● LIVE</span>
                            @elseif($item->status === 'completed')
                                <span class="badge badge-completed">✓ Done</span>
                            @else
                                <span class="badge badge-scheduled">⏳ Soon</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-icon">📅</div>
                                <h3>No Schedule Items Found</h3>
                                <p>
                                    @if($selectedCategory || $selectedDay)
                                        No activities match your filters. Try resetting them.
                                    @else
                                        No schedule has been added yet. Items created in the Admin panel appear here in real time.
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
