@extends('librarian.layout')
@section('title', 'Book Catalog')

@push('styles')
<style>
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
.btn-pdf { background:#3b4f7a; color:white; border:none; border-radius:8px; padding:9px 18px; font-family:'Outfit',sans-serif; font-size:.82rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; text-decoration:none; transition:background .2s; }
.btn-pdf:hover { background:#2a3050; }
.btn-pdf svg { width:15px; height:15px; }

/* ── Modal base ── */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(10,10,20,.55); z-index:300; align-items:center; justify-content:center; padding:16px; }
.modal-overlay.open { display:flex; }
@keyframes popIn { from{transform:scale(.93);opacity:0} to{transform:scale(1);opacity:1} }

/* ── Wide two-column modal ── */
.modal-box-wide {
    background:white; border-radius:14px; width:100%; max-width:780px;
    box-shadow:0 24px 60px rgba(0,0,0,.28); animation:popIn .2s ease;
    display:flex; overflow:hidden; max-height:92vh;
}

/* Left cover panel */
.modal-cover-panel {
    width:220px; flex-shrink:0;
    background:linear-gradient(160deg,#1a1a2e 0%,#2a3050 100%);
    display:flex; flex-direction:column; align-items:center;
    justify-content:center; padding:24px 16px; gap:14px;
}
.modal-cover-panel img {
    width:160px; aspect-ratio:2/3; object-fit:cover;
    border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,.4);
    display:none;
}
.modal-cover-panel .cover-ph {
    width:160px; aspect-ratio:2/3; border-radius:10px;
    background:rgba(255,255,255,.08); border:2px dashed rgba(168,164,224,.4);
    display:flex; flex-direction:column; align-items:center;
    justify-content:center; gap:8px;
}
.modal-cover-panel .cover-ph svg  { color:rgba(168,164,224,.5); width:36px; height:36px; }
.modal-cover-panel .cover-ph span { font-size:.7rem; color:rgba(168,164,224,.6); text-align:center; }
.modal-cover-panel .cover-title {
    font-size:.82rem; font-weight:600; color:rgba(255,255,255,.85);
    text-align:center; line-height:1.4; max-width:170px;
}
.modal-cover-panel .cover-author {
    font-size:.72rem; color:rgba(168,164,224,.7); text-align:center;
}

/* Right content panel */
.modal-content-panel {
    flex:1; display:flex; flex-direction:column; overflow:hidden;
}
.modal-content-header {
    padding:20px 24px 16px; border-bottom:1px solid #f0eef8;
    font-size:1rem; font-weight:700; color:#1a1a2e;
}
.modal-content-body {
    flex:1; overflow-y:auto; padding:16px 24px;
}
.modal-content-footer {
    padding:14px 24px; border-top:1px solid #f0eef8;
    display:flex; gap:10px; justify-content:flex-end;
}

/* ── Buttons ── */
.btn-modal-cancel { background:#f0eef8; color:#5a5a7a; border:none; border-radius:8px; padding:9px 22px; font-family:'Outfit',sans-serif; font-size:.84rem; font-weight:600; cursor:pointer; }
.btn-modal-save   { background:#2a3050; color:white; border:none; border-radius:8px; padding:9px 22px; font-family:'Outfit',sans-serif; font-size:.84rem; font-weight:600; cursor:pointer; }
.btn-modal-save:hover   { background:#3b4f7a; }
.btn-modal-cancel:hover { background:#e0def0; }

/* ── Form fields ── */
.field { display:flex; flex-direction:column; gap:5px; margin-bottom:14px; }
.field label { font-size:.76rem; font-weight:600; color:#5a5a7a; text-transform:uppercase; letter-spacing:.04em; }
.field input, .field select {
    background:#f0eef8; border:1.5px solid transparent; border-radius:8px;
    padding:10px 12px; font-family:'Outfit',sans-serif; font-size:.88rem; color:#1a1a2e;
    outline:none; transition:border-color .2s;
}
.field input:focus, .field select:focus { border-color:#a8a4e0; background:#faf8ff; }
.field select { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238884a8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 10px center; cursor:pointer; }
.field-ro input { background:#e8e6f0; color:#8884a8; cursor:not-allowed; }

/* ── View detail rows ── */
.detail-row { display:flex; justify-content:space-between; align-items:baseline; padding:8px 0; border-bottom:1px solid #f5f4fc; font-size:.84rem; }
.detail-row:last-child { border-bottom:none; }
.detail-row .d-label { color:#8884a8; flex-shrink:0; }
.detail-row .d-value { font-weight:600; color:#1a1a2e; text-align:right; max-width:60%; word-break:break-word; }
.detail-row .d-badge { background:#f0eef8; border-radius:20px; padding:2px 12px; font-size:.8rem; }

.copies-red { color:#e74c3c; font-weight:700; }
.copies-grn { color:#27ae60; font-weight:600; }
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

    <div class="page-header">
        <span style="font-size:1.25rem;font-weight:700;color:#1a1a2e;">Book Catalog</span>
        <a href="{{ route('librarian.books.catalog-pdf') }}" class="btn-pdf" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Catalog Report
        </a>
    </div>

    <form method="GET" action="{{ route('librarian.books.index') }}">
        <div class="filter-row">
            <label>Category:</label>
            <select name="category" class="filter-select">
                <option value="all" {{ request('category','all')==='all'?'selected':'' }}>All</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->category_id }}" {{ request('category')==$cat->category_id?'selected':'' }}>
                        {{ $cat->category_name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn-filter">Apply Filter</button>
        </div>
    </form>

    @if($books->isEmpty())
        <div style="text-align:center;padding:32px;color:#8884a8;font-size:.88rem;">No books found.</div>
    @else
    <table class="data-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Available Copies</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($books as $book)
        <tr>
            <td>{{ $book->title }}</td>
            <td>{{ $book->author->author_name ?? '—' }}</td>
            <td>{{ $book->category->category_name ?? '—' }}</td>
            <td>
                <span class="{{ $book->available_copies > 0 ? 'copies-grn' : 'copies-red' }}">
                    {{ $book->available_copies }}
                </span>
            </td>
            <td>
                {{-- View --}}
                <button class="action-btn btn-view" title="View Details"
                    onclick="openView(
                        '{{ addslashes($book->title) }}',
                        '{{ addslashes($book->author->author_name ?? '—') }}',
                        '{{ $book->isbn ?? '—' }}',
                        '{{ $book->format->format_type ?? '—' }}',
                        '{{ addslashes($book->category->category_name ?? '—') }}',
                        {{ $book->total_copies }},
                        {{ $book->available_copies }},
                        '{{ $book->status }}',
                        '{{ addslashes($book->cover_url ?? '') }}'
                    )">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
                {{-- Edit --}}
                <button class="action-btn btn-edit" title="Edit"
                    onclick="openEdit(
                        {{ $book->ebook_id }},
                        '{{ addslashes($book->title) }}',
                        '{{ addslashes($book->author->author_name ?? '—') }}',
                        '{{ $book->isbn ?? '—' }}',
                        '{{ $book->format->format_type ?? '—' }}',
                        '{{ addslashes($book->category->category_name ?? '—') }}',
                        {{ $book->total_copies }},
                        {{ $book->available_copies }},
                        '{{ $book->status }}',
                        '{{ addslashes($book->cover_url ?? '') }}'
                    )">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>

    <div class="pagination-row">
        @if($books->onFirstPage())
            <span style="color:#ccc;display:flex;align-items:center;gap:4px;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Previous
            </span>
        @else
            <a href="{{ $books->previousPageUrl() }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Previous
            </a>
        @endif
        <span style="font-size:.8rem;color:#aaa;">Page {{ $books->currentPage() }} of {{ $books->lastPage() }}</span>
        @if($books->hasMorePages())
            <a href="{{ $books->nextPageUrl() }}">
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

{{-- ══════════════════════════════════════════════════════════
     VIEW MODAL — two-column wide layout
══════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="viewModal">
  <div class="modal-box-wide">

    {{-- Left: cover --}}
    <div class="modal-cover-panel">
        <img id="vCoverImg" src="" alt="Cover">
        <div id="vCoverPh" class="cover-ph">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span>No cover</span>
        </div>
        <div class="cover-title"  id="vCoverTitle"></div>
        <div class="cover-author" id="vCoverAuthor"></div>
    </div>

    {{-- Right: details --}}
    <div class="modal-content-panel">
        <div class="modal-content-header">Book Details</div>
        <div class="modal-content-body">
            <div class="detail-row"><span class="d-label">Title</span>        <span class="d-value" id="vTitle"></span></div>
            <div class="detail-row"><span class="d-label">Author</span>       <span class="d-value" id="vAuthor"></span></div>
            <div class="detail-row"><span class="d-label">ISBN</span>         <span class="d-value" id="vIsbn"></span></div>
            <div class="detail-row"><span class="d-label">Format</span>       <span class="d-value d-badge" id="vFormat"></span></div>
            <div class="detail-row"><span class="d-label">Category</span>     <span class="d-value d-badge" id="vCat"></span></div>
            <div class="detail-row"><span class="d-label">Total Copies</span> <span class="d-value" id="vTotal"></span></div>
            <div class="detail-row"><span class="d-label">Available</span>    <span class="d-value" id="vAvail"></span></div>
            <div class="detail-row"><span class="d-label">Status</span>       <span class="d-value d-badge" id="vStatus"></span></div>
        </div>
        <div class="modal-content-footer">
            <button class="btn-modal-cancel" onclick="closeModal('viewModal')">Close</button>
        </div>
    </div>

  </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     EDIT MODAL — two-column wide layout
══════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="editModal">
  <div class="modal-box-wide">

    {{-- Left: cover --}}
    <div class="modal-cover-panel">
        <img id="editCoverImg" src="" alt="Cover">
        <div id="editCoverPh" class="cover-ph">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span>Current cover</span>
        </div>
        <div class="cover-title"  id="editCoverTitle"></div>
        <div class="cover-author" id="editCoverAuthor"></div>
        <div style="font-size:.65rem;color:rgba(168,164,224,.55);text-align:center;max-width:170px;line-height:1.4;margin-top:4px;">
            Cover is set by Admin.<br>Shown here for reference.
        </div>
    </div>

    {{-- Right: edit form --}}
    <div class="modal-content-panel">
        <div class="modal-content-header">Edit Book</div>
        <div class="modal-content-body">
            <form method="POST" id="editForm" action="">
                @csrf @method('PUT')
                <div class="field field-ro"><label>Title</label><input type="text" id="eTitle" readonly></div>
                <div class="field field-ro"><label>Author</label><input type="text" id="eAuthor" readonly></div>
                <div class="field field-ro"><label>ISBN</label><input type="text" id="eIsbn" readonly></div>
                <div class="field field-ro"><label>Format</label><input type="text" id="eFormat" readonly></div>
                <div class="field field-ro"><label>Category</label><input type="text" id="eCat" readonly></div>
                <div class="field field-ro"><label>Total Copies</label><input type="text" id="eTotal" readonly></div>
                <div class="field">
                    <label>Available Copies</label>
                    <input type="number" name="available_copies" id="eAvail" min="0">
                </div>
                <div class="field">
                    <label>Status</label>
                    <select name="status" id="eStatus">
                        <option value="active">Active</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-content-footer">
            <button type="button" class="btn-modal-cancel" onclick="closeModal('editModal')">Cancel</button>
            <button type="submit" class="btn-modal-save" form="editForm">Submit Changes</button>
        </div>
    </div>

  </div>
</div>

@endsection

@push('scripts')
<script>
function closeModal(id){ document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(m => m.addEventListener('click', e => { if(e.target===m) m.classList.remove('open'); }));

function setCover(imgId, phId, coverUrl) {
    var img = document.getElementById(imgId);
    var ph  = document.getElementById(phId);
    if (coverUrl) {
        img.onload  = function(){ img.style.display='block'; ph.style.display='none'; };
        img.onerror = function(){ img.style.display='none';  ph.style.display='flex'; };
        img.src = coverUrl;
    } else {
        img.style.display = 'none';
        ph.style.display  = 'flex';
    }
}

function openView(title, author, isbn, format, cat, total, avail, status, coverUrl) {
    document.getElementById('vTitle').textContent       = title;
    document.getElementById('vAuthor').textContent      = author;
    document.getElementById('vIsbn').textContent        = isbn;
    document.getElementById('vFormat').textContent      = format;
    document.getElementById('vCat').textContent         = cat;
    document.getElementById('vTotal').textContent       = total;
    document.getElementById('vAvail').textContent       = avail;
    document.getElementById('vStatus').textContent      = status.charAt(0).toUpperCase() + status.slice(1);
    document.getElementById('vCoverTitle').textContent  = title;
    document.getElementById('vCoverAuthor').textContent = author;
    setCover('vCoverImg', 'vCoverPh', coverUrl);
    document.getElementById('viewModal').classList.add('open');
}

function openEdit(id, title, author, isbn, format, cat, total, avail, status, coverUrl) {
    document.getElementById('editForm').action          = `/librarian/books/${id}`;
    document.getElementById('eTitle').value             = title;
    document.getElementById('eAuthor').value            = author;
    document.getElementById('eIsbn').value              = isbn;
    document.getElementById('eFormat').value            = format;
    document.getElementById('eCat').value               = cat;
    document.getElementById('eTotal').value             = total;
    document.getElementById('eAvail').value             = avail;
    document.getElementById('eStatus').value            = status;
    document.getElementById('editCoverTitle').textContent  = title;
    document.getElementById('editCoverAuthor').textContent = author;
    setCover('editCoverImg', 'editCoverPh', coverUrl);
    document.getElementById('editModal').classList.add('open');
}
</script>
@endpush