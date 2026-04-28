@extends('student.layout')
@section('title', 'My Books')

@push('styles')
<style>
.modal-backdrop {
    display: flex;
    align-items: center;
    justify-content: center;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
    padding: 1rem;
}
.modal-backdrop.is-open {
    opacity: 1;
    pointer-events: all;
}

/* Tabs */
.my-books-tabs {
    display: flex;
    gap: 0;
    border-bottom: 2px solid #e5e7eb;
    margin-bottom: 1.5rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.my-books-tab {
    padding: 0.6rem 1.25rem;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    margin-bottom: -2px;
    flex-shrink: 0;
}

@media (max-width: 768px) {
    /* Modal: full screen on mobile */
    #my-book-modal > div,
    #cancel-modal > div {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        border-radius: 1rem 1rem 0 0 !important;
        position: fixed !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
    }
    .modal-backdrop {
        align-items: flex-end !important;
        padding: 0 !important;
    }
}

@media (max-width: 540px) {
    .my-books-tab { font-size: 0.8rem; padding: 0.5rem 0.9rem; }
}
</style>
@endpush

@section('content')

@php
    $rp = Auth::user()->isFaculty() ? 'faculty' : 'student';
@endphp

<div class="content-card">

    <p style="font-size:1.3rem;font-weight:700;color:#1a1a2e;margin-bottom:20px;">My Books</p>

    {{-- Tabs --}}
    <div class="my-books-tabs">
        <a href="{{ route($rp.'.my-books.index', array_merge(request()->except(['tab','page']), ['tab'=>'active'])) }}"
           class="my-books-tab"
           style="border-bottom:3px solid {{ $tab === 'active' ? '#4f46e5' : 'transparent' }};color:{{ $tab === 'active' ? '#4f46e5' : '#6b7280' }};">
            Current Borrows
            @if($activeCount > 0)
                <span style="margin-left:6px;background:{{ $tab === 'active' ? '#4f46e5' : '#e5e7eb' }};color:{{ $tab === 'active' ? '#fff' : '#374151' }};font-size:0.7rem;font-weight:700;padding:1px 7px;border-radius:999px;">{{ $activeCount }}</span>
            @endif
        </a>
        <a href="{{ route($rp.'.my-books.index', array_merge(request()->except(['tab','page']), ['tab'=>'history'])) }}"
           class="my-books-tab"
           style="border-bottom:3px solid {{ $tab === 'history' ? '#4f46e5' : 'transparent' }};color:{{ $tab === 'history' ? '#4f46e5' : '#6b7280' }};">
            Borrow History
            @if($historyCount > 0)
                <span style="margin-left:6px;background:{{ $tab === 'history' ? '#4f46e5' : '#e5e7eb' }};color:{{ $tab === 'history' ? '#fff' : '#374151' }};font-size:0.7rem;font-weight:700;padding:1px 7px;border-radius:999px;">{{ $historyCount }}</span>
            @endif
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route($rp.'.my-books.index') }}">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div class="filter-row">
            <label>Category:</label>
            <select name="category" class="filter-select">
                <option value="all" {{ request('category', 'all') === 'all' ? 'selected' : '' }}>All</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->category_id }}" {{ request('category') == $cat->category_id ? 'selected' : '' }}>
                        {{ $cat->category_name }}
                    </option>
                @endforeach
            </select>

            @if($tab === 'active')
                <label>Status:</label>
                <select name="status" class="filter-select">
                    <option value="all"      {{ request('status', 'all') === 'all'     ? 'selected' : '' }}>All</option>
                    <option value="pending"  {{ request('status') === 'pending'        ? 'selected' : '' }}>Pending</option>
                    <option value="active"   {{ request('status') === 'active'         ? 'selected' : '' }}>Active</option>
                    <option value="due_soon" {{ request('status') === 'due_soon'       ? 'selected' : '' }}>Due Soon</option>
                </select>
            @endif

            <input type="text" name="search" class="search-input"
                   placeholder="Search by title or author..."
                   value="{{ request('search') }}">

            <button type="submit" class="btn-filter">Apply Filter</button>

            @if(request()->hasAny(['search','category','status']))
                <a href="{{ route($rp.'.my-books.index', ['tab' => $tab]) }}"
                   style="font-size:0.8rem;color:#6b7280;align-self:center;text-decoration:none;">Clear</a>
            @endif
        </div>
    </form>

    {{-- Empty state --}}
    @if($borrows->isEmpty())
        <div style="text-align:center;padding:4rem 1rem;color:#9ca3af;">
            <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"
                 style="margin:0 auto 1rem;display:block;color:#d1d5db;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            {{ $tab === 'history' ? 'No borrow history yet.' : 'You have no active or pending borrows.' }}
        </div>

    @else
        {{-- Card Grid --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:1.25rem;margin-bottom:1.5rem;">
            @foreach($borrows as $borrow)
                @php
                    $s    = $borrow->status;
                    $book = $borrow->ebook;
                    $bid  = $borrow->borrow_id;
                @endphp

                <div onclick="openMyBookModal(
                        '{{ addslashes($book->title ?? '') }}',
                        '{{ addslashes($book->author->author_name ?? '') }}',
                        '{{ addslashes($book->category->category_name ?? '') }}',
                        '{{ $book->format->format_type ?? '' }}',
                        '{{ $book->isbn ?? '' }}',
                        '{{ $book->cover_url ?? '' }}',
                        '{{ $s }}',
                        '{{ $borrow->requested_at ? \Carbon\Carbon::parse($borrow->requested_at)->format('M d, Y') : '---' }}',
                        '{{ $borrow->borrow_date ? $borrow->borrow_date->format('M d, Y') : '---' }}',
                        '{{ $borrow->due_date ? $borrow->due_date->format('M d, Y') : '---' }}',
                        '{{ $borrow->access_expires_at ? \Carbon\Carbon::parse($borrow->access_expires_at)->format('M d, Y') : '---' }}',
                        {{ $bid }},
                        '{{ route($rp.'.my-books.read', $borrow) }}',
                        '{{ route($rp.'.my-books.cancel', $borrow) }}'
                    )"
                    style="cursor:pointer;display:flex;flex-direction:column;align-items:center;gap:0.6rem;padding:0.75rem;border-radius:0.75rem;background:#fff;border:1px solid #f0eeff;transition:box-shadow 0.2s,transform 0.2s;position:relative;"
                    onmouseover="this.style.boxShadow='0 4px 16px rgba(168,164,224,0.25)';this.style.transform='translateY(-2px)'"
                    onmouseout="this.style.boxShadow='none';this.style.transform='translateY(0)'"
                >
                    {{-- Status badge --}}
                    @if($s === 'pending')
                        <div style="position:absolute;top:8px;left:8px;background:#f59e0b;color:#fff;font-size:0.65rem;font-weight:700;padding:2px 8px;border-radius:999px;letter-spacing:0.04em;">PENDING</div>
                    @elseif($s === 'due_soon')
                        <div style="position:absolute;top:8px;left:8px;background:#d97706;color:#fff;font-size:0.65rem;font-weight:700;padding:2px 8px;border-radius:999px;letter-spacing:0.04em;">DUE SOON</div>
                    @elseif($s === 'expired')
                        <div style="position:absolute;top:8px;left:8px;background:#6b7280;color:#fff;font-size:0.65rem;font-weight:700;padding:2px 8px;border-radius:999px;letter-spacing:0.04em;">EXPIRED</div>
                    @elseif($s === 'cancelled')
                        <div style="position:absolute;top:8px;left:8px;background:#ef4444;color:#fff;font-size:0.65rem;font-weight:700;padding:2px 8px;border-radius:999px;letter-spacing:0.04em;">CANCELLED</div>
                    @elseif($s === 'active')
                        <div style="position:absolute;top:8px;left:8px;background:#16a34a;color:#fff;font-size:0.65rem;font-weight:700;padding:2px 8px;border-radius:999px;letter-spacing:0.04em;">ACTIVE</div>
                    @endif

                    {{-- Cover --}}
                    @if($book->cover_url)
                        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}"
                             style="width:100%;aspect-ratio:2/3;object-fit:cover;border-radius:0.5rem;background:#f3f4f6;
                                    {{ in_array($s, ['expired','cancelled']) ? 'opacity:0.45;' : '' }}">
                    @else
                        <div style="width:100%;aspect-ratio:2/3;border-radius:0.5rem;
                                    background:linear-gradient(135deg,#ede9fe 0%,#c4b5fd 100%);
                                    display:flex;flex-direction:column;align-items:center;justify-content:center;
                                    padding:0.75rem;gap:0.5rem;
                                    {{ in_array($s, ['expired','cancelled']) ? 'opacity:0.45;' : '' }}">
                            <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="1.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span style="font-size:0.7rem;font-weight:600;color:#5b21b6;text-align:center;line-height:1.3;">
                                {{ Str::limit($book->title ?? '', 40) }}
                            </span>
                        </div>
                    @endif

                    {{-- Title & Author --}}
                    <div style="width:100%;text-align:center;">
                        <p style="margin:0;font-size:0.78rem;font-weight:600;color:#1a1a2e;line-height:1.3;
                                   display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            {{ $book->title ?? '---' }}
                        </p>
                        <p style="margin:0.2rem 0 0;font-size:0.7rem;color:#9ca3af;
                                   white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $book->author->author_name ?? '---' }}
                        </p>
                    </div>
                </div>

                {{-- Hidden cancel form --}}
                @if($s === 'pending')
                    <form id="cancel-form-{{ $bid }}"
                          method="POST"
                          action="{{ route($rp.'.my-books.cancel', $borrow) }}"
                          style="display:none;">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif

            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="pagination-row">
            @if($borrows->onFirstPage())
                <span style="color:#ccc;display:flex;align-items:center;gap:4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Previous
                </span>
            @else
                <a href="{{ $borrows->previousPageUrl() }}" style="display:flex;align-items:center;gap:4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Previous
                </a>
            @endif
            <span style="font-size:.8rem;color:#aaa;">Page {{ $borrows->currentPage() }} of {{ $borrows->lastPage() }}</span>
            @if($borrows->hasMorePages())
                <a href="{{ $borrows->nextPageUrl() }}" style="display:flex;align-items:center;gap:4px;">
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

{{-- ── MY BOOK DETAIL MODAL ── --}}
<div id="my-book-modal" class="modal-backdrop">
    <div style="background:#fff;border-radius:1rem;width:100%;max-width:520px;overflow:hidden;position:relative;box-shadow:0 20px 60px rgba(0,0,0,0.2);">

        <button type="button" onclick="closeMyBookModal()"
                style="position:absolute;top:1rem;right:1rem;background:none;border:none;font-size:1.25rem;cursor:pointer;color:#9ca3af;z-index:1;">✕</button>

        {{-- Cover + Info --}}
        <div style="display:flex;gap:1.25rem;padding:1.5rem;flex-wrap:wrap;">
            <div style="flex-shrink:0;width:110px;">
                <img id="mb-cover-img" src="" alt=""
                     style="width:110px;aspect-ratio:2/3;object-fit:cover;border-radius:0.5rem;background:#f3f4f6;display:none;">
                <div id="mb-cover-placeholder"
                     style="width:110px;aspect-ratio:2/3;border-radius:0.5rem;background:linear-gradient(135deg,#ede9fe 0%,#c4b5fd 100%);display:flex;align-items:center;justify-content:center;">
                    <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="1.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>

            <div style="flex:1;min-width:0;">
                <p id="mb-title"  style="margin:0 0 0.2rem;font-size:1rem;font-weight:700;color:#1a1a2e;line-height:1.3;padding-right:1.5rem;"></p>
                <p id="mb-author" style="margin:0 0 0.75rem;font-size:0.8rem;color:#6b7280;"></p>

                <table style="width:100%;font-size:0.8rem;border-collapse:collapse;">
                    <tr><td style="padding:0.25rem 0;color:#9ca3af;width:110px;">Category</td>   <td id="mb-category" style="color:#374151;font-weight:500;"></td></tr>
                    <tr><td style="padding:0.25rem 0;color:#9ca3af;">Format</td>                 <td id="mb-format"   style="color:#374151;font-weight:500;"></td></tr>
                    <tr><td style="padding:0.25rem 0;color:#9ca3af;">ISBN</td>                   <td id="mb-isbn"     style="color:#374151;font-weight:500;"></td></tr>
                    <tr><td style="padding:0.25rem 0;color:#9ca3af;">Requested</td>              <td id="mb-requested"style="color:#374151;"></td></tr>
                    <tr><td style="padding:0.25rem 0;color:#9ca3af;">Borrow Date</td>            <td id="mb-borrow"   style="color:#374151;"></td></tr>
                    <tr><td style="padding:0.25rem 0;color:#9ca3af;">Due Date</td>               <td id="mb-due"      style="color:#374151;font-weight:600;"></td></tr>
                    <tr><td style="padding:0.25rem 0;color:#9ca3af;">Status</td>                 <td id="mb-status"></td></tr>
                </table>
            </div>
        </div>

        {{-- Footer actions --}}
        <div style="padding:1rem 1.5rem 1.5rem;border-top:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;gap:0.75rem;flex-wrap:wrap;">
            <p id="mb-msg" style="margin:0;font-size:0.78rem;color:#6b7280;flex:1;min-width:100px;"></p>

            <div style="display:flex;gap:0.6rem;flex-shrink:0;">
                <button type="button" id="mb-cancel-btn"
                        style="display:none;padding:0.55rem 1.1rem;border:1px solid #fca5a5;border-radius:0.5rem;background:#fff;color:#dc2626;font-size:0.8rem;font-weight:600;cursor:pointer;">
                    Cancel Request
                </button>
                <a id="mb-read-btn" href="#" target="_blank"
                   style="display:none;padding:0.55rem 1.25rem;border:none;border-radius:0.5rem;background:#4f46e5;color:#fff;font-size:0.875rem;font-weight:600;cursor:pointer;text-decoration:none;white-space:nowrap;">
                    📖 Read Book
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── CANCEL CONFIRMATION MODAL ── --}}
<div id="cancel-modal" class="modal-backdrop">
    <div style="background:#fff;border-radius:1rem;padding:2rem;width:100%;max-width:420px;position:relative;">
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem;">
            <span style="width:40px;height:40px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#dc2626" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </span>
            <h3 style="margin:0;font-size:1rem;font-weight:700;color:#111827;">Cancel Borrow Request?</h3>
        </div>
        <p style="margin:0 0 1.5rem;font-size:0.875rem;color:#6b7280;">
            Are you sure you want to cancel your request for
            <strong id="cancel-book-title" style="color:#111827;"></strong>?
            This cannot be undone.
        </p>
        <div style="display:flex;gap:0.75rem;justify-content:flex-end;">
            <button type="button" onclick="closeCancelModal()"
                    style="padding:0.5rem 1.25rem;border:1px solid #e5e7eb;border-radius:0.5rem;background:#fff;font-size:0.875rem;cursor:pointer;color:#374151;">
                Keep Request
            </button>
            <button type="button" id="cancel-confirm-btn"
                    style="padding:0.5rem 1.25rem;border:none;border-radius:0.5rem;background:#dc2626;color:#fff;font-size:0.875rem;font-weight:600;cursor:pointer;">
                Yes, Cancel It
            </button>
        </div>
    </div>
</div>

<script>
let currentCancelId = null;

const statusColors = {
    active:    '#16a34a',
    due_soon:  '#d97706',
    pending:   '#f59e0b',
    expired:   '#6b7280',
    cancelled: '#ef4444',
};

function openMyBookModal(title, author, category, format, isbn, coverUrl,
                         status, requested, borrowDate, dueDate, expiresAt,
                         borrowId, readUrl, cancelUrl) {

    var img = document.getElementById('mb-cover-img');
    var ph  = document.getElementById('mb-cover-placeholder');
    if (coverUrl) {
        img.src = coverUrl;
        img.style.display = 'block';
        ph.style.display  = 'none';
    } else {
        img.style.display = 'none';
        ph.style.display  = 'flex';
    }

    document.getElementById('mb-title').textContent     = title;
    document.getElementById('mb-author').textContent    = author;
    document.getElementById('mb-category').textContent  = category || '---';
    document.getElementById('mb-format').textContent    = format   || '---';
    document.getElementById('mb-isbn').textContent      = isbn     || '---';
    document.getElementById('mb-requested').textContent = requested;
    document.getElementById('mb-borrow').textContent    = borrowDate;
    document.getElementById('mb-due').textContent       = (status === 'active' || status === 'due_soon') ? dueDate : expiresAt;

    var statusEl = document.getElementById('mb-status');
    statusEl.textContent  = status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    statusEl.style.color  = statusColors[status] || '#374151';
    statusEl.style.fontWeight = '700';

    document.getElementById('mb-due').style.color = status === 'due_soon' ? '#d97706' : '#374151';

    var readBtn   = document.getElementById('mb-read-btn');
    var cancelBtn = document.getElementById('mb-cancel-btn');
    var msg       = document.getElementById('mb-msg');

    readBtn.style.display   = 'none';
    cancelBtn.style.display = 'none';
    msg.textContent         = '';
    msg.style.color         = '#9ca3af';

    if (status === 'active' || status === 'due_soon') {
        readBtn.href             = readUrl;
        readBtn.style.display    = 'inline-flex';
        readBtn.style.alignItems = 'center';
        if (status === 'due_soon') {
            msg.textContent = '⚠️ Access expiring soon!';
            msg.style.color = '#d97706';
        }
    } else if (status === 'pending') {
        currentCancelId         = borrowId;
        cancelBtn.style.display = 'inline-block';
        msg.textContent         = 'Awaiting librarian approval.';
        cancelBtn.onclick = function () {
            closeMyBookModal();
            document.getElementById('cancel-book-title').textContent = title;
            document.getElementById('cancel-modal').classList.add('is-open');
        };
    } else if (status === 'expired') {
        msg.textContent = 'Your access to this book has expired.';
    } else if (status === 'cancelled') {
        msg.textContent = 'This request was cancelled.';
    }

    document.getElementById('my-book-modal').classList.add('is-open');
}

function closeMyBookModal() {
    document.getElementById('my-book-modal').classList.remove('is-open');
}

document.getElementById('cancel-confirm-btn').addEventListener('click', function () {
    if (currentCancelId) {
        var form = document.getElementById('cancel-form-' + currentCancelId);
        if (form) {
            form.submit();
        } else {
            console.error('cancel-form-' + currentCancelId + ' not found in DOM');
        }
    }
});

function closeCancelModal() {
    currentCancelId = null;
    document.getElementById('cancel-modal').classList.remove('is-open');
}

['my-book-modal', 'cancel-modal'].forEach(function (id) {
    document.getElementById(id).addEventListener('click', function (e) {
        if (e.target === this) this.classList.remove('is-open');
    });
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-backdrop.is-open')
                .forEach(function (m) { m.classList.remove('is-open'); });
    }
});
</script>

@endsection