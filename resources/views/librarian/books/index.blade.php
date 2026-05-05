@extends('librarian.layout')
@section('title', 'Book Catalog')

@push('styles')
<style>
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
.btn-pdf { background:#3b4f7a; color:white; border:none; border-radius:8px; padding:9px 18px; font-family:'Outfit',sans-serif; font-size:.82rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; text-decoration:none; transition:background .2s; }
.btn-pdf:hover { background:#2a3050; }
.btn-pdf svg { width:15px; height:15px; }

/* Modal */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(10,10,20,.5); z-index:300; align-items:center; justify-content:center; }
.modal-overlay.open { display:flex; }
.modal-box { background:white; border-radius:12px; padding:28px 32px 24px; max-width:500px; width:90%; box-shadow:0 20px 50px rgba(0,0,0,.25); animation:popIn .2s ease; max-height:90vh; overflow-y:auto; }
@keyframes popIn { from{transform:scale(.92);opacity:0} to{transform:scale(1);opacity:1} }
.modal-title { font-size:1.1rem; font-weight:700; color:#1a1a2e; text-align:center; margin-bottom:20px; padding-bottom:14px; border-bottom:1px solid #f0eef8; letter-spacing:.06em; text-transform:uppercase; }

/* View modal layout */
.view-modal-body { display:flex; gap:16px; align-items:flex-start; }
.view-cover-box {
    flex-shrink:0;
    width:90px;
}
.view-cover-box img {
    width:90px; aspect-ratio:2/3; object-fit:cover;
    border-radius:8px; background:#f0eef8; display:none;
}
.view-cover-placeholder {
    width:90px; aspect-ratio:2/3; border-radius:8px;
    background:linear-gradient(135deg,#ede9fe,#c4b5fd);
    display:flex; align-items:center; justify-content:center;
}

.detail-row { display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid #f5f4fc; font-size:.85rem; }
.detail-row:last-of-type { border-bottom:none; }
.detail-row .d-label { color:#8884a8; }
.detail-row .d-value { font-weight:600; color:#1a1a2e; background:#f5f4fc; border-radius:20px; padding:3px 12px; font-size:.82rem; text-align:center; min-width:80px; }

/* Edit modal cover preview */
.cover-preview-row {
    display:grid;
    grid-template-columns:90px 1fr;
    gap:12px;
    align-items:start;
    margin-bottom:12px;
}
.cover-preview-box {
    width:90px; aspect-ratio:2/3; border-radius:8px;
    background:#f0eef8; border:1.5px dashed #c8c5e8;
    overflow:hidden; display:flex; flex-direction:column;
    align-items:center; justify-content:center; flex-shrink:0;
    transition:border-color .2s;
}
.cover-preview-box img { width:100%; height:100%; object-fit:cover; display:none; }
.cover-placeholder { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:5px; padding:6px; text-align:center; }
.cover-placeholder svg { color:#c8c5e8; width:24px; height:24px; }
.cover-placeholder span { font-size:.62rem; color:#b0acd0; line-height:1.3; }
.cover-preview-box.has-image { border-style:solid; border-color:#a8a4e0; }

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

.modal-actions { display:flex; gap:10px; justify-content:center; margin-top:22px; }
.btn-modal-cancel  { background:white; color:#3a3a5a; border:2px solid #e0def0; border-radius:8px; padding:10px 32px; font-family:'Outfit',sans-serif; font-size:.88rem; font-weight:600; cursor:pointer; transition:background .15s; }
.btn-modal-cancel:hover { background:#f5f4fc; }
.btn-modal-submit  { background:#2a3050; color:white; border:none; border-radius:8px; padding:10px 32px; font-family:'Outfit',sans-serif; font-size:.88rem; font-weight:600; cursor:pointer; transition:background .15s; }
.btn-modal-submit:hover { background:#3b4f7a; }

.copies-red  { color:#e74c3c; font-weight:700; }
.copies-grn  { color:#27ae60; font-weight:600; }
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

    {{-- Filters --}}
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

{{-- ── VIEW MODAL ── --}}
<div class="modal-overlay" id="viewModal">
  <div class="modal-box">
    <div class="modal-title">Book Details</div>
    <div class="view-modal-body">
        {{-- Cover --}}
        <div class="view-cover-box">
            <img id="vCoverImg" src="" alt="Cover">
            <div id="vCoverPlaceholder" class="view-cover-placeholder">
                <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="1.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        </div>
        {{-- Details --}}
        <div style="flex:1;">
            <div class="detail-row"><span class="d-label">Title</span>        <span class="d-value" id="vTitle"></span></div>
            <div class="detail-row"><span class="d-label">Author</span>       <span class="d-value" id="vAuthor"></span></div>
            <div class="detail-row"><span class="d-label">ISBN</span>         <span class="d-value" id="vIsbn"></span></div>
            <div class="detail-row"><span class="d-label">Format</span>       <span class="d-value" id="vFormat"></span></div>
            <div class="detail-row"><span class="d-label">Category</span>     <span class="d-value" id="vCat"></span></div>
            <div class="detail-row"><span class="d-label">Total Copies</span> <span class="d-value" id="vTotal"></span></div>
            <div class="detail-row"><span class="d-label">Available</span>    <span class="d-value" id="vAvail"></span></div>
            <div class="detail-row"><span class="d-label">Status</span>       <span class="d-value" id="vStatus"></span></div>
        </div>
    </div>
    <div class="modal-actions">
        <button class="btn-modal-cancel" onclick="closeModal('viewModal')">Close</button>
    </div>
  </div>
</div>

{{-- ── EDIT MODAL ── --}}
<div class="modal-overlay" id="editModal">
  <div class="modal-box">
    <div class="modal-title">Edit Book</div>
    <form method="POST" id="editForm" action="">
      @csrf @method('PUT')

      {{-- Cover preview --}}
      <div class="cover-preview-row">
        <div class="cover-preview-box" id="editCoverBox">
            <img id="editCoverImg" src="" alt="Cover Preview">
            <div class="cover-placeholder" id="editCoverPlaceholder">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span>Cover preview</span>
            </div>
        </div>
        <div>
            <p style="font-size:.76rem;font-weight:600;color:#5a5a7a;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px;">Current Cover</p>
            <p style="font-size:.78rem;color:#8884a8;line-height:1.5;">Cover image is set by Admin. Shown here for reference only.</p>
        </div>
      </div>

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
      <div class="modal-actions">
        <button type="button" class="btn-modal-cancel" onclick="closeModal('editModal')">Cancel</button>
        <button type="submit" class="btn-modal-submit">Submit Changes</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
function closeModal(id){ document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(m => m.addEventListener('click', e => { if(e.target===m) m.classList.remove('open'); }));

function openView(title, author, isbn, format, cat, total, avail, status, coverUrl) {
    document.getElementById('vTitle').textContent  = title;
    document.getElementById('vAuthor').textContent = author;
    document.getElementById('vIsbn').textContent   = isbn;
    document.getElementById('vFormat').textContent = format;
    document.getElementById('vCat').textContent    = cat;
    document.getElementById('vTotal').textContent  = total;
    document.getElementById('vAvail').textContent  = avail;
    document.getElementById('vStatus').textContent = status.charAt(0).toUpperCase() + status.slice(1);

    var img = document.getElementById('vCoverImg');
    var ph  = document.getElementById('vCoverPlaceholder');
    if (coverUrl) {
        img.src = coverUrl;
        img.style.display = 'block';
        ph.style.display  = 'none';
    } else {
        img.style.display = 'none';
        ph.style.display  = 'flex';
    }

    document.getElementById('viewModal').classList.add('open');
}

function openEdit(id, title, author, isbn, format, cat, total, avail, status, coverUrl) {
    document.getElementById('editForm').action = `/librarian/books/${id}`;
    document.getElementById('eTitle').value    = title;
    document.getElementById('eAuthor').value   = author;
    document.getElementById('eIsbn').value     = isbn;
    document.getElementById('eFormat').value   = format;
    document.getElementById('eCat').value      = cat;
    document.getElementById('eTotal').value    = total;
    document.getElementById('eAvail').value    = avail;
    document.getElementById('eStatus').value   = status;

    // Show cover preview in edit modal
    var img = document.getElementById('editCoverImg');
    var ph  = document.getElementById('editCoverPlaceholder');
    var box = document.getElementById('editCoverBox');
    if (coverUrl) {
        img.src = coverUrl;
        img.style.display = 'block';
        ph.style.display  = 'none';
        box.classList.add('has-image');
    } else {
        img.style.display = 'none';
        ph.style.display  = 'flex';
        box.classList.remove('has-image');
    }

    document.getElementById('editModal').classList.add('open');
}
</script>
@endpush