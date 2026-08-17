@extends('layouts.app')

@section('title', 'Admin - Parishes & Contingents Desk')

@section('content')
<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.5rem; color: #3b82f6; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">
            <span><a href="{{ route('admin.index') }}" style="color: #94a3b8; text-decoration: none;">⚙️ Admin</a> &bull; Contingent Operations</span>
        </div>
        <h2 style="font-family: var(--font-display); font-size: 2.2rem; font-weight: 800; margin-bottom: 0.25rem; letter-spacing: -0.02em;">
            ⛪ Parishes & Campsite Check-In Desk
        </h2>
        <p style="color: var(--text-muted);">Manage arriving contingents, update camper headcounts, and maintain patron/matron contacts.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('new-parish-modal').style.display='block'">
            ➕ Register New Parish
        </button>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="glass-card" style="margin-bottom: 1.5rem;">
    <form method="GET" action="{{ route('admin.parishes') }}" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 240px;">
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Search</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by Parish Name, Code or Patron..." style="width: 100%;">
        </div>

        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Deanery</label>
            <select name="deanery" onchange="this.form.submit()">
                <option value="">All Deaneries</option>
                @foreach($deaneries as $d)
                    <option value="{{ $d }}" {{ $deanery == $d ? 'selected' : '' }}>{{ $d }}</option>
                @endforeach
            </select>
        </div>

        <div style="margin-top: auto; display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary">Search</button>
            @if($search || $deanery)
                <a href="{{ route('admin.parishes') }}" class="btn btn-secondary">Reset</a>
            @endif
        </div>
    </form>
</div>

<!-- Parishes Table -->
<div class="glass-card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 70px;">Code</th>
                    <th>Parish Name</th>
                    <th>Deanery</th>
                    <th>Patron / Matron Details</th>
                    <th style="width: 140px;">Camp Contingent</th>
                    <th style="width: 160px; text-align: center;">Campsite Check-In</th>
                    <th style="text-align: right; width: 100px;">Edit Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse($parishes as $p)
                    <tr>
                        <td>
                            <strong style="color: #f59e0b; font-family: var(--font-display); font-size: 1rem;">{{ $p->code }}</strong>
                        </td>
                        <td>
                            <div style="font-weight: 700; font-size: 1.05rem; color: #fff;">{{ $p->name }}</div>
                        </td>
                        <td>
                            <span style="color: #cbd5e1; font-weight: 600;">{{ $p->deanery }}</span>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #e2e8f0;">{{ $p->patron_matron_name ?? 'Not specified' }}</div>
                            <div style="font-size: 0.8rem; color: #38bdf8;">📞 {{ $p->patron_contact ?? 'No phone' }}</div>
                        </td>
                        <td>
                            <span style="font-family: var(--font-display); font-size: 1.25rem; font-weight: 800; color: #fff;">
                                {{ $p->camp_contingent_count }}
                            </span>
                            <span style="color: var(--text-muted); font-size: 0.8rem;">campers</span>
                        </td>
                        <td style="text-align: center;">
                            <form method="POST" action="{{ route('admin.parishes.checkin', $p->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $p->camp_checked_in ? 'btn-primary' : 'btn-secondary' }}" style="width: 130px; font-weight: 700;">
                                    @if($p->camp_checked_in)
                                        ✓ Checked In
                                    @else
                                        ⏳ Mark Arrival
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td style="text-align: right;">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="openEditModal({{ json_encode($p) }})">
                                ✏️ Edit
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            No parishes found matching your search criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Register New Parish -->
<div id="new-parish-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 100; backdrop-filter: blur(8px);">
    <div style="max-width: 500px; margin: 8vh auto; background: #0f172a; border: 1px solid var(--border-card); border-radius: 16px; padding: 2rem; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-family: var(--font-display); font-size: 1.35rem; font-weight: 800; color: #fff;">
                ➕ Register Parish
            </h3>
            <button type="button" onclick="document.getElementById('new-parish-modal').style.display='none'" style="background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>

        <form method="POST" action="{{ route('admin.parishes.store') }}" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Parish Name</label>
                <select name="name" id="new_parish_name" required style="width: 100%;" onchange="autoFillParish(this.value)">
                    <option value="">-- Select Diocesan Parish --</option>
                    <optgroup label="1. Livingstone Deanery">
                        <option value="St. Theresa's Cathedral" data-code="STC" data-deanery="Livingstone Deanery">St. Theresa’s Cathedral (Livingstone)</option>
                        <option value="Christ the King Parish" data-code="CTK" data-deanery="Livingstone Deanery">Christ the King Parish (Maramba)</option>
                        <option value="Kazungula Parish" data-code="KZP" data-deanery="Livingstone Deanery">Kazungula Parish (Kazungula)</option>
                        <option value="Maria Regina Parish" data-code="MRP" data-deanery="Livingstone Deanery">Maria Regina Parish</option>
                        <option value="Our Lady of Angels Parish" data-code="OLA" data-deanery="Livingstone Deanery">Our Lady of Angels Parish</option>
                        <option value="St. Francis' Parish" data-code="SFP" data-deanery="Livingstone Deanery">St. Francis’ Parish</option>
                        <option value="St. Peter's Parish" data-code="SPP" data-deanery="Livingstone Deanery">St. Peter’s Parish</option>
                        <option value="St. Thomas the Apostle Parish" data-code="STP" data-deanery="Livingstone Deanery">St. Thomas the Apostle Parish</option>
                    </optgroup>
                    <optgroup label="2. Sesheke Deanery">
                        <option value="St. Kizito's Sesheke Parish" data-code="SKS" data-deanery="Sesheke Deanery">St. Kizito’s Sesheke Parish</option>
                        <option value="St. Fidelis' Sichili Parish" data-code="SFS" data-deanery="Sesheke Deanery">St. Fidelis’ Sichili Parish</option>
                        <option value="St. Mary's Njoko Parish" data-code="SMN" data-deanery="Sesheke Deanery">St. Mary’s Njoko Parish</option>
                        <option value="St. Arnold Janssen's Mwandi Parish" data-code="SAJ" data-deanery="Sesheke Deanery">St. Arnold Janssen’s Mwandi Parish</option>
                        <option value="Nawinda Parish" data-code="NWP" data-deanery="Sesheke Deanery">Nawinda Parish</option>
                    </optgroup>
                    <optgroup label="3. Sioma Deanery">
                        <option value="Lusu Parish" data-code="LSP" data-deanery="Sioma Deanery">Lusu Parish</option>
                        <option value="Sioma Parish" data-code="SMP" data-deanery="Sioma Deanery">Sioma Parish</option>
                        <option value="Shangombo Parish" data-code="SGP" data-deanery="Sioma Deanery">Shangombo Parish</option>
                    </optgroup>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Parish Code</label>
                    <input type="text" id="new_parish_code" name="code" required placeholder="e.g. SJP" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Deanery</label>
                    <select id="new_parish_deanery" name="deanery" required style="width: 100%;">
                        <option value="Livingstone Deanery">Livingstone Deanery</option>
                        <option value="Sesheke Deanery">Sesheke Deanery</option>
                        <option value="Sioma Deanery">Sioma Deanery</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Patron / Matron Name</label>
                    <input type="text" name="patron_matron_name" placeholder="Full name" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Contact Phone</label>
                    <input type="text" name="patron_contact" placeholder="+260..." style="width: 100%;">
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Contingent Size (Campers Count)</label>
                <input type="number" name="camp_contingent_count" value="25" min="0" required style="width: 100%;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('new-parish-modal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Parish</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Parish Details -->
<div id="edit-parish-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 100; backdrop-filter: blur(8px);">
    <div style="max-width: 500px; margin: 8vh auto; background: #0f172a; border: 1px solid var(--border-card); border-radius: 16px; padding: 2rem; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-family: var(--font-display); font-size: 1.35rem; font-weight: 800; color: #fff;">
                ✏️ Edit Parish Details
            </h3>
            <button type="button" onclick="document.getElementById('edit-parish-modal').style.display='none'" style="background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>

        <form id="edit-parish-form" method="POST" action="" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf
            @method('PUT')
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Parish Name</label>
                <input type="text" id="edit_name" name="name" required style="width: 100%;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Parish Code</label>
                    <input type="text" id="edit_code" name="code" required style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Deanery</label>
                    <select id="edit_deanery" name="deanery" required style="width: 100%;">
                        <option value="Livingstone Deanery">Livingstone Deanery</option>
                        <option value="Sesheke Deanery">Sesheke Deanery</option>
                        <option value="Sioma Deanery">Sioma Deanery</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Patron / Matron Name</label>
                    <input type="text" id="edit_patron_matron_name" name="patron_matron_name" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Contact Phone</label>
                    <input type="text" id="edit_patron_contact" name="patron_contact" style="width: 100%;">
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Contingent Count (Campers)</label>
                <input type="number" id="edit_camp_contingent_count" name="camp_contingent_count" min="0" required style="width: 100%;">
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem;">
                <input type="checkbox" id="edit_camp_checked_in" name="camp_checked_in" value="1">
                <label for="edit_camp_checked_in" style="font-size: 0.85rem; font-weight: 600; color: #fff;">Contingent is already Checked-In to Camp</label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('edit-parish-modal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Details</button>
            </div>
        </form>
    </div>
</div>

<script>
function autoFillParish(val) {
    const select = document.getElementById('new_parish_name');
    const selectedOpt = select.options[select.selectedIndex];
    if (selectedOpt && selectedOpt.dataset.code) {
        document.getElementById('new_parish_code').value = selectedOpt.dataset.code;
        document.getElementById('new_parish_deanery').value = selectedOpt.dataset.deanery;
    }
}

function openEditModal(parish) {
    document.getElementById('edit-parish-form').action = '/admin/parishes/' + parish.id;
    document.getElementById('edit_name').value = parish.name;
    document.getElementById('edit_code').value = parish.code;
    document.getElementById('edit_deanery').value = parish.deanery;
    document.getElementById('edit_patron_matron_name').value = parish.patron_matron_name || '';
    document.getElementById('edit_patron_contact').value = parish.patron_contact || '';
    document.getElementById('edit_camp_contingent_count').value = parish.camp_contingent_count || 25;
    document.getElementById('edit_camp_checked_in').checked = parish.camp_checked_in ? true : false;
    document.getElementById('edit-parish-modal').style.display = 'block';
}
</script>
@endsection
