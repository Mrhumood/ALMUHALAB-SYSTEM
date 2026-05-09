<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Service Requests Export — {{ now()->format('Y-m-d') }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 11px; color: #111; background: #fff; padding: 24px; }

    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; border-bottom: 2px solid #0f172a; padding-bottom: 12px; }
    .header-brand { display: flex; align-items: center; gap: 10px; }
    .brand-mark { width: 36px; height: 36px; background: linear-gradient(145deg,#b45309,#f59e0b); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 16px; font-weight: 900; flex-shrink: 0; }
    .brand-name { font-size: 14px; font-weight: 700; color: #0f172a; }
    .brand-sub  { font-size: 8px; color: #6b7280; text-transform: uppercase; letter-spacing: .08em; }
    .header-meta { text-align: right; color: #6b7280; font-size: 9.5px; line-height: 1.6; }

    h2 { font-size: 13px; font-weight: 700; margin-bottom: 12px; color: #0f172a; }

    table { width: 100%; border-collapse: collapse; font-size: 10.5px; }
    thead tr { background: #0f172a; color: #fff; }
    thead th { padding: 6px 8px; text-align: left; font-weight: 600; white-space: nowrap; }
    tbody tr:nth-child(even) { background: #f8fafc; }
    tbody tr:hover { background: #eff6ff; }
    tbody td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; vertical-align: middle; }

    .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 600; }
    .badge-urgent   { background:#fee2e2; color:#dc2626; }
    .badge-high     { background:#fef3c7; color:#d97706; }
    .badge-normal   { background:#dbeafe; color:#2563eb; }
    .badge-low      { background:#f3f4f6; color:#6b7280; }
    .badge-new      { background:#dbeafe; color:#1d4ed8; }
    .badge-review   { background:#cffafe; color:#0e7490; }
    .badge-approved { background:#dcfce7; color:#16a34a; }
    .badge-rejected { background:#fee2e2; color:#dc2626; }
    .badge-completed{ background:#f3f4f6; color:#374151; }

    .footer { margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 8px; display: flex; justify-content: space-between; color: #9ca3af; font-size: 9px; }

    @media print {
        body { padding: 0; }
        @page { margin: 1.5cm; size: A4 landscape; }
        button.print-btn { display: none; }
    }
</style>
</head>
<body>

<div class="header">
    <div class="header-brand">
        <div class="brand-mark">م</div>
        <div>
            <div class="brand-name">ALMuhalab</div>
            <div class="brand-sub">International Co.</div>
        </div>
    </div>
    <div class="header-meta">
        <div><strong>Service Requests Report</strong></div>
        <div>Generated: {{ now()->format('d M Y, H:i') }}</div>
        <div>Total records: {{ $rows->count() }}</div>
    </div>
</div>

<div style="margin-bottom:8px;display:flex;justify-content:space-between;align-items:center">
    <h2>Service Requests — {{ now()->format('d M Y') }}</h2>
    <button class="print-btn" onclick="window.print()"
        style="background:#0f172a;color:#fff;border:none;padding:6px 14px;border-radius:6px;font-size:11px;cursor:pointer">
        🖨 Print / Save PDF
    </button>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Request No.</th>
            <th>Title</th>
            <th>Service Type</th>
            <th>Status</th>
            @if($isAdmin)
            <th>Submitted By</th>
            <th>Assigned To</th>
            @endif
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $i => $r)
        @php
            $statusBadge = match($r->status) {
                'New'          => 'badge-new',
                'Under Review' => 'badge-review',
                'Approved'     => 'badge-approved',
                'Rejected'     => 'badge-rejected',
                'Completed'    => 'badge-completed',
                default        => '',
            };
        @endphp
        <tr>
            <td>{{ $i + 1 }}</td>
            <td style="font-family:monospace;font-weight:600;color:#2563eb;font-size:9.5px">{{ $r->request_number ?? '—' }}</td>
            <td>{{ $r->title }}</td>
            <td>{{ $r->serviceType->name ?? '—' }}</td>
            <td><span class="badge {{ $statusBadge }}">{{ $r->status }}</span></td>
            @if($isAdmin)
            <td>{{ $r->user->name ?? '—' }}</td>
            <td>{{ $r->assignedTo->name ?? '—' }}</td>
            @endif
            <td style="white-space:nowrap">{{ $r->created_at->format('d M Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    <span>ALMuhalab International Co. — Kuwait City</span>
    <span>Confidential — For internal use only</span>
    <span>© {{ date('Y') }} ALMuhalab International Co.</span>
</div>

<script>
    // Auto-open print dialog if no button is clicked
    // window.onload = () => window.print();
</script>
</body>
</html>
