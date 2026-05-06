@extends('admin.layout')
@section('title', 'Manage Books')

@push('styles')
<style>
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
.page-title  { font-size:1.25rem; font-weight:700; color:#1a1a2e; }
.btn-add     { background:#2a3050; color:white; border:none; border-radius:8px; padding:9px 20px; font-family:'Outfit',sans-serif; font-size:0.84rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; text-decoration:none; transition:background .2s; }
.btn-add:hover { background:#3b4f7a; }
.btn-add svg { width:15px; height:15px; }
.btn-pdf { background:#3b4f7a; color:white; border:none; border-radius:8px; padding:9px 18px; font-family:'Outfit',sans-serif; font-size:.82rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; text-decoration:none; transition:background .2s; }
.btn-pdf:hover { background:#2a3050; }
.btn-pdf svg { width:15px; height:15px; }

/* ── Modal base ── */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(10,10,20,.55); z-index:300; align-items:center; justify-content:center; padding:16px; }
.modal-overlay.open { display:flex; }
@keyframes popIn { from{transform:scale(.93);opacity:0} to{transform:scale(1);opacity:1} }

/* ── Wide two-column modal (view + edit) ── */
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
    flex:1; display:flex; flex-direction:column;
    overflow:hidden;
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

/* ── Narrow modal (add + archive) ── */
.modal-box { background:white; border-radius:12px; padding:28px 28px 24px; max-width:560px; width:90%; box-shadow:0 20px 50px rgba(0,0,0,.25); animation:popIn .2s ease; max-height:92vh; overflow-y:auto; }
.modal-box.sm { max-width:380px; }
.modal-title { font-size:1rem; font-weight:700; color:#1a1a2e; margin-bottom:18px; padding-bottom:12px; border-bottom:1px solid #f0eef8; }
.modal-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:20px; }
.modal-actions button, .modal-actions a { border:none; border-radius:8px; padding:9px 20px; font-family:'Outfit',sans-serif; font-size:.84rem; font-weight:600; cursor:pointer; text-decoration:none; display:inline-block; transition:opacity .15s; }
.modal-actions button:hover, .modal-actions a:hover { opacity:.85; }
.btn-cancel-modal { background:#f0eef8; color:#5a5a7a; }
.btn-confirm-save { background:#2a3050; color:white; }
.btn-confirm-del  { background:#e74c3c; color:white; }
.btn-modal-cancel { background:#f0eef8; color:#5a5a7a; border:none; border-radius:8px; padding:9px 22px; font-family:'Outfit',sans-serif; font-size:.84rem; font-weight:600; cursor:pointer; }
.btn-modal-save   { background:#2a3050; color:white; border:none; border-radius:8px; padding:9px 22px; font-family:'Outfit',sans-serif; font-size:.84rem; font-weight:600; cursor:pointer; }
.btn-modal-save:hover   { background:#3b4f7a; }
.btn-modal-cancel:hover { background:#e0def0; }

/* ── Form fields ── */
.form-grid2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.field { display:flex; flex-direction:column; gap:5px; margin-bottom:12px; }
.field label { font-size:.76rem; font-weight:600; color:#5a5a7a; letter-spacing:.04em; text-transform:uppercase; }
.field input, .field select, .field textarea {
    background:#f0eef8; border:1.5px solid transparent; border-radius:8px;
    padding:10px 12px; font-family:'Outfit',sans-serif; font-size:.88rem; color:#1a1a2e;
    outline:none; transition:border-color .2s;
}
.field input:focus, .field select:focus, .field textarea:focus { border-color:#a8a4e0; background:#faf8ff; }
.field textarea { resize:vertical; min-height:68px; }
.field select { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238884a8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 10px center; cursor:pointer; }
.field-ro input { background:#e8e6f0; color:#8884a8; cursor:not-allowed; }

/* ── Add modal cover preview (inline small) ── */
.add-cover-preview-row { display:grid; grid-template-columns:90px 1fr; gap:12px; align-items:start; margin-bottom:12px; }
.add-cover-box {
    width:90px; aspect-ratio:2/3; border-radius:8px;
    background:#f0eef8; border:1.5px dashed #c8c5e8;
    overflow:hidden; display:flex; align-items:center; justify-content:center;
    flex-shrink:0; transition:border-color .2s;
}
.add-cover-box img { width:100%; height:100%; object-fit:cover; display:none; }
.add-cover-ph { display:flex; flex-direction:column; align-items:center; gap:4px; }
.add-cover-ph svg   { color:#c8c5e8; width:22px; height:22px; }
.add-cover-ph span  { font-size:.62rem; color:#b0acd0; }
.add-cover-box.has-image { border-style:solid; border-color:#a8a4e0; }

/* ── View detail rows ── */
.detail-row { display:flex; justify-content:space-between; align-items:baseline; padding:8px 0; border-bottom:1px solid #f5f4fc; font-size:.84rem; }
.detail-row:last-child { border-bottom:none; }
.detail-row .d-label { color:#8884a8; flex-shrink:0; }
.detail-row .d-value { font-weight:600; color:#1a1a2e; text-align:right; max-width:55%; word-break:break-word; }
.detail-row .d-badge { background:#f0eef8; border-radius:20px; padding:2px 12px; font-size:.8rem; }

/* ── Archive modal ── */
.detail-table { width:100%; font-size:.85rem; border-collapse:collapse; }
.detail-table td { padding:7px 0; vertical-align:top; }
.detail-table td:first-child { color:#8884a8; width:130px; }
.detail-table td:last-child  { font-weight:500; color:#1a1a2e; }
</style>
@endpush

@section('content')
<div class="content-card">

    @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif

    <div class="page-header">
        <span class="page-title">Manage Books</span>
        <div style="display:flex;align-items:center;gap:10px;">
            <a href="{{ route('admin.books.catalog-pdf') }}" class="btn-pdf" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Catalog Report
            </a>
            <button class="btn-add" onclick="openAddModal()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Book
            </button>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.books.index') }}">
        <div class="filter-row">
            <label>Category:</label>
            <select name="category" class="filter-select">
                <option value="all" {{ request('category','all')==='all'?'selected':'' }}>All</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->category_id }}" {{ request('category')==$cat->category_id?'selected':'' }}>{{ $cat->category_name }}</option>
                @endforeach
            </select>
            <label>Status:</label>
            <select name="status" class="filter-select">
                <option value="all"         {{ request('status','all')==='all'        ?'selected':'' }}>All</option>
                <option value="active"      {{ request('status')==='active'           ?'selected':'' }}>Active</option>
                <option value="unavailable" {{ request('status')==='unavailable'      ?'selected':'' }}>Unavailable</option>
                <option value="archived"    {{ request('status')==='archived'         ?'selected':'' }}>Archived</option>
            </select>
            <button type="submit" class="btn-filter">Apply Filter</button>
        </div>
    </form>

    @if($books->isEmpty())
        <div style="text-align:center;padding:32px;color:#8884a8;font-size:.88rem;">No books found.</div>
    @else
    <table class="data-table">
        <thead><tr>
            <th>Title</th><th>Author</th><th>Category</th>
            <th>Available</th><th>Status</th><th>Action</th>
        </tr></thead>
        <tbody>
        @foreach($books as $book)
        <tr>
            <td>{{ $book->title }}</td>
            <td>{{ $book->author->author_name ?? '—' }}</td>
            <td>{{ $book->category->category_name ?? '—' }}</td>
            <td>
                <span style="display:inline-flex;align-items:center;gap:6px;">
                    {{ $book->available_copies }}
                    <span style="width:8px;height:8px;border-radius:50%;background:{{ $book->available_copies > 0 ? '#27ae60' : '#e74c3c' }};display:inline-block;"></span>
                </span>
            </td>
            <td>
                <span class="badge {{ $book->status==='active'?'badge-active':($book->status==='unavailable'?'badge-inactive':'badge-expired') }}">
                    {{ ucfirst($book->status) }}
                </span>
            </td>
            <td>
                <button class="action-btn btn-view" title="View"
                    onclick="openViewBook(
                        '{{ addslashes($book->title) }}',
                        '{{ addslashes($book->author->author_name ?? '—') }}',
                        '{{ addslashes($book->category->category_name ?? '—') }}',
                        '{{ $book->format->format_type ?? '—' }}',
                        '{{ $book->isbn ?? '—' }}',
                        '{{ $book->total_copies }}',
                        '{{ $book->available_copies }}',
                        '{{ $book->status }}',
                        '{{ addslashes($book->description ?? '—') }}',
                        '{{ addslashes($book->cover_url ?? '') }}'
                    )">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
                <button class="action-btn btn-edit" title="Edit"
                    data-id="{{ $book->ebook_id }}"
                    data-title="{{ addslashes($book->title) }}"
                    data-author-id="{{ $book->author_id }}"
                    data-category-id="{{ $book->category_id }}"
                    data-format-id="{{ $book->format_id }}"
                    data-isbn="{{ addslashes($book->isbn ?? '') }}"
                    data-total-copies="{{ $book->total_copies }}"
                    data-available-copies="{{ $book->available_copies }}"
                    data-status="{{ $book->status }}"
                    data-file-url="{{ addslashes($book->file_url ?? '') }}"
                    data-cover-url="{{ addslashes($book->cover_url ?? '') }}"
                    data-description="{{ addslashes($book->description ?? '') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <button class="action-btn btn-deactivate" title="Archive"
                    onclick="openArchiveModal({{ $book->ebook_id }}, '{{ addslashes($book->title) }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>

    <div class="pagination-row">
        @if($books->onFirstPage())
            <span style="color:#ccc;display:flex;align-items:center;gap:4px;"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Previous</span>
        @else
            <a href="{{ $books->previousPageUrl() }}"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Previous</a>
        @endif
        <span style="font-size:.8rem;color:#aaa;">Page {{ $books->currentPage() }} of {{ $books->lastPage() }}</span>
        @if($books->hasMorePages())
            <a href="{{ $books->nextPageUrl() }}">Next <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>
        @else
            <span style="color:#ccc;display:flex;align-items:center;gap:4px;">Next <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></span>
        @endif
    </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════
     VIEW BOOK MODAL — two-column wide layout
══════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="viewBookModal">
  <div class="modal-box-wide">

    {{-- Left: cover panel --}}
    <div class="modal-cover-panel">
        <img id="vbCoverImg" src="" alt="Cover">
        <div id="vbCoverPh" class="cover-ph">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span>No cover</span>
        </div>
        <div class="cover-title" id="vbCoverTitle"></div>
        <div class="cover-author" id="vbCoverAuthor"></div>
    </div>

    {{-- Right: details --}}
    <div class="modal-content-panel">
        <div class="modal-content-header">Book Details</div>
        <div class="modal-content-body">
            <div class="detail-row"><span class="d-label">Title</span>        <span class="d-value" id="vbTitle"></span></div>
            <div class="detail-row"><span class="d-label">Author</span>       <span class="d-value" id="vbAuthor"></span></div>
            <div class="detail-row"><span class="d-label">Category</span>     <span class="d-value d-badge" id="vbCat"></span></div>
            <div class="detail-row"><span class="d-label">Format</span>       <span class="d-value d-badge" id="vbFormat"></span></div>
            <div class="detail-row"><span class="d-label">ISBN</span>         <span class="d-value" id="vbIsbn"></span></div>
            <div class="detail-row"><span class="d-label">Total Copies</span> <span class="d-value" id="vbTotal"></span></div>
            <div class="detail-row"><span class="d-label">Available</span>    <span class="d-value" id="vbAvail"></span></div>
            <div class="detail-row"><span class="d-label">Status</span>       <span class="d-value d-badge" id="vbStatus"></span></div>
            <div class="detail-row" style="align-items:flex-start;">
                <span class="d-label">Description</span>
                <span class="d-value" id="vbDesc" style="white-space:pre-wrap;text-align:left;max-width:65%;font-weight:400;font-size:.82rem;color:#3a3a5a;"></span>
            </div>
        </div>
        <div class="modal-content-footer">
            <button class="btn-modal-cancel" onclick="closeModal('viewBookModal')">Close</button>
        </div>
    </div>

  </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     EDIT BOOK MODAL — two-column wide layout
══════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="editModal">
  <div class="modal-box-wide">

    {{-- Left: cover panel --}}
    <div class="modal-cover-panel">
        <img id="editCoverImg" src="" alt="Cover">
        <div id="editCoverPh" class="cover-ph">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span>Paste URL to preview</span>
        </div>
        <div style="width:160px;">
            <div style="font-size:.68rem;color:rgba(168,164,224,.6);text-align:center;line-height:1.4;">
                Cover updates live as you type the URL →
            </div>
        </div>
    </div>

    {{-- Right: edit form --}}
    <div class="modal-content-panel">
        <div class="modal-content-header">Edit Book</div>
        <div class="modal-content-body">
            <form method="POST" id="editForm" action="">
                @csrf @method('PUT')

                <div class="form-grid2">
                    <div class="field field-ro"><label>Title (read-only)</label><input type="text" id="eTitle" readonly></div>
                    <div class="field field-ro"><label>ISBN (read-only)</label><input type="text" id="eIsbn" readonly></div>
                </div>
                <div class="form-grid2">
                    <div class="field"><label>Category</label>
                        <select name="category_id" id="eCat">
                            @foreach($categories as $c)<option value="{{ $c->category_id }}">{{ $c->category_name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="field"><label>Status</label>
                        <select name="status" id="eStatus">
                            <option value="active">Active</option>
                            <option value="unavailable">Unavailable</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                </div>
                <div class="form-grid2">
                    <div class="field"><label>Total Copies</label><input type="number" name="total_copies" id="eTotalCopies" min="1"></div>
                    <div class="field"><label>Available Copies</label><input type="number" name="available_copies" id="eAvailCopies" min="0"></div>
                </div>
                <div class="field">
                    <label>Cover Image URL</label>
                    <input type="url" name="cover_url" id="eCoverUrl" placeholder="https://example.com/cover.jpg"
                           oninput="liveEditCover(this.value)">
                    <small style="color:#8884a8;font-size:.76rem;margin-top:3px;">Paste a direct image URL — preview updates on the left.</small>
                </div>
                <div class="field"><label>File URL</label><input type="text" name="file_url" id="eFileUrl" placeholder="https://drive.google.com/..."></div>
                <div class="field"><label>Description</label><textarea name="description" id="eDesc"></textarea></div>
        </div>
        <div class="modal-content-footer">
            <button type="button" class="btn-modal-cancel" onclick="closeModal('editModal')">Cancel</button>
            <button type="submit" class="btn-modal-save" form="editForm">Save Changes</button>
        </div>
            </form>
    </div>

  </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     ADD BOOK MODAL — standard narrow
══════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="addModal">
  <div class="modal-box">
    <div class="modal-title">Add New Book</div>
    <form method="POST" action="{{ route('admin.books.store') }}">
      @csrf
      <div class="form-grid2">
        <div class="field"><label>Title</label><input type="text" name="title" required placeholder="Book title"></div>
        <div class="field"><label>ISBN</label><input type="text" name="isbn" placeholder="978-x-xx-xxxxxx-x"></div>
      </div>
      <div class="form-grid2">
        <div class="field"><label>Author</label>
          <input type="text" name="author_input" id="authorInput" placeholder="Type author name..."
                 autocomplete="off" required list="author-suggestions">
          <datalist id="author-suggestions">
              @foreach($authors as $a)<option value="{{ $a->author_name }}">@endforeach
          </datalist>
        </div>
        <div class="field"><label>Category</label>
          <select name="category_id" required>
            <option value="">Select category</option>
            @foreach($categories as $c)<option value="{{ $c->category_id }}">{{ $c->category_name }}</option>@endforeach
          </select>
        </div>
      </div>
      <div class="form-grid2">
        <div class="field"><label>Format</label>
          <select name="format_id" required>
            <option value="">Select format</option>
            @foreach($formats as $f)<option value="{{ $f->format_id }}">{{ $f->format_type }}</option>@endforeach
          </select>
        </div>
        <div class="field"><label>Total Copies</label><input type="number" name="total_copies" min="1" value="1" required></div>
      </div>

      {{-- Cover preview row --}}
      <div class="add-cover-preview-row">
        <div class="add-cover-box" id="addCoverBox">
            <img id="addCoverImg" src="" alt="Cover">
            <div class="add-cover-ph" id="addCoverPh">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span>Preview</span>
            </div>
        </div>
        <div class="field" style="margin-bottom:0;">
            <label>Cover Image URL</label>
            <input type="url" name="cover_url" id="addCoverUrl" placeholder="https://example.com/cover.jpg"
                   oninput="liveAddCover(this.value)">
            <small style="color:#8884a8;font-size:.76rem;margin-top:3px;">Direct image URL — students see this as thumbnail.</small>
        </div>
      </div>

      <div class="field"><label>File URL</label><input type="text" name="file_url" placeholder="https://drive.google.com/..."></div>
      <div class="field"><label>Description</label><textarea name="description" placeholder="Optional book description"></textarea></div>
      <div class="modal-actions">
        <button type="button" class="btn-cancel-modal" onclick="closeModal('addModal')">Cancel</button>
        <button type="submit" class="btn-confirm-save">Add Book</button>
      </div>
    </form>
  </div>
</div>

{{-- ARCHIVE MODAL --}}
<div class="modal-overlay" id="archiveModal">
  <div class="modal-box sm">
    <div class="modal-title">Archive Book</div>
    <div id="archiveBody" style="font-size:.86rem;color:#5a5a7a;margin-bottom:22px;line-height:1.5;"></div>
    <form id="archiveForm" method="POST">
      @csrf @method('DELETE')
      <div class="modal-actions">
        <button type="button" class="btn-cancel-modal" onclick="closeModal('archiveModal')">Cancel</button>
        <button type="submit" class="btn-confirm-del">Archive</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
function closeModal(id){ document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(m => m.addEventListener('click', e => { if(e.target===m) m.classList.remove('open'); }));

// ── Live cover preview for edit modal (updates left panel) ─────────
function liveEditCover(url) {
    var img = document.getElementById('editCoverImg');
    var ph  = document.getElementById('editCoverPh');
    if (!url) { img.style.display='none'; ph.style.display='flex'; img.src=''; return; }
    img.onload  = function(){ img.style.display='block'; ph.style.display='none'; };
    img.onerror = function(){ img.style.display='none';  ph.style.display='flex'; };
    img.src = url;
}

// ── Live cover preview for add modal ──────────────────────────────
function liveAddCover(url) {
    var img = document.getElementById('addCoverImg');
    var ph  = document.getElementById('addCoverPh');
    var box = document.getElementById('addCoverBox');
    if (!url) { img.style.display='none'; ph.style.display='flex'; img.src=''; box.classList.remove('has-image'); return; }
    img.onload  = function(){ img.style.display='block'; ph.style.display='none'; box.classList.add('has-image'); };
    img.onerror = function(){ img.style.display='none';  ph.style.display='flex'; box.classList.remove('has-image'); };
    img.src = url;
}

// ── Open Add modal ─────────────────────────────────────────────────
function openAddModal() {
    document.getElementById('addModal').classList.add('open');
    document.getElementById('addCoverUrl').value = '';
    liveAddCover('');
}

// ── Open Edit modal ────────────────────────────────────────────────
function openEditModal(id, title, authorId, catId, fmtId, isbn, total, avail, status, fileUrl, coverUrl, desc) {
    document.getElementById('editForm').action    = `/admin/books/${id}`;
    document.getElementById('eTitle').value       = title;
    document.getElementById('eIsbn').value        = isbn;
    document.getElementById('eTotalCopies').value = total;
    document.getElementById('eAvailCopies').value = avail;
    document.getElementById('eFileUrl').value     = fileUrl  || '';
    document.getElementById('eDesc').value        = desc     || '';
    document.getElementById('eCat').value         = catId;
    document.getElementById('eStatus').value      = status;
    document.getElementById('eCoverUrl').value    = coverUrl || '';
    liveEditCover(coverUrl || '');
    document.getElementById('editModal').classList.add('open');
}

// ── Open View modal ────────────────────────────────────────────────
function openViewBook(title, author, cat, fmt, isbn, total, avail, status, desc, coverUrl) {
    document.getElementById('vbTitle').textContent      = title;
    document.getElementById('vbAuthor').textContent     = author;
    document.getElementById('vbCat').textContent        = cat;
    document.getElementById('vbFormat').textContent     = fmt;
    document.getElementById('vbIsbn').textContent       = isbn;
    document.getElementById('vbTotal').textContent      = total;
    document.getElementById('vbAvail').textContent      = avail;
    document.getElementById('vbStatus').textContent     = status;
    document.getElementById('vbDesc').textContent       = desc;
    document.getElementById('vbCoverTitle').textContent = title;
    document.getElementById('vbCoverAuthor').textContent= author;

    var img = document.getElementById('vbCoverImg');
    var ph  = document.getElementById('vbCoverPh');
    if (coverUrl) { img.src=coverUrl; img.style.display='block'; ph.style.display='none'; }
    else          { img.style.display='none'; ph.style.display='flex'; }

    document.getElementById('viewBookModal').classList.add('open');
}

// ── Archive modal ──────────────────────────────────────────────────
function openArchiveModal(id, title) {
    document.getElementById('archiveBody').innerHTML = `Archive <strong>${title}</strong>? It will be hidden from the catalog but retained in the database.`;
    document.getElementById('archiveForm').action = `/admin/books/${id}`;
    document.getElementById('archiveModal').classList.add('open');
}

// ── Edit button listeners ──────────────────────────────────────────
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function () {
        openEditModal(
            this.dataset.id, this.dataset.title, this.dataset.authorId,
            this.dataset.categoryId, this.dataset.formatId, this.dataset.isbn,
            this.dataset.totalCopies, this.dataset.availableCopies, this.dataset.status,
            this.dataset.fileUrl, this.dataset.coverUrl, this.dataset.description
        );
    });
});
</script>
@endpush