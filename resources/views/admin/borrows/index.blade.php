@extends('admin.layout')
@section('title', 'Borrow Records')

@push('styles')
<style>
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px; }
.btn-pdf { background:#2a3050; color:white; border:none; border-radius:8px; padding:9px 18px; font-family:'Outfit',sans-serif; font-size:.82rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; text-decoration:none; transition:background .2s; }
.btn-pdf:hover { background:#3b4f7a; }
.btn-pdf svg { width:15px; height:15px; }

/* View modal */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(10,10,20,.5); z-index:300; align-items:center; justify-content:center; }
.modal-overlay.open { display:flex; }
.modal-box { background:white; border-radius:12px; padding:28px; max-width:440px; width:90%; box-shadow:0 20px 50px rgba(0,0,0,.25); animation:popIn .2s ease; }
@keyframes popIn { from{transform:scale(.92);opacity:0} to{transform:scale(1);opacity:1} }
.modal-title { font-size:1rem; font-weight:700; color:#1a1a2e; margin-bottom:16px; padding-bottom:12px; border-bottom:1px solid #f0eef8; }
.detail-section { margin-bottom:16px; }
.detail-section h4 { font-size:.72rem; font-weight:700; color:#a8a4e0; text-transform:uppercase; letter-spacing:.1em; margin-bottom:8px; }
.detail-table { width:100%; font-size:.84rem; border-collapse:collapse; }
.detail-table td { padding:5px 0; vertical-align:top; }
.detail-table td:first-child { color:#8884a8; width:120px; }
.detail-table td:last-child { font-weight:500; color:#1a1a2e; }
.modal-actions { display:flex; justify-content:flex-end; margin-top:18px; }
.btn-cancel-modal { background:#f0eef8; color:#5a5a7a; border:none; border-radius:8px; padding:9px 20px; font-family:'Outfit',sans-serif; font-size:.84rem; font-weight:600; cursor:pointer; }
</style>
@endpush

@section('content')
<div class="content-card">

    @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif

    {{-- Special message when coming from "Expiring Today" KPI --}}
    @if(request('expiring_today'))
        <div class="flash" style="background:#8b2e2e; color:white; border:1px solid #6b1f1f;">
            <strong>Expiring Today</strong> — Showing only active borrows with expiry date <strong>{{ today()->format('M d, Y') }}</strong>
        </div>
    @endif

    <div class="page-header">
        <div>
            {{-- Filters --}}
            <form method="GET" action="{{ route('admin.borrows.index') }}" style="display:inline;">
                <div class="filter-row" style="margin-bottom:0;">
                    <label>Department:</label>
                    <select name="department" class="filter-select">
                        <option value="all" {{ request('department','all')==='all'?'selected':'' }}>All</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->department_id }}" {{ request('department')==$dept->department_id?'selected':'' }}>{{ $dept->department_name }}</option>
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

        <a href="{{ route('admin.borrows.pdf', request()->query()) }}" class="btn-pdf" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Generate PDF Report
        </a>
    </div>

    @if($borrows->isEmpty())
        <div style="text-align:center;padding:32px;color:#8884a8;font-size:.88rem;">
            No borrow records found.
        </div>
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
                <td>{{ $b->due_date ? \Carbon\Carbon::parse($b->due_date)->format('M d, Y') : '—' }}</td>
                <td>
                    @php $s = $b->status; @endphp
                    <span class="badge
                        {{ $s==='active'   ?'badge-active'   :'' }}
                        {{ $s==='pending'  ?'badge-pending'  :'' }}
                        {{ $s==='expired'  ?'badge-expired'  :'' }}
                        {{ $s==='due_soon' ?'badge-due-soon' :'' }}
                        {{ $s==='cancelled'?'badge-inactive' :'' }}
                    ">
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
                            '{{ $b->borrow_date ? \Carbon\Carbon::parse($b->borrow_date)->format('M d, Y') : '—' }}',
                            '{{ $b->due_date   ? \Carbon\Carbon::parse($b->due_date)->format('M d, Y')   : '—' }}',
                            '{{ ucfirst(str_replace('_', ' ', $b->status)) }}'
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

        {{-- Pagination --}}
        <div class="pagination-row">
            @if($borrows->onFirstPage())
                <span style="color:#ccc;display:flex;align-items:center;gap:4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg> Previous
                </span>
            @else
                <a href="{{ $borrows->previousPageUrl() }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg> Previous
                </a>
            @endif
            <span style="font-size:.8rem;color:#aaa;">Page {{ $borrows->currentPage() }} of {{ $borrows->lastPage() }}</span>
            @if($borrows->hasMorePages())
                <a href="{{ $borrows->nextPageUrl() }}">Next 
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            @else
                <span style="color:#ccc;display:flex;align-items:center;gap:4px;">
                    Next 
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </span>
            @endif
        </div>
    @endif
</div>

{{-- View Modal (unchanged) --}}
<div class="modal-overlay" id="viewModal">
  <div class="modal-box">
    <div class="modal-title">Borrow Record Details</div>
    <div class="detail-section">
      <h4>Borrower</h4>
      <table class="detail-table">
        <tr><td>Name</td>      <td id="vName"></td></tr>
        <tr><td>Department</td><td id="vDept"></td></tr>
        <tr><td>Email</td>     <td id="vEmail"></td></tr>
      </table>
    </div>
    <div class="detail-section">
      <h4>Book</h4>
      <table class="detail-table">
        <tr><td>Title</td>   <td id="vTitle"></td></tr>
        <tr><td>Author</td>  <td id="vAuthor"></td></tr>
        <tr><td>Category</td><td id="vCat"></td></tr>
        <tr><td>Format</td>  <td id="vFmt"></td></tr>
      </table>
    </div>
    <div class="detail-section">
      <h4>Borrow Details</h4>
      <table class="detail-table">
        <tr><td>Borrow Date</td><td id="vBorrowDate"></td></tr>
        <tr><td>Due Date</td>   <td id="vDueDate"></td></tr>
        <tr><td>Status</td>     <td id="vStatus"></td></tr>
      </table>
    </div>
    <div class="modal-actions">
        <button class="btn-cancel-modal" onclick="document.getElementById('viewModal').classList.remove('open')">Close</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => {
        if (e.target === m) m.classList.remove('open');
    });
});

function openView(name, dept, email, title, author, cat, fmt, borrowDate, dueDate, status) {
    document.getElementById('vName').textContent       = name;
    document.getElementById('vDept').textContent       = dept;
    document.getElementById('vEmail').textContent      = email;
    document.getElementById('vTitle').textContent      = title;
    document.getElementById('vAuthor').textContent     = author;
    document.getElementById('vCat').textContent        = cat;
    document.getElementById('vFmt').textContent        = fmt;
    document.getElementById('vBorrowDate').textContent = borrowDate;
    document.getElementById('vDueDate').textContent    = dueDate;
    document.getElementById('vStatus').textContent     = status;
    document.getElementById('viewModal').classList.add('open');
}
</script>
@endpush