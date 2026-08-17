@extends('layouts.app')

@section('title', 'Parishes & Contingent Registration')

@section('content')
<div style="margin-bottom: 2rem;">
    <h2 style="font-family: var(--font-display); font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem;">
        ⛪ Checked-In Parishes & Contingents
    </h2>
    <p style="color: var(--text-muted);">Official roster of Diocesan parish contingents that have arrived and completed camp check-in.</p>
</div>

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
            <div class="stat-val">{{ $stats['checked_in'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color: #f59e0b; background: rgba(245, 158, 11, 0.15);">👥</div>
        <div class="stat-content">
            <h4>Total Contingent Size</h4>
            <div class="stat-val">{{ $stats['total_contingent'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color: #8b5cf6; background: rgba(139, 92, 246, 0.15);">🏕️</div>
        <div class="stat-content">
            <h4>Campers In Camp</h4>
            <div class="stat-val">{{ $stats['checked_in_contingent'] }}</div>
        </div>
    </div>
</div>

<div class="glass-card" style="margin-bottom: 1.5rem;">
    <form method="GET" action="{{ route('registration.index') }}" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 220px;">
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Search Parish</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by Parish Name, Code or Patron..." style="width: 100%; background: rgba(15, 23, 42, 0.8); border: 1px solid var(--border-card); color: #fff; padding: 0.55rem 1rem; border-radius: 8px;">
        </div>

        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Deanery</label>
            <select name="deanery" style="background: rgba(15, 23, 42, 0.8); border: 1px solid var(--border-card); color: #fff; padding: 0.55rem 1rem; border-radius: 8px;" onchange="this.form.submit()">
                <option value="">All Deaneries</option>
                @foreach($deaneries as $d)
                    <option value="{{ $d }}" {{ $deanery == $d ? 'selected' : '' }}>{{ $d }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Check-In Status</label>
            <select name="status" style="background: rgba(15, 23, 42, 0.8); border: 1px solid var(--border-card); color: #fff; padding: 0.55rem 1rem; border-radius: 8px;" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="checked_in" {{ ($status ?? '') == 'checked_in' ? 'selected' : '' }}>✓ Checked In</option>
                <option value="pending" {{ ($status ?? '') == 'pending' ? 'selected' : '' }}>⏳ Pending Arrival</option>
            </select>
        </div>

        <div style="margin-top: auto; display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary" style="padding: 0.55rem 1.25rem;">Filter</button>
            @if($search || $deanery || $status)
                <a href="{{ route('registration.index') }}" class="btn btn-secondary" style="padding: 0.55rem 1rem;">Reset</a>
            @endif
        </div>
    </form>
</div>

<div class="glass-card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Parish Name</th>
                    <th>Deanery</th>
                    <th>Patron / Matron</th>
                    <th>Contact Phone</th>
                    <th>Contingent</th>
                    <th>Check-In Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($parishes as $p)
                    <tr>
                        <td>
                            <strong style="color: #f59e0b; font-family: var(--font-display);">{{ $p->code }}</strong>
                        </td>
                        <td>
                            <div style="font-weight: 700; font-size: 1rem; color: #fff;">{{ $p->name }}</div>
                        </td>
                        <td>
                            <span style="color: #cbd5e1;">{{ $p->deanery ?? 'Livingstone' }}</span>
                        </td>
                        <td>
                            <div>{{ $p->patron_matron_name ?? 'Not Specified' }}</div>
                        </td>
                        <td>
                            <span style="color: #38bdf8;">{{ $p->patron_contact ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <span style="font-weight: 700; color: #fff;">{{ $p->camp_contingent_count }}</span> youths
                        </td>
                        <td>
                            @if($p->camp_checked_in)
                                <span class="badge badge-completed">✓ Checked In</span>
                            @else
                                <span class="badge badge-scheduled">⏳ Pending Arrival</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 3.5rem 1rem; color: var(--text-muted);">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">⛪</div>
                            <div style="font-size: 1.1rem; font-weight: 700; color: #cbd5e1; margin-bottom: 0.25rem;">No Parishes Found</div>
                            <p style="font-size: 0.9rem; max-width: 500px; margin: 0 auto; color: var(--text-muted);">
                                @if($search || $deanery || $status)
                                    No parishes match your active filter criteria. Try resetting the filters.
                                @else
                                    No parishes have been registered in the system yet. Parishes added in the Admin panel will appear here.
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
