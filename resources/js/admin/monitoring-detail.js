/**
 * NetFly - Admin Device Monitoring Detail Module
 * (Chart.js real-time rendering with Line / Bar / Pie switcher, dynamic dummy telemetry,
 *  and data persistence across Turbo navigation)
 */
import Chart from 'chart.js/auto';

// Global references for interval and active chart instance
let activeTelemetryInterval = null;
let activeChartInstance = null;
let activeFetchController = null;

// Cleanup helper called on page unload or before Turbo caches/re-renders
export function cleanupAdminMonitoringDetail() {
    if (activeTelemetryInterval) {
        clearInterval(activeTelemetryInterval);
        activeTelemetryInterval = null;
    }
    if (activeFetchController) {
        try {
            activeFetchController.abort();
        } catch (e) {}
        activeFetchController = null;
    }
    if (activeChartInstance) {
        try {
            activeChartInstance.destroy();
        } catch (e) {}
        activeChartInstance = null;
    }
}

// Global Turbo lifecycle listeners (registered once)
if (typeof window !== 'undefined' && !window._monitoringDetailLifecycleAttached) {
    document.addEventListener('turbo:before-cache', cleanupAdminMonitoringDetail);
    document.addEventListener('turbo:before-render', cleanupAdminMonitoringDetail);
    window._monitoringDetailLifecycleAttached = true;
}

export function initAdminMonitoringDetail() {
    const rootEl = document.getElementById('monitoringDetailRoot');
    const canvas = document.getElementById('liveTrafficChart');

    // Not on monitoring detail page -> cleanup and return
    if (!rootEl || !canvas) {
        cleanupAdminMonitoringDetail();
        return;
    }

    // Cleanup previous chart instance if any existed
    cleanupAdminMonitoringDetail();
    const existingChart = Chart.getChart(canvas);
    if (existingChart) {
        existingChart.destroy();
    }

    const deviceId = rootEl.dataset.deviceId || 'unknown';
    let currentStatus = (rootEl.dataset.status || 'online').toLowerCase();
    const endpoint = rootEl.dataset.endpoint;

    let thresholds = {};
    try {
        thresholds = JSON.parse(rootEl.dataset.thresholds || '{}');
    } catch (e) {
        thresholds = {};
    }

    const baseCpu = Number(rootEl.dataset.baseCpu || 0);
    const baseRam = Number(rootEl.dataset.baseRam || 0);
    const basePing = Number(rootEl.dataset.basePing || 0);
    const baseRx = Number(rootEl.dataset.baseRx || 0);
    const baseTx = Number(rootEl.dataset.baseTx || 0);
    const baseLoss = Number(rootEl.dataset.basePacketLoss || 0);

    // [PANDUAN UBAH CHART - 1] Kecepatan update grafik & tipe grafik default
    // - POLL_INTERVAL: jeda update dalam milidetik (2500 = 2.5 detik; ubah ke 1000 untuk 1 detik, 5000 untuk 5 detik)
    // - BUFFER_LIMIT : batas maksimal riwayat titik data yang disimpan
    // - visiblePoints: jumlah titik awal yang tampil (sesuai tab 1m = 20 titik)
    // - currentChartType: tipe grafik awal ('line' = Garis, 'bar' = Batang, 'pie' = Pie)
    const POLL_INTERVAL = 2500;
    const BUFFER_LIMIT = 300; // ~12.5 menit @ 2.5 detik
    let visiblePoints = 20;   // default 1m

    const CHART_TYPE_KEY = `netfly_chart_type_${deviceId}`;
    const VALID_TYPES = ['line', 'bar', 'pie'];
    let currentChartType = 'line';
    try {
        const savedType = sessionStorage.getItem(CHART_TYPE_KEY);
        if (savedType && VALID_TYPES.includes(savedType)) {
            currentChartType = savedType;
        }
    } catch (e) {}

    // State terakhir untuk simulasi fallback offline/tanpa koneksi server
    let lastMetrics = {
        cpu_load: baseCpu > 0 ? baseCpu : (currentStatus === 'offline' ? 0 : 28.5),
        ram_load: baseRam > 0 ? baseRam : (currentStatus === 'offline' ? 0 : 45.0),
        ping_ms: basePing > 0 ? basePing : (currentStatus === 'offline' ? 0 : 9),
        rx: baseRx > 0 ? baseRx : (currentStatus === 'offline' ? 0 : 120.5),
        tx: baseTx > 0 ? baseTx : (currentStatus === 'offline' ? 0 : 68.4),
        packet_loss: baseLoss,
    };

    const BAR = { normal: 'bg-emerald-500', warning: 'bg-amber-500', critical: 'bg-rose-500' };
    const TEXT = { normal: 'text-slate-900', warning: 'text-amber-600', critical: 'text-rose-600' };

    const levelOf = (value, metric) => {
        const threshold = thresholds[metric];
        if (!threshold || !Array.isArray(threshold)) return 'normal';
        const [warn, crit] = threshold;
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
            barEl.style.width = `${Math.min(100, Math.max(0, barPercent))}%`;
            barEl.classList.remove(...Object.values(BAR));
            barEl.classList.add(BAR[level]);
        }
    }

    // -------------------------------------------------------------
    // Helper: Random-walk dummy generator (berubah dinamis tanpa koneksi)
    // -------------------------------------------------------------
    const clamp = (val, min, max) => Math.min(max, Math.max(min, val));
    const round1 = val => Math.round(val * 10) / 10;

    function generateClientDummyTelemetry() {
        const nowStr = new Date().toTimeString().slice(0, 8);
        if (currentStatus === 'offline') {
            return {
                timestamp: nowStr,
                status: 'offline',
                cpu_load: 0,
                ram_load: 0,
                ping_ms: 0,
                rx: 0,
                tx: 0,
                packet_loss: 100,
            };
        }

        // [PANDUAN UBAH CHART - 5] Batas min/max data dummy lokal (fallback jika koneksi ke server gagal)
        // Format: clamp(nilaiBaru, batasMinimum, batasMaksimum)
        const isWarning = currentStatus === 'warning';
        const cpu = round1(clamp(lastMetrics.cpu_load + (Math.random() - 0.5) * 9, isWarning ? 70 : 12, isWarning ? 88 : 66));
        const ram = round1(clamp(lastMetrics.ram_load + (Math.random() - 0.5) * 5, isWarning ? 75 : 24, isWarning ? 90 : 72));
        const rx = round1(clamp(lastMetrics.rx + (Math.random() - 0.5) * 28, 12, 280));
        const tx = round1(clamp(lastMetrics.tx + (Math.random() - 0.5) * 18, 6, 180));
        const ping = Math.round(clamp(lastMetrics.ping_ms + (Math.random() - 0.5) * 6, isWarning ? 32 : 3, isWarning ? 82 : 28));
        const loss = isWarning
            ? round1(clamp((lastMetrics.packet_loss || 3.5) + (Math.random() - 0.5) * 2, 1, 12))
            : (Math.random() < 0.1 ? 0.1 : 0);

        return {
            timestamp: nowStr,
            status: currentStatus,
            cpu_load: cpu,
            ram_load: ram,
            ping_ms: ping,
            rx,
            tx,
            packet_loss: loss,
        };
    }

    // -------------------------------------------------------------
    // Session Storage Telemetry Buffer (Persists across navbar menu clicks)
    // -------------------------------------------------------------
    const STORAGE_KEY = `netfly_telemetry_buf_${deviceId}`;

    function loadBuffer() {
        const isOffline = (currentStatus === 'offline');
        try {
            const raw = sessionStorage.getItem(STORAGE_KEY);
            if (raw) {
                const parsed = JSON.parse(raw);
                if (parsed && Array.isArray(parsed.labels) && parsed.labels.length > 0) {
                    const hasAllZeroWhileOnline = !isOffline
                        && parsed.ping.every(p => p === 0)
                        && parsed.rx.every(r => r === 0)
                        && parsed.tx.every(t => t === 0);

                    if (isOffline && (parsed.ping.some(p => p > 0) || parsed.rx.some(r => r > 0))) {
                        sessionStorage.removeItem(STORAGE_KEY);
                    } else if (hasAllZeroWhileOnline) {
                        sessionStorage.removeItem(STORAGE_KEY);
                    } else {
                        return parsed;
                    }
                }
            }
        } catch (e) {}

        // Inisialisasi riwayat awal dinamis agar grafik langsung hidup saat halaman dibuka
        const buf = { labels: [], ping: [], rx: [], tx: [] };
        const now = Date.now();
        const isWarning = currentStatus === 'warning';

        let walkPing = isOffline ? 0 : (basePing > 0 ? basePing : (isWarning ? 45 : 10));
        let walkRx = isOffline ? 0 : (baseRx > 0 ? baseRx : 115.0);
        let walkTx = isOffline ? 0 : (baseTx > 0 ? baseTx : 64.0);

        const seriesPing = [];
        const seriesRx = [];
        const seriesTx = [];

        for (let i = 0; i < visiblePoints; i++) {
            if (isOffline) {
                seriesPing.push(0);
                seriesRx.push(0);
                seriesTx.push(0);
            } else {
                seriesPing.unshift(Math.round(walkPing));
                seriesRx.unshift(round1(walkRx));
                seriesTx.unshift(round1(walkTx));

                walkPing = clamp(walkPing + (Math.random() - 0.5) * 5, isWarning ? 32 : 3, isWarning ? 82 : 28);
                walkRx = clamp(walkRx + (Math.random() - 0.5) * 24, 12, 280);
                walkTx = clamp(walkTx + (Math.random() - 0.5) * 16, 6, 180);
            }
        }

        for (let i = visiblePoints - 1; i >= 0; i--) {
            buf.labels.push(new Date(now - i * POLL_INTERVAL).toTimeString().slice(0, 8));
        }
        buf.ping = seriesPing;
        buf.rx = seriesRx;
        buf.tx = seriesTx;

        return buf;
    }

    const buffer = loadBuffer();

    function persistBuffer() {
        try {
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(buffer));
        } catch (e) {}
    }

    // -------------------------------------------------------------
    // Chart.js Builder (Garis / Batang / Pie)
    // [PANDUAN UBAH CHART - 2] Data & warna untuk Pie Chart
    // - Ubah labels/data/units/colors di bawah jika ingin mengganti isi irisan Pie (misal tambah CPU/RAM dari lastMetrics)
    // -------------------------------------------------------------
    function getPieDataPayload() {
        const latestRx = buffer.rx.length ? Number(buffer.rx[buffer.rx.length - 1]) || 0 : 0;
        const latestTx = buffer.tx.length ? Number(buffer.tx[buffer.tx.length - 1]) || 0 : 0;
        const latestPing = buffer.ping.length ? Number(buffer.ping[buffer.ping.length - 1]) || 0 : 0;
        const total = latestRx + latestTx + latestPing;

        if (total <= 0) {
            return {
                labels: ['Offline (Tidak ada trafik)'],
                data: [1],
                units: [''],
                colors: ['#cbd5e1'],
                borderColors: ['#ffffff'],
                isEmpty: true,
            };
        }

        return {
            labels: ['Download Rx (Mbps)', 'Upload Tx (Mbps)', 'Latensi (ms)'],
            data: [round1(latestRx), round1(latestTx), round1(latestPing)],
            units: ['Mbps', 'Mbps', 'ms'],
            colors: ['#2563eb', '#10b981', '#f59e0b'],
            borderColors: ['#ffffff', '#ffffff', '#ffffff'],
            isEmpty: false,
        };
    }

    // [PANDUAN UBAH CHART - 3] Konfigurasi visual Chart.js (warna, bentuk Garis/Batang/Pie, legenda, animasi)
    // - Pie: ubah type: 'pie' jadi 'doughnut' untuk bentuk donat; ubah legend.position ('bottom'|'right'|'top')
    // - Garis/Batang: ubah borderColor, backgroundColor, tension (kelengkungan garis), fill (arsiran), borderRadius & maxBarThickness (batang)
    function buildChartConfig(type) {
        if (type === 'pie') {
            const pie = getPieDataPayload();
            return {
                type: 'doughnut', // 'pie' untuk bentuk pie, 'doughnut' untuk bentuk donat
                data: {
                    labels: pie.labels,
                    datasets: [
                        {
                            data: pie.data,
                            backgroundColor: pie.colors,
                            borderColor: pie.borderColors,
                            borderWidth: 2,
                            hoverOffset: 8,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 400 },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 16,
                                font: { size: 11, weight: '600' },
                                color: '#475569',
                            },
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => {
                                    const currentPie = getPieDataPayload();
                                    if (currentPie.isEmpty) {
                                        return ' Perangkat Offline (0 Mbps / 0 ms)';
                                    }
                                    const val = Number(ctx.parsed || 0);
                                    const sum = ctx.dataset.data.reduce((acc, n) => acc + Number(n || 0), 0);
                                    const pct = sum > 0 ? Math.round((val / sum) * 1000) / 10 : 0;
                                    const unit = currentPie.units[ctx.dataIndex] || '';
                                    return ` ${ctx.label}: ${val} ${unit} (${pct}%)`;
                                },
                            },
                        },
                    },
                },
            };
        }

        const isBar = type === 'bar';

        return {
            type: isBar ? 'bar' : 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Latensi (ms)',
                        data: [],
                        yAxisID: 'y1',
                        borderColor: '#f59e0b',
                        backgroundColor: isBar ? 'rgba(245, 158, 11, 0.85)' : 'rgba(245, 158, 11, 0.08)',
                        borderWidth: isBar ? 0 : 2,
                        borderRadius: isBar ? 4 : 0,
                        maxBarThickness: isBar ? 20 : undefined,
                        fill: !isBar,
                        tension: isBar ? 0 : 0.35,
                        pointRadius: 0,
                        pointHoverRadius: 4,
                    },
                    {
                        label: 'Rx (Mbps)',
                        data: [],
                        yAxisID: 'y',
                        borderColor: '#2563eb',
                        backgroundColor: isBar ? 'rgba(37, 99, 235, 0.85)' : 'rgba(37, 99, 235, 0.08)',
                        borderWidth: isBar ? 0 : 2,
                        borderRadius: isBar ? 4 : 0,
                        maxBarThickness: isBar ? 20 : undefined,
                        fill: !isBar,
                        tension: isBar ? 0 : 0.35,
                        pointRadius: 0,
                        pointHoverRadius: 4,
                    },
                    {
                        label: 'Tx (Mbps)',
                        data: [],
                        yAxisID: 'y',
                        borderColor: '#10b981',
                        backgroundColor: isBar ? 'rgba(16, 185, 129, 0.85)' : 'rgba(16, 185, 129, 0.08)',
                        borderWidth: isBar ? 0 : 2,
                        borderRadius: isBar ? 4 : 0,
                        maxBarThickness: isBar ? 20 : undefined,
                        fill: !isBar,
                        tension: isBar ? 0 : 0.35,
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
                        type: 'linear',
                        position: 'left',
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        border: { display: false },
                        title: { display: true, text: 'Mbps', font: { size: 10 }, color: '#64748b' },
                        ticks: { font: { size: 10 }, color: '#64748b' },
                    },
                    y1: {
                        type: 'linear',
                        position: 'right',
                        beginAtZero: true,
                        grid: { drawOnChartArea: false },
                        border: { display: false },
                        title: { display: true, text: 'ms', font: { size: 10 }, color: '#d97706' },
                        ticks: { font: { size: 10 }, color: '#d97706' },
                    },
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                const unit = ctx.dataset.label.includes('Latensi') ? 'ms' : 'Mbps';
                                return ` ${ctx.dataset.label}: ${ctx.parsed.y} ${unit}`;
                            },
                        },
                    },
                },
            },
        };
    }

    function createChart(type) {
        if (activeChartInstance) {
            try {
                activeChartInstance.destroy();
            } catch (e) {}
            activeChartInstance = null;
        }
        const staleChart = Chart.getChart(canvas);
        if (staleChart) {
            staleChart.destroy();
        }
        activeChartInstance = new Chart(canvas, buildChartConfig(type));
    }

    // [PANDUAN UBAH CHART - 4] Update data ke grafik setiap tick / ganti rentang waktu
    // - Jika menambah dataset baru pada grafik Garis/Batang, tambahkan array di buffer & isi di datasets[...] di sini
    function renderChart() {
        if (!activeChartInstance) return;

        if (currentChartType === 'pie') {
            const pie = getPieDataPayload();
            activeChartInstance.data.labels = pie.labels;
            activeChartInstance.data.datasets[0].data = pie.data;
            activeChartInstance.data.datasets[0].backgroundColor = pie.colors;
            activeChartInstance.data.datasets[0].borderColor = pie.borderColors;
            activeChartInstance.update();
            return;
        }

        const sliceCount = currentChartType === 'bar' ? Math.min(visiblePoints, 30) : visiblePoints;
        activeChartInstance.data.labels = buffer.labels.slice(-sliceCount);
        activeChartInstance.data.datasets[0].data = buffer.ping.slice(-sliceCount);
        activeChartInstance.data.datasets[1].data = buffer.rx.slice(-sliceCount);
        activeChartInstance.data.datasets[2].data = buffer.tx.slice(-sliceCount);
        activeChartInstance.update('none');
    }

    // ---- Tombol Pengganti Tipe Grafik (Garis / Batang / Pie) ----
    const chartTypeButtons = rootEl.querySelectorAll('.chart-type-btn');
    const btnCycleChartType = document.getElementById('btnCycleChartType');
    const activeChartTypeLabel = document.getElementById('activeChartTypeLabel');
    const rangeWrapper = document.getElementById('chartRangeWrapper');
    const CHART_TYPE_LABELS = { line: 'Garis', bar: 'Batang', pie: 'Pie' };
    const CHART_BTN_ACTIVE = ['bg-blue-600', 'text-white', 'shadow-sm'];
    const CHART_BTN_IDLE = ['text-slate-600', 'hover:text-slate-900'];
    const TAB_ACTIVE = ['bg-white', 'text-slate-900', 'shadow-sm'];
    const TAB_IDLE = ['text-slate-500', 'hover:text-slate-800'];

    function syncChartTypeButtons() {
        chartTypeButtons.forEach(btn => {
            const isActive = btn.dataset.chartType === currentChartType;
            btn.classList.remove(...(isActive ? CHART_BTN_IDLE : CHART_BTN_ACTIVE));
            btn.classList.add(...(isActive ? CHART_BTN_ACTIVE : CHART_BTN_IDLE));
            btn.setAttribute('aria-pressed', String(isActive));
        });

        if (activeChartTypeLabel) {
            activeChartTypeLabel.textContent = CHART_TYPE_LABELS[currentChartType] || 'Garis';
        }

        if (rangeWrapper) {
            rangeWrapper.classList.toggle('opacity-40', currentChartType === 'pie');
            rangeWrapper.classList.toggle('pointer-events-none', currentChartType === 'pie');
        }
    }

    function switchChartType(nextType) {
        if (!nextType || !VALID_TYPES.includes(nextType) || nextType === currentChartType) return;

        currentChartType = nextType;
        try {
            sessionStorage.setItem(CHART_TYPE_KEY, currentChartType);
        } catch (e) {}

        syncChartTypeButtons();
        createChart(currentChartType);
        renderChart();
    }

    chartTypeButtons.forEach(btn => {
        btn.addEventListener('click', () => switchChartType(btn.dataset.chartType));
    });

    if (btnCycleChartType) {
        btnCycleChartType.addEventListener('click', () => {
            const currentIndex = VALID_TYPES.indexOf(currentChartType);
            const nextType = VALID_TYPES[(currentIndex + 1) % VALID_TYPES.length];
            switchChartType(nextType);
        });
    }

    // Inisialisasi grafik pertama kali
    syncChartTypeButtons();
    createChart(currentChartType);
    renderChart();

    // ---- Pilihan rentang waktu ----
    const rangeTabs = rootEl.querySelectorAll('.range-tab');

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

    // ---- Terapkan data telemetri ke DOM & Chart ----
    function applyTelemetryUpdate({ timestamp, status, cpuVal, ramVal, pingVal, rxNum, txNum, packetLoss }) {
        currentStatus = (status || currentStatus || 'online').toLowerCase();

        lastMetrics = {
            cpu_load: cpuVal,
            ram_load: ramVal,
            ping_ms: pingVal,
            rx: rxNum,
            tx: txNum,
            packet_loss: packetLoss,
        };

        setMetric('cpu', cpuVal, cpuVal);
        setMetric('ram', ramVal, ramVal);
        setMetric('ping', pingVal, Math.min(100, pingVal));

        const pingHint = document.getElementById('metric-ping-hint');
        if (pingHint) pingHint.textContent = `Packet loss ${packetLoss}%`;

        const currentPingEl = document.getElementById('currentPingValue');
        if (currentPingEl) currentPingEl.textContent = `${pingVal} ms`;

        const currentRxEl = document.getElementById('currentRxValue');
        if (currentRxEl) currentRxEl.textContent = `${rxNum} Mbps`;

        const currentTxEl = document.getElementById('currentTxValue');
        if (currentTxEl) currentTxEl.textContent = `${txNum} Mbps`;

        const lastUpdatedClock = document.getElementById('lastUpdatedClock');
        if (lastUpdatedClock) lastUpdatedClock.textContent = `${timestamp} WIB`;

        const chartLiveIndicator = document.getElementById('chartLiveIndicator');
        if (chartLiveIndicator) {
            if (currentStatus === 'offline') {
                chartLiveIndicator.className = 'inline-flex items-center gap-1 text-[11px] font-medium text-rose-600';
                chartLiveIndicator.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Offline (0 Tx/Rx)';
            } else {
                chartLiveIndicator.className = 'inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600';
                chartLiveIndicator.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Live Telemetri';
            }
        }

        buffer.labels.push(timestamp);
        buffer.ping.push(pingVal);
        buffer.rx.push(rxNum);
        buffer.tx.push(txNum);

        if (buffer.labels.length > BUFFER_LIMIT) {
            buffer.labels.shift();
            buffer.ping.shift();
            buffer.rx.shift();
            buffer.tx.shift();
        }

        persistBuffer();
        renderChart();
    }

    // ---- Polling telemetri (Backend dummy stream + fallback lokal tanpa koneksi) ----
    let isFetching = false;

    const parseRateToMbps = raw => {
        const str = String(raw ?? '0').trim();
        const match = str.match(/([\d.]+)\s*([TtGgMmKk])?bps?/i);
        if (!match) return parseFloat(str) || 0;
        const val = parseFloat(match[1]) || 0;
        const unit = (match[2] || 'M').toUpperCase();
        if (unit === 'T') return Math.round(val * 1000000 * 100) / 100;
        if (unit === 'G') return Math.round(val * 1000 * 100) / 100;
        if (unit === 'K') return Math.round((val / 1000) * 1000) / 1000;
        return val;
    };

    async function fetchTelemetry() {
        if (isFetching || document.hidden) return;
        isFetching = true;

        try {
            if (!endpoint) {
                const dummy = generateClientDummyTelemetry();
                applyTelemetryUpdate({
                    timestamp: dummy.timestamp,
                    status: dummy.status,
                    cpuVal: dummy.cpu_load,
                    ramVal: dummy.ram_load,
                    pingVal: dummy.ping_ms,
                    rxNum: dummy.rx,
                    txNum: dummy.tx,
                    packetLoss: dummy.packet_loss,
                });
                return;
            }

            activeFetchController = new AbortController();
            const res = await fetch(endpoint, {
                headers: { Accept: 'application/json' },
                signal: activeFetchController.signal,
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const json = await res.json();
            if (!json.success) throw new Error('Telemetry response unsuccessful');

            const m = json.metrics || {};
            const cpuVal = Number(m.cpu_load ?? 0);
            const ramVal = Number(m.ram_load ?? 0);
            const pingVal = m.latency_ms !== null && m.latency_ms !== undefined ? Number(m.latency_ms) : Number(m.ping_ms ?? 0);
            const rxNum = parseRateToMbps(m.rx_mbps);
            const txNum = parseRateToMbps(m.tx_mbps);
            const packetLoss = Number(m.packet_loss ?? 0);

            applyTelemetryUpdate({
                timestamp: json.timestamp || new Date().toTimeString().slice(0, 8),
                status: json.status || currentStatus,
                cpuVal,
                ramVal,
                pingVal,
                rxNum,
                txNum,
                packetLoss,
            });
        } catch (e) {
            if (e.name !== 'AbortError') {
                // Fallback otomatis ke data dummy lokal jika koneksi ke endpoint gagal
                const dummy = generateClientDummyTelemetry();
                applyTelemetryUpdate({
                    timestamp: dummy.timestamp,
                    status: dummy.status,
                    cpuVal: dummy.cpu_load,
                    ramVal: dummy.ram_load,
                    pingVal: dummy.ping_ms,
                    rxNum: dummy.rx,
                    txNum: dummy.tx,
                    packetLoss: dummy.packet_loss,
                });
            }
        } finally {
            isFetching = false;
            activeFetchController = null;
        }
    }

    // Aktifkan interval polling/simulasi dinamis reguler
    activeTelemetryInterval = setInterval(fetchTelemetry, POLL_INTERVAL);

    // ---- Tombol poll manual ----
    const btnRefresh = document.getElementById('btnRefreshLive');
    const spinner = document.getElementById('refreshSpinner');
    if (btnRefresh && spinner) {
        btnRefresh.onclick = async () => {
            spinner.classList.add('animate-spin');
            btnRefresh.disabled = true;
            await fetchTelemetry();
            setTimeout(() => {
                spinner.classList.remove('animate-spin');
                btnRefresh.disabled = false;
            }, 400);
        };
    }
}
