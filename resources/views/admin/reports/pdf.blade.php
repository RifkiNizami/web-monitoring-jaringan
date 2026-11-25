<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kinerja SLA - {{ $evaluatedPartner }} ({{ $formattedPeriod }})</title>
    <style>
        @page {
            margin: 15mm 15mm 18mm 15mm;
            size: A4 portrait;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.35;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Utility Classes */
        .w-full { width: 100%; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .font-semibold { font-weight: 600; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .uppercase { text-transform: uppercase; }
        .capitalize { text-transform: capitalize; }
        .tracking-tight { letter-spacing: -0.2px; }
        .text-muted { color: #64748b; }
        .text-dark { color: #0f172a; }
        .text-blue { color: #0066cc; }
        .text-emerald { color: #047857; }
        .text-amber { color: #b45309; }
        .text-red { color: #b91c1c; }

        /* KOP SURAT RESMI (LETTERHEAD STANDAR BAKU INDONESIA) */
        .letterhead-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }
        .letterhead-table td {
            vertical-align: middle;
        }
        .letterhead-logo-cell {
            width: 18%;
            text-align: center;
        }
        .letterhead-text-cell {
            width: 64%;
            text-align: center;
            padding: 0 4px;
        }
        .letterhead-spacer-cell {
            width: 18%;
        }
        .company-name {
            font-size: 12.5pt;
            font-weight: 900;
            color: #000000;
            letter-spacing: 0.3px;
            margin: 0;
            line-height: 1.15;
            text-transform: uppercase;
        }
        .company-desc {
            font-size: 7.2pt;
            font-weight: bold;
            color: #1e293b;
            margin: 2px 0 3px 0;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .company-address {
            font-size: 6.2pt;
            color: #334155;
            line-height: 1.35;
            margin: 0;
        }
        /* Garis ganda pembatas kop surat resmi Indonesia */
        .kop-divider {
            margin-top: 6px;
            margin-bottom: 12px;
        }
        .kop-line-thick {
            height: 2.2px;
            background-color: #000000;
            margin-bottom: 1.5px;
        }
        .kop-line-thin {
            height: 0.7px;
            background-color: #000000;
        }

        /* DOCUMENT TITLE & METADATA TABLE (FORMAL REPORT STYLE) */
        .report-title-container {
            text-align: center;
            margin-bottom: 10px;
        }
        .report-title {
            font-size: 11pt;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .report-subtitle {
            font-size: 7.5pt;
            color: #475569;
            margin-top: 2px;
        }
        .report-meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-bottom: 12px;
            border: 1px solid #cbd5e1;
        }
        .report-meta-table td {
            padding: 3.5px 6px;
            vertical-align: middle;
            border: 1px solid #e2e8f0;
        }
        .meta-label {
            width: 22%;
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
        }
        .meta-val {
            width: 28%;
            color: #0f172a;
            font-weight: bold;
        }

        /* KPI SUMMARY CARDS (4 Columns) */
        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin-bottom: 14px;
        }
        .kpi-cell {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 10px;
            vertical-align: top;
            width: 25%;
        }
        .kpi-title {
            font-size: 6.5pt;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 4px;
        }
        .kpi-value-lg {
            font-size: 15pt;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
            margin-bottom: 3px;
        }
        .kpi-sub {
            font-size: 6.5pt;
            color: #64748b;
        }
        .badge-sla {
            display: inline-block;
            font-size: 6pt;
            font-weight: bold;
            padding: 1px 5px;
            border-radius: 3px;
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
            float: right;
        }

        /* SECTION TITLES */
        .section-header {
            font-size: 8.5pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin: 14px 0 6px 0;
            padding-bottom: 3px;
            border-bottom: 1px solid #e2e8f0;
        }
        .section-header .section-count {
            font-size: 7pt;
            color: #64748b;
            font-weight: normal;
        }

        /* DATA TABLES */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-bottom: 12px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 6.5pt;
            letter-spacing: 0.3px;
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        .data-table td {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) td {
            background-color: #fafafa;
        }

        /* STATUS BADGES */
        .pill {
            display: inline-block;
            padding: 1.5px 5px;
            border-radius: 8px;
            font-size: 6.5pt;
            font-weight: bold;
            text-align: center;
        }
        .pill-green {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .pill-amber {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .pill-dark {
            background-color: #0f172a;
            color: #ffffff;
        }
        .pill-slate {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        /* AUDIT NOTES & VERIFICATION */
        .notes-box {
            background-color: #f8fafc;
            border-left: 3px solid #0066cc;
            padding: 6px 10px;
            font-size: 7pt;
            color: #334155;
            margin-bottom: 16px;
            border-top: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }

        /* SIGNATURE SECTION */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .signature-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }
        .sign-card {
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 12px;
            background-color: #ffffff;
        }
        .sign-title {
            font-size: 7pt;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 40px;
        }
        .sign-name {
            font-size: 8pt;
            font-weight: bold;
            color: #0f172a;
            border-top: 1px solid #94a3b8;
            padding-top: 4px;
            margin-bottom: 1px;
        }
        .sign-role {
            font-size: 6.5pt;
            color: #64748b;
        }
        .stamp-box {
            float: right;
            border: 1.5px dashed #0066cc;
            color: #0066cc;
            font-size: 6pt;
            font-weight: bold;
            padding: 3px 6px;
            text-align: center;
            border-radius: 3px;
            margin-top: 0;
            text-transform: uppercase;
        }

        /* FOOTER */
        .page-footer {
            position: fixed;
            bottom: -8mm;
            left: 0;
            right: 0;
            height: 10mm;
            border-top: 0.5px solid #cbd5e1;
            padding-top: 3px;
            font-size: 6.5pt;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT RESMI (LETTERHEAD STANDAR BAKU INDONESIA) -->
    <table class="letterhead-table">
        <tr>
            <td class="letterhead-logo-cell">
                @php
                    $hdLogo = public_path('assets/images/netfly-logo-hd.png');
                    $logoPath = file_exists($hdLogo) ? $hdLogo : public_path('assets/images/netfly-logo.png');
                    $logoBase64 = '';
                    if (file_exists($logoPath)) {
                        $logoData = file_get_contents($logoPath);
                        $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
                    }
                @endphp
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="NetFly" style="width: 118px; height: auto;">
                @else
                    <span style="font-size: 16pt; font-weight: 900; color: #0066cc;">NET<span style="color: #62b300;">FLY</span></span>
                @endif
            </td>
            <td class="letterhead-text-cell">
                <h1 class="company-name">PT. NETMEDIA FIBEROPTIK LANCARMULYA</h1>
                <div class="company-desc">NETWORK OPERATION CENTER & MANAGED SERVICE PROVIDER</div>
                <p class="company-address">
                    Jl. Taman Aloha E1 - 45, Sidoarjo, Jawa Timur (Kawasan Surabaya Hub) 61256<br>
                    Izin Penyelenggara ISP Kominfo RI No. 420/DIRJEN/2021 • Telp: (031) 33557377 • Hotline NOC: 0813-5843-9094<br>
                    Email: noc@netfly.net.id • Website: www.netfly.co.id
                </p>
            </td>
            <td class="letterhead-spacer-cell"></td>
        </tr>
    </table>

    <!-- GARIS PEMBATAS KOP GANDA (STANDAR DOKUMEN RESMI) -->
    <div class="kop-divider">
        <div class="kop-line-thick"></div>
        <div class="kop-line-thin"></div>
    </div>

    <!-- JUDUL DOKUMEN & METADATA AUDIT FORMAL -->
    <div class="report-title-container">
        <h2 class="report-title">LAPORAN EVALUASI SERVICE LEVEL AGREEMENT (SLA) & AUDIT JARINGAN</h2>
        <div class="report-subtitle">
            Nomor Registrasi: <span class="font-mono font-bold text-dark">{{ $reportDocumentNumber }}</span>
        </div>
    </div>

    <table class="report-meta-table">
        <tr>
            <td class="meta-label">Bulan & Periode Audit</td>
            <td class="meta-val">{{ $formattedPeriod }}</td>
            <td class="meta-label">Status SLA Kontrak</td>
            <td class="meta-val">
                @php
                    $slaBadgeInlineStyle = match ($slaStatusColor ?? 'emerald') {
                        'amber' => 'background:#fffbeb;color:#b45309;border:1px solid #fde68a;',
                        'slate' => 'background:#f1f5f9;color:#475569;border:1px solid #cbd5e1;',
                        default => 'background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;',
                    };
                @endphp
                <span class="badge-sla" style="float: none; display: inline-block; {{ $slaBadgeInlineStyle }}">{{ strtoupper($slaStatusLabel) }}</span>
            </td>
        </tr>
        <tr>
            <td class="meta-label">Mitra / Entitas Pelanggan</td>
            <td class="meta-val text-blue">{{ $evaluatedPartner }}</td>
            <td class="meta-label">Target Komitmen SLA</td>
            <td class="meta-val">Min. Ketersediaan {{ $targetSlaMin }}</td>
        </tr>
        <tr>
            <td class="meta-label">Waktu Cetak Dokumen</td>
            <td class="meta-val font-mono">{{ $generatedAt }}</td>
            <td class="meta-label">Klasifikasi Dokumen</td>
            <td class="meta-val text-dark">Confidential / Legal SLA Evidence</td>
        </tr>
    </table>

    <!-- 4 EXECUTIVE SUMMARY KPI BOXES -->
    <table class="kpi-table">
        <tr>
            <!-- Box 1 -->
            <td class="kpi-cell">
                <div class="kpi-title">Ketersediaan SLA</div>
                @php
                    $minThresholdNum = (float) str_replace('%', '', $targetSlaMin);
                    $isSlaFulfilled = ($actualSla >= $minThresholdNum && $totalDevices > 0);
                @endphp
                <div class="kpi-value-lg {{ $isSlaFulfilled ? 'text-emerald' : ($actualSla > 0 ? 'text-amber' : 'text-muted') }}">{{ number_format($actualSla, 1) }}%</div>
                <div class="kpi-sub">Target Kontrak: Min {{ $targetSlaMin }}</div>
                <div class="kpi-sub" style="color: {{ $isSlaFulfilled ? '#047857' : '#b45309' }}; margin-top: 2px;">
                    {{ $isSlaFulfilled ? '[OK] ' : '[!] ' }}{{ $slaCommitmentText }}
                </div>
            </td>
            <!-- Box 2 -->
            <td class="kpi-cell">
                <div class="kpi-title">Total Insiden & Tiket</div>
                <div class="kpi-value-lg">{{ $totalTickets }}</div>
                <div class="kpi-sub">{{ $resolvedTickets }} Insiden Terselesaikan</div>
                <div class="kpi-sub">MTTR: {{ $avgResolutionTime }}</div>
            </td>
            <!-- Box 3 -->
            <td class="kpi-cell">
                <div class="kpi-title">Node Hardware</div>
                <div class="kpi-value-lg">{{ $totalDevices }}</div>
                <div class="kpi-sub">{{ $normalDevices }} Unit Beroperasi Normal</div>
                <div class="kpi-sub" style="color: #b45309;">{{ $warningDevices }} Unit Dalam Pengawasan</div>
            </td>
            <!-- Box 4 -->
            <td class="kpi-cell">
                <div class="kpi-title">Cakupan Evaluasi</div>
                <div class="kpi-value-lg" style="font-size: 10pt; line-height: 1.2; height: 26px; overflow: hidden;">
                    {{ $selectedCustomer === 'all' ? 'Seluruh Mitra NetFly' : \Illuminate\Support\Str::limit($evaluatedPartner, 24) }}
                </div>
                <div class="kpi-sub">Periode: {{ $formattedPeriod }}</div>
                <div class="kpi-sub text-blue">Status Kontrak: Aktif</div>
            </td>
        </tr>
    </table>

    <!-- SECTION 1: INVENTARIS NODE HARDWARE -->
    <div class="section-header">
        1. DAFTAR PERANGKAT JARINGAN DALAM LAPORAN
        <span class="section-count">({{ $totalDevices }} Node Terpantau Berjalan)</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 25%;">HOSTNAME / PERANGKAT</th>
                <th style="width: 14%;">JENIS</th>
                <th style="width: 18%;">IP ADDRESS</th>
                <th style="width: 22%;">LOKASI PENEMPATAN</th>
                <th style="width: 16%; text-align: right;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($devices as $index => $device)
                <tr>
                    <td class="text-center font-mono">{{ $index + 1 }}</td>
                    <td class="font-bold font-mono text-dark">{{ $device['hostname'] }}</td>
                    <td>{{ $device['type'] }}</td>
                    <td class="font-mono">{{ $device['ip_address'] }}</td>
                    <td class="text-muted">{{ $device['location'] }}</td>
                    <td class="text-right">
                        @if($device['status_state'] === 'online')
                            <span class="pill pill-green">Online (Normal)</span>
                        @elseif($device['status_state'] === 'warning')
                            <span class="pill pill-amber">Warning (Degradasi)</span>
                        @else
                            <span class="pill" style="background-color: #fee2e2; color: #991b1b;">Offline (Padam)</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted" style="padding: 10px;">
                        Tidak ada catatan perangkat untuk parameter mitra ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- SECTION 2: RIWAYAT TIKET PENANGANAN GANGGUAN -->
    <div class="section-header">
        2. RIWAYAT TIKET & PENANGANAN INSIDEN PERIODE INI
        <span class="section-count">({{ $totalTickets }} Tiket Terdaftar)</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 16%;">NOMOR TIKET</th>
                <th style="width: 33%;">SUBJEK / RINGKASAN GANGGUAN</th>
                <th style="width: 12%;">PRIORITAS</th>
                <th style="width: 18%;">TEKNISI NOC</th>
                <th style="width: 16%; text-align: right;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $index => $ticket)
                <tr>
                    <td class="text-center font-mono">{{ $index + 1 }}</td>
                    <td class="font-bold font-mono text-blue">{{ $ticket['ticket_number'] }}</td>
                    <td>
                        <strong class="text-dark">{{ $ticket['full_subject'] }}</strong>
                    </td>
                    <td>
                        <span class="text-muted" style="font-size: 6.5pt; font-weight: 600;">{{ $ticket['priority'] }}</span>
                    </td>
                    <td>{{ $ticket['technician'] }}</td>
                    <td class="text-right">
                        @if($ticket['status_type'] === 'in_progress')
                            <span class="pill pill-dark">In Progress</span>
                        @elseif($ticket['status_type'] === 'assigned')
                            <span class="pill pill-slate">Assigned</span>
                        @elseif($ticket['status_type'] === 'open')
                            <span class="pill pill-amber">Open</span>
                        @elseif($ticket['status_type'] === 'resolved')
                            <span class="pill pill-green">Resolved</span>
                        @else
                            <span class="pill pill-slate">{{ $ticket['status'] }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted" style="padding: 10px;">
                        Tidak ada catatan insiden gangguan selama periode ini. Kinerja transmisi tercatat nihil downtime.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- SECTION 3: TANDA TANGAN & PENGESAHAN DOKUMEN -->
    <table class="signature-table">
        <tr>
            <!-- Pihak 1: NetFly NOC -->
            <td>
                <div class="sign-card">
                    <div class="stamp-box">
                        NETFLY NOC<br>VERIFIED
                    </div>
                    <div class="sign-title">Diterbitkan & Diverifikasi Oleh:</div>
                    <div style="height: 35px;"></div>
                    <div class="sign-name">{{ $issuerName }}</div>
                    <div class="sign-role">{{ $issuerRole }}</div>
                </div>
            </td>
            <!-- Pihak 2: Mitra Customer -->
            <td>
                <div class="sign-card">
                    <div class="sign-title">Diterima & Disetujui Oleh:</div>
                    <div style="height: 35px;"></div>
                    <div class="sign-name">{{ $selectedCustomer === 'all' ? 'PERWAKILAN RESMI MITRA B2B' : 'IT INFRASTRUCTURE MANAGER' }}</div>
                    <div class="sign-role">{{ $evaluatedPartner }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- FOOTER WITH LEGAL NOTICE -->
    <div class="page-footer">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="text-align: left; width: 60%;">
                    NetFly Enterprise NMS System • Hak Cipta &copy; 2026 PT Netmedia Fiberoptik Lancarmulya.
                </td>
                <td style="text-align: right; width: 40%; font-family: 'Courier New', Courier, monospace;">
                    Doc ID: {{ $reportDocumentNumber }} | Hal 1 dari 1
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
