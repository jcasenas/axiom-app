<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Borrow Records</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 7px 9px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #e8e8f0;
            font-weight: bold;
            color: #1a1a2e;
        }
        tbody tr:nth-child(even) {
            background-color: #f7f7fb;
        }
        h1 {
            text-align: center;
            font-size: 15px;
            margin-bottom: 6px;
            color: #1a1a2e;
        }
        .header-block {
            width: 100%;
            margin-bottom: 18px;
        }
        .header-left {
            display: inline-block;
            width: 60%;
            vertical-align: top;
        }
        .header-right {
            display: inline-block;
            width: 38%;
            text-align: right;
            vertical-align: top;
        }
        .brand { font-size: 16px; font-weight: bold; color: #1a1a2e; }
        .subtitle { font-size: 10px; color: #5a5a7a; margin-top: 2px; }
        .meta { font-size: 10px; color: #3a3a5a; line-height: 1.7; }
        .divider { border: none; border-top: 2px solid #2a3050; margin: 10px 0 16px; }
        .text-center { text-align: center; }
        .footer { margin-top: 24px; text-align: center; font-size: 9px; color: #8884a8; }

        /* Status pill approximation for PDF (no border-radius in DomPDF, use padding) */
        .status-active    { color: #1e8449; font-weight: bold; }
        .status-pending   { color: #b7600a; font-weight: bold; }
        .status-expired   { color: #c0392b; font-weight: bold; }
        .status-due-soon  { color: #9a6300; font-weight: bold; }
        .status-cancelled { color: #666;    font-weight: bold; }
    </style>
</head>
<body>

    {{-- Header --}}
    <table style="border:none;margin-top:0;" cellpadding="0" cellspacing="0">
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
                        <br><strong>Filter:</strong> {{ ucfirst(str_replace('_', ' ', request('status'))) }}
                    @endif
                    @if(request('department') && request('department') !== 'all')
                        <br><strong>Department:</strong> Filtered
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <hr class="divider">
    <h1>Borrow Records Report</h1>

    @if($borrows->isEmpty())
        <p class="text-center" style="margin-top:30px;color:#8884a8;">No borrow records found for the selected filters.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width:4%;">#</th>
                    <th style="width:16%;">Borrower</th>
                    <th style="width:14%;">Department</th>
                    <th style="width:22%;">Book Title</th>
                    <th style="width:16%;">Author</th>
                    <th style="width:11%;">Requested</th>
                    <th style="width:11%;">Due Date</th>
                    <th style="width:6%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($borrows as $key => $borrow)
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
                    $statusLabel = ucfirst(str_replace('_', ' ', $status));
                @endphp
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $borrow->user->full_name ?? 'N/A' }}</td>
                    <td>{{ $borrow->user->department->department_name ?? '—' }}</td>
                    <td>{{ $borrow->ebook->title ?? 'N/A' }}</td>
                    <td>{{ $borrow->ebook->author->author_name ?? '—' }}</td>
                    <td>{{ $borrow->requested_at ? $borrow->requested_at->format('M d, Y') : '—' }}</td>
                    <td>{{ $borrow->due_date ? $borrow->due_date->format('M d, Y') : '—' }}</td>
                    <td class="{{ $statusClass }}">{{ $statusLabel }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Generated by AXIOM Library E-Resource Management System &nbsp;·&nbsp; {{ now()->format('F d, Y') }}
    </div>

</body>
</html>