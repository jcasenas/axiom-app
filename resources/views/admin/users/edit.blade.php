@extends('admin.layout')
@section('title', 'Edit User')

@push('styles')
<style>
    .page-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 28px;
    }

    .page-title span {
        font-weight: 400;
        color: #8884a8;
        font-size: 1rem;
    }

    /* Form fields */
    .field {
        margin-bottom: 18px;
    }

    .field label {
        display: block;
        font-size: 0.76rem;
        font-weight: 600;
        color: #5a5a7a;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 7px;
    }

    .field small {
        font-size: 0.75rem;
        color: #a8a4e0;
        text-transform: none;
        letter-spacing: 0;
        font-weight: 400;
    }

    .field input,
    .field select {
        width: 100%;
        background: #f0eef8;
        border: 1.5px solid #e0def0;
        border-radius: 8px;
        padding: 11px 14px;
        font-family: 'Outfit', sans-serif;
        font-size: 0.9rem;
        color: #1a1a2e;
        outline: none;
        transition: border-color 0.2s, background 0.2s;
        appearance: none;
    }

    .field input:focus,
    .field select:focus {
        border-color: #a8a4e0;
        background: #faf8ff;
    }

    .field input::placeholder {
        color: #b0acc8;
    }

    .field select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238884a8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-color: #f0eef8;
        padding-right: 36px;
        cursor: pointer;
    }

    /* Two-column grid */
    .form-grid2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    /* Password section divider */
    .section-divider {
        border: none;
        border-top: 1px dashed #e0def0;
        margin: 24px 0 20px;
    }

    .section-label {
        font-size: 0.76rem;
        font-weight: 700;
        color: #a8a4e0;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 16px;
    }

    /* Validation error per field */
    .field-error {
        font-size: 0.76rem;
        color: #e74c3c;
        margin-top: 5px;
    }

    /* Form actions */
    .form-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 32px;
        padding-top: 20px;
        border-top: 1px solid #f0eef8;
    }

    .btn-update {
        background: #2a3050;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 11px 28px;
        font-family: 'Outfit', sans-serif;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, transform 0.15s;
    }

    .btn-update:hover  { background: #3b4f7a; transform: translateY(-1px); }
    .btn-update:active { transform: translateY(0); }

    .btn-back {
        background: #f0eef8;
        color: #5a5a7a;
        border: none;
        border-radius: 8px;
        padding: 11px 24px;
        font-family: 'Outfit', sans-serif;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: background 0.15s;
    }

    .btn-back:hover { background: #e0def0; }

    /* User meta pill at top */
    .user-meta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f0eef8;
        border-radius: 20px;
        padding: 5px 14px;
        font-size: 0.78rem;
        color: #5a5a7a;
        margin-bottom: 24px;
    }

    .user-meta .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #a8a4e0;
    }
</style>
@endpush

@section('content')
<div class="content-card">

    {{-- Flash / Errors --}}
    @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="flash flash-error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Header --}}
    <div class="page-title">
        Edit User <span>/ {{ $user->full_name }}</span>
    </div>

    {{-- Meta pill --}}
    <div class="user-meta">
        <span class="dot"></span>
        {{ $user->email }}
        &nbsp;·&nbsp;
        Registered {{ $user->created_at->format('M d, Y') }}
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user->user_id) }}">
        @csrf
        @method('PUT')

        {{-- Full Name --}}
        <div class="field">
            <label>Full Name</label>
            <input type="text" name="full_name"
                   value="{{ old('full_name', $user->full_name) }}"
                   placeholder="Full name" required>
            @error('full_name')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="field">
            <label>Email Address</label>
            <input type="email" name="email"
                   value="{{ old('email', $user->email) }}"
                   placeholder="email@example.com" required>
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Role + Department --}}
        <div class="form-grid2">
            <div class="field">
                <label>Role</label>
                <select name="role_id" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->role_id }}"
                            {{ old('role_id', $user->role_id) == $role->role_id ? 'selected' : '' }}>
                            {{ $role->role_name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label>Department</label>
                <select name="department_id">
                    <option value="">No Department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->department_id }}"
                            {{ old('department_id', $user->department_id) == $dept->department_id ? 'selected' : '' }}>
                            {{ $dept->department_name }}
                        </option>
                    @endforeach
                </select>
                @error('department_id')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Password section --}}
        <hr class="section-divider">
        <div class="section-label">Change Password</div>
        <div class="form-grid2">
            <div class="field">
                <label>New Password <small>(leave blank to keep current)</small></label>
                <input type="password" name="password"
                       placeholder="Min. 8 characters"
                       autocomplete="new-password">
                @error('password')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label>Confirm New Password</label>
                <input type="password" name="password_confirmation"
                       placeholder="Repeat new password"
                       autocomplete="new-password">
            </div>
        </div>

        {{-- Actions --}}
        <div class="form-actions">
            <button type="submit" class="btn-update">Update User</button>
            <a href="{{ route('admin.users.index') }}" class="btn-back">Cancel</a>
        </div>

    </form>
</div>
@endsection