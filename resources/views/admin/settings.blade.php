@extends('admin.layout')
@section('title', 'System Settings')

@push('styles')
<style>
.settings-title { font-size:1.5rem; font-weight:700; color:#1a1a2e; margin-bottom:28px; }

.settings-table { width:100%; border-collapse:collapse; }
.settings-table tr { border-bottom:1px solid #f0eef8; }
.settings-table tr:last-child { border-bottom:none; }
.settings-table td { padding:16px 8px; vertical-align:middle; }
.settings-table td:first-child {
    font-size:.95rem; font-weight:500; color:#3a3a5a;
    width:280px;
}
.settings-table td:last-child { padding-left:24px; }

/* Inputs */
.setting-input {
    width:100%; max-width:460px;
    background:#f0eef8; border:1.5px solid #e0def0; border-radius:8px;
    padding:11px 14px; font-family:'Outfit',sans-serif; font-size:.92rem;
    color:#1a1a2e; outline:none; transition:border-color .2s, background .2s;
}
.setting-input:focus { border-color:#a8a4e0; background:#faf8ff; }

.setting-select {
    width:100%; max-width:460px;
    background:#f0eef8; border:1.5px solid #e0def0; border-radius:8px;
    padding:11px 36px 11px 14px; font-family:'Outfit',sans-serif; font-size:.92rem;
    color:#1a1a2e; outline:none; appearance:none; cursor:pointer;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%238884a8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
    background-repeat:no-repeat; background-position:right 12px center;
    transition:border-color .2s, background .2s;
}
.setting-select:focus { border-color:#a8a4e0; background-color:#faf8ff; }

/* Maintenance warning */
.maintenance-hint {
    font-size:.76rem; color:#e67e22; margin-top:5px; max-width:460px;
    display:none;
}
.maintenance-hint.show { display:block; }

/* Save button */
.btn-apply {
    background:#2a3050; color:white; border:none; border-radius:8px;
    padding:12px 36px; font-family:'Outfit',sans-serif; font-size:.92rem;
    font-weight:700; cursor:pointer; margin-top:28px;
    transition:background .2s, transform .15s;
}
.btn-apply:hover { background:#3b4f7a; transform:translateY(-1px); }
.btn-apply:active { transform:translateY(0); }
</style>
@endpush

@section('content')
<div class="content-card">

    @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif

    <div class="settings-title">System Settings</div>

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf @method('PUT')

        <table class="settings-table">
            <tr>
                <td>Library Name</td>
                <td>
                    <input type="text" name="library_name" class="setting-input"
                           value="{{ $settings['library_name'] ?? 'University of Mindanao Library' }}"
                           placeholder="Library display name" required>
                </td>
            </tr>
            <tr>
                <td>Borrow Window (Days)</td>
                <td>
                    <input type="number" name="borrow_window_days" class="setting-input"
                           value="{{ $settings['borrow_window_days'] ?? 7 }}"
                           min="1" max="365" required style="max-width:200px;">
                </td>
            </tr>
            <tr>
                <td>Max Borrows Per Student</td>
                <td>
                    <input type="number" name="max_borrows_student" class="setting-input"
                           value="{{ $settings['max_borrows_student'] ?? 3 }}"
                           min="1" max="20" required style="max-width:200px;">
                </td>
            </tr>
            <tr>
                <td>Max Borrows Per Faculty</td>
                <td>
                    <input type="number" name="max_borrows_faculty" class="setting-input"
                           value="{{ $settings['max_borrows_faculty'] ?? 5 }}"
                           min="1" max="20" required style="max-width:200px;">
                </td>
            </tr>
            <tr>
                <td>System Status</td>
                <td>
                    <select name="system_status" id="systemStatus" class="setting-select"
                            onchange="toggleMaintenanceHint(this.value)">
                        <option value="active"      {{ ($settings['system_status'] ?? 'active')==='active'      ?'selected':'' }}>Active</option>
                        <option value="maintenance" {{ ($settings['system_status'] ?? 'active')==='maintenance' ?'selected':'' }}>Maintenance</option>
                    </select>
                    <div class="maintenance-hint {{ ($settings['system_status'] ?? 'active')==='maintenance' ? 'show' : '' }}" id="maintenanceHint">
                        ⚠️ Setting to Maintenance will prevent all non-admin users from logging in.
                    </div>
                </td>
            </tr>
        </table>

        <button type="submit" class="btn-apply">Apply Changes</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
function toggleMaintenanceHint(val){
    document.getElementById('maintenanceHint').classList.toggle('show', val === 'maintenance');
}
</script>
@endpush