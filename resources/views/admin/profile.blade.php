@extends('admin.layout')
@section('title', 'My Profile')

@push('styles')
<style>
.profile-title { font-size:1.5rem; font-weight:700; color:#1a1a2e; margin-bottom:28px; }

.profile-body {
    display:grid;
    grid-template-columns:220px 1fr;
    gap:40px;
    align-items:start;
}

/* Avatar column */
.avatar-col { display:flex; flex-direction:column; align-items:center; gap:10px; }

.avatar-circle {
    width:120px; height:120px; border-radius:50%;
    background:#d8d6e8; overflow:hidden;
    display:flex; align-items:center; justify-content:center;
    position:relative;
}

.avatar-circle img { width:100%; height:100%; object-fit:cover; }

.avatar-initials {
    font-size:2.2rem; font-weight:700; color:#8884a8;
    font-family:'Outfit',sans-serif;
}

.btn-photo, .btn-password {
    width:100%; background:#2a3050; color:white; border:none;
    border-radius:8px; padding:9px 16px; font-family:'Outfit',sans-serif;
    font-size:.82rem; font-weight:600; cursor:pointer;
    transition:background .2s;
}
.btn-photo:hover, .btn-password:hover { background:#3b4f7a; }

/* Info column */
.info-section { margin-bottom:28px; }
.info-section h3 { font-size:1.05rem; font-weight:700; color:#1a1a2e; margin-bottom:14px; }

.info-table { width:100%; border-collapse:collapse; }
.info-table tr { border-bottom:1px solid #f0eef8; }
.info-table tr:last-child { border-bottom:none; }
.info-table td { padding:10px 0; font-size:.9rem; }
.info-table td:first-child { color:#5a5a7a; width:160px; }
.info-table td:last-child  { font-weight:700; color:#1a1a2e; text-align:right; }

.summary-table td:first-child { color:#5a5a7a; }
.summary-num { font-size:1.1rem; }

/* Password modal */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(10,10,20,.5); z-index:300; align-items:center; justify-content:center; }
.modal-overlay.open { display:flex; }
.modal-box { background:white; border-radius:12px; padding:28px; max-width:400px; width:90%; box-shadow:0 20px 50px rgba(0,0,0,.25); animation:popIn .2s ease; }
@keyframes popIn { from{transform:scale(.92);opacity:0} to{transform:scale(1);opacity:1} }
.modal-title { font-size:1rem; font-weight:700; color:#1a1a2e; margin-bottom:18px; padding-bottom:12px; border-bottom:1px solid #f0eef8; }
.field { display:flex; flex-direction:column; gap:5px; margin-bottom:14px; }
.field label { font-size:.76rem; font-weight:600; color:#5a5a7a; text-transform:uppercase; letter-spacing:.04em; }
.field input { background:#f0eef8; border:1.5px solid transparent; border-radius:8px; padding:10px 12px; font-family:'Outfit',sans-serif; font-size:.88rem; color:#1a1a2e; outline:none; transition:border-color .2s; }
.field input:focus { border-color:#a8a4e0; background:#faf8ff; }
.modal-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:6px; }
.btn-cancel-modal { background:#f0eef8; color:#5a5a7a; border:none; border-radius:8px; padding:9px 20px; font-family:'Outfit',sans-serif; font-size:.84rem; font-weight:600; cursor:pointer; }
.btn-save-pw { background:#2a3050; color:white; border:none; border-radius:8px; padding:9px 20px; font-family:'Outfit',sans-serif; font-size:.84rem; font-weight:600; cursor:pointer; }
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

    <div class="profile-title">My Profile</div>

    <div class="profile-body">

        {{-- Avatar column --}}
        <div class="avatar-col">
            <div class="avatar-circle">
                @if(Auth::user()->profile_photo)
                    <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Profile Photo">
                @else
                    <span class="avatar-initials">
                        {{ strtoupper(substr(Auth::user()->full_name, 0, 1)) }}
                    </span>
                @endif
            </div>

            {{-- Change Photo --}}
            <form method="POST" action="{{ route('admin.profile.photo') }}" enctype="multipart/form-data" style="width:100%;">
                @csrf @method('PUT')
                <input type="file" name="photo" id="photoInput" accept="image/*"
                       style="display:none;" onchange="this.form.submit()">
                <button type="button" class="btn-photo"
                        onclick="document.getElementById('photoInput').click()">
                    Change Photo
                </button>
            </form>

            <button type="button" class="btn-password" onclick="openPasswordModal()">Change Password</button>
        </div>

        {{-- Info column --}}
        <div>
            {{-- Personal Information --}}
            <div class="info-section">
                <h3>Personal Information</h3>
                <table class="info-table">
                    <tr><td>Name</td>        <td>{{ Auth::user()->full_name }}</td></tr>
                    <tr><td>Email</td>       <td>{{ Auth::user()->email }}</td></tr>
                    <tr><td>Role</td>        <td>Admin</td></tr>
                    <tr><td>Registered</td>  <td>{{ Auth::user()->created_at->format('M d, Y') }}</td></tr>
                </table>
            </div>

            {{-- Summary --}}
            <div class="info-section">
                <h3>Summary</h3>
                <table class="info-table summary-table">
                    <tr>
                        <td>Total Users Managed</td>
                        <td class="summary-num">{{ $totalUsers }}</td>
                    </tr>
                    <tr>
                        <td>Total Books in Catalog</td>
                        <td class="summary-num">{{ $totalBooks }}</td>
                    </tr>
                    <tr>
                        <td>Last Login</td>
                        <td class="summary-num" style="font-size:.88rem;">
                            {{ Auth::user()->last_login_at
                                ? \Carbon\Carbon::parse(Auth::user()->last_login_at)
                                    ->setTimezone('Asia/Manila')
                                    ->format('m/d/Y g:iA')
                                : '—' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- Change Password Modal --}}
<div class="modal-overlay" id="passwordModal">
  <div class="modal-box">
    <div class="modal-title">Change Password</div>
    <form method="POST" action="{{ route('admin.profile.password') }}">
      @csrf @method('PUT')
      <div class="field">
        <label>Current Password</label>
        <input type="password" name="current_password" required autocomplete="current-password">
      </div>
      <div class="field">
        <label>New Password</label>
        <input type="password" name="password" required autocomplete="new-password" minlength="8">
      </div>
      <div class="field">
        <label>Confirm New Password</label>
        <input type="password" name="password_confirmation" required autocomplete="new-password">
      </div>
      <div class="modal-actions">
        <button type="button" class="btn-cancel-modal" onclick="closePasswordModal()">Cancel</button>
        <button type="submit" class="btn-save-pw">Save Password</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function openPasswordModal(){ document.getElementById('passwordModal').classList.add('open'); }
function closePasswordModal(){ document.getElementById('passwordModal').classList.remove('open'); }
document.getElementById('passwordModal').addEventListener('click', function(e){
    if(e.target === this) closePasswordModal();
});
</script>
@endpush