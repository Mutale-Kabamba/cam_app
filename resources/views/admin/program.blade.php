@extends('layouts.app')

@section('title', 'Admin - Timetable & Program Management')

@section('content')
<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.5rem; color: #3b82f6; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">
            <span><a href="{{ route('admin.index') }}" style="color: #94a3b8; text-decoration: none;">⚙️ Admin</a> &bull; Timetable Operations</span>
        </div>
        <h2 style="font-family: var(--font-display); font-size: 2.2rem; font-weight: 800; margin-bottom: 0.25rem; letter-spacing: -0.02em;">
            📅 Timetable & Program Stage Control
        </h2>
        <p style="color: var(--text-muted);">Manage performance order, trigger live stage states, record timekeeper penalties, and schedule activities.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('new-activity-modal').style.display='block'">
            ➕ Add Schedule Activity
        </button>
    </div>
</div>

<!-- Day & Category Filter Bar -->
<div class="glass-card" style="margin-bottom: 1.5rem;">
    <form method="GET" action="{{ route('admin.program') }}" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Filter Day</label>
            <select name="day_name" onchange="this.form.submit()">
                <option value="Monday" {{ $selectedDay == 'Monday' ? 'selected' : '' }}>Monday</option>
                <option value="Tuesday" {{ $selectedDay == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                <option value="Wednesday" {{ $selectedDay == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                <option value="Thursday" {{ $selectedDay == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                <option value="Friday" {{ $selectedDay == 'Friday' ? 'selected' : '' }}>Friday</option>
                <option value="Saturday" {{ $selectedDay == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                <option value="Sunday" {{ $selectedDay == 'Sunday' ? 'selected' : '' }}>Sunday</option>
            </select>
        </div>

        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Filter Category</label>
            <select name="category_id" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $selectedCategory == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        @if($selectedCategory || $selectedDay)
            <div style="margin-top: auto;">
                <a href="{{ route('admin.program') }}" class="btn btn-secondary" style="padding: 0.55rem 1rem;">Reset</a>
            </div>
        @endif
    </form>
</div>

<!-- Schedule Table with Live Stage Controls -->
<div class="glass-card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 70px;">Order</th>
                    <th style="width: 140px;">Time Slot</th>
                    <th>Venue</th>
                    <th>Activity / Parish</th>
                    <th>Category</th>
                    <th style="width: 150px;">Stage Status</th>
                    <th style="width: 130px;">Time Penalty</th>
                    <th style="text-align: right; width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($scheduleItems as $item)
                    <tr style="{{ $item->status === 'in_progress' ? 'background: rgba(239, 68, 68, 0.08);' : '' }}">
                        <td>
                            <span style="display: inline-flex; width: 32px; height: 32px; background: rgba(255,255,255,0.06); border-radius: 8px; align-items: center; justify-content: center; font-weight: 700; color: #f59e0b;">
                                {{ $item->performance_order ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <strong>{{ \Carbon\Carbon::parse($item->scheduled_start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->scheduled_end_time)->format('H:i') }}</strong>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $item->day_name }}</div>
                        </td>
                        <td>
                            <span style="color: #cbd5e1;">📍 {{ $item->venue }}</span>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #fff; font-size: 0.95rem;">
                                {{ $item->activity_title }}
                            </div>
                            @if($item->parish)
                                <div style="font-size: 0.8rem; color: #38bdf8;">
                                    ⛪ {{ $item->parish->name }} ({{ $item->parish->code }}) &bull; {{ $item->parish->deanery }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <span style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: 600; font-size: 0.8rem;">
                                {{ $item->category?->name ?? 'General' }}
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.program.status', $item->id) }}" style="display: inline;">
                                @csrf
                                <select name="status" onchange="this.form.submit()" style="padding: 0.35rem 0.6rem; font-size: 0.8rem; font-weight: 700; border-radius: 6px; {{ $item->status === 'in_progress' ? 'border-color:#ef4444; color:#f87171;' : ($item->status === 'completed' ? 'border-color:#10b981; color:#34d399;' : 'color:#60a5fa;') }}">
                                    <option value="scheduled" {{ $item->status === 'scheduled' ? 'selected' : '' }}>⏳ Scheduled</option>
                                    <option value="in_progress" {{ $item->status === 'in_progress' ? 'selected' : '' }}>● LIVE (On Stage)</option>
                                    <option value="completed" {{ $item->status === 'completed' ? 'selected' : '' }}>✓ Completed</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.program.status', $item->id) }}" style="display: flex; align-items: center; gap: 0.3rem;">
                                @csrf
                                <input type="hidden" name="status" value="{{ $item->status }}">
                                <input type="number" name="time_penalty_marks" value="{{ $item->time_penalty_marks }}" min="0" max="50" style="width: 55px; padding: 0.25rem; font-size: 0.85rem; text-align: center;" title="Deduction marks (e.g. 2, 5, 10, 15)">
                                <button type="submit" class="btn btn-sm btn-secondary" style="padding: 0.25rem 0.45rem;" title="Save Penalty">💾</button>
                            </form>
                        </td>
                        <td style="text-align: right;">
                            <form method="POST" action="{{ route('admin.program.delete', $item->id) }}" onsubmit="return confirm('Remove this schedule activity?')" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" style="padding: 0.25rem 0.55rem;">🗑️</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            No schedule activities found for the selected day/category.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Add New Activity -->
<div id="new-activity-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 100; backdrop-filter: blur(8px);">
    <div style="max-width: 540px; margin: 5vh auto; background: #0f172a; border: 1px solid var(--border-card); border-radius: 16px; padding: 2rem; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-family: var(--font-display); font-size: 1.35rem; font-weight: 800; color: #fff;">
                ➕ Schedule New Activity
            </h3>
            <button type="button" onclick="document.getElementById('new-activity-modal').style.display='none'" style="background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>

        <form method="POST" action="{{ route('admin.program.store') }}" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Activity Title</label>
                <input type="text" name="activity_title" required placeholder="e.g. Choir Competition: St. Theresa's" style="width: 100%;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Day Name</label>
                    <select name="day_name" required style="width: 100%;">
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Event Date</label>
                    <input type="date" name="event_date" value="2026-08-18" required style="width: 100%;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Start Time</label>
                    <input type="time" name="scheduled_start_time" required style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">End Time</label>
                    <input type="time" name="scheduled_end_time" required style="width: 100%;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Category</label>
                    <select name="category_id" style="width: 100%;">
                        <option value="">-- Optional / General --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Parish</label>
                    <select name="parish_id" style="width: 100%;">
                        <option value="">-- Optional / Unassigned --</option>
                        @foreach($parishes as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Venue</label>
                    <input type="text" name="venue" value="Main Stage" required style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Performance Order</label>
                    <input type="number" name="performance_order" placeholder="e.g. 1" style="width: 100%;">
                </div>
            </div>

            <input type="hidden" name="status" value="scheduled">

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('new-activity-modal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Activity</button>
            </div>
        </form>
    </div>
</div>
@endsection
