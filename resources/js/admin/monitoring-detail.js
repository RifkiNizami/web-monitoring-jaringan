/**
 * NetFly - Admin Device Monitoring Detail Module
 * (Chart.js real-time rendering, telemetry polling, data persistence across Turbo navigation)
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
    const deviceStatus = (rootEl.dataset.status || 'offline').toLowerCase();
    const endpoint = rootEl.dataset.endpoint;
    let thresholds = {};
    try {
        thresholds = JSON.parse(rootEl.dataset.thresholds || '{}');
    } catch (e) {
        thresholds = {};
    }

    const basePing = Number(rootEl.dataset.basePing || 0);
    const baseRx = Number(rootEl.dataset.baseRx || 0);
    const baseTx = Number(rootEl.dataset.baseTx || 0);

    const POLL_INTERVAL = 3000;
    const BUFFER_LIMIT = 300; // 15 menit @ 3 detik
    let visiblePoints = 20;   // default 1 menit

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
    // Session Storage Telemetry Buffer (Persists across navbar menu clicks)
    // -------------------------------------------------------------
    const STORAGE_KEY = `netfly_telemetry_buf_${deviceId}`;

    function loadBuffer() {
        const isOffline = (deviceStatus === 'offline');
        try {
            const raw = sessionStorage.getItem(STORAGE_KEY);
            if (raw) {
                const parsed = JSON.parse(raw);
                if (parsed && Array.isArray(parsed.labels) && parsed.labels.length > 0) {
                    // Jika device offline, bersihkan cache buffer lama yang pernah menyimpan angka dummy/acak
                    if (isOffline && (parsed.ping.some(p => p > 0) || parsed.rx.some(r => r > 0))) {
                        sessionStorage.removeItem(STORAGE_KEY);
                    } else {
                        return parsed;
                    }
                }
            }
        } catch (e) {}

        // Inisialisasi data awal murni dari database (tanpa Math.random dummy)
        const buf = { labels: [], ping: [], rx: [], tx: [] };
        const now = Date.now();
        const initialPing = isOffline ? 0 : Math.max(0, basePing);
        const initialRx = isOffline ? 0 : Math.max(0, baseRx);
        const initialTx = isOffline ? 0 : Math.max(0, baseTx);

        for (let i = visiblePoints - 1; i >= 0; i--) {
            buf.labels.push(new Date(now - i * POLL_INTERVAL).toTimeString().slice(0, 8));
            buf.ping.push(initialPing);
            buf.rx.push(initialRx);
            buf.tx.push(initialTx);
        }
        return buf;
    }

    const buffer = loadBuffer();

    function persistBuffer() {
        try {
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(buffer));
        } catch (e) {}
    }

    // Buat Chart.js instance baru (Dual Y-Axis: kiri untuk Throughput Mbps, kanan untuk Latensi ms)
    activeChartInstance = new Chart(canvas, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Latensi (ms)',
                    data: [],
                    yAxisID: 'y1',
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.08)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                },
                {
                    label: 'Rx (Mbps)',
                    data: [],
                    yAxisID: 'y',
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
                    yAxisID: 'y',
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
    });

    function renderChart() {
        if (!activeChartInstance) return;
        activeChartInstance.data.labels = buffer.labels.slice(-visiblePoints);
        activeChartInstance.data.datasets[0].data = buffer.ping.slice(-visiblePoints);
        activeChartInstance.data.datasets[1].data = buffer.rx.slice(-visiblePoints);
        activeChartInstance.data.datasets[2].data = buffer.tx.slice(-visiblePoints);
        activeChartInstance.update('none');
    }

    // Render data pertama kali langsung
    renderChart();

    // ---- Pilihan rentang waktu ----
    const rangeTabs = rootEl.querySelectorAll('.range-tab');
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
        if (isFetching || document.hidden || !endpoint) return;
        isFetching = true;

        try {
            activeFetchController = new AbortController();
            const res = await fetch(endpoint, {
                headers: { Accept: 'application/json' },
                signal: activeFetchController.signal,
            });
            if (!res.ok) return;
            const json = await res.json();
            if (!json.success) return;

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

            const m = json.metrics || {};
            const cpuVal = Number(m.cpu_load ?? 0);
            const ramVal = Number(m.ram_load ?? 0);
            const pingVal = m.latency_ms !== null && m.latency_ms !== undefined ? Number(m.latency_ms) : Number(m.ping_ms ?? 0);
            const rxNum = parseRateToMbps(m.rx_mbps);
            const txNum = parseRateToMbps(m.tx_mbps);

            setMetric('cpu', cpuVal, cpuVal);
            setMetric('ram', ramVal, ramVal);
            setMetric('ping', pingVal, pingVal);

            const pingHint = document.getElementById('metric-ping-hint');
            if (pingHint) pingHint.textContent = `Packet loss ${m.packet_loss ?? 0}%`;

            const currentPingEl = document.getElementById('currentPingValue');
            if (currentPingEl) currentPingEl.textContent = `${pingVal} ms`;

            const currentRxEl = document.getElementById('currentRxValue');
            if (currentRxEl) currentRxEl.textContent = `${rxNum} Mbps`;

            const currentTxEl = document.getElementById('currentTxValue');
            if (currentTxEl) currentTxEl.textContent = `${txNum} Mbps`;

            const lastUpdatedClock = document.getElementById('lastUpdatedClock');
            if (lastUpdatedClock) lastUpdatedClock.textContent = `${json.timestamp} WIB`;

            const chartLiveIndicator = document.getElementById('chartLiveIndicator');
            if (chartLiveIndicator) {
                if (json.status === 'offline') {
                    chartLiveIndicator.className = 'inline-flex items-center gap-1 text-[11px] font-medium text-rose-600';
                    chartLiveIndicator.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Offline (0 Tx/Rx)';
                } else {
                    chartLiveIndicator.className = 'inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600';
                    chartLiveIndicator.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Live Telemetri';
                }
            }

            buffer.labels.push(json.timestamp);
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
        } catch (e) {
            if (e.name !== 'AbortError') {
                console.error('Poller error:', e);
            }
        } finally {
            isFetching = false;
            activeFetchController = null;
        }
    }

    // Aktifkan interval polling reguler
    if (endpoint) {
        activeTelemetryInterval = setInterval(fetchTelemetry, POLL_INTERVAL);
    }

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
