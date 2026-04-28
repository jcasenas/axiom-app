@extends('student.layout')
@php
    $rp      = Auth::user()->isFaculty() ? 'faculty' : 'student';
    $lastDay = $borrowing->access_expires_at
        ? \Carbon\Carbon::parse($borrowing->access_expires_at)->subDay()->format('M d, Y')
        : null;
    $isDueSoon = $borrowing->status === 'due_soon';
@endphp

@section('title', $ebook->title ?? 'Reading')

@push('styles')
<style>
    .reader-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    /* ── Header card ── */
    .reader-header {
        background: white;
        padding: 1.75rem 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.07);
        margin-bottom: 2rem;
        display: flex;
        gap: 2rem;
        align-items: stretch;       /* both columns same height */
    }

    /* ── Cover column ── */
    .reader-cover { flex-shrink: 0; }

    .reader-cover img {
        width: 130px;
        aspect-ratio: 2/3;
        object-fit: cover;
        border-radius: 8px;
        display: block;
    }

    /* ── Info column: flex column so back link is pinned to bottom ── */
    .reader-info {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
    }

    .reader-info h1 {
        margin: 0 0 0.2rem;
        font-size: 1.55rem;
        font-weight: 700;
        color: #1a1a2e;
        line-height: 1.3;
    }

    .reader-author {
        margin: 0 0 1.1rem;
        color: #6b7280;
        font-size: 0.95rem;
    }

    /* ── Metadata table — consistent label/value grid ── */
    .reader-meta-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
        margin-bottom: 1rem;
    }

    .reader-meta-table td {
        padding: 0.3rem 0;
        vertical-align: top;
    }

    .reader-meta-table td:first-child {
        color: #9ca3af;
        width: 130px;
        font-weight: 500;
        white-space: nowrap;
    }

    .reader-meta-table td:last-child {
        color: #1a1a2e;
        font-weight: 600;
    }

    /* Last day of access row — no background, just colored text for due_soon */
    .meta-expiry-normal { color: #1a1a2e; }
    .meta-expiry-soon   { color: #b45309; font-weight: 700; }

    /* ── Back link pinned to bottom of info column ── */
    .reader-back {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #4f46e5;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.88rem;
        margin-top: auto;
        padding-top: 0.5rem;
        transition: opacity 0.15s;
    }

    .reader-back:hover { opacity: 0.75; }

    /* ── iframe ── */
    .iframe-wrap { position: relative; }

    .reader-iframe {
        width: 100%;
        height: 85vh;
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        background: #f8fafc;
        display: block;
    }

    .iframe-loader {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        border-radius: 12px;
        gap: 12px;
        font-size: 0.88rem;
        color: #8884a8;
        pointer-events: none;
    }

    .spinner {
        width: 36px; height: 36px;
        border: 3px solid #e0def0;
        border-top-color: #a8a4e0;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    .reader-footer {
        text-align: center;
        margin-top: 1.5rem;
        color: #6b7280;
        font-size: 0.88rem;
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .reader-container { margin: 1rem auto; padding: 0 0.5rem; }
        .reader-header {
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 1.25rem;
            padding: 1.25rem;
        }
        .reader-cover img { width: 100px; }
        .reader-info h1   { font-size: 1.2rem; }
        .reader-meta-table td:first-child { width: 110px; }
        .reader-iframe    { height: 72vh; border-radius: 8px; }
        .reader-back      { justify-content: center; }
    }

    @media (max-width: 540px) {
        .reader-header  { border-radius: 8px; margin-bottom: 1rem; }
        .reader-iframe  { height: 65vh; }
    }
</style>
@endpush

@section('content')
<div class="reader-container">

    <div class="reader-header">

        {{-- Cover --}}
        @if($ebook->cover_url)
        <div class="reader-cover">
            <img src="{{ $ebook->cover_url }}" alt="{{ $ebook->title }}">
        </div>
        @endif

        {{-- Info column --}}
        <div class="reader-info">

            <h1>{{ $ebook->title }}</h1>
            <p class="reader-author">by {{ $ebook->author->author_name ?? 'Unknown Author' }}</p>

            {{-- Aligned metadata table --}}
            <table class="reader-meta-table">
                @if($ebook->category)
                <tr>
                    <td>Category</td>
                    <td>{{ $ebook->category->category_name }}</td>
                </tr>
                @endif
                @if($ebook->format)
                <tr>
                    <td>Format</td>
                    <td>{{ $ebook->format->format_type }}</td>
                </tr>
                @endif
                @if($ebook->isbn)
                <tr>
                    <td>ISBN</td>
                    <td>{{ $ebook->isbn }}</td>
                </tr>
                @endif
                <tr>
                    <td>Borrow Date</td>
                    <td>{{ $borrowing->borrow_date ? $borrowing->borrow_date->format('M d, Y') : '—' }}</td>
                </tr>
                @if($lastDay)
                <tr>
                    <td>Last Day of Access</td>
                    <td class="{{ $isDueSoon ? 'meta-expiry-soon' : 'meta-expiry-normal' }}">
                        {{ $lastDay }}
                        @if($isDueSoon)
                            &nbsp;⚠️
                        @endif
                    </td>
                </tr>
                @endif
            </table>

            {{-- Back link pinned to the bottom of the info column --}}
            <a href="{{ route($rp . '.my-books.index') }}" class="reader-back">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to My Books
            </a>

        </div>
    </div>

    {{-- iframe with loading spinner --}}
    <div class="iframe-wrap">
        <div class="iframe-loader" id="iframeLoader">
            <div class="spinner"></div>
            <span>Loading book...</span>
        </div>
        <iframe
            src="{{ $ebook->file_url }}"
            class="reader-iframe"
            allowfullscreen
            onload="document.getElementById('iframeLoader').style.display='none'"
            sandbox="allow-scripts allow-same-origin allow-popups allow-downloads">
        </iframe>
    </div>

    <div class="reader-footer">
        Having trouble viewing?
        <a href="{{ $ebook->file_url }}" target="_blank" style="color:#4f46e5;">Open in new tab</a>
    </div>

</div>
@endsection