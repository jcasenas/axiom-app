@extends('librarian.layout')
@section('title', 'Dashboard')

@push('styles')
<style>
.welcome { font-size:1.35rem; font-weight:400; color:#3a3a5a; margin-bottom:24px; }
.welcome strong { font-weight:700; color:#1a1a2e; }

.kpi-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:32px; }
.kpi-card {
    border-radius:10px; padding:24px 24px 20px;
    display:flex; flex-direction:column; justify-content:space-between;
    min-height:140px; text-decoration:none;
    transition:transform .18s, box-shadow .18s;
    cursor:pointer; position:relative; overflow:hidden;
}
.kpi-card:hover { transform:translateY(-3px); box-shadow:0 10px 28px rgba(0,0,0,.18); }
.kpi-card-1 { background:#3b4f7a; }
.kpi-card-2 { background:#8b2e2e; }
.kpi-card-3 { background:#2a3050; }

.kpi-number { font-size:2.8rem; font-weight:700; color:white; line-height:1; text-align:right }
.kpi-label  { font-size:1rem; font-weight:400; color:rgba(255,255,255,.88); margin-top:6px; line-height:1.3; text-align:left;}
.kpi-arrow  { position:absolute; bottom:18px; right:20px; color:rgba(255,255,255,.7); }
.kpi-arrow svg { width:22px; height:22px; }

.section-title { font-size:1rem; font-weight:600; color:#3a3a5a; margin-bottom:14px; }
.empty-state   { text-align:center; padding:32px; color:var(--muted); font-size:.88rem; }
</style>
@endpush

@section('content')
<div class="content-card">

    @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif

    <p class="welcome">Welcome back, <strong>{{ Auth::user()->full_name }}</strong></p>

    <div class="kpi-grid">

        {{-- KPI 1: Pending borrow requests --}}
        <a href="{{ route('librarian.borrows.index', ['status' => 'pending']) }}" class="kpi-card kpi-card-1">
            <div>
                <div class="kpi-number">{{ $borrowRequests }}</div>
                <div class="kpi-label">Borrow Requests</div>
            </div>
            <div class="kpi-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </div>
        </a>

        {{-- KPI 2: Expiring today --}}
        <a href="{{ route('librarian.borrows.index', ['filter' => 'expiring_today']) }}" class="kpi-card kpi-card-2">
            <div>
                <div class="kpi-number">{{ $expiringToday }}</div>
                <div class="kpi-label">Expiring Today</div>
            </div>
            <div class="kpi-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </div>
        </a>

        {{-- KPI 3: Borrows today --}}
        <a href="{{ route('librarian.borrows.index') }}" class="kpi-card kpi-card-3">
            <div>
                <div class="kpi-number">{{ $borrowsToday }}</div>
                <div class="kpi-label">Borrows Today</div>
            </div>
            <div class="kpi-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </div>
        </a>

    </div>

    <p class="section-title">Today's Borrow Activity</p>

    @if($todayActivity->isEmpty())
        <div class="empty-state">No borrow activity recorded today.</div>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th>Borrower's Name</th>
                    <th>Department</th>
                    <th>Title</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($todayActivity as $record)
                <tr>
                    <td>{{ $record->user->full_name ?? '—' }}</td>
                    <td>{{ $record->user->department->department_name ?? '—' }}</td>
                    <td>{{ $record->ebook->title ?? '—' }}</td>
                    <td>{{ $record->due_date ? $record->due_date->format('M d, Y') : '—' }}</td>
                    <td>
                        @php $s = $record->status; @endphp
                        <span class="badge
                            {{ $s==='active'   ?'badge-active'   :'' }}
                            {{ $s==='pending'  ?'badge-pending'  :'' }}
                            {{ $s==='expired'  ?'badge-expired'  :'' }}
                            {{ $s==='due_soon' ?'badge-due-soon' :'' }}
                            {{ $s==='cancelled'?'badge-inactive' :'' }}">
                            {{ ucfirst(str_replace('_',' ',$s)) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</div>
@endsection