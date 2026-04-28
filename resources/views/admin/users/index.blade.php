@extends('admin.layout')
@section('title', 'Manage Users')

@push('styles')
<style>
    .btn-add {
        background: #2a3050;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 9px 20px;
        font-family: 'Outfit', sans-serif;
        font-size: 0.84rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.2s;
        margin-bottom: 20px;
    }
    .btn-add:hover { background: #3b4f7a; }
    .btn-add svg   { width: 16px; height: 16px; }

    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(10,10,20,0.5);
        z-index: 300;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.open { display: flex; }

    .modal-box {
        background: white;
        border-radius: 12px;
        padding: 28px 28px 24px;
        max-width: 460px;
        width: 90%;
        box-shadow: 0 20px 50px rgba(0,0,0,0.25);
        animation: popIn 0.2s ease;
    }
    @keyframes popIn {
        from { transform: scale(0.92); opacity: 0; }
        to   { transform: scale(1);    opacity: 1; }
    }

    .modal-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f0eef8;
    }

    .modal-body {
        font-size: 0.86rem;
        color: #5a5a7a;
        margin-bottom: 22px;
        line-height: 1.5;
    }

    .field { margin-bottom: 14px; }
    .field label {
        font-size: 0.76rem;
        font-weight: 600;
        color: #5a5a7a;
        display: block;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .field input,
    .field select {
        width: 100%;
        padding: 10px 12px;
        background: #f0eef8;
        border: 1.5px solid #e0def0;
        border-radius: 8px;
        font-family: 'Outfit', sans-serif;
        font-size: 0.88rem;
        color: #1a1a2e;
        outline: none;
        appearance: none;
        transition: border-color 0.2s;
    }
    .field input:focus,
    .field select:focus { border-color: #a8a4e0; background: #faf8ff; }
    .field select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238884a8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-color: #f0eef8;
        cursor: pointer;
    }
    .field select:focus { background-color: #faf8ff; }

    .form-grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

    .modal-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
    }

    .btn-modal {
        border: none;
        border-radius: 8px;
        padding: 9px 20px;
        font-family: 'Outfit', sans-serif;
        font-size: 0.84rem;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.15s;
        display: inline-block;
        text-decoration: none;
    }
    .btn-modal:hover { opacity: 0.85; }
    .btn-cancel  { background: #f0eef8; color: #5a5a7a; }
    .btn-approve { background: #27ae60; color: white; }
    .btn-reject  { background: #e74c3c; color: white; }
    .btn-primary { background: #2a3050; color: white; }
</style>
@endpush

@section('content')
<div class="content-card">

    @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="flash flash-error">{{ $errors->first() }}</div>
    @endif

    <button class="btn-add" onclick="openCreateModal()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Add New User
    </button>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.users.index') }}">
        <div class="filter-row">
            <label>Department:</label>
            <select name="department" class="filter-select">
                <option value="all" {{ request('department','all')==='all'?'selected':'' }}>All</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->department_id }}"
                        {{ request('department') == $dept->department_id ? 'selected' : '' }}>
                        {{ $dept->department_name }}
                    </option>
                @endforeach
            </select>

            <label>Role:</label>
            <select name="role" class="filter-select">
                <option value="all" {{ request('role','all')==='all'?'selected':'' }}>All</option>
                @foreach($roles as $role)
                    <option value="{{ $role->role_id }}"
                        {{ request('role') == $role->role_id ? 'selected' : '' }}>
                        {{ $role->role_name }}
                    </option>
                @endforeach
            </select>

            <label>Status:</label>
            <select name="status" class="filter-select">
                <option value="all"      {{ request('status','all')==='all'     ?'selected':'' }}>All</option>
                <option value="pending"  {{ request('status')==='pending'        ?'selected':'' }}>Pending</option>
                <option value="active"   {{ request('status')==='active'         ?'selected':'' }}>Active</option>
                <option value="inactive" {{ request('status')==='inactive'       ?'selected':'' }}>Inactive</option>
            </select>

            <button type="submit" class="btn-filter">Apply Filter</button>
        </div>
    </form>

    @if($users->isEmpty())
        <div style="text-align:center;padding:32px;color:#8884a8;font-size:0.88rem;">
            No users found matching the selected filters.
        </div>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Role</th>
                    <th>Email Address</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->full_name }}</td>
                    <td>{{ $user->department->department_name ?? '—' }}</td>
                    <td>{{ $user->role->role_name ?? '—' }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge
                            {{ $user->account_status === 'active'   ? 'badge-active'   : '' }}
                            {{ $user->account_status === 'pending'  ? 'badge-pending'  : '' }}
                            {{ $user->account_status === 'inactive' ? 'badge-inactive' : '' }}">
                            {{ ucfirst($user->account_status) }}
                        </span>
                    </td>
                    <td>
                        {{-- VIEW --}}
                        <button class="action-btn btn-view" title="View"
                            onclick="openViewModal(
                                '{{ addslashes($user->full_name) }}',
                                '{{ addslashes($user->department->department_name ?? '—') }}',
                                '{{ addslashes($user->role->role_name ?? '—') }}',
                                '{{ $user->email }}',
                                '{{ $user->account_status }}',
                                '{{ $user->created_at->format('M d, Y') }}'
                            )">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>

                        @if($user->account_status === 'pending')
                            <button class="action-btn btn-approve" title="Approve"
                                onclick="openConfirm('approve', {{ $user->user_id }}, '{{ addslashes($user->full_name) }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                            <button class="action-btn btn-reject" title="Reject"
                                onclick="openConfirm('reject', {{ $user->user_id }}, '{{ addslashes($user->full_name) }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>

                        @elseif($user->account_status === 'active')
                            <a href="{{ route('admin.users.edit', $user->user_id) }}"
                               class="action-btn btn-edit" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <button class="action-btn btn-deactivate" title="Deactivate"
                                onclick="openConfirm('deactivate', {{ $user->user_id }}, '{{ addslashes($user->full_name) }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>

                        @elseif($user->account_status === 'inactive')
                            <button class="action-btn btn-approve" title="Reactivate"
                                onclick="openConfirm('approve', {{ $user->user_id }}, '{{ addslashes($user->full_name) }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination-row">
            @if($users->onFirstPage())
                <span style="color:#ccc;display:flex;align-items:center;gap:4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Previous
                </span>
            @else
                <a href="{{ $users->previousPageUrl() }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Previous
                </a>
            @endif
            <span style="font-size:0.8rem;color:#aaa;">Page {{ $users->currentPage() }} of {{ $users->lastPage() }}</span>
            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}">
                    Next
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            @else
                <span style="color:#ccc;display:flex;align-items:center;gap:4px;">
                    Next
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </span>
            @endif
        </div>
    @endif

</div>

{{-- ── CONFIRM ACTION MODAL ── --}}
<div class="modal-overlay" id="confirmModal">
    <div class="modal-box" style="max-width:380px;">
        <div class="modal-title" id="confirmTitle">Confirm Action</div>
        <div class="modal-body"  id="confirmBody">Are you sure?</div>
        <div class="modal-actions">
            <button type="button" class="btn-modal btn-cancel"
                    onclick="closeModal('confirmModal')">Cancel</button>
            {{-- PATCH spoofing added — routes are now PATCH not POST --}}
            <form id="confirmForm" method="POST" style="display:inline;">
                @csrf
                @method('PATCH')
                <button type="submit" id="confirmBtn" class="btn-modal btn-primary">Confirm</button>
            </form>
        </div>
    </div>
</div>

{{-- ── VIEW DETAILS MODAL ── --}}
<div class="modal-overlay" id="viewModal">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-title">User Details</div>
        <table style="width:100%;font-size:0.85rem;margin:14px 0 20px;border-collapse:collapse;">
            <tr><td style="padding:7px 0;color:#8884a8;width:120px;">Full Name</td>  <td id="vName"   style="font-weight:500;"></td></tr>
            <tr><td style="padding:7px 0;color:#8884a8;">Department</td>             <td id="vDept"></td></tr>
            <tr><td style="padding:7px 0;color:#8884a8;">Role</td>                   <td id="vRole"></td></tr>
            <tr><td style="padding:7px 0;color:#8884a8;">Email</td>                  <td id="vEmail"></td></tr>
            <tr><td style="padding:7px 0;color:#8884a8;">Status</td>                 <td id="vStatus"></td></tr>
            <tr><td style="padding:7px 0;color:#8884a8;">Registered</td>             <td id="vDate"></td></tr>
        </table>
        <div class="modal-actions">
            <button type="button" class="btn-modal btn-cancel"
                    onclick="closeModal('viewModal')">Close</button>
        </div>
    </div>
</div>

{{-- ── CREATE USER MODAL ── --}}
<div class="modal-overlay" id="createModal">
    <div class="modal-box">
        <div class="modal-title">Add New User</div>
        <form method="POST" action="{{ route('admin.users.store') }}" id="createForm">
            @csrf

            <div class="field">
                <label>Full Name</label>
                <input type="text" name="full_name" required placeholder="e.g. Juan dela Cruz">
            </div>

            <div class="field">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="email@example.com">
            </div>

            <div class="form-grid2">
                <div class="field">
                    <label>Role</label>
                    <select name="role_id" required>
                        <option value="">Select role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Department</label>
                    <select name="department_id">
                        <option value="">No Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->department_id }}">{{ $dept->department_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-grid2">
                <div class="field">
                    <label>Password</label>
                    <input type="password" name="password" required minlength="8" placeholder="Min. 8 characters">
                </div>
                <div class="field">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" required placeholder="Repeat password">
                </div>
            </div>

            <div class="field">
                <label>Initial Status</label>
                <select name="account_status">
                    <option value="active">Active</option>
                    <option value="pending">Pending Approval</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-modal btn-cancel"
                        onclick="closeModal('createModal')">Cancel</button>
                <button type="submit" class="btn-modal btn-primary">Create User</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function closeModal(id) {
    document.getElementById(id).classList.remove('open');
}

document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('open');
    });
});

const actionConfig = {
    approve: {
        title:  'Approve Account',
        body:   'Approve the account of <strong>{name}</strong>? They will be able to log in immediately.',
        label:  'Approve',
        style:  'background:#27ae60;color:white;',
    },
    reject: {
        title:  'Reject Registration',
        body:   'Reject the registration of <strong>{name}</strong>? Their status will be set to Inactive.',
        label:  'Reject',
        style:  'background:#e74c3c;color:white;',
    },
    deactivate: {
        title:  'Deactivate Account',
        body:   'Deactivate <strong>{name}</strong>? They will no longer be able to log in.',
        label:  'Deactivate',
        style:  'background:#e74c3c;color:white;',
    },
};

// Updated to use PATCH routes — matches web.php registration
const actionRoutes = {
    approve:    '/admin/users/{id}/approve',
    reject:     '/admin/users/{id}/reject',
    deactivate: '/admin/users/{id}/deactivate',
};

function openConfirm(action, userId, name) {
    const cfg = actionConfig[action];

    document.getElementById('confirmTitle').textContent = cfg.title;
    document.getElementById('confirmBody').innerHTML    = cfg.body.replace('{name}', name);

    const btn = document.getElementById('confirmBtn');
    btn.textContent = cfg.label;
    btn.style.cssText = 'border:none;border-radius:8px;padding:9px 20px;font-family:Outfit,sans-serif;font-size:.84rem;font-weight:600;cursor:pointer;' + cfg.style;

    document.getElementById('confirmForm').action =
        actionRoutes[action].replace('{id}', userId);

    document.getElementById('confirmModal').classList.add('open');
}

function openViewModal(name, dept, role, email, status, date) {
    document.getElementById('vName').textContent   = name;
    document.getElementById('vDept').textContent   = dept;
    document.getElementById('vRole').textContent   = role;
    document.getElementById('vEmail').textContent  = email;
    document.getElementById('vStatus').textContent = status.charAt(0).toUpperCase() + status.slice(1);
    document.getElementById('vDate').textContent   = date;
    document.getElementById('viewModal').classList.add('open');
}

function openCreateModal() {
    document.getElementById('createForm').reset();
    document.getElementById('createModal').classList.add('open');
}
</script>
@endpush