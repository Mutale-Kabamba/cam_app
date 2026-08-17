@extends('layouts.app')

@section('title', 'Admin Executive Dashboard')

@section('content')
<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.5rem; color: #3b82f6; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">
            <span>⚙️ Central Festival Operations Control Panel</span>
        </div>
        <h2 style="font-family: var(--font-display); font-size: 2.2rem; font-weight: 800; margin-bottom: 0.25rem; letter-spacing: -0.02em;">
            Executive Committee Dashboard
        </h2>
        <p style="color: var(--text-muted);">Manage festival timetable, parish camp contingents, check-ins, and 3-judge scoring consolidation.</p>
    </div>
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="{{ route('admin.program') }}" class="btn btn-primary">
            📅 Manage Timetable
        </a>
        <a href="{{ route('admin.parishes') }}" class="btn btn-secondary">
            ⛪ Manage Parishes & Contingents
        </a>
        <a href="{{ route('admin.consolidation') }}" class="btn btn-secondary" style="border-color: rgba(139, 92, 246, 0.4); color: #c4b5fd;">
            📊 3-Judge Consolidation
        </a>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid-stats">
    <div class="stat-card">
        <div class="stat-icon" style="color: #3b82f6; background: rgba(59, 130, 246, 0.15);">⛪</div>
        <div class="stat-content">
            <h4>Total Parishes</h4>
            <div class="stat-val">{{ $stats['total_parishes'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color: #10b981; background: rgba(16, 185, 129, 0.15);">🎟️</div>
        <div class="stat-content">
            <h4>Checked-In Parishes</h4>
            <div class="stat-val">{{ $stats['checked_in_parishes'] }} <span style="font-size: 0.9rem; color: var(--text-muted);">/ {{ $stats['total_parishes'] }}</span></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color: #f59e0b; background: rgba(245, 158, 11, 0.15);">👥</div>
        <div class="stat-content">
            <h4>Total Camp Contingent</h4>
            <div class="stat-val">{{ $stats['total_contingent'] }} youths</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color: #8b5cf6; background: rgba(139, 92, 246, 0.15);">🏕️</div>
        <div class="stat-content">
            <h4>Campers In Camp</h4>
            <div class="stat-val">{{ $stats['checked_in_contingent'] }} youths</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color: #ec4899; background: rgba(236, 72, 153, 0.15);">📋</div>
        <div class="stat-content">
            <h4>Total Activities</h4>
            <div class="stat-val">{{ $stats['total_schedules'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color: #ef4444; background: rgba(239, 68, 68, 0.15);">⚡</div>
        <div class="stat-content">
            <h4>Live / On Stage</h4>
            <div class="stat-val">{{ $stats['in_progress_schedules'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color: #10b981; background: rgba(16, 185, 129, 0.15);">✅</div>
        <div class="stat-content">
            <h4>Completed Performances</h4>
            <div class="stat-val">{{ $stats['completed_schedules'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color: #f59e0b; background: rgba(245, 158, 11, 0.15);">🏆</div>
        <div class="stat-content">
            <h4>Finalized Categories</h4>
            <div class="stat-val">{{ $stats['finalized_categories'] }} <span style="font-size: 0.9rem; color: var(--text-muted);">/ 8</span></div>
        </div>
    </div>
</div>

<!-- Quick Action Control Panels -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Timetable Control Card -->
    <div class="glass-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-family: var(--font-display); font-size: 1.25rem; font-weight: 700; color: #fff;">
                📅 Stage & Timetable Manager
            </h3>
            <a href="{{ route('admin.program') }}" class="btn btn-sm btn-primary">Open Timetable &rarr;</a>
        </div>
        <p style="color: var(--text-muted); font-size: 0.88rem; margin-bottom: 1.25rem;">
            Control the live stage: switch status to <em>Live / On Stage</em> or <em>Completed</em>, set timekeeper penalty marks, and reorder performances.
        </p>
        <div style="background: rgba(15, 23, 42, 0.5); border-radius: 10px; padding: 0.75rem; font-size: 0.85rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
                <span style="color: var(--text-muted);">Upcoming Events:</span>
                <strong>{{ $stats['total_schedules'] - $stats['completed_schedules'] }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--text-muted);">Current Active Stage:</span>
                <span class="badge badge-live">Live Control Ready</span>
            </div>
        </div>
    </div>

    <!-- Parishes & Check-In Control Card -->
    <div class="glass-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-family: var(--font-display); font-size: 1.25rem; font-weight: 700; color: #fff;">
                ⛪ Parishes & Contingents Desk
            </h3>
            <a href="{{ route('admin.parishes') }}" class="btn btn-sm btn-primary">Check-In Desk &rarr;</a>
        </div>
        <p style="color: var(--text-muted); font-size: 0.88rem; margin-bottom: 1.25rem;">
            Process arriving contingents from Livingstone, Sesheke, and Sioma Deaneries, adjust headcount, and manage patron/matron credentials.
        </p>
        <div style="background: rgba(15, 23, 42, 0.5); border-radius: 10px; padding: 0.75rem; font-size: 0.85rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
                <span style="color: var(--text-muted);">Camp Check-In Rate:</span>
                <strong>{{ round(($stats['checked_in_parishes'] / max(1, $stats['total_parishes'])) * 100, 1) }}%</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--text-muted);">Pending Arrivals:</span>
                <span style="color: #f59e0b; font-weight: 700;">{{ $stats['total_parishes'] - $stats['checked_in_parishes'] }} Parishes</span>
            </div>
        </div>
    </div>

    <!-- 3-Judge Consolidation Hub Card -->
    <div class="glass-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-family: var(--font-display); font-size: 1.25rem; font-weight: 700; color: #fff;">
                📊 3-Judge Consolidation Hub
            </h3>
            <a href="{{ route('admin.consolidation') }}" class="btn btn-sm btn-primary">Open Hub &rarr;</a>
        </div>
        <p style="color: var(--text-muted); font-size: 0.88rem; margin-bottom: 1.25rem;">
            Compare scorecards from Judge 1, Judge 2, and Judge 3, enter timekeeper penalties, and publish finalized results to the live leaderboard.
        </p>
        <div style="background: rgba(15, 23, 42, 0.5); border-radius: 10px; padding: 0.75rem; font-size: 0.85rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
                <span style="color: var(--text-muted);">Submitted Judge Marks:</span>
                <strong>{{ $stats['total_adjudications'] }} total</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--text-muted);">3-Judge Sync:</span>
                <span class="badge badge-completed">Automated Calculation</span>
            </div>
        </div>
    </div>
</div>
@endsection
