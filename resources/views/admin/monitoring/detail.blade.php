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

    <!-- Chart.js (render grafik canvas) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    @php
        // Nilai numerik untuk grafik & indikator
        $rxValue = (float) filter_var($device['rx_mbps'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $txValue = (float) filter_var($device['tx_mbps'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $tempValue = (int) filter_var($device['temperature'], FILTER_SANITIZE_NUMBER_INT);

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
                'value' => $device['cpu_load'],
                'unit' => '%',
                'bar' => $device['cpu_load'],
                'level' => $levelOf($device['cpu_load'], 'cpu'),
                'hint' => 'Batas aman di bawah 70%',
            ],
            [
                'key' => 'ram',
                'label' => 'Memori (RAM)',
                'value' => $device['ram_load'],
                'unit' => '%',
                'bar' => $device['ram_load'],
                'level' => $levelOf($device['ram_load'], 'ram'),
                'hint' => 'Batas aman di bawah 75%',
            ],
            [
                'key' => 'ping',
                'label' => 'Latensi',
                'value' => $device['last_ping_ms'],
                'unit' => 'ms',
                'bar' => min(100, $device['last_ping_ms']),
                'level' => $levelOf($device['last_ping_ms'], 'ping'),
                'hint' => 'Packet loss ' . $device['packet_loss'] . '%',
            ],
            [
                'key' => 'temp',
                'label' => 'Suhu board',
                'value' => $tempValue,
                'unit' => '°C',
                'bar' => min(100, round($tempValue / 90 * 100)),
                'level' => $levelOf($tempValue, 'temp'),
                'hint' => 'Tegangan PSU ' . $device['voltage'],
            ],
        ];

        $statusStyles = [
            'online' => ['label' => 'Online', 'dot' => 'bg-emerald-500', 'pill' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'],
            'warning' => ['label' => 'Warning', 'dot' => 'bg-amber-500', 'pill' => 'bg-amber-50 text-amber-700 ring-amber-600/20'],
            'offline' => ['label' => 'Offline', 'dot' => 'bg-rose-500', 'pill' => 'bg-rose-50 text-rose-700 ring-rose-600/20'],
        ];
        $st = $statusStyles[$device['status']] ?? $statusStyles['offline'];

        $infoItems = [
            ['label' => 'IP Address', 'value' => $device['ip_address'], 'mono' => true],
            ['label' => 'Hardware', 'value' => $device['model'], 'mono' => false],
            ['label' => 'Lokasi', 'value' => $device['rack_location'], 'mono' => false],
            ['label' => 'Project', 'value' => $device['project'], 'mono' => false],
            ['label' => 'Uptime', 'value' => $device['uptime'], 'mono' => true],
            ['label' => 'Interval polling', 'value' => 'Setiap 3 detik', 'mono' => false],
        ];

        $upInterfaces = collect($device['interfaces'])->where('status', 'up')->count();
    @endphp

    <div class="h-screen flex w-full overflow-hidden">

        <!-- SIDEBAR (Include Partial) -->
        @include('partials.sidebar')

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0 h-full overflow-y-auto">

            <!-- TOP NAVBAR / HEADER -->
            <header class="bg-white border-b border-slate-200/80 px-6 py-3.5 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Mobile Hamburger -->
                    <button id="openSidebar" class="lg:hidden p-1.5 -ml-1 text-slate-600 hover:text-slate-900 rounded-md hover:bg-slate-100 cursor-pointer" aria-label="Buka menu">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
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
            <main class="flex-1 p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">

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

                        <div class="flex items-center gap-3 shrink-0">
                            <div class="text-right">
                                <p class="text-[11px] text-slate-400">Update terakhir</p>
                                <p id="lastUpdatedClock" class="text-xs font-medium text-slate-700 tabular-nums">Baru saja</p>
                            </div>
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
                                    data-progress-width="{{ $metric['bar'] }}"
                                ></div>
                            </div>
                            <p id="metric-{{ $metric['key'] }}-hint" class="mt-2 text-[11px] text-slate-400">{{ $metric['hint'] }}</p>
                        </div>
                    @endforeach
                </section>

                <!-- 3. BANDWIDTH CHART -->
                <section class="bg-white rounded-xl border border-slate-200 p-5">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-sm font-semibold text-slate-900">Bandwidth real-time</h2>
                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Live
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Total transmisi download (Rx) dan upload (Tx) perangkat</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <div class="flex items-center gap-4 text-xs">
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

                            <div class="inline-flex p-1 bg-slate-100 rounded-lg" role="tablist" aria-label="Rentang waktu grafik">
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
                    </div>

                    <div class="mt-4 w-full h-72 relative">
                        <canvas id="liveTrafficChart" aria-label="Grafik bandwidth real-time" role="img"></canvas>
                    </div>
                </section>

                <!-- 4. INTERFACES & LOG -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                    <!-- Interface table -->
                    <section class="lg:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
                            <h2 class="text-sm font-semibold text-slate-900">Port & interface</h2>
                            <span class="text-xs text-slate-500">{{ $upInterfaces }} dari {{ count($device['interfaces']) }} aktif</span>
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
                                    @foreach ($device['interfaces'] as $iface)
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="py-3 px-5 font-medium text-slate-800">{{ $iface['name'] }}</td>
                                            <td class="py-3 px-5">
                                                @if ($iface['status'] === 'up')
                                                    <span class="inline-flex items-center gap-1.5 text-emerald-700">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Up
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 text-rose-700">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Down
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-5 text-slate-500 tabular-nums">{{ $iface['speed'] }}</td>
                                            <td class="py-3 px-5 text-right text-slate-700 tabular-nums whitespace-nowrap">
                                                ↓ {{ $iface['rx'] }} <span class="text-slate-300 mx-1">/</span> ↑ {{ $iface['tx'] }}
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
                                <div class="px-5 py-3.5 border-l-[3px] {{ $item['severity'] === 'warning' ? 'border-amber-500' : 'border-blue-500' }}">
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
                    <p>© 2026 PT Netmedia Fiberoptik Lancarmulya • Network Operation Center</p>
                </footer>

            </main>
        </div>
    </div>

    <!-- LIVE TELEMETRY POLLING & CHART -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const endpoint = @json(route('monitoring.telemetry', $device['id']));
            const thresholds = @json($thresholds);
            const POLL_INTERVAL = 3000;
            const BUFFER_LIMIT = 300; // 15 menit @ 3 detik
            let visiblePoints = 20;   // default 1 menit

            const BAR = { normal: 'bg-emerald-500', warning: 'bg-amber-500', critical: 'bg-rose-500' };
            const TEXT = { normal: 'text-slate-900', warning: 'text-amber-600', critical: 'text-rose-600' };

            const levelOf = (value, metric) => {
                const [warn, crit] = thresholds[metric];
                return value >= crit ? 'critical' : (value >= warn ? 'warning' : 'normal');
            };

            function setMetric(key, value, barPercent) {
                const valueEl = document.getElementById(`metric-${key}-value`);
                const barEl = document.getElementById(`metric-${key}-bar`);
                const level = levelOf(value, key);

                if (valueEl) {
                    valueEl.textContent = value;
                    valueEl.classList.remove(...Object.values(TEXT));
                    valueEl.classList.add(TEXT[level]);
                }
                if (barEl) {
                    barEl.style.width = `${Math.min(100, barPercent)}%`;
                    barEl.classList.remove(...Object.values(BAR));
                    barEl.classList.add(BAR[level]);
                }
            }

            // ---- Buffer data grafik (disimpan sampai 15 menit) ----
            const buffer = { labels: [], rx: [], tx: [] };
            const baseRx = @json($rxValue);
            const baseTx = @json($txValue);
            const now = Date.now();

            for (let i = visiblePoints - 1; i >= 0; i--) {
                buffer.labels.push(new Date(now - i * POLL_INTERVAL).toTimeString().slice(0, 8));
                buffer.rx.push(+(baseRx + (Math.random() * 4 - 2)).toFixed(1));
                buffer.tx.push(+(baseTx + (Math.random() * 3 - 1.5)).toFixed(1));
            }

            const chart = new Chart(document.getElementById('liveTrafficChart'), {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Rx (Mbps)',
                            data: [],
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.08)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                        },
                        {
                            label: 'Tx (Mbps)',
                            data: [],
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.08)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { maxTicksLimit: 8, font: { size: 10 }, color: '#94a3b8' },
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            border: { display: false },
                            ticks: { font: { size: 10 }, color: '#94a3b8', callback: v => `${v} Mbps` },
                        },
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: { label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y} Mbps` },
                        },
                    },
                },
            });

            function renderChart() {
                chart.data.labels = buffer.labels.slice(-visiblePoints);
                chart.data.datasets[0].data = buffer.rx.slice(-visiblePoints);
                chart.data.datasets[1].data = buffer.tx.slice(-visiblePoints);
                chart.update('none');
            }
            renderChart();

            // ---- Pilihan rentang waktu ----
            const rangeTabs = document.querySelectorAll('.range-tab');
            const TAB_ACTIVE = ['bg-white', 'text-slate-900', 'shadow-sm'];
            const TAB_IDLE = ['text-slate-500', 'hover:text-slate-800'];

            rangeTabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    visiblePoints = parseInt(tab.dataset.points, 10) || 20;
                    rangeTabs.forEach(t => {
                        const active = t === tab;
                        t.classList.remove(...(active ? TAB_IDLE : TAB_ACTIVE));
                        t.classList.add(...(active ? TAB_ACTIVE : TAB_IDLE));
                        t.setAttribute('aria-selected', String(active));
                    });
                    renderChart();
                });
            });

            // ---- Polling telemetri ----
            let isFetching = false;

            async function fetchTelemetry() {
                if (isFetching || document.hidden) return; // hemat request saat tab tidak aktif
                isFetching = true;

                try {
                    const res = await fetch(endpoint, { headers: { Accept: 'application/json' } });
                    if (!res.ok) return;
                    const json = await res.json();
                    if (!json.success) return;

                    const m = json.metrics;
                    setMetric('cpu', m.cpu_load, m.cpu_load);
                    setMetric('ram', m.ram_load, m.ram_load);
                    setMetric('ping', m.ping_ms, m.ping_ms);

                    const pingHint = document.getElementById('metric-ping-hint');
                    if (pingHint) pingHint.textContent = `Packet loss ${m.packet_loss}%`;

                    document.getElementById('currentRxValue').textContent = `${m.rx_mbps} Mbps`;
                    document.getElementById('currentTxValue').textContent = `${m.tx_mbps} Mbps`;
                    document.getElementById('lastUpdatedClock').textContent = `${json.timestamp} WIB`;

                    buffer.labels.push(json.timestamp);
                    buffer.rx.push(m.rx_mbps);
                    buffer.tx.push(m.tx_mbps);
                    if (buffer.labels.length > BUFFER_LIMIT) {
                        buffer.labels.shift();
                        buffer.rx.shift();
                        buffer.tx.shift();
                    }
                    renderChart();
                } catch (e) {
                    console.error('Poller error:', e);
                } finally {
                    isFetching = false;
                }
            }

            setInterval(fetchTelemetry, POLL_INTERVAL);

            // ---- Tombol poll manual ----
            const btnRefresh = document.getElementById('btnRefreshLive');
            const spinner = document.getElementById('refreshSpinner');
            btnRefresh.addEventListener('click', async () => {
                spinner.classList.add('animate-spin');
                btnRefresh.disabled = true;
                await fetchTelemetry();
                setTimeout(() => {
                    spinner.classList.remove('animate-spin');
                    btnRefresh.disabled = false;
                }, 400);
            });
        });
    </script>
</body>
</html>
