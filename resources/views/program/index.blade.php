@extends('layouts.app')

@section('title', 'Festival Timetable & Live Tracker')

@section('content')
<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-family: var(--font-display); font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem;">
            📅 Festival Timetable & Schedule Tracker
        </h2>
        <p style="color: var(--text-muted);">Real-time competition performance order, allocated times, and live status.</p>
    </div>
    <div>
        <a href="{{ route('leaderboard.big_screen') }}" class="btn btn-primary" target="_blank">
            📺 Launch Big Screen Mode
        </a>
    </div>
</div>

<div class="grid-stats">
    <div class="stat-card">
        <div class="stat-icon" style="color: #f59e0b; background: rgba(245, 158, 11, 0.15);">🏆</div>
        <div class="stat-content">
            <h4>Total Categories</h4>
            <div class="stat-val">{{ $stats['total_categories'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color: #ef4444; background: rgba(239, 68, 68, 0.15);">⚡</div>
        <div class="stat-content">
            <h4>In Progress (On Stage)</h4>
            <div class="stat-val">{{ $stats['in_progress'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color: #3b82f6; background: rgba(59, 130, 246, 0.15);">⏳</div>
        <div class="stat-content">
            <h4>Upcoming</h4>
            <div class="stat-val">{{ $stats['upcoming'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color: #10b981; background: rgba(16, 185, 129, 0.15);">✅</div>
        <div class="stat-content">
            <h4>Completed</h4>
            <div class="stat-val">{{ $stats['completed'] }}</div>
        </div>
    </div>
</div>

<div class="glass-card" style="margin-bottom: 1.5rem;">
    <form method="GET" action="{{ route('program.index') }}" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Day Filter</label>
            <select name="day_name" style="background: rgba(15, 23, 42, 0.8); border: 1px solid var(--border-card); color: #fff; padding: 0.55rem 1rem; border-radius: 8px; font-weight: 500;" onchange="this.form.submit()">
                <option value="">All Days</option>
                @php
                    $daysList = !empty($availableDays) ? $availableDays : ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                @endphp
                @foreach($daysList as $day)
                    <option value="{{ $day }}" {{ $selectedDay == $day ? 'selected' : '' }}>{{ $day }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Category Filter</label>
            <select name="category_id" style="background: rgba(15, 23, 42, 0.8); border: 1px solid var(--border-card); color: #fff; padding: 0.55rem 1rem; border-radius: 8px; font-weight: 500;" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $selectedCategory == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        
        @if($selectedCategory || $selectedDay)
            <div style="margin-top: auto;">
                <a href="{{ route('program.index') }}" class="btn btn-secondary" style="padding: 0.55rem 1rem;">Reset Filters</a>
            </div>
        @endif
    </form>
</div>

<div class="glass-card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Time Slot</th>
                    <th>Venue</th>
                    <th>Parish / Participant</th>
                    <th>Category</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($scheduleItems as $item)
                    <tr>
                        <td>
                            <span style="display: inline-flex; width: 32px; height: 32px; background: rgba(255,255,255,0.06); border-radius: 8px; align-items: center; justify-content: center; font-weight: 700; color: #f59e0b;">
                                {{ $item->performance_order ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <strong style="color: #fff;">{{ \Carbon\Carbon::parse($item->scheduled_start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->scheduled_end_time)->format('H:i') }}</strong>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $item->day_name }}</div>
                        </td>
                        <td>
                            <span style="display: inline-flex; align-items: center; gap: 0.3rem; color: #cbd5e1;">
                                📍 {{ $item->venue }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #fff; font-size: 0.95rem;">
                                {{ $item->activity_title }}
                            </div>
                            @if($item->parish)
                                <div style="font-size: 0.8rem; color: #f59e0b; font-weight: 600; margin-top: 0.15rem;">
                                    ⛪ {{ $item->parish->name }} &bull; <span style="color: #94a3b8;">{{ $item->parish->deanery }}</span>
                                </div>
                            @else
                                <div style="font-size: 0.75rem; color: #38bdf8; margin-top: 0.15rem;">
                                    Diocesan Assembly
                                </div>
                            @endif
                        </td>
                        <td>
                            <span style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: 600; font-size: 0.8rem;">
                                {{ $item->category?->name ?? 'General' }}
                            </span>
                        </td>
                        <td>
                            @if($item->status === 'live' || $item->status === 'in_progress')
                                <span class="badge badge-live">● ON STAGE</span>
                            @elseif($item->status === 'completed')
                                <span class="badge badge-completed">✓ Finished</span>
                            @else
                                <span class="badge badge-scheduled">⏳ Scheduled</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3.5rem 1rem; color: var(--text-muted);">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📅</div>
                            <div style="font-size: 1.1rem; font-weight: 700; color: #cbd5e1; margin-bottom: 0.25rem;">No Schedule Items Found</div>
                            <p style="font-size: 0.9rem; max-width: 500px; margin: 0 auto; color: var(--text-muted);">
                                @if($selectedCategory || $selectedDay)
                                    No activities match the selected day or category filters. Try resetting the filters.
                                @else
                                    No schedule items have been added to the timetable yet. Items created in the Admin panel will appear here in real time.
                                @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
