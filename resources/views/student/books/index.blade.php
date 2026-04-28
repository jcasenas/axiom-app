@extends('student.layout')
@section('title', 'Browse Books')
@section('content')

@php $rp = Auth::user()->isFaculty() ? 'faculty' : 'student'; @endphp

@push('styles')
<style>
@media (max-width: 768px) {
    #book-modal {
        align-items: flex-end !important;
        padding: 0 !important;
    }
    #book-modal > div {
        max-width: 100% !important;
        border-radius: 1rem 1rem 0 0 !important;
    }
    .modal-book-inner {
        flex-direction: column !important;
        align-items: center !important;
    }
    .modal-book-cover { width: 90px !important; }
    .modal-book-cover img,
    .modal-book-cover > div { width: 90px !important; }
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
                   style="font-size:0.8rem;color:#6b7280;align-self:center;text-decoration:none;">
                    Clear
                </a>
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

            {{-- Pass description as the 12th argument --}}
            <div data-book-id="{{ $book->ebook_id }}"
                 onclick="openBookModal(
                    {{ $book->ebook_id }},
                    '{{ addslashes($book->title) }}',
                    '{{ addslashes($book->author->author_name ?? '') }}',
                    '{{ addslashes($book->category->category_name ?? '') }}',
                    '{{ $book->format->format_type ?? '' }}',
                    '{{ $book->isbn ?? '' }}',
                    {{ $book->available_copies }},
                    '{{ $book->cover_url ?? '' }}',
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

{{-- ── Book Detail Modal ── --}}
<div id="book-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:1000;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:#fff;border-radius:1rem;width:100%;max-width:540px;overflow:hidden;position:relative;box-shadow:0 20px 60px rgba(0,0,0,0.2);max-height:90vh;display:flex;flex-direction:column;">

        <button onclick="closeBookModal()"
                style="position:absolute;top:1rem;right:1rem;background:none;border:none;font-size:1.25rem;cursor:pointer;color:#9ca3af;z-index:1;">✕</button>

        {{-- Scrollable content area --}}
        <div style="overflow-y:auto;flex:1;min-height:0;">

            {{-- Cover + metadata --}}
            <div class="modal-book-inner" style="display:flex;gap:1.25rem;padding:1.5rem;">
                <div class="modal-book-cover" style="flex-shrink:0;width:110px;">
                    <img id="modal-cover-img" src="" alt=""
                         style="width:110px;aspect-ratio:2/3;object-fit:cover;border-radius:0.5rem;background:#f3f4f6;display:none;">
                    <div id="modal-cover-placeholder"
                         style="width:110px;aspect-ratio:2/3;border-radius:0.5rem;background:linear-gradient(135deg,#ede9fe 0%,#c4b5fd 100%);display:flex;align-items:center;justify-content:center;">
                        <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="1.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>

                <div style="flex:1;min-width:0;">
                    <p id="modal-title"  style="margin:0 0 0.25rem;font-size:1rem;font-weight:700;color:#1a1a2e;line-height:1.3;padding-right:1.5rem;"></p>
                    <p id="modal-author" style="margin:0 0 0.75rem;font-size:0.8rem;color:#6b7280;"></p>
                    <table style="width:100%;font-size:0.8rem;border-collapse:collapse;">
                        <tr><td style="padding:0.3rem 0;color:#9ca3af;width:110px;">Category</td><td id="modal-category" style="color:#374151;font-weight:500;"></td></tr>
                        <tr><td style="padding:0.3rem 0;color:#9ca3af;">Format</td>              <td id="modal-format"   style="color:#374151;font-weight:500;"></td></tr>
                        <tr><td style="padding:0.3rem 0;color:#9ca3af;">ISBN</td>                <td id="modal-isbn"     style="color:#374151;font-weight:500;"></td></tr>
                        <tr><td style="padding:0.3rem 0;color:#9ca3af;">Available</td>           <td id="modal-copies"   style="font-weight:700;"></td></tr>
                    </table>
                </div>
            </div>

            {{-- Description — only rendered when content exists --}}
            <div id="modal-description-wrap"
                 style="display:none;padding:0 1.5rem 1.25rem;border-top:1px solid #f3f4f6;">
                <p style="margin:0.9rem 0 0.4rem;font-size:0.72rem;font-weight:700;color:#a8a4e0;text-transform:uppercase;letter-spacing:0.08em;">
                    About this Book
                </p>
                <p id="modal-description"
                   style="margin:0;font-size:0.82rem;color:#4b5563;line-height:1.65;white-space:pre-wrap;text-align:justify;"></p>
            </div>

        </div>

        {{-- Sticky footer --}}
        <div style="padding:1rem 1.5rem;border-top:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;flex-shrink:0;background:#fff;">
            <p id="modal-status-msg" style="margin:0;font-size:0.8rem;color:#6b7280;flex:1;min-width:100px;"></p>
            <form id="modal-borrow-form" method="POST" action="" style="margin:0;">
                @csrf
                <button id="modal-borrow-btn" type="submit"
                    style="padding:0.6rem 1.5rem;border:none;border-radius:0.5rem;font-size:0.875rem;font-weight:600;cursor:pointer;background:#4f46e5;color:#fff;white-space:nowrap;">
                    Borrow
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
var borrowRouteBase = '{{ url($rp."/books") }}';

// 12 parameters — description is the new 12th
function openBookModal(id, title, author, category, format, isbn, copies, coverUrl,
                       alreadyHave, atLimit, noStock, description) {

    document.getElementById('modal-title').textContent    = title;
    document.getElementById('modal-author').textContent   = author;
    document.getElementById('modal-category').textContent = category || '---';
    document.getElementById('modal-format').textContent   = format   || '---';
    document.getElementById('modal-isbn').textContent     = isbn     || '---';

    var copiesEl = document.getElementById('modal-copies');
    copiesEl.textContent = copies;
    copiesEl.style.color = copies > 0 ? '#16a34a' : '#dc2626';

    var img         = document.getElementById('modal-cover-img');
    var placeholder = document.getElementById('modal-cover-placeholder');
    if (coverUrl) {
        img.src = coverUrl;
        img.style.display         = 'block';
        placeholder.style.display = 'none';
    } else {
        img.style.display         = 'none';
        placeholder.style.display = 'flex';
    }

    // Show description section only when there is content
    var descWrap = document.getElementById('modal-description-wrap');
    var descEl   = document.getElementById('modal-description');
    if (description && description.trim() !== '') {
        descEl.textContent       = description;
        descWrap.style.display   = 'block';
    } else {
        descWrap.style.display   = 'none';
    }

    var btn  = document.getElementById('modal-borrow-btn');
    var msg  = document.getElementById('modal-status-msg');
    var form = document.getElementById('modal-borrow-form');

    form.action = borrowRouteBase + '/' + id + '/borrow';

    if (alreadyHave) {
        btn.disabled = true; btn.style.background = '#e5e7eb'; btn.style.color = '#9ca3af';
        btn.style.cursor = 'not-allowed'; btn.textContent = 'Already Borrowed';
        msg.textContent = '';
    } else if (atLimit) {
        btn.disabled = true; btn.style.background = '#e5e7eb'; btn.style.color = '#9ca3af';
        btn.style.cursor = 'not-allowed'; btn.textContent = 'Limit Reached';
        msg.textContent = 'You have reached your borrow limit.';
    } else if (noStock) {
        btn.disabled = true; btn.style.background = '#e5e7eb'; btn.style.color = '#9ca3af';
        btn.style.cursor = 'not-allowed'; btn.textContent = 'Unavailable';
        msg.textContent = 'No copies available at this time.';
    } else {
        btn.disabled = false; btn.style.background = '#2a3050'; btn.style.color = '#fff';
        btn.style.cursor = 'pointer'; btn.textContent = 'Borrow'; msg.textContent = '';
    }

    document.getElementById('book-modal').style.display = 'flex';

    // Clean up ?highlight param without reloading
    if (window.history.replaceState) {
        var url = new URL(window.location.href);
        url.searchParams.delete('highlight');
        window.history.replaceState({}, '', url.toString());
    }
}

function closeBookModal() {
    document.getElementById('book-modal').style.display = 'none';
}

document.getElementById('book-modal').addEventListener('click', function(e) {
    if (e.target === this) closeBookModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeBookModal();
});

// Auto-open modal when arriving from Dashboard Discover section via ?highlight=id
(function () {
    var params      = new URLSearchParams(window.location.search);
    var highlightId = params.get('highlight');
    if (!highlightId) return;
    var card = document.querySelector('[data-book-id="' + highlightId + '"]');
    if (card) {
        setTimeout(function () { card.click(); }, 150);
    }
})();
</script>
@endpush

@endsection