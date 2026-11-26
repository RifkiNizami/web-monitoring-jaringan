<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $device['hostname'] }} — Monitoring NetFly</title>
    <link rel="icon" href="{{ asset('assets/images/netfly-logo.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    @php
        // Nilai numerik untuk grafik & indikator (aman terhadap null / undefined index)
        $rxValue = \App\Http\Controllers\Admin\MonitoringController::parseRateToMbps($device['rx_mbps'] ?? '0');
        $txValue = \App\Http\Controllers\Admin\MonitoringController::parseRateToMbps($device['tx_mbps'] ?? '0');
        $rawTemp = $device['temperature'] ?? null;
        $hasTemp = is_string($rawTemp) && preg_match('/-?\d+(\.\d+)?/', $rawTemp, $tempMatch);
        $tempValue = $hasTemp ? (int) round((float) $tempMatch[0]) : null;
        $cpuLoad = (float) ($device['cpu_load'] ?? 0.0);
        $ramLoad = (float) ($device['ram_load'] ?? 0.0);
        $pingMs = array_key_exists('latency_ms', $device) && $device['latency_ms'] !== null
            ? (int) $device['latency_ms']
            : (int) ($device['last_ping_ms'] ?? 0);
        $packetLoss = (float) ($device['packet_loss'] ?? 0.0);
        $devPort = (int) ($device['api_port'] ?? 8728);
        $devUnitType = $device['unit_type'] ?? 'Router';
        $isNonMikrotik = !empty($device['is_non_mikrotik'])
            || in_array($devPort, [80, 8080], true)
            || in_array(strtolower($devUnitType), ['access point', 'generic router', 'generic router (http/ping)'], true);
        $interfacesList = is_array($device['interfaces'] ?? null) ? $device['interfaces'] : [];

        // Ambang batas: [warning, critical]
        $thresholds = [
            'cpu' => [70, 85],
            'ram' => [75, 85],
            'ping' => [30, 80],
            'temp' => [55, 70],
        ];

        $levelOf = function (float $value, string $metric) use ($thresholds) {
            [$warn, $crit] = $thresholds[$metric];
            return $value >= $crit ? 'critical' : ($value >= $warn ? 'warning' : 'normal');
        };

        $barClass = ['normal' => 'bg-emerald-500', 'warning' => 'bg-amber-500', 'critical' => 'bg-rose-500'];
        $textClass = ['normal' => 'text-slate-900', 'warning' => 'text-amber-600', 'critical' => 'text-rose-600'];

        $metrics = [
            [
                'key' => 'cpu',
                'label' => 'CPU',
                'value' => $cpuLoad,
                'unit' => '%',
                'bar' => $cpuLoad,
                'level' => $levelOf($cpuLoad, 'cpu'),
                'hint' => $isNonMikrotik ? 'Default 0% (Mode HTTP/Ping Port ' . $devPort . ')' : 'Batas aman di bawah 70%',
            ],
            [
                'key' => 'ram',
                'label' => 'Memori (RAM)',
                'value' => $ramLoad,
                'unit' => '%',
                'bar' => $ramLoad,
                'level' => $levelOf($ramLoad, 'ram'),
                'hint' => $isNonMikrotik ? 'Default 0% (Mode HTTP/Ping Port ' . $devPort . ')' : 'Batas aman di bawah 75%',
            ],
            [
                'key' => 'ping',
                'label' => 'Latensi (ms)',
                'value' => $pingMs,
                'unit' => 'ms',
                'bar' => min(100, $pingMs),
                'level' => $levelOf((float) $pingMs, 'ping'),
                'hint' => 'Packet loss ' . $packetLoss . '%',
            ],
            [
                'key' => 'temp',
                'label' => 'Suhu board',
                'value' => $hasTemp ? $tempValue : 'N/A',
                'unit' => $hasTemp ? '°C' : '',
                'bar' => $hasTemp ? min(100, max(0, round($tempValue / 90 * 100))) : 0,
                'level' => $hasTemp ? $levelOf((float) $tempValue, 'temp') : 'normal',
                'hint' => $hasTemp ? ('Tegangan PSU ' . ($device['voltage'] ?? '-')) : 'Sensor suhu tidak tersedia',
            ],
        ];

        $statusStyles = [
            'online' => ['label' => 'Online', 'dot' => 'bg-emerald-500', 'pill' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'],
            'warning' => ['label' => 'Warning', 'dot' => 'bg-amber-500', 'pill' => 'bg-amber-50 text-amber-700 ring-amber-600/20'],
            'offline' => ['label' => 'Offline', 'dot' => 'bg-rose-500', 'pill' => 'bg-rose-50 text-rose-700 ring-rose-600/20'],
        ];
        $st = $statusStyles[$device['status'] ?? 'online'] ?? $statusStyles['offline'];

        $infoItems = [
            ['label' => 'IP Address & Port', 'value' => ($device['ip_address'] ?? '-') . ':' . $devPort, 'mono' => true],
            ['label' => 'Hardware', 'value' => $device['model'] ?? '-', 'mono' => false],
            ['label' => 'Lokasi', 'value' => $device['rack_location'] ?? '-', 'mono' => false],
            ['label' => 'Project', 'value' => $device['project'] ?? '-', 'mono' => false],
            ['label' => 'Uptime', 'value' => $device['uptime'] ?? '-', 'mono' => true],
            ['label' => 'Metode Polling', 'value' => $isNonMikrotik ? 'HTTP/TCP Socket (Port ' . $devPort . ')' : 'RouterOS API (Port ' . $devPort . ')', 'mono' => false],
        ];

        $upInterfaces = collect($interfacesList)->where('status', 'up')->count();
    @endphp

    <div class="h-screen flex w-full overflow-hidden">

        <!-- SIDEBAR (Include Partial) -->
        @include('partials.sidebar')

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0 h-full overflow-y-auto">

            <!-- TOP NAVBAR / HEADER -->
            <header class="bg-white border-b border-slate-200/80 px-6 py-3.5 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Collapsible Sidebar Navbar Toggle Button -->
                    <button id="openSidebar" type="button" class="sidebar-toggle-btn p-1.5 -ml-1 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 cursor-pointer transition-colors" title="Buka / Tutup Menu Navigasi (Ctrl+B)" aria-label="Toggle menu navigasi" aria-expanded="true">
                        <svg class="w-5 h-5 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path class="bar-top" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16" />
                            <path class="bar-mid" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16" />
                            <path class="bar-bot" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 18h16" />
                        </svg>
                    </button>

                    <!-- Breadcrumb -->
                    <nav class="flex items-center gap-2 text-xs font-medium text-slate-400 min-w-0" aria-label="Breadcrumb">
                        <a href="{{ route('monitoring.index') }}" class="hover:text-slate-700 transition">Monitoring</a>
                        <span>›</span>
                        <span class="text-slate-800 font-semibold truncate">{{ $device['hostname'] }}</span>
                    </nav>
                </div>

                <a
                    href="{{ route('monitoring.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 px-3 py-1.5 rounded-lg transition shrink-0"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Daftar perangkat</span>
                </a>
            </header>

            <!-- MAIN BODY CONTENT -->
            <main id="monitoringDetailRoot"
                  data-device-id="{{ $device['id'] }}"
                  data-endpoint="{{ route('monitoring.telemetry', $device['id']) }}"
                  data-thresholds="{{ json_encode($thresholds) }}"
                  data-base-cpu="{{ $cpuLoad }}"
                  data-base-ram="{{ $ramLoad }}"
                  data-base-ping="{{ $pingMs }}"
                  data-base-rx="{{ $rxValue }}"
                  data-base-tx="{{ $txValue }}"
                  data-base-packet-loss="{{ $packetLoss }}"
                  data-status="{{ $device['status'] ?? 'offline' }}"
                  class="p-6 lg:p-8 space-y-6">

                <!-- 1. DEVICE HEADER -->
                <section class="bg-white rounded-xl border border-slate-200">
                    <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">{{ $device['hostname'] }}</h1>
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-medium ring-1 ring-inset {{ $st['pill'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $st['dot'] }}"></span>
                                        {{ $st['label'] }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-600">{{ $device['unit_type'] }}</span>
                                </div>
                                <p class="mt-1 text-sm text-slate-500 truncate">{{ $device['customer'] }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5 shrink-0">
                            <div class="text-right mr-1">
                                <p class="text-[11px] text-slate-400">Update terakhir</p>
                                <p id="lastUpdatedClock" class="text-xs font-medium text-slate-700 tabular-nums">Baru saja</p>
                            </div>
                            <a
                                href="{{ route('monitoring.devices.edit', $device['id']) }}"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold transition"
                            >
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit perangkat
                            </a>
                            <button
                                id="btnRefreshLive"
                                type="button"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold cursor-pointer active:scale-95 transition"
                            >
                                <svg id="refreshSpinner" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Poll sekarang
                            </button>
                        </div>
                    </div>

                    <!-- Info grid -->
                    <dl class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 border-t border-slate-100 divide-slate-100">
                        @foreach ($infoItems as $item)
                            <div class="px-5 py-3.5 {{ !$loop->last ? 'lg:border-r border-slate-100' : '' }}">
                                <dt class="text-[11px] text-slate-400">{{ $item['label'] }}</dt>
                                <dd class="mt-0.5 text-xs font-medium text-slate-800 truncate {{ $item['mono'] ? 'font-mono' : '' }}" title="{{ $item['value'] }}">
                                    {{ $item['value'] }}
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </section>

                <!-- 2. METRIC CARDS -->
                <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($metrics as $metric)
                        <div class="bg-white rounded-xl border border-slate-200 p-4">
                            <p class="text-xs font-medium text-slate-500">{{ $metric['label'] }}</p>
                            <p class="mt-1 tabular-nums">
                                <span id="metric-{{ $metric['key'] }}-value" class="text-2xl font-semibold {{ $textClass[$metric['level']] }}">{{ $metric['value'] }}</span>
                                <span class="text-sm font-medium text-slate-400">{{ $metric['unit'] }}</span>
                            </p>
                            <div class="mt-3 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                <div
                                    id="metric-{{ $metric['key'] }}-bar"
                                    class="h-full rounded-full transition-all duration-500 {{ $barClass[$metric['level']] }}"
                                    style="width: {{ min(100, max(0, (float) $metric['bar'])) }}%"
                                    data-progress-width="{{ $metric['bar'] }}"
                                ></div>
                            </div>
                            <p id="metric-{{ $metric['key'] }}-hint" class="mt-2 text-[11px] text-slate-400">{{ $metric['hint'] }}</p>
                        </div>
                    @endforeach
                </section>

                <!-- 3. BANDWIDTH & LATENCY CHART -->
                <section class="bg-white rounded-xl border border-slate-200 p-5">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-sm font-semibold text-slate-900">Grafik Telemetri & Latensi (ms) Real-time</h2>
                                @if (($device['status'] ?? 'offline') === 'offline')
                                    <span id="chartLiveIndicator" class="inline-flex items-center gap-1 text-[11px] font-medium text-rose-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Offline (0 Tx/Rx)
                                    </span>
                                @else
                                    <span id="chartLiveIndicator" class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Live Telemetri
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Pemantauan waktu respons Latensi (ms) serta transmisi download (Rx) dan upload (Tx)</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-4 text-xs">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-sm bg-amber-500"></span>
                                <span class="text-slate-500">Latensi</span>
                                <span id="currentPingValue" class="font-semibold text-slate-900 tabular-nums">{{ $pingMs }} ms</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-sm bg-blue-600"></span>
                                <span class="text-slate-500">Rx</span>
                                <span id="currentRxValue" class="font-semibold text-slate-900 tabular-nums">{{ $rxValue }} Mbps</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-sm bg-emerald-500"></span>
                                <span class="text-slate-500">Tx</span>
                                <span id="currentTxValue" class="font-semibold text-slate-900 tabular-nums">{{ $txValue }} Mbps</span>
                            </div>
                        </div>
                    </div>

                    {{-- [PANDUAN UBAH CHART] Toolbar tombol ganti tipe grafik & rentang waktu --}}
                    {{-- - Ubah warna/teks/ikon tombol di #btnCycleChartType atau .chart-type-btn (data-chart-type="line|bar|pie") --}}
                    {{-- - Ubah opsi rentang waktu pada array ['1m' => 20, '5m' => 100, '15m' => 300] di #chartRangeWrapper --}}
                    <!-- TOOLBAR KONTROL GRAFIK: Tombol Ganti Chart (Garis, Batang, Pie) & Rentang Waktu -->
                    <div class="mt-4 pt-3.5 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <button
                                id="btnCycleChartType"
                                type="button"
                                title="Klik untuk mengganti jenis grafik (Garis → Batang → Pie)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs active:scale-95 transition cursor-pointer"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                                <span>Ganti Grafik: <strong id="activeChartTypeLabel">Garis</strong></span>
                            </button>

                            <div class="inline-flex p-1 bg-slate-100 border border-slate-200/80 rounded-lg" role="group" aria-label="Pilih tipe grafik">
                                <button
                                    type="button"
                                    data-chart-type="line"
                                    aria-pressed="true"
                                    title="Tampilkan grafik garis"
                                    class="chart-type-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-md transition cursor-pointer bg-blue-600 text-white shadow-sm"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 17l6-6 4 4 8-8" />
                                    </svg>
                                    <span>Garis</span>
                                </button>
                                <button
                                    type="button"
                                    data-chart-type="bar"
                                    aria-pressed="false"
                                    title="Tampilkan grafik batang"
                                    class="chart-type-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-md transition cursor-pointer text-slate-600 hover:text-slate-900"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6m6 0h6m-6 0V9a2 2 0 012-2h2a2 2 0 012 2v10m0 0h2a2 2 0 002-2v-4a2 2 0 00-2-2h-2" />
                                    </svg>
                                    <span>Batang</span>
                                </button>
                                <button
                                    type="button"
                                    data-chart-type="pie"
                                    aria-pressed="false"
                                    title="Tampilkan grafik pie"
                                    class="chart-type-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-md transition cursor-pointer text-slate-600 hover:text-slate-900"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                                    </svg>
                                    <span>Pie</span>
                                </button>
                            </div>
                        </div>

                        <div id="chartRangeWrapper" class="inline-flex p-1 bg-slate-100 rounded-lg" role="tablist" aria-label="Rentang waktu grafik">
                            @foreach (['1m' => 20, '5m' => 100, '15m' => 300] as $rangeLabel => $points)
                                <button
                                    type="button"
                                    role="tab"
                                    data-points="{{ $points }}"
                                    aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                    class="range-tab px-2.5 py-1 text-[11px] font-medium rounded-md transition cursor-pointer {{ $loop->first ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}"
                                >
                                    {{ $rangeLabel }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- [PANDUAN UBAH CHART] Tinggi area grafik dikontrol oleh class "h-72" (misal ganti ke "h-96" agar lebih tinggi) --}}
                    <div class="mt-4 w-full h-72 relative">
                        <canvas id="liveTrafficChart" aria-label="Grafik telemetri dan latensi real-time" role="img"></canvas>
                    </div>
                </section>

                <!-- 4. INTERFACES & LOG -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                    <!-- Interface table -->
                    <section class="lg:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
                            <h2 class="text-sm font-semibold text-slate-900">Port & interface</h2>
                            <span class="text-xs text-slate-500">{{ $upInterfaces }} dari {{ count($interfacesList) }} aktif</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="text-[11px] text-slate-400 bg-slate-50/60">
                                        <th class="py-2.5 px-5 font-medium">Interface</th>
                                        <th class="py-2.5 px-5 font-medium">Status</th>
                                        <th class="py-2.5 px-5 font-medium">Speed</th>
                                        <th class="py-2.5 px-5 font-medium text-right">Rx / Tx</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($interfacesList as $iface)
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="py-3 px-5 font-medium text-slate-800">{{ $iface['name'] ?? 'eth0' }}</td>
                                            <td class="py-3 px-5">
                                                @if (($iface['status'] ?? 'up') === 'up')
                                                    <span class="inline-flex items-center gap-1.5 text-emerald-700">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Up
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 text-rose-700">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Down
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-5 text-slate-500 tabular-nums">{{ $iface['speed'] ?? '100 Mbps' }}</td>
                                            <td class="py-3 px-5 text-right text-slate-700 tabular-nums whitespace-nowrap">
                                                ↓ {{ $iface['rx'] ?? '0 Mbps' }} <span class="text-slate-300 mx-1">/</span> ↑ {{ $iface['tx'] ?? '0 Mbps' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- Log -->
                    <section class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-slate-100">
                            <h2 class="text-sm font-semibold text-slate-900">Riwayat log</h2>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @foreach ($alerts as $item)
                                @php
                                    $logBorderClass = match ($item['severity'] ?? 'info') {
                                        'critical' => 'border-rose-500',
                                        'warning' => 'border-amber-500',
                                        default => 'border-blue-500',
                                    };
                                @endphp
                                <div class="px-5 py-3.5 border-l-[3px] {{ $logBorderClass }}">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-xs font-semibold text-slate-900">{{ $item['title'] }}</p>
                                        <span class="text-[11px] text-slate-400 whitespace-nowrap tabular-nums">{{ $item['time'] }}</span>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-600 leading-relaxed">{{ $item['desc'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                </div>

                <!-- FOOTER -->
                <footer class="pt-4 pb-6 text-center text-xs text-slate-400">
                    <p>© {{ date('Y') }} PT Netmedia Fiberoptik Lancarmulya • Network Operation Center</p>
                </footer>

            </main>
        </div>
    </div>
</body>
</html>
