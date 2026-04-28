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

/* Modal */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(10,10,20,.5); z-index:300; align-items:center; justify-content:center; }
.modal-overlay.open { display:flex; }
.modal-box { background:white; border-radius:12px; padding:28px 28px 24px; max-width:520px; width:90%; box-shadow:0 20px 50px rgba(0,0,0,.25); animation:popIn .2s ease; }
.modal-box.sm { max-width:380px; }
@keyframes popIn { from{transform:scale(.92);opacity:0} to{transform:scale(1);opacity:1} }
.modal-title { font-size:1rem; font-weight:700; color:#1a1a2e; margin-bottom:18px; padding-bottom:12px; border-bottom:1px solid #f0eef8; }
.modal-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:20px; }
.modal-actions button, .modal-actions a { border:none; border-radius:8px; padding:9px 20px; font-family:'Outfit',sans-serif; font-size:.84rem; font-weight:600; cursor:pointer; text-decoration:none; display:inline-block; transition:opacity .15s; }
.modal-actions button:hover, .modal-actions a:hover { opacity:.85; }
.btn-cancel-modal  { background:#f0eef8; color:#5a5a7a; }
.btn-confirm-save  { background:#2a3050; color:white; }
.btn-confirm-del   { background:#e74c3c; color:white; }

/* Form inside modal */
.form-grid2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.field { display:flex; flex-direction:column; gap:5px; margin-bottom:12px; }
.field label { font-size:.78rem; font-weight:600; color:#5a5a7a; letter-spacing:.04em; text-transform:uppercase; }
.field input, .field select, .field textarea {
    background:#f0eef8; border:1.5px solid transparent; border-radius:8px;
    padding:10px 12px; font-family:'Outfit',sans-serif; font-size:.88rem; color:#1a1a2e;
    outline:none; transition:border-color .2s;
}
.field input:focus, .field select:focus, .field textarea:focus { border-color:#a8a4e0; background:#faf8ff; }
.field textarea { resize:vertical; min-height:72px; }
.field select { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238884a8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 10px center; cursor:pointer; }
.field-ro input { background:#e8e6f0; color:#8884a8; cursor:not-allowed; }

/* View details table */
.detail-table { width:100%; font-size:.85rem; border-collapse:collapse; margin:14px 0; }
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

    {{-- Filters --}}
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
                {{-- View --}}
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
                        '{{ addslashes($book->description ?? '—') }}'
                    )">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
                {{-- Edit --}}
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
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
                {{-- Archive --}}
                <button class="action-btn btn-deactivate" title="Archive"
                    onclick="openArchiveModal({{ $book->ebook_id }}, '{{ addslashes($book->title) }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>

    {{-- Pagination --}}
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

{{-- ── ADD BOOK MODAL ── --}}
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
          <input type="text" name="author_input" id="authorInput"
                 placeholder="Type author name..." autocomplete="off"
                 required list="author-suggestions" style="width:100%;">
          <datalist id="author-suggestions">
              @foreach($authors as $a)
                  <option value="{{ $a->author_name }}">
              @endforeach
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
      <div class="field">
          <label>Cover Image URL</label>
          <input type="url" name="cover_url" placeholder="https://example.com/cover.jpg" style="width:100%;">
          <small style="color:#8884a8;font-size:0.78rem;">Optional: Direct image link (JPG, PNG, WebP).</small>
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

{{-- ── EDIT BOOK MODAL ── --}}
<div class="modal-overlay" id="editModal">
  <div class="modal-box">
    <div class="modal-title">Edit Book</div>
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
          <input type="url" name="cover_url" id="eCoverUrl" placeholder="https://example.com/cover.jpg" style="width:100%;">
          <small style="color:#8884a8;font-size:0.78rem;">Optional: Direct image link (JPG, PNG, WebP).</small>
      </div>
      <div class="field"><label>File URL</label><input type="text" name="file_url" id="eFileUrl" placeholder="https://..."></div>
      <div class="field"><label>Description</label><textarea name="description" id="eDesc"></textarea></div>
      <div class="modal-actions">
        <button type="button" class="btn-cancel-modal" onclick="closeModal('editModal')">Cancel</button>
        <button type="submit" class="btn-confirm-save">Save Changes</button>
      </div>
    </form>
  </div>
</div>

{{-- ── VIEW BOOK MODAL ── --}}
<div class="modal-overlay" id="viewBookModal">
  <div class="modal-box">
    <div class="modal-title">Book Details</div>
    <table class="detail-table">
      <tr><td>Title</td>       <td id="vbTitle"></td></tr>
      <tr><td>Author</td>      <td id="vbAuthor"></td></tr>
      <tr><td>Category</td>    <td id="vbCat"></td></tr>
      <tr><td>Format</td>      <td id="vbFormat"></td></tr>
      <tr><td>ISBN</td>        <td id="vbIsbn"></td></tr>
      <tr><td>Total Copies</td><td id="vbTotal"></td></tr>
      <tr><td>Available</td>   <td id="vbAvail"></td></tr>
      <tr><td>Status</td>      <td id="vbStatus"></td></tr>
      <tr><td>Description</td> <td id="vbDesc" style="white-space:pre-wrap;"></td></tr>
    </table>
    <div class="modal-actions"><button class="btn-cancel-modal" onclick="closeModal('viewBookModal')">Close</button></div>
  </div>
</div>

{{-- ── ARCHIVE CONFIRM MODAL ── --}}
<div class="modal-overlay" id="archiveModal">
  <div class="modal-box sm">
    <div class="modal-title">Archive Book</div>
    <div class="modal-body" id="archiveBody" style="font-size:.86rem;color:#5a5a7a;margin-bottom:22px;line-height:1.5;"></div>
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
document.querySelectorAll('.modal-overlay').forEach(m=>m.addEventListener('click',e=>{if(e.target===m)m.classList.remove('open');}));

function openAddModal(){ document.getElementById('addModal').classList.add('open'); }

function openEditModal(id, title, authorId, catId, fmtId, isbn, total, avail, status, fileUrl, coverUrl, desc) {
    document.getElementById('editForm').action    = `/admin/books/${id}`;
    document.getElementById('eTitle').value       = title;
    document.getElementById('eIsbn').value        = isbn;
    document.getElementById('eTotalCopies').value = total;
    document.getElementById('eAvailCopies').value = avail;
    document.getElementById('eFileUrl').value     = fileUrl  || '';
    document.getElementById('eCoverUrl').value    = coverUrl || '';
    document.getElementById('eDesc').value        = desc     || '';
    document.getElementById('eCat').value         = catId;
    document.getElementById('eStatus').value      = status;
    document.getElementById('editModal').classList.add('open');
}

function openViewBook(title, author, cat, fmt, isbn, total, avail, status, desc) {
    document.getElementById('vbTitle').textContent  = title;
    document.getElementById('vbAuthor').textContent = author;
    document.getElementById('vbCat').textContent    = cat;
    document.getElementById('vbFormat').textContent = fmt;
    document.getElementById('vbIsbn').textContent   = isbn;
    document.getElementById('vbTotal').textContent  = total;
    document.getElementById('vbAvail').textContent  = avail;
    document.getElementById('vbStatus').textContent = status;
    document.getElementById('vbDesc').textContent   = desc;
    document.getElementById('viewBookModal').classList.add('open');
}

function openArchiveModal(id, title) {
    document.getElementById('archiveBody').innerHTML = `Archive <strong>${title}</strong>? It will be hidden from the catalog but retained in the database.`;
    document.getElementById('archiveForm').action = `/admin/books/${id}`;
    document.getElementById('archiveModal').classList.add('open');
}

document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function () {
        openEditModal(
            this.dataset.id,
            this.dataset.title,
            this.dataset.authorId,
            this.dataset.categoryId,
            this.dataset.formatId,
            this.dataset.isbn,
            this.dataset.totalCopies,
            this.dataset.availableCopies,
            this.dataset.status,
            this.dataset.fileUrl,
            this.dataset.coverUrl,
            this.dataset.description
        );
    });
});
</script>
@endpush