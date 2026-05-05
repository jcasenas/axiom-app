<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Borrow Records</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 20px;
            color: #1a1a2e;
        }

        /* ── Header ── */
        .brand       { font-size: 16px; font-weight: bold; color: #1a1a2e; }
        .subtitle    { font-size: 9px; color: #5a5a7a; margin-top: 2px; }
        .meta        { font-size: 9px; color: #3a3a5a; line-height: 1.8; }
        .divider     { border: none; border-top: 2px solid #2a3050; margin: 10px 0 14px; }
        .report-title { text-align: center; font-size: 13px; font-weight: bold; margin-bottom: 16px; color: #1a1a2e; }

        /* ── Group headings ── */
        .group-category {
            background: #2a3050;
            color: white;
            font-size: 10px;
            font-weight: bold;
            padding: 5px 8px;
            margin-top: 18px;
            margin-bottom: 0;
        }
        .group-department {
            background: #e8e6f8;
            color: #2a3050;
            font-size: 9px;
            font-weight: bold;
            padding: 4px 8px;
            margin-top: 0;
            margin-bottom: 0;
            border-left: 3px solid #2a3050;
        }
        .group-user {
            background: #f5f4fc;
            color: #3a3a5a;
            font-size: 9px;
            font-weight: bold;
            padding: 3px 8px 3px 16px;
            margin-top: 0;
            margin-bottom: 0;
            border-left: 3px solid #a8a4e0;
        }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px 7px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #eeedf8;
            font-weight: bold;
            font-size: 9px;
            color: #3a3a5a;
        }
        tbody tr:nth-child(even) { background-color: #faf9ff; }

        /* ── Status colors ── */
        .status-active    { color: #1e8449; font-weight: bold; }
        .status-pending   { color: #b7600a; font-weight: bold; }
        .status-expired   { color: #c0392b; font-weight: bold; }
        .status-due-soon  { color: #9a6300; font-weight: bold; }
        .status-cancelled { color: #666;    font-weight: bold; }

        /* ── Summary box ── */
        .summary-table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .summary-table td { border: 1px solid #ddd; padding: 5px 10px; font-size: 9px; }
        .summary-table th { background: #2a3050; color: white; padding: 5px 10px; font-size: 9px; text-align: left; }

        /* ── Footer ── */
        .footer { margin-top: 20px; text-align: center; font-size: 8px; color: #8884a8; }
    </style>
</head>
<body>

    {{-- ── Page Header ── --}}
    <table style="border:none;margin-top:0;margin-bottom:0;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="border:none;padding:0;width:60%;vertical-align:top;">
                <div class="brand">AXIOM</div>
                <div class="subtitle">Library E-Resource Management System</div>
            </td>
            <td style="border:none;padding:0;text-align:right;vertical-align:top;">
                <div class="meta">
                    <strong>Date Generated:</strong> {{ now()->format('F d, Y g:i A') }}<br>
                    <strong>Total Records:</strong> {{ $borrows->count() }}
                    @if(request('status') && request('status') !== 'all')
                        <br><strong>Status Filter:</strong> {{ ucfirst(str_replace('_', ' ', request('status'))) }}
                    @endif
                    @if(request('department') && request('department') !== 'all')
                        <br><strong>Department Filter:</strong> Applied
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <hr class="divider">
    <div class="report-title">Borrow Records Report</div>

    @if($borrows->isEmpty())
        <p style="text-align:center;margin-top:30px;color:#8884a8;">No borrow records found for the selected filters.</p>
    @else

    @php
        // ── Group: Category → Department → User ──────────────────
        $grouped = $borrows->groupBy(function($b) {
            return $b->ebook->category->category_name ?? 'Uncategorized';
        })->map(function($catGroup) {
            return $catGroup->groupBy(function($b) {
                return $b->user->department->department_name ?? 'No Department';
            })->map(function($deptGroup) {
                return $deptGroup->groupBy(function($b) {
                    return $b->user->full_name ?? 'Unknown User';
                });
            });
        })->sortKeys();

        $counter = 1;
    @endphp

    {{-- ── Summary by Category ── --}}
    <table class="summary-table">
        <tr>
            <th colspan="3">Summary by Category</th>
        </tr>
        <tr>
            <td style="font-weight:bold;background:#f0eef8;">Category</td>
            <td style="font-weight:bold;background:#f0eef8;">Total Borrows</td>
            <td style="font-weight:bold;background:#f0eef8;">Departments Involved</td>
        </tr>
        @foreach($grouped as $categoryName => $deptGroups)
        <tr>
            <td>{{ $categoryName }}</td>
            <td>{{ $deptGroups->flatten()->count() }}</td>
            <td>{{ $deptGroups->keys()->join(', ') }}</td>
        </tr>
        @endforeach
    </table>

    <br>

    {{-- ── Detailed Records grouped by Category → Department → User ── --}}
    @foreach($grouped as $categoryName => $deptGroups)

        <div class="group-category">📚 {{ $categoryName }} &nbsp;({{ $deptGroups->flatten()->count() }} record/s)</div>

        @foreach($deptGroups as $deptName => $userGroups)

            <div class="group-department">🏛 {{ $deptName }} &nbsp;({{ $userGroups->flatten()->count() }} record/s)</div>

            @foreach($userGroups as $userName => $userBorrows)

                <div class="group-user">👤 {{ $userName }} &nbsp;({{ $userBorrows->count() }} borrow/s)</div>

                <table>
                    <thead>
                        <tr>
                            <th style="width:4%;">#</th>
                            <th style="width:28%;">Book Title</th>
                            <th style="width:12%;">Format</th>
                            <th style="width:12%;">Requested</th>
                            <th style="width:12%;">Borrow Date</th>
                            <th style="width:12%;">Due Date</th>
                            <th style="width:10%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userBorrows as $borrow)
                        @php
                            $status = $borrow->status;
                            $statusClass = match($status) {
                                'active'    => 'status-active',
                                'pending'   => 'status-pending',
                                'expired'   => 'status-expired',
                                'due_soon'  => 'status-due-soon',
                                'cancelled' => 'status-cancelled',
                                default     => '',
                            };
                        @endphp
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td>{{ $borrow->ebook->title ?? 'N/A' }}</td>
                            <td>{{ $borrow->ebook->format->format_type ?? '—' }}</td>
                            <td>{{ $borrow->requested_at ? $borrow->requested_at->format('M d, Y') : '—' }}</td>
                            <td>{{ $borrow->borrow_date ? $borrow->borrow_date->format('M d, Y') : '—' }}</td>
                            <td>{{ $borrow->due_date ? $borrow->due_date->format('M d, Y') : '—' }}</td>
                            <td class="{{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            @endforeach {{-- end user --}}
        @endforeach {{-- end department --}}
    @endforeach {{-- end category --}}

    @endif

    <div class="footer">
        Generated by AXIOM Library E-Resource Management System &nbsp;·&nbsp; {{ now()->format('F d, Y g:i A') }}
    </div>

</body>
</html>