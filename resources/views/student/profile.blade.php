@extends('student.layout')

@section('title', 'My Profile')

@push('styles')
<style>
.modal-overlay { display: none; position: fixed; inset: 0; background: rgba(10,10,20,.5); z-index: 300; align-items: center; justify-content: center; }
.modal-overlay.open { display: flex; }
.modal-box { background: white; border-radius: 12px; padding: 28px; max-width: 400px; width: 90%; box-shadow: 0 20px 50px rgba(0,0,0,.25); animation: popIn .2s ease; }
@keyframes popIn { from{transform:scale(.92);opacity:0} to{transform:scale(1);opacity:1} }
.modal-title { font-size: 1rem; font-weight: 700; color: #1a1a2e; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid #f0eef8; }
.modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 6px; }

@media (max-width: 540px) {
    .modal-box { padding: 20px 16px; }
    .modal-actions { flex-direction: column-reverse; }
    .btn-cancel-modal, .btn-save-pw { width: 100%; text-align: center; padding: 12px; }
}
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
                    <img src="{{ Auth::user()->profile_photo }}" alt="Profile Photo">
                @else
                    <span class="avatar-initials">{{ strtoupper(substr(Auth::user()->full_name, 0, 1)) }}</span>
                @endif
            </div>

            <form method="POST" action="{{ route('student.profile.photo') }}" enctype="multipart/form-data" style="width:100%;">
                @csrf @method('PUT')
                <input type="file" name="photo" id="photoInput" accept="image/*"
                       style="display:none;" onchange="this.form.submit()">
                <button type="button" class="btn-photo" onclick="document.getElementById('photoInput').click()">
                    Change Photo
                </button>
            </form>

            <button type="button" class="btn-password" onclick="openPasswordModal()">Change Password</button>
        </div>

        {{-- Info column --}}
        <div>
            <div class="info-section">
                <h3>Personal Information</h3>
                <table class="info-table">
                    <tr><td>Name</td>       <td>{{ Auth::user()->full_name }}</td></tr>
                    <tr><td>Department</td> <td>{{ Auth::user()->department->department_name ?? '---' }}</td></tr>
                    <tr><td>Email</td>      <td>{{ Auth::user()->email }}</td></tr>
                    <tr><td>Country</td>    <td>Philippines</td></tr>
                </table>
            </div>

            <div class="info-section">
                <h3>Summary</h3>
                <table class="info-table">
                    <tr>
                        <td>Total Books Borrowed</td>
                        <td>{{ $totalBorrowed }}</td>
                    </tr>
                    <tr>
                        <td>Currently Borrowed</td>
                        <td>{{ $currentlyBorrowed }}</td>
                    </tr>
                    <tr>
                        <td>Last Login</td>
                        <td style="font-size:.88rem;">
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

<div class="modal-overlay" id="passwordModal">
  <div class="modal-box">
    <div class="modal-title">Change Password</div>
    <form method="POST" action="{{ route('student.profile.password') }}">
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
function openPasswordModal()  { document.getElementById('passwordModal').classList.add('open'); }
function closePasswordModal() { document.getElementById('passwordModal').classList.remove('open'); }
document.getElementById('passwordModal').addEventListener('click', function(e) {
    if (e.target === this) closePasswordModal();
});
</script>
@endpush