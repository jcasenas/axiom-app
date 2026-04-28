@extends('admin.layout')
@section('title', 'Departments')

@push('styles')
<style>
.btn-add { background:#2a3050; color:white; border:none; border-radius:8px; padding:9px 20px; font-family:'Outfit',sans-serif; font-size:.84rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; margin-bottom:20px; transition:background .2s; }
.btn-add:hover { background:#3b4f7a; }
.btn-add svg { width:15px; height:15px; }

/* Modal */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(10,10,20,.5); z-index:300; align-items:center; justify-content:center; }
.modal-overlay.open { display:flex; }
.modal-box { background:white; border-radius:12px; padding:28px; max-width:420px; width:90%; box-shadow:0 20px 50px rgba(0,0,0,.25); animation:popIn .2s ease; }
@keyframes popIn { from{transform:scale(.92);opacity:0} to{transform:scale(1);opacity:1} }
.modal-title { font-size:1.05rem; font-weight:700; color:#1a1a2e; margin-bottom:18px; text-align:center; padding-bottom:14px; border-bottom:1px solid #f0eef8; }
.field { display:flex; flex-direction:column; gap:5px; margin-bottom:14px; }
.field input, .field textarea {
    background:#f5f4fc; border:1.5px solid #e0def0; border-radius:8px;
    padding:11px 14px; font-family:'Outfit',sans-serif; font-size:.9rem; color:#1a1a2e;
    outline:none; transition:border-color .2s;
}
.field input:focus, .field textarea:focus { border-color:#a8a4e0; background:#faf8ff; }
.field input::placeholder, .field textarea::placeholder { color:#a8a4c0; }
.field textarea { resize:vertical; min-height:80px; }
.modal-actions { display:flex; gap:10px; justify-content:center; margin-top:8px; }
.btn-m-cancel { background:white; color:#3a3a5a; border:2px solid #e0def0; border-radius:8px; padding:9px 28px; font-family:'Outfit',sans-serif; font-size:.88rem; font-weight:600; cursor:pointer; transition:background .15s; }
.btn-m-cancel:hover { background:#f5f4fc; }
.btn-m-submit { background:#2a3050; color:white; border:none; border-radius:8px; padding:9px 28px; font-family:'Outfit',sans-serif; font-size:.88rem; font-weight:600; cursor:pointer; transition:background .15s; }
.btn-m-submit:hover { background:#3b4f7a; }

.confirm-body { font-size:.86rem; color:#5a5a7a; margin-bottom:22px; line-height:1.5; }
.btn-m-del { background:#e74c3c; color:white; border:none; border-radius:8px; padding:9px 28px; font-family:'Outfit',sans-serif; font-size:.88rem; font-weight:600; cursor:pointer; }
</style>
@endpush

@section('content')
<div class="content-card">

    @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash flash-error">{{ session('error') }}</div>
    @endif

    <button class="btn-add" onclick="openAddModal()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Department
    </button>

    @if($departments->isEmpty())
        <div style="text-align:center;padding:32px;color:#8884a8;font-size:.88rem;">No departments found.</div>
    @else
    <table class="data-table">
        <thead><tr>
            <th>#</th><th>Department</th><th>Number of Users</th><th>Action</th>
        </tr></thead>
        <tbody>
        @foreach($departments as $i => $dept)
        <tr>
            <td>{{ $departments->firstItem() + $i }}</td>
            <td>
                <div style="font-weight:600;">{{ $dept->department_name }}</div>
                @if($dept->description)
                    <div style="font-size:.77rem;color:#8884a8;margin-top:2px;">{{ $dept->description }}</div>
                @endif
            </td>
            <td>{{ $dept->users_count }}</td>
            <td>
                {{-- Edit --}}
                <button class="action-btn btn-edit" title="Edit"
                    onclick="openEditModal({{ $dept->department_id }}, '{{ addslashes($dept->department_name) }}', '{{ addslashes($dept->description ?? '') }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                {{-- Delete --}}
                <button class="action-btn btn-deactivate" title="Delete"
                    onclick="openDeleteModal({{ $dept->department_id }}, '{{ addslashes($dept->department_name) }}', {{ $dept->users_count }})">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>

    <div class="pagination-row">
        @if($departments->onFirstPage())
            <span style="color:#ccc;display:flex;align-items:center;gap:4px;"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Previous</span>
        @else
            <a href="{{ $departments->previousPageUrl() }}"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Previous</a>
        @endif
        <span style="font-size:.8rem;color:#aaa;">Page {{ $departments->currentPage() }} of {{ $departments->lastPage() }}</span>
        @if($departments->hasMorePages())
            <a href="{{ $departments->nextPageUrl() }}">Next <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>
        @else
            <span style="color:#ccc;display:flex;align-items:center;gap:4px;">Next <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></span>
        @endif
    </div>
    @endif
</div>

{{-- ADD MODAL --}}
<div class="modal-overlay" id="addModal">
  <div class="modal-box">
    <div class="modal-title">ADD DEPARTMENT</div>
    <form method="POST" action="{{ route('admin.departments.store') }}">
      @csrf
      <div class="field"><input type="text" name="department_name" placeholder="Department Name" required></div>
      <div class="field"><textarea name="description" placeholder="Description (Optional)"></textarea></div>
      <div class="modal-actions">
        <button type="button" class="btn-m-cancel" onclick="closeModal('addModal')">Cancel</button>
        <button type="submit" class="btn-m-submit">Submit</button>
      </div>
    </form>
  </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal-overlay" id="editModal">
  <div class="modal-box">
    <div class="modal-title">EDIT DEPARTMENT</div>
    <form method="POST" id="editDeptForm" action="">
      @csrf @method('PUT')
      <div class="field"><input type="text" name="department_name" id="editDeptName" placeholder="Department Name" required></div>
      <div class="field"><textarea name="description" id="editDeptDesc" placeholder="Description (Optional)"></textarea></div>
      <div class="modal-actions">
        <button type="button" class="btn-m-cancel" onclick="closeModal('editModal')">Cancel</button>
        <button type="submit" class="btn-m-submit">Save</button>
      </div>
    </form>
  </div>
</div>

{{-- DELETE CONFIRM MODAL --}}
<div class="modal-overlay" id="deleteModal">
  <div class="modal-box">
    <div class="modal-title">Delete Department</div>
    <div class="confirm-body" id="deleteBody"></div>
    <form id="deleteForm" method="POST">
      @csrf @method('DELETE')
      <div class="modal-actions">
        <button type="button" class="btn-m-cancel" onclick="closeModal('deleteModal')">Cancel</button>
        <button type="submit" class="btn-m-del" id="deleteConfirmBtn">Delete</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function closeModal(id){ document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(m=>m.addEventListener('click',e=>{if(e.target===m)m.classList.remove('open');}));

function openAddModal(){ document.getElementById('addModal').classList.add('open'); }

function openEditModal(id,name,desc){
    document.getElementById('editDeptForm').action = `/admin/departments/${id}`;
    document.getElementById('editDeptName').value  = name;
    document.getElementById('editDeptDesc').value  = desc;
    document.getElementById('editModal').classList.add('open');
}

function openDeleteModal(id,name,userCount){
    const btn  = document.getElementById('deleteConfirmBtn');
    const body = document.getElementById('deleteBody');
    if(userCount > 0){
        body.innerHTML = `<strong>${name}</strong> has <strong>${userCount}</strong> user(s) assigned and cannot be deleted. Please reassign or deactivate those users first.`;
        btn.style.display = 'none';
    } else {
        body.innerHTML = `Delete department <strong>${name}</strong>? This action cannot be undone.`;
        btn.style.display = 'inline-block';
        document.getElementById('deleteForm').action = `/admin/departments/${id}`;
    }
    document.getElementById('deleteModal').classList.add('open');
}
</script>
@endpush