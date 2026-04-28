@extends('student.layout')

@section('title', 'Dashboard')

@push('styles')
<style>
    .welcome { font-size: 1.35rem; font-weight: 400; color: #3a3a5a; margin-bottom: 24px; }
    .welcome strong { font-weight: 700; color: #1a1a2e; }

    /* ── Discover Section ── */
    .discover-header {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        margin-bottom: 16px;
        margin-top: 36px;
    }

    .discover-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1a1a2e;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .discover-title::before {
        content: '';
        display: inline-block;
        width: 4px;
        height: 18px;
        background: linear-gradient(180deg, #a8a4e0 0%, #3b4f7a 100%);
        border-radius: 2px;
        flex-shrink: 0;
    }

    .discover-see-all {
        font-size: 0.78rem;
        font-weight: 600;
        color: #a8a4e0;
        text-decoration: none;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        transition: color 0.15s;
    }
    .discover-see-all:hover { color: #3b4f7a; }

    .discover-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
        gap: 14px;
        margin-bottom: 8px;
    }

    .discover-card {
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem;
        border-radius: 10px;
        background: #fff;
        border: 1px solid #f0eeff;
        transition: box-shadow 0.2s, transform 0.2s;
        position: relative;
        text-decoration: none;
    }

    .discover-card:hover {
        box-shadow: 0 6px 20px rgba(168, 164, 224, 0.28);
        transform: translateY(-3px);
    }

    .discover-card-cover {
        width: 100%;
        aspect-ratio: 2/3;
        border-radius: 6px;
        object-fit: cover;
        background: #f3f4f6;
    }

    .discover-card-placeholder {
        width: 100%;
        aspect-ratio: 2/3;
        border-radius: 6px;
        background: linear-gradient(135deg, #ede9fe 0%, #c4b5fd 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 0.5rem;
        gap: 0.4rem;
    }

    .discover-card-title {
        margin: 0;
        font-size: 0.72rem;
        font-weight: 600;
        color: #1a1a2e;
        line-height: 1.3;
        text-align: center;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        width: 100%;
    }

    .discover-card-author {
        margin: 0;
        font-size: 0.66rem;
        color: #9ca3af;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 100%;
        text-align: center;
    }

    .new-badge {
        position: absolute;
        top: 6px;
        right: 6px;
        background: #3b4f7a;
        color: #fff;
        font-size: 0.58rem;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 999px;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    /* Scrollable on very small screens */
    @media (max-width: 540px) {
        .discover-grid {
            grid-template-columns: repeat(auto-fill, minmax(95px, 1fr));
            gap: 10px;
        }
        .welcome { font-size: 1.1rem; margin-bottom: 16px; }
    }
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

    <p class="welcome">Welcome back, <strong>{{ Auth::user()->full_name }}</strong></p>

    {{-- KPI Cards --}}
    <div class="kpi-grid">
        {{-- Pending Borrows --}}
        <a href="{{ route($routePrefix . '.my-books.index', ['status' => 'pending']) }}" class="kpi-card kpi-card-1">
            <div>
                <div class="kpi-number" style="text-align: right;">{{ $pendingBorrows }}</div>
                <div class="kpi-label">Pending Borrows</div>
            </div>
            <div class="kpi-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </div>
        </a>

        {{-- Due Soon --}}
        <a href="{{ route($routePrefix . '.my-books.index', ['status' => 'due_soon']) }}" class="kpi-card kpi-card-2">
            <div>
                <div class="kpi-number" style="text-align: right;">{{ $dueSoon }}</div>
                <div class="kpi-label">Due Soon</div>
            </div>
            <div class="kpi-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </div>
        </a>

        {{-- Active Borrows --}}
        <a href="{{ route($routePrefix . '.my-books.index', ['status' => 'active']) }}" class="kpi-card kpi-card-3">
            <div>
                <div class="kpi-number" style="text-align: right;">{{ $activeBorrows }}</div>
                <div class="kpi-label">Active Borrows</div>
            </div>
            <div class="kpi-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </div>
        </a>
    </div>

    {{-- Currently Borrowed Table --}}
    <p class="section-title">Currently Borrowed</p>

    @if($currentlyBorrowed->isEmpty())
        <div class="empty-state">You have no active or pending borrows at the moment.</div>
    @else
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($currentlyBorrowed as $record)
                    <tr>
                        <td>{{ $record->ebook->title ?? '---' }}</td>
                        <td>{{ $record->due_date ? $record->due_date->format('M d, Y') : '---' }}</td>
                        <td>
                            @php $s = $record->status; @endphp
                            <span class="badge
                                {{ $s === 'active'    ? 'badge-active'   : '' }}
                                {{ $s === 'pending'   ? 'badge-pending'  : '' }}
                                {{ $s === 'due_soon'  ? 'badge-due-soon' : '' }}
                                {{ $s === 'expired'   ? 'badge-expired'  : '' }}
                                {{ $s === 'cancelled' ? 'badge-cancelled': '' }}">
                                {{ ucfirst(str_replace('_', ' ', $s)) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- ── DISCOVER SECTION ── --}}
    @if($recentBooks->isNotEmpty())
    <div class="discover-header">
        <span class="discover-title">Discover New Books</span>
        <a href="{{ route($routePrefix . '.books.index') }}" class="discover-see-all">See All →</a>
    </div>

    <div class="discover-grid">
        @foreach($recentBooks as $book)
        @php
            $alreadyHave = in_array($book->ebook_id, $alreadyBorrowed);
            $noStock     = $book->available_copies < 1;
        @endphp
        <div class="discover-card"
             onclick="goToBooksWithModal(
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
                 {{ $noStock ? 'true' : 'false' }}
             )">

            {{-- NEW badge for books added in the last 14 days --}}
            @if($book->created_at->diffInDays(now()) <= 14)
                <span class="new-badge">New</span>
            @endif

            @if($book->cover_url)
                <img src="{{ $book->cover_url }}"
                     alt="{{ $book->title }}"
                     class="discover-card-cover">
            @else
                <div class="discover-card-placeholder">
                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="1.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span style="font-size:0.62rem;font-weight:600;color:#5b21b6;text-align:center;line-height:1.3;">
                        {{ Str::limit($book->title, 30) }}
                    </span>
                </div>
            @endif

            <p class="discover-card-title">{{ $book->title }}</p>
            <p class="discover-card-author">{{ $book->author->author_name ?? '---' }}</p>
        </div>
        @endforeach
    </div>
    @endif

</div>

{{-- ── BOOK DETAIL MODAL (mirrors Browse Books modal) ── --}}
<div id="dash-book-modal"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:1000;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:#fff;border-radius:1rem;width:100%;max-width:520px;overflow:hidden;position:relative;box-shadow:0 20px 60px rgba(0,0,0,0.2);">

        <button onclick="closeDashModal()"
                style="position:absolute;top:1rem;right:1rem;background:none;border:none;font-size:1.25rem;cursor:pointer;color:#9ca3af;z-index:1;">✕</button>

        {{-- Cover + Info --}}
        <div style="display:flex;gap:1.25rem;padding:1.5rem;flex-wrap:wrap;">
            <div style="flex-shrink:0;width:110px;">
                <img id="dm-cover-img" src="" alt=""
                     style="width:110px;aspect-ratio:2/3;object-fit:cover;border-radius:0.5rem;background:#f3f4f6;display:none;">
                <div id="dm-cover-ph"
                     style="width:110px;aspect-ratio:2/3;border-radius:0.5rem;background:linear-gradient(135deg,#ede9fe 0%,#c4b5fd 100%);display:flex;align-items:center;justify-content:center;">
                    <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="1.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>

            <div style="flex:1;min-width:0;">
                <p id="dm-title"  style="margin:0 0 0.25rem;font-size:1rem;font-weight:700;color:#1a1a2e;line-height:1.3;padding-right:1.5rem;"></p>
                <p id="dm-author" style="margin:0 0 1rem;font-size:0.8rem;color:#6b7280;"></p>

                <table style="width:100%;font-size:0.8rem;border-collapse:collapse;">
                    <tr><td style="padding:0.3rem 0;color:#9ca3af;width:110px;">Category</td><td id="dm-category" style="color:#374151;font-weight:500;"></td></tr>
                    <tr><td style="padding:0.3rem 0;color:#9ca3af;">Format</td>              <td id="dm-format"   style="color:#374151;font-weight:500;"></td></tr>
                    <tr><td style="padding:0.3rem 0;color:#9ca3af;">ISBN</td>                <td id="dm-isbn"     style="color:#374151;font-weight:500;"></td></tr>
                    <tr><td style="padding:0.3rem 0;color:#9ca3af;">Available</td>           <td id="dm-copies"   style="font-weight:700;"></td></tr>
                </table>
            </div>
        </div>

        {{-- Footer --}}
        <div style="padding:1rem 1.5rem 1.5rem;border-top:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
            <p id="dm-status-msg" style="margin:0;font-size:0.8rem;color:#6b7280;flex:1;min-width:100px;"></p>

            <div style="display:flex;gap:0.6rem;flex-shrink:0;">
                {{-- "Browse & Borrow" button navigates to books page with highlight --}}
                <a id="dm-browse-btn" href="#"
                   style="padding:0.6rem 1.25rem;border:none;border-radius:0.5rem;font-size:0.875rem;font-weight:600;cursor:pointer;background:#2a3050;color:#fff;text-decoration:none;white-space:nowrap;display:inline-block;">
                    View in Catalog
                </a>

                {{-- Direct borrow form --}}
                <form id="dm-borrow-form" method="POST" action="" style="margin:0;">
                    @csrf
                    <button id="dm-borrow-btn" type="submit"
                        style="padding:0.6rem 1.5rem;border:none;border-radius:0.5rem;font-size:0.875rem;font-weight:600;cursor:pointer;background:#4f46e5;color:#fff;white-space:nowrap;">
                        Borrow
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
var borrowUrl   = function(id) { return '{{ url('/') }}/' + '{{ $routePrefix }}' + '/books/' + id + '/borrow'; };
var booksIndexUrl = '{{ route($routePrefix . '.books.index') }}';

function goToBooksWithModal(id, title, author, category, format, isbn, copies, coverUrl, alreadyHave, atLimit, noStock) {
    // Populate modal
    document.getElementById('dm-title').textContent    = title;
    document.getElementById('dm-author').textContent   = author;
    document.getElementById('dm-category').textContent = category || '---';
    document.getElementById('dm-format').textContent   = format   || '---';
    document.getElementById('dm-isbn').textContent     = isbn     || '---';

    var copiesEl = document.getElementById('dm-copies');
    copiesEl.textContent = copies;
    copiesEl.style.color = copies > 0 ? '#16a34a' : '#dc2626';

    var img = document.getElementById('dm-cover-img');
    var ph  = document.getElementById('dm-cover-ph');
    if (coverUrl) {
        img.src = coverUrl;
        img.style.display = 'block';
        ph.style.display  = 'none';
    } else {
        img.style.display = 'none';
        ph.style.display  = 'flex';
    }

    // "View in Catalog" always links to the books page with ?highlight=id so the modal
    // auto-opens over there as well
    document.getElementById('dm-browse-btn').href = booksIndexUrl + '?highlight=' + id;

    var btn  = document.getElementById('dm-borrow-btn');
    var msg  = document.getElementById('dm-status-msg');
    var form = document.getElementById('dm-borrow-form');

    form.action = borrowUrl(id);

    if (alreadyHave) {
        btn.disabled = true;
        btn.style.cssText = 'padding:0.6rem 1.5rem;border:none;border-radius:0.5rem;font-size:0.875rem;font-weight:600;cursor:not-allowed;background:#e5e7eb;color:#9ca3af;white-space:nowrap;';
        btn.textContent = 'Already Borrowed';
        msg.textContent = 'You already have an active or pending request for this book.';
    } else if (atLimit) {
        btn.disabled = true;
        btn.style.cssText = 'padding:0.6rem 1.5rem;border:none;border-radius:0.5rem;font-size:0.875rem;font-weight:600;cursor:not-allowed;background:#e5e7eb;color:#9ca3af;white-space:nowrap;';
        btn.textContent = 'Limit Reached';
        msg.textContent = 'You have reached your borrow limit.';
    } else if (noStock) {
        btn.disabled = true;
        btn.style.cssText = 'padding:0.6rem 1.5rem;border:none;border-radius:0.5rem;font-size:0.875rem;font-weight:600;cursor:not-allowed;background:#e5e7eb;color:#9ca3af;white-space:nowrap;';
        btn.textContent = 'Unavailable';
        msg.textContent = 'No copies available at this time.';
    } else {
        btn.disabled = false;
        btn.style.cssText = 'padding:0.6rem 1.5rem;border:none;border-radius:0.5rem;font-size:0.875rem;font-weight:600;cursor:pointer;background:#4f46e5;color:#fff;white-space:nowrap;';
        btn.textContent = 'Borrow';
        msg.textContent = '';
    }

    document.getElementById('dash-book-modal').style.display = 'flex';
}

function closeDashModal() {
    document.getElementById('dash-book-modal').style.display = 'none';
}

document.getElementById('dash-book-modal').addEventListener('click', function(e) {
    if (e.target === this) closeDashModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDashModal();
});
</script>
@endpush