@extends('layouts.app')

@section('title', 'Parishes & Contingent Registration')

@section('hero')
<div class="page-hero-eyebrow">⛪ Official Roster</div>
<h2>Checked-In Parishes &amp; Contingents</h2>
<p>Official roster of Diocesan parish contingents that have arrived and completed camp check-in.</p>
@endsection

@section('content')

{{-- Stats --}}
<div class="grid-stats">
    <div class="stat-card" style="--stat-color: #3b82f6;">
        <div class="stat-icon" style="color: #3b82f6; background: rgba(59,130,246,0.12);">⛪</div>
        <div class="stat-content">
            <h4>Total Parishes</h4>
            <div class="stat-val">{{ $stats['total_parishes'] }}</div>
            <div class="stat-sub">registered diocesan parishes</div>
        </div>
    </div>
    <div class="stat-card" style="--stat-color: #10b981;">
        <div class="stat-icon" style="color: #10b981; background: rgba(16,185,129,0.12);">🎟️</div>
        <div class="stat-content">
            <h4>Checked-In</h4>
            <div class="stat-val">{{ $stats['checked_in'] }}</div>
            <div class="stat-sub">parishes at camp</div>
        </div>
    </div>
    <div class="stat-card" style="--stat-color: #f59e0b;">
        <div class="stat-icon" style="color: #f59e0b; background: rgba(245,158,11,0.12);">👥</div>
        <div class="stat-content">
            <h4>Total Contingent</h4>
            <div class="stat-val">{{ $stats['total_contingent'] }}</div>
            <div class="stat-sub">
                ♂ {{ $stats['total_male'] ?? 0 }} Male &bull; ♀ {{ $stats['total_female'] ?? 0 }} Female
            </div>
        </div>
    </div>
    <div class="stat-card" style="--stat-color: #8b5cf6;">
        <div class="stat-icon" style="color: #8b5cf6; background: rgba(139,92,246,0.12);">🏕️</div>
        <div class="stat-content">
            <h4>In Camp</h4>
            <div class="stat-val">{{ $stats['checked_in_contingent'] }}</div>
            <div class="stat-sub">
                ♂ {{ $stats['checked_in_male'] ?? 0 }} Male &bull; ♀ {{ $stats['checked_in_female'] ?? 0 }} Female
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="glass-card" style="margin-bottom: 1.5rem; padding: 1.1rem 1.35rem;">
    <form method="GET" action="{{ route('registration.index') }}" class="filter-bar">
        <div class="filter-group" style="flex: 1; min-width: 220px;">
            <label>Search Parish</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, code or patron…" style="width: 100%;">
        </div>
        <div class="filter-group">
            <label>Deanery</label>
            <select name="deanery" onchange="this.form.submit()">
                <option value="">All Deaneries</option>
                @foreach($deaneries as $d)
                    <option value="{{ $d }}" {{ $deanery == $d ? 'selected' : '' }}>{{ $d }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label>Status</label>
            <select name="status" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="checked_in" {{ ($status ?? '') == 'checked_in' ? 'selected' : '' }}>✓ Checked In</option>
                <option value="pending" {{ ($status ?? '') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
            </select>
        </div>
        <div style="margin-top: auto; display: flex; gap: 0.5rem; align-items: center;">
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            @if($search || $deanery || $status)
                <a href="{{ route('registration.index') }}" class="btn btn-secondary btn-sm">✕ Reset</a>
            @endif
        </div>
    </form>
</div>

{{-- Table --}}
<div class="glass-card" style="padding: 0; overflow: hidden;">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Parish</th>
                    <th>Deanery</th>
                    <th>Patron / Matron</th>
                    <th>Contact</th>
                    <th style="text-align: center;">Contingent</th>
                    <th style="text-align: center;">Check-In</th>
                </tr>
            </thead>
            <tbody>
                @forelse($parishes as $p)
                    <tr>
                        <td>
                            <span style="font-family: var(--font-display); font-weight: 800; font-size: 0.95rem; color: #f59e0b;">
                                {{ $p->code }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 700; font-size: 0.95rem; color: #fff;">{{ $p->name }}</div>
                        </td>
                        <td>
                            <span style="color: var(--text-sub); font-size: 0.85rem;">{{ $p->deanery ?? 'Livingstone' }}</span>
                        </td>
                        <td>
                            <span style="color: var(--text-sub); font-size: 0.85rem;">{{ $p->patron_matron_name ?? '—' }}</span>
                        </td>
                        <td>
                            <span style="color: #38bdf8; font-size: 0.85rem;">{{ $p->patron_contact ?? '—' }}</span>
                        </td>
                        <td style="text-align: center;">
                            <div style="font-family: var(--font-display); font-weight: 800; font-size: 1.1rem; color: #fff;">
                                {{ $p->camp_contingent_count }}
                                <span style="font-size: 0.72rem; font-weight: 500; color: var(--text-muted);">youths</span>
                            </div>
                            <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.1rem;">
                                ♂ {{ $p->male_count }} &bull; ♀ {{ $p->female_count }}
                            </div>
                        </td>
                        <td style="text-align: center;">
                            @if($p->camp_checked_in)
                                <span class="badge badge-completed">✓ In Camp</span>
                            @else
                                <span class="badge badge-scheduled">⏳ Pending</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-icon">⛪</div>
                                <h3>No Parishes Found</h3>
                                <p>
                                    @if($search || $deanery || $status)
                                        No parishes match your filters — try resetting them.
                                    @else
                                        No parishes have been registered yet. Entries added in the Admin panel appear here.
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
