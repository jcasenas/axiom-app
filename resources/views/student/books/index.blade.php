@extends('student.layout')
@section('title', 'Browse Books')
@section('content')

@php $rp = Auth::user()->isFaculty() ? 'faculty' : 'student'; @endphp

@push('styles')
<style>
/* ── Wide two-column modal ── */
.book-modal-overlay {
    display:none; position:fixed; inset:0;
    background:rgba(0,0,0,0.55); z-index:1000;
    align-items:center; justify-content:center; padding:1rem;
}
.book-modal-overlay.open { display:flex; }

.book-modal-wide {
    background:#fff; border-radius:14px; width:100%; max-width:720px;
    box-shadow:0 24px 60px rgba(0,0,0,.28);
    display:flex; overflow:hidden; max-height:92vh;
    animation:bookPop .2s ease;
}
@keyframes bookPop { from{transform:scale(.93);opacity:0} to{transform:scale(1);opacity:1} }

/* Left cover panel */
.bm-cover-panel {
    width:200px; flex-shrink:0;
    background:linear-gradient(160deg,#1a1a2e 0%,#2a3050 100%);
    display:flex; flex-direction:column; align-items:center;
    justify-content:center; padding:24px 14px; gap:12px;
}
.bm-cover-panel img {
    width:148px; aspect-ratio:2/3; object-fit:cover;
    border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,.4);
    display:none;
}
.bm-cover-ph {
    width:148px; aspect-ratio:2/3; border-radius:10px;
    background:rgba(255,255,255,.08);
    border:2px dashed rgba(168,164,224,.35);
    display:flex; flex-direction:column;
    align-items:center; justify-content:center; gap:8px;
}
.bm-cover-ph svg  { color:rgba(168,164,224,.45); width:36px; height:36px; }
.bm-cover-ph span { font-size:.7rem; color:rgba(168,164,224,.55); text-align:center; }
.bm-cover-title  {
    font-size:.82rem; font-weight:600; color:rgba(255,255,255,.85);
    text-align:center; line-height:1.4; max-width:160px;
}
.bm-cover-author {
    font-size:.72rem; color:rgba(168,164,224,.65); text-align:center; max-width:160px;
}

/* Right content panel */
.bm-content-panel {
    flex:1; display:flex; flex-direction:column; overflow:hidden;
    min-width:0;
}
.bm-content-body {
    flex:1; overflow-y:auto; padding:1.5rem;
}
.bm-content-footer {
    padding:1rem 1.5rem; border-top:1px solid #f3f4f6;
    display:flex; align-items:center; justify-content:space-between;
    gap:1rem; flex-wrap:wrap; flex-shrink:0; background:#fff;
}

.bm-close {
    position:absolute; top:1rem; right:1rem;
    background:none; border:none; font-size:1.25rem;
    cursor:pointer; color:rgba(168,164,224,.7); z-index:1;
    line-height:1;
}
.bm-close:hover { color:#fff; }

/* Meta table inside right panel */
.bm-meta-table { width:100%; font-size:.82rem; border-collapse:collapse; margin-top:.5rem; }
.bm-meta-table td { padding:.3rem 0; vertical-align:top; }
.bm-meta-table td:first-child { color:#9ca3af; width:110px; flex-shrink:0; }
.bm-meta-table td:last-child  { font-weight:500; color:#374151; }

/* Description */
.bm-desc-wrap { margin-top:1rem; padding-top:1rem; border-top:1px solid #f3f4f6; }
.bm-desc-label {
    font-size:.7rem; font-weight:700; color:#a8a4e0;
    text-transform:uppercase; letter-spacing:.08em; margin-bottom:.4rem;
}
.bm-desc-text {
    font-size:.82rem; color:#4b5563; line-height:1.65;
    white-space:pre-wrap; text-align:justify; margin:0;
}

/* Status badge inside modal */
.bm-badge {
    display:inline-block; font-size:.72rem; font-weight:700;
    padding:2px 10px; border-radius:999px;
}
.bm-badge-unavail  { background:#fee2e2; color:#dc2626; }
.bm-badge-borrowed { background:#ede9fe; color:#6d28d9; }

/* Mobile: slide up from bottom */
@media (max-width: 640px) {
    .book-modal-overlay { align-items:flex-end; padding:0; }
    .book-modal-wide {
        max-width:100%; border-radius:1rem 1rem 0 0;
        flex-direction:column; max-height:88vh;
    }
    .bm-cover-panel {
        width:100%; flex-direction:row; padding:1rem 1.25rem;
        gap:1rem; justify-content:flex-start; min-height:unset;
    }
    .bm-cover-panel img,
    .bm-cover-ph { width:72px; }
    .bm-cover-ph  { aspect-ratio:2/3; }
}
</style>
@endpush

<div class="content-card">

    @if($atLimit)
        <div class="flash flash-error" style="margin-bottom:1.25rem;">
            You have reached your borrow limit of {{ $borrowLimit }} book(s).
            Return or wait for existing borrows to expire before requesting new ones.
        </div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route($rp.'.books.index') }}">
        <div class="filter-row">
            <label>Category:</label>
            <select name="category" class="filter-select">
                <option value="all" {{ request('category', 'all') === 'all' ? 'selected' : '' }}>All</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->category_id }}"
                        {{ request('category') == $cat->category_id ? 'selected' : '' }}>
                        {{ $cat->category_name }}
                    </option>
                @endforeach
            </select>
            <input type="text" name="search" class="search-input"
                   placeholder="Title, author, or ISBN..."
                   value="{{ request('search') }}">
            <button type="submit" class="btn-filter">Apply Filter</button>
            @if(request()->hasAny(['search','category']))
                <a href="{{ route($rp.'.books.index') }}"
                   style="font-size:0.8rem;color:#6b7280;align-self:center;text-decoration:none;">Clear</a>
            @endif
        </div>
    </form>

    @if(!$books->isEmpty())
        <p style="font-size:0.8rem;color:#9ca3af;margin-bottom:1.25rem;">
            Showing {{ $books->firstItem() }}–{{ $books->lastItem() }} of {{ $books->total() }} books
        </p>
    @endif

    @if($books->isEmpty())
        <div style="text-align:center;padding:4rem 1rem;color:#9ca3af;">
            <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"
                 style="margin:0 auto 1rem;display:block;color:#d1d5db;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            No books found matching your criteria.
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:1.25rem;margin-bottom:1.5rem;">
            @foreach($books as $book)
            @php
                $alreadyHave = in_array($book->ebook_id, $alreadyBorrowed);
                $noStock     = $book->available_copies < 1;
            @endphp

            <div data-book-id="{{ $book->ebook_id }}"
                 onclick="openBookModal(
                    {{ $book->ebook_id }},
                    '{{ addslashes($book->title) }}',
                    '{{ addslashes($book->author->author_name ?? '') }}',
                    '{{ addslashes($book->category->category_name ?? '') }}',
                    '{{ $book->format->format_type ?? '' }}',
                    '{{ $book->isbn ?? '' }}',
                    {{ $book->available_copies }},
                    '{{ addslashes($book->cover_url ?? '') }}',
                    {{ $alreadyHave ? 'true' : 'false' }},
                    {{ $atLimit ? 'true' : 'false' }},
                    {{ $noStock ? 'true' : 'false' }},
                    '{{ addslashes($book->description ?? '') }}'
                )"
                style="cursor:pointer;display:flex;flex-direction:column;align-items:center;gap:0.6rem;padding:0.75rem;border-radius:0.75rem;background:#fff;border:1px solid #f0eeff;transition:box-shadow 0.2s,transform 0.2s;position:relative;"
                onmouseover="this.style.boxShadow='0 4px 16px rgba(168,164,224,0.25)';this.style.transform='translateY(-2px)'"
                onmouseout="this.style.boxShadow='none';this.style.transform='translateY(0)'"
            >
                @if($noStock)
                    <div style="position:absolute;top:8px;left:8px;background:#ef4444;color:#fff;font-size:0.65rem;font-weight:700;padding:2px 8px;border-radius:999px;letter-spacing:0.04em;">UNAVAILABLE</div>
                @elseif($alreadyHave)
                    <div style="position:absolute;top:8px;left:8px;background:#a8a4e0;color:#fff;font-size:0.65rem;font-weight:700;padding:2px 8px;border-radius:999px;letter-spacing:0.04em;">BORROWED</div>
                @endif

                @if($book->cover_url)
                    <img src="{{ $book->cover_url }}" alt="{{ $book->title }}"
                         style="width:100%;aspect-ratio:2/3;object-fit:cover;border-radius:0.5rem;background:#f3f4f6;">
                @else
                    <div style="width:100%;aspect-ratio:2/3;border-radius:0.5rem;background:linear-gradient(135deg,#ede9fe 0%,#c4b5fd 100%);display:flex;flex-direction:column;align-items:center;justify-content:center;padding:0.75rem;gap:0.5rem;">
                        <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="1.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span style="font-size:0.7rem;font-weight:600;color:#5b21b6;text-align:center;line-height:1.3;">
                            {{ Str::limit($book->title, 40) }}
                        </span>
                    </div>
                @endif

                <div style="width:100%;text-align:center;">
                    <p style="margin:0;font-size:0.78rem;font-weight:600;color:#1a1a2e;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        {{ $book->title }}
                    </p>
                    <p style="margin:0.2rem 0 0;font-size:0.7rem;color:#9ca3af;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $book->author->author_name ?? '---' }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="pagination-row">
            @if($books->onFirstPage())
                <span style="color:#ccc;display:flex;align-items:center;gap:4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Previous
                </span>
            @else
                <a href="{{ $books->previousPageUrl() }}" style="display:flex;align-items:center;gap:4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Previous
                </a>
            @endif
            <span style="font-size:.8rem;color:#aaa;">Page {{ $books->currentPage() }} of {{ $books->lastPage() }}</span>
            @if($books->hasMorePages())
                <a href="{{ $books->nextPageUrl() }}" style="display:flex;align-items:center;gap:4px;">
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
     BOOK DETAIL MODAL — two-column wide layout
══════════════════════════════════════════════════════════ --}}
<div id="book-modal" class="book-modal-overlay">
  <div class="book-modal-wide" style="position:relative;">

    <button class="bm-close" onclick="closeBookModal()">✕</button>

    {{-- Left: dark cover panel --}}
    <div class="bm-cover-panel">
        <img id="bm-cover-img" src="" alt="Cover">
        <div id="bm-cover-ph" class="bm-cover-ph">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span>No cover</span>
        </div>
        <div class="bm-cover-title"  id="bm-cover-title"></div>
        <div class="bm-cover-author" id="bm-cover-author"></div>
    </div>

    {{-- Right: details + borrow --}}
    <div class="bm-content-panel">

        <div class="bm-content-body">

            {{-- Title + author --}}
            <p id="bm-title"  style="margin:0 2rem .2rem 0;font-size:1.05rem;font-weight:700;color:#1a1a2e;line-height:1.35;"></p>
            <p id="bm-author" style="margin:0 0 .85rem;font-size:.82rem;color:#6b7280;"></p>

            {{-- Meta --}}
            <table class="bm-meta-table">
                <tr><td>Category</td> <td id="bm-category"></td></tr>
                <tr><td>Format</td>   <td id="bm-format"></td></tr>
                <tr><td>ISBN</td>     <td id="bm-isbn"></td></tr>
                <tr><td>Available</td><td id="bm-copies" style="font-weight:700;"></td></tr>
            </table>

            {{-- Description --}}
            <div id="bm-desc-wrap" class="bm-desc-wrap" style="display:none;">
                <p class="bm-desc-label">About this Book</p>
                <p id="bm-desc" class="bm-desc-text"></p>
            </div>

        </div>

        {{-- Sticky footer --}}
        <div class="bm-content-footer">
            <p id="bm-status-msg" style="margin:0;font-size:.8rem;color:#6b7280;flex:1;min-width:80px;"></p>
            <form id="bm-borrow-form" method="POST" action="" style="margin:0;">
                @csrf
                <button id="bm-borrow-btn" type="submit"
                    style="padding:.6rem 1.5rem;border:none;border-radius:.5rem;font-size:.875rem;font-weight:600;cursor:pointer;background:#2a3050;color:#fff;white-space:nowrap;transition:background .2s;">
                    Borrow
                </button>
            </form>
        </div>

    </div>

  </div>
</div>

@push('scripts')
<script>
var borrowRouteBase = '{{ url($rp."/books") }}';

function openBookModal(id, title, author, category, format, isbn, copies,
                       coverUrl, alreadyHave, atLimit, noStock, description) {

    // Left panel
    document.getElementById('bm-cover-title').textContent  = title;
    document.getElementById('bm-cover-author').textContent = author;

    var img = document.getElementById('bm-cover-img');
    var ph  = document.getElementById('bm-cover-ph');
    if (coverUrl) {
        img.src = coverUrl;
        img.style.display = 'block';
        ph.style.display  = 'none';
    } else {
        img.style.display = 'none';
        ph.style.display  = 'flex';
    }

    // Right panel meta
    document.getElementById('bm-title').textContent    = title;
    document.getElementById('bm-author').textContent   = author;
    document.getElementById('bm-category').textContent = category || '---';
    document.getElementById('bm-format').textContent   = format   || '---';
    document.getElementById('bm-isbn').textContent     = isbn     || '---';

    var copiesEl = document.getElementById('bm-copies');
    copiesEl.textContent = copies;
    copiesEl.style.color = copies > 0 ? '#16a34a' : '#dc2626';

    // Description
    var descWrap = document.getElementById('bm-desc-wrap');
    var descEl   = document.getElementById('bm-desc');
    if (description && description.trim()) {
        descEl.textContent     = description;
        descWrap.style.display = 'block';
    } else {
        descWrap.style.display = 'none';
    }

    // Borrow button state
    var btn  = document.getElementById('bm-borrow-btn');
    var msg  = document.getElementById('bm-status-msg');
    var form = document.getElementById('bm-borrow-form');

    form.action = borrowRouteBase + '/' + id + '/borrow';
    msg.textContent = '';

    if (alreadyHave) {
        btn.disabled = true;
        btn.style.cssText = 'padding:.6rem 1.5rem;border:none;border-radius:.5rem;font-size:.875rem;font-weight:600;cursor:not-allowed;background:#e5e7eb;color:#9ca3af;white-space:nowrap;';
        btn.textContent = 'Already Borrowed';
    } else if (atLimit) {
        btn.disabled = true;
        btn.style.cssText = 'padding:.6rem 1.5rem;border:none;border-radius:.5rem;font-size:.875rem;font-weight:600;cursor:not-allowed;background:#e5e7eb;color:#9ca3af;white-space:nowrap;';
        btn.textContent = 'Limit Reached';
        msg.textContent = 'You have reached your borrow limit.';
    } else if (noStock) {
        btn.disabled = true;
        btn.style.cssText = 'padding:.6rem 1.5rem;border:none;border-radius:.5rem;font-size:.875rem;font-weight:600;cursor:not-allowed;background:#e5e7eb;color:#9ca3af;white-space:nowrap;';
        btn.textContent = 'Unavailable';
        msg.textContent = 'No copies available at this time.';
    } else {
        btn.disabled = false;
        btn.style.cssText = 'padding:.6rem 1.5rem;border:none;border-radius:.5rem;font-size:.875rem;font-weight:600;cursor:pointer;background:#2a3050;color:#fff;white-space:nowrap;transition:background .2s;';
        btn.textContent = 'Borrow';
    }

    document.getElementById('book-modal').classList.add('open');

    // Clean up ?highlight param
    if (window.history.replaceState) {
        var url = new URL(window.location.href);
        url.searchParams.delete('highlight');
        window.history.replaceState({}, '', url.toString());
    }
}

function closeBookModal() {
    document.getElementById('book-modal').classList.remove('open');
}

document.getElementById('book-modal').addEventListener('click', function(e) {
    if (e.target === this) closeBookModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeBookModal();
});

// Auto-open when arriving via ?highlight=id
(function () {
    var params      = new URLSearchParams(window.location.search);
    var highlightId = params.get('highlight');
    if (!highlightId) return;
    var card = document.querySelector('[data-book-id="' + highlightId + '"]');
    if (card) setTimeout(function(){ card.click(); }, 150);
})();
</script>
@endpush

@endsection