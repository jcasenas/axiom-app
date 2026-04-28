@extends('librarian.layout')
@section('title', 'Borrow Records')

@push('styles')
<style>
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px; }
.btn-pdf { background:#2a3050; color:white; border:none; border-radius:8px; padding:9px 18px; font-family:'Outfit',sans-serif; font-size:.82rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; text-decoration:none; transition:background .2s; }
.btn-pdf:hover { background:#3b4f7a; }
.btn-pdf svg { width:15px; height:15px; }

.modal-overlay { display:none; position:fixed; inset:0; background:rgba(10,10,20,.5); z-index:300; align-items:center; justify-content:center; }
.modal-overlay.open { display:flex; }
.modal-box { background:white; border-radius:12px; padding:32px 36px 28px; max-width:480px; width:90%; box-shadow:0 20px 50px rgba(0,0,0,.25); animation:popIn .2s ease; }
@keyframes popIn { from{transform:scale(.92);opacity:0} to{transform:scale(1);opacity:1} }
.modal-title { font-size:1.1rem; font-weight:700; color:#1a1a2e; text-align:center; margin-bottom:20px; padding-bottom:14px; border-bottom:1px solid #f0eef8; letter-spacing:.06em; text-transform:uppercase; }
.detail-row { display:flex; justify-content:space-between; align-items:baseline; padding:8px 0; border-bottom:1px solid #f5f4fc; font-size:.87rem; }
.detail-row:last-of-type { border-bottom:none; }
.detail-row .d-label { color:#8884a8; flex-shrink:0; width:120px; }
.detail-row .d-value { font-weight:600; color:#1a1a2e; text-align:right; }
.modal-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:22px; flex-wrap:wrap; }

.btn-modal-exit    { background:#f0eef8; color:#5a5a7a; border:none; border-radius:8px; padding:10px 22px; font-family:'Outfit',sans-serif; font-size:.88rem; font-weight:600; cursor:pointer; transition:background .15s; }
.btn-modal-exit:hover    { background:#e0def0; }
.btn-modal-approve { background:#27ae60; color:white; border:none; border-radius:8px; padding:10px 22px; font-family:'Outfit',sans-serif; font-size:.88rem; font-weight:600; cursor:pointer; transition:background .15s; }
.btn-modal-approve:hover { background:#219a52; }
.btn-modal-reject  { background:#e74c3c; color:white; border:none; border-radius:8px; padding:10px 22px; font-family:'Outfit',sans-serif; font-size:.88rem; font-weight:600; cursor:pointer; transition:background .15s; }
.btn-modal-reject:hover  { background:#c0392b; }

/* Confirmation overlay — z-index above the details modal */
.confirm-overlay { display:none; position:fixed; inset:0; background:rgba(10,10,20,.35); z-index:400; align-items:center; justify-content:center; }
.confirm-overlay.open { display:flex; }
.confirm-box { background:white; border-radius:12px; padding:28px 32px 24px; max-width:380px; width:90%; box-shadow:0 20px 50px rgba(0,0,0,.3); animation:popIn .18s ease; }
.confirm-icon { width:44px; height:44px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 14px; }
.confirm-icon.approve { background:#d4edda; }
.confirm-icon.reject  { background:#fde8e8; }
.confirm-icon svg { width:22px; height:22px; }
.confirm-heading { font-size:.98rem; font-weight:700; color:#1a1a2e; text-align:center; margin-bottom:8px; }
.confirm-body    { font-size:.84rem; color:#5a5a7a; text-align:center; line-height:1.55; margin-bottom:22px; }
.confirm-actions { display:flex; gap:10px; justify-content:center; }
.btn-confirm-cancel { background:#f0eef8; color:#5a5a7a; border:none; border-radius:8px; padding:9px 22px; font-family:'Outfit',sans-serif; font-size:.86rem; font-weight:600; cursor:pointer; transition:background .15s; }
.btn-confirm-cancel:hover { background:#e0def0; }
.btn-confirm-ok { border:none; border-radius:8px; padding:9px 22px; font-family:'Outfit',sans-serif; font-size:.86rem; font-weight:600; cursor:pointer; color:white; transition:opacity .15s; }
.btn-confirm-ok:hover { opacity:.88; }
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

    @if(request('filter') === 'expiring_today')
        <div class="flash" style="background:#8b2e2e;color:white;border:1px solid #6b1f1f;margin-bottom:16px;">
            ⚠️ <strong>Expiring Today</strong> — Showing active borrows whose access expires on
            <strong>{{ today()->format('M d, Y') }}</strong>. These have not expired yet.
        </div>
    @endif

    <div class="page-header">
        <div>
            <form method="GET" action="{{ route('librarian.borrows.index') }}" style="display:inline;">
                <div class="filter-row" style="margin-bottom:0;">
                    <label>Department:</label>
                    <select name="department" class="filter-select">
                        <option value="all" {{ request('department','all')==='all'?'selected':'' }}>All</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->department_id }}" {{ request('department')==$dept->department_id?'selected':'' }}>
                                {{ $dept->department_name }}
                            </option>
                        @endforeach
                    </select>
                    <label>Status:</label>
                    <select name="status" class="filter-select">
                        <option value="all"       {{ request('status','all')==='all'      ?'selected':'' }}>All</option>
                        <option value="pending"   {{ request('status')==='pending'         ?'selected':'' }}>Pending</option>
                        <option value="active"    {{ request('status')==='active'          ?'selected':'' }}>Active</option>
                        <option value="due_soon"  {{ request('status')==='due_soon'        ?'selected':'' }}>Due Soon</option>
                        <option value="expired"   {{ request('status')==='expired'         ?'selected':'' }}>Expired</option>
                        <option value="cancelled" {{ request('status')==='cancelled'       ?'selected':'' }}>Cancelled</option>
                    </select>
                    <button type="submit" class="btn-filter">Apply Filter</button>
                </div>
            </form>
        </div>
        <a href="{{ route('librarian.borrows.pdf', request()->query()) }}" class="btn-pdf" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Generate PDF Report
        </a>
    </div>

    @if($borrows->isEmpty())
        <div style="text-align:center;padding:32px;color:#8884a8;font-size:.88rem;">No borrow records found.</div>
    @else
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th><th>Name</th><th>Department</th>
                <th>Title</th><th>Due Date</th><th>Status</th><th>Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($borrows as $i => $b)
        <tr>
            <td>{{ $borrows->firstItem() + $i }}</td>
            <td>{{ $b->user->full_name ?? '—' }}</td>
            <td>{{ $b->user->department->department_name ?? '—' }}</td>
            <td>{{ $b->ebook->title ?? '—' }}</td>
            <td>{{ $b->due_date ? $b->due_date->format('M d, Y') : '—' }}</td>
            <td>
                @php $s = $b->status; @endphp
                <span class="badge
                    {{ $s==='active'   ?'badge-active'   :'' }}
                    {{ $s==='pending'  ?'badge-pending'  :'' }}
                    {{ $s==='expired'  ?'badge-expired'  :'' }}
                    {{ $s==='due_soon' ?'badge-due-soon' :'' }}
                    {{ $s==='cancelled'?'badge-inactive' :'' }}">
                    {{ ucfirst(str_replace('_',' ',$s)) }}
                </span>
            </td>
            <td>
                <button class="action-btn btn-view" title="View"
                    onclick="openView(
                        '{{ addslashes($b->user->full_name ?? '—') }}',
                        '{{ addslashes($b->user->department->department_name ?? '—') }}',
                        '{{ addslashes($b->user->email ?? '—') }}',
                        '{{ addslashes($b->ebook->title ?? '—') }}',
                        '{{ addslashes($b->ebook->author->author_name ?? '—') }}',
                        '{{ addslashes($b->ebook->category->category_name ?? '—') }}',
                        '{{ $b->ebook->format->format_type ?? '—' }}',
                        '{{ $b->borrow_date ? $b->borrow_date->format('M d, Y') : '—' }}',
                        '{{ $b->due_date   ? $b->due_date->format('M d, Y')   : '—' }}',
                        '{{ ucfirst(str_replace('_', ' ', $b->status)) }}',
                        '{{ $b->status }}',
                        {{ $b->borrow_id }}
                    )">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>

    <div class="pagination-row">
        @if($borrows->onFirstPage())
            <span style="color:#ccc;display:flex;align-items:center;gap:4px;"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Previous</span>
        @else
            <a href="{{ $borrows->previousPageUrl() }}"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Previous</a>
        @endif
        <span style="font-size:.8rem;color:#aaa;">Page {{ $borrows->currentPage() }} of {{ $borrows->lastPage() }}</span>
        @if($borrows->hasMorePages())
            <a href="{{ $borrows->nextPageUrl() }}">Next <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>
        @else
            <span style="color:#ccc;display:flex;align-items:center;gap:4px;">Next <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></span>
        @endif
    </div>
    @endif
</div>

{{-- ── BORROW DETAILS MODAL ── --}}
<div class="modal-overlay" id="viewModal">
  <div class="modal-box">
    <div class="modal-title">Borrow Details</div>
    <div class="detail-row"><span class="d-label">Name</span>        <span class="d-value" id="vName"></span></div>
    <div class="detail-row"><span class="d-label">Department</span>  <span class="d-value" id="vDept"></span></div>
    <div class="detail-row"><span class="d-label">Email</span>       <span class="d-value" id="vEmail"></span></div>
    <div class="detail-row"><span class="d-label">Book Title</span>  <span class="d-value" id="vTitle"></span></div>
    <div class="detail-row"><span class="d-label">Author</span>      <span class="d-value" id="vAuthor"></span></div>
    <div class="detail-row"><span class="d-label">Category</span>    <span class="d-value" id="vCat"></span></div>
    <div class="detail-row"><span class="d-label">Format</span>      <span class="d-value" id="vFmt"></span></div>
    <div class="detail-row"><span class="d-label">Borrow Date</span> <span class="d-value" id="vBorrowDate"></span></div>
    <div class="detail-row"><span class="d-label">Due Date</span>    <span class="d-value" id="vDueDate"></span></div>
    <div class="detail-row"><span class="d-label">Status</span>      <span class="d-value" id="vStatus"></span></div>

    <div class="modal-actions">
        <button id="btnAccept" type="button" class="btn-modal-approve"
                style="display:none;" onclick="openConfirm('approve')">Accept</button>
        <button id="btnReject" type="button" class="btn-modal-reject"
                style="display:none;" onclick="openConfirm('reject')">Reject</button>
        <button class="btn-modal-exit" onclick="closeModal('viewModal')">Exit</button>
    </div>
  </div>
</div>

{{-- ── CONFIRMATION MODAL ── --}}
<div class="confirm-overlay" id="confirmOverlay">
  <div class="confirm-box">

    <div id="confirmApproveContent">
        <div class="confirm-icon approve">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#27ae60" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div class="confirm-heading">Approve Borrow Request?</div>
        <p class="confirm-body">
            Access will be granted to <strong id="confirmBorrowerName"></strong> for
            "<strong id="confirmBookTitle"></strong>".
            This action cannot be undone.
        </p>
    </div>

    <div id="confirmRejectContent" style="display:none;">
        <div class="confirm-icon reject">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#e74c3c" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <div class="confirm-heading">Reject Borrow Request?</div>
        <p class="confirm-body">
            The request from <strong id="confirmBorrowerNameReject"></strong> for
            "<strong id="confirmBookTitleReject"></strong>" will be rejected.
            The borrower will be notified.
        </p>
    </div>

    <div class="confirm-actions">
        <button class="btn-confirm-cancel" onclick="closeConfirm()">Cancel</button>
        <form id="confirmForm" method="POST" action="" style="display:inline;">
            @csrf @method('PATCH')
            <button id="confirmOkBtn" type="submit" class="btn-confirm-ok">Confirm</button>
        </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
var _borrowId    = null;
var _borrowerName = '';
var _bookTitle    = '';

function closeModal(id) {
    document.getElementById(id).classList.remove('open');
}

function openView(name, dept, email, title, author, cat, fmt, borrowDate, dueDate, statusLabel, statusRaw, borrowId) {
    document.getElementById('vName').textContent       = name;
    document.getElementById('vDept').textContent       = dept;
    document.getElementById('vEmail').textContent      = email;
    document.getElementById('vTitle').textContent      = title;
    document.getElementById('vAuthor').textContent     = author;
    document.getElementById('vCat').textContent        = cat;
    document.getElementById('vFmt').textContent        = fmt;
    document.getElementById('vBorrowDate').textContent = borrowDate;
    document.getElementById('vDueDate').textContent    = dueDate;
    document.getElementById('vStatus').textContent     = statusLabel;

    _borrowId     = borrowId;
    _borrowerName = name;
    _bookTitle    = title;

    var showActions = statusRaw === 'pending';
    document.getElementById('btnAccept').style.display = showActions ? 'inline-block' : 'none';
    document.getElementById('btnReject').style.display = showActions ? 'inline-block' : 'none';

    document.getElementById('viewModal').classList.add('open');
}

function openConfirm(action) {
    if (!_borrowId) return;

    var approveEl = document.getElementById('confirmApproveContent');
    var rejectEl  = document.getElementById('confirmRejectContent');
    var okBtn     = document.getElementById('confirmOkBtn');
    var form      = document.getElementById('confirmForm');

    if (action === 'approve') {
        approveEl.style.display = 'block';
        rejectEl.style.display  = 'none';
        document.getElementById('confirmBorrowerName').textContent = _borrowerName;
        document.getElementById('confirmBookTitle').textContent    = _bookTitle;
        form.action           = '/librarian/borrows/' + _borrowId + '/approve';
        okBtn.style.background = '#27ae60';
        okBtn.textContent      = 'Yes, Approve';
    } else {
        approveEl.style.display = 'none';
        rejectEl.style.display  = 'block';
        document.getElementById('confirmBorrowerNameReject').textContent = _borrowerName;
        document.getElementById('confirmBookTitleReject').textContent    = _bookTitle;
        form.action           = '/librarian/borrows/' + _borrowId + '/reject';
        okBtn.style.background = '#e74c3c';
        okBtn.textContent      = 'Yes, Reject';
    }

    document.getElementById('confirmOverlay').classList.add('open');
}

function closeConfirm() {
    document.getElementById('confirmOverlay').classList.remove('open');
}

// Backdrop click closes details modal
document.getElementById('viewModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal('viewModal');
});

// Escape: dismiss confirm first if open, then the details modal
document.addEventListener('keydown', function(e) {
    if (e.key !== 'Escape') return;
    var confirm = document.getElementById('confirmOverlay');
    if (confirm.classList.contains('open')) {
        closeConfirm();
    } else {
        closeModal('viewModal');
    }
});
</script>
@endpush