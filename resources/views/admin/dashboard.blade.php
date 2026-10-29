<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Portal Manajemen Jaringan NetFly</title>
    <link rel="icon" href="{{ asset('assets/images/netfly-logo.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 font-sans text-slate-800 antialiased">

    <div class="h-screen flex w-full overflow-hidden">
        
        <!-- SIDEBAR (Include Partial) -->
        @include('partials.sidebar')

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0 h-full overflow-y-auto">
            
            <!-- TOP NAVBAR / HEADER -->
            <header class="bg-white border-b border-slate-200/80 px-6 py-3.5 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger -->
                    <button id="openSidebar" class="lg:hidden p-1.5 -ml-1 text-slate-600 hover:text-slate-900 rounded-md hover:bg-slate-100">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Breadcrumb -->
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                        <span class="tracking-wide uppercase">MONITORING & KENDALI</span>
                        <span>›</span>
                        <span class="text-slate-800 font-bold">Dashboard</span>
                    </div>
                </div>

                <!-- Right Header Tools -->
                <div class="flex items-center gap-4">
                    <!-- Notification Bell -->
                    <button class="relative p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer" aria-label="Notifikasi">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1 right-1 w-3.5 h-3.5 bg-rose-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center border-2 border-white">
                            3
                        </span>
                    </button>

                    <!-- Realtime Digital Clock -->
                    <div class="hidden sm:flex items-center gap-1.5 text-xs font-mono text-slate-600 bg-slate-100/80 px-2.5 py-1 rounded-md border border-slate-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="liveClock">--:--:-- WIB</span>
                    </div>
                </div>
            </header>

            <!-- MAIN BODY PADDING -->
            <main class="p-6 lg:p-8 space-y-6">
                
                <!-- WELCOME BANNER & ACTION BUTTON -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-1.5">
                            <span>OPERASIONAL NOC</span>
                            <span>•</span>
                            <span class="text-emerald-600 font-semibold">SHIFT AKTIF</span>
                        </div>
                        <h1 class="text-xl lg:text-2xl font-extrabold text-slate-900 tracking-tight">
                            Halo Operator, ini pusat kendali jaringan
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Ringkasan ketersediaan transmisi, status gateway, dan performa 30 hari terakhir.
                        </p>
                    </div>

                    <div>
                        <button class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Buat Tiket</span>
                        </button>
                    </div>
                </div>

                <!-- 4 STATS CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    
                    <!-- Card 1: Customer Aktif -->
                    <div class="bg-white p-4.5 rounded-xl border border-slate-200/80 shadow-2xs flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>{{ $metrics['customer_active']['title'] }}</span>
                            </div>
                            <span class="px-2 py-0.5 text-[10px] font-medium bg-slate-100 text-slate-600 rounded-full border border-slate-200/60">
                                {{ $metrics['customer_active']['badge'] }}
                            </span>
                        </div>
                        <div class="flex items-baseline gap-2 mb-4">
                            <span class="text-2xl font-black text-slate-900 tracking-tight">{{ $metrics['customer_active']['value'] }}</span>
                            <span class="text-xs font-medium text-emerald-600">{{ $metrics['customer_active']['subvalue'] }}</span>
                        </div>
                        <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                            <span>{{ $metrics['customer_active']['footer_left'] }}</span>
                            <span class="text-slate-600 font-semibold">{{ $metrics['customer_active']['footer_right'] }}</span>
                        </div>
                    </div>

                    <!-- Card 2: Project Aktif -->
                    <div class="bg-white p-4.5 rounded-xl border border-slate-200/80 shadow-2xs flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                                <span>{{ $metrics['project_active']['title'] }}</span>
                            </div>
                            <span class="px-2 py-0.5 text-[10px] font-medium bg-slate-100 text-slate-600 rounded-full border border-slate-200/60">
                                {{ $metrics['project_active']['badge'] }}
                            </span>
                        </div>
                        <div class="flex items-baseline gap-2 mb-4">
                            <span class="text-2xl font-black text-slate-900 tracking-tight">{{ $metrics['project_active']['value'] }}</span>
                            <span class="text-xs font-medium text-slate-500">{{ $metrics['project_active']['subvalue'] }}</span>
                        </div>
                        <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                            <span>{{ $metrics['project_active']['footer_left'] }}</span>
                            <span class="text-slate-600 font-semibold">{{ $metrics['project_active']['footer_right'] }}</span>
                        </div>
                    </div>

                    <!-- Card 3: Perangkat Online -->
                    <div class="bg-white p-4.5 rounded-xl border border-slate-200/80 shadow-2xs flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                </svg>
                                <span>{{ $metrics['device_online']['title'] }}</span>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-medium bg-emerald-50 text-emerald-600 rounded-full border border-emerald-200/60">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ $metrics['device_online']['badge'] }}
                            </span>
                        </div>
                        <div class="flex items-baseline gap-2 mb-4">
                            <span class="text-2xl font-black text-slate-900 tracking-tight">{{ $metrics['device_online']['value'] }}</span>
                            <span class="text-xs font-medium text-slate-400">{{ $metrics['device_online']['subvalue'] }}</span>
                        </div>
                        <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                            <span>{{ $metrics['device_online']['footer_left'] }}</span>
                            <span class="text-emerald-600 font-semibold">{{ $metrics['device_online']['footer_right'] }}</span>
                        </div>
                    </div>

                    <!-- Card 4: Perangkat Offline -->
                    <div class="bg-white p-4.5 rounded-xl border border-slate-200/80 shadow-2xs flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>{{ $metrics['device_offline']['title'] }}</span>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-medium bg-rose-50 text-rose-600 rounded-full border border-rose-200/60">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                {{ $metrics['device_offline']['badge'] }}
                            </span>
                        </div>
                        <div class="flex items-baseline gap-2 mb-4">
                            <span class="text-2xl font-black text-rose-600 tracking-tight">{{ $metrics['device_offline']['value'] }}</span>
                            <span class="text-xs font-medium text-slate-400">{{ $metrics['device_offline']['subvalue'] }}</span>
                        </div>
                        <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                            <span>{{ $metrics['device_offline']['footer_left'] }}</span>
                            <span class="text-rose-600 font-semibold">{{ $metrics['device_offline']['footer_right'] }}</span>
                        </div>
                    </div>

                </div>

                <!-- MIDDLE SECTION: CHART & LIVE ALERTS -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Left: Uptime SLA Chart (2 Cols) -->
                    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200/80 p-5 shadow-2xs flex flex-col justify-between">
                        <div>
                            <!-- Chart Header -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                                <div>
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                                        LAST 30 DAYS • KETERSEDIAAN JARINGAN & ALERT
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-900">
                                        Grafik Ketersediaan Jaringan (Uptime SLA)
                                    </h3>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        Statistik ketersediaan transmisi backbone dan link pelanggan ({{ $uptimeStats['date_start'] }} - {{ $uptimeStats['date_end'] }})
                                    </p>
                                </div>
                                <div>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold {{ $uptimeStats['is_sla_met'] ? 'text-blue-700 bg-blue-50 border-blue-200/60' : 'text-amber-700 bg-amber-50 border-amber-200/60' }} border rounded-md">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $uptimeStats['is_sla_met'] ? 'bg-blue-600' : 'bg-amber-600' }}"></span>
                                        Rata-rata: {{ $uptimeStats['average'] }}%
                                    </span>
                                </div>
                            </div>

                            <!-- Metrics Summary Subrow -->
                            <div class="grid grid-cols-3 gap-3 p-3 bg-slate-50/80 rounded-lg border border-slate-100 text-center mb-6">
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-tight">UPTIME TERTINGGI</div>
                                    <div class="text-sm font-bold text-slate-800 mt-0.5">{{ $uptimeStats['highest'] }}</div>
                                </div>
                                <div class="border-x border-slate-200/60">
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-tight">TITIK TERENDAH</div>
                                    <div class="text-sm font-bold text-slate-800 mt-0.5">{{ $uptimeStats['lowest'] }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-tight">STATUS SLA BULANAN</div>
                                    <div class="text-sm font-bold {{ $uptimeStats['is_sla_met'] ? 'text-emerald-600' : 'text-rose-600' }} mt-0.5">
                                        {{ $uptimeStats['is_sla_met'] ? 'Tercapai (' . $uptimeStats['sla_target'] . ')' : 'Deviasi (< ' . $uptimeStats['sla_target'] . ')' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SVG Line Chart (Dinamis dari Database, Proporsional & Anti-Gepeng) -->
                        <div class="w-full relative mt-2 pt-2">
                            <!-- Chart Canvas Container -->
                            <div class="w-full h-64 sm:h-72 lg:h-80 relative">
                                <svg class="w-full h-full overflow-visible" viewBox="0 0 760 240" preserveAspectRatio="none">
                                    <defs>
                                        <!-- Gradient Fill for Area -->
                                        <linearGradient id="slaGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                            <stop offset="0%" stop-color="#2563eb" stop-opacity="0.30" />
                                            <stop offset="60%" stop-color="#3b82f6" stop-opacity="0.08" />
                                            <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.0" />
                                        </linearGradient>

                                        <!-- Shadow for Main Spline -->
                                        <filter id="lineGlow" x="-20%" y="-20%" width="140%" height="140%">
                                            <feDropShadow dx="0" dy="3" stdDeviation="3" flood-color="#2563eb" flood-opacity="0.25" />
                                        </filter>
                                    </defs>

                                    <!-- Grid Lines & Y-Axis Labels -->
                                    @foreach ($uptimeStats['y_ticks'] as $tick)
                                        <line 
                                            x1="{{ $uptimeStats['plot_left'] }}" 
                                            y1="{{ $tick['y'] }}" 
                                            x2="{{ $uptimeStats['plot_right'] }}" 
                                            y2="{{ $tick['y'] }}" 
                                            stroke="#e2e8f0" 
                                            stroke-width="1" 
                                            stroke-dasharray="{{ $loop->last ? 'none' : '4,4' }}" 
                                        />
                                        <text 
                                            x="{{ $uptimeStats['plot_left'] - 8 }}" 
                                            y="{{ $tick['y'] + 3.5 }}" 
                                            text-anchor="end" 
                                            fill="#94a3b8" 
                                            font-size="10" 
                                            font-weight="500" 
                                            font-family="ui-monospace, SFMono-Regular, monospace"
                                        >{{ $tick['label'] }}</text>
                                    @endforeach

                                    <!-- SLA Target Threshold Line (99.50%) -->
                                    <line 
                                        x1="{{ $uptimeStats['plot_left'] }}" 
                                        y1="{{ $uptimeStats['sla_target_y'] }}" 
                                        x2="{{ $uptimeStats['plot_right'] }}" 
                                        y2="{{ $uptimeStats['sla_target_y'] }}" 
                                        stroke="#f59e0b" 
                                        stroke-width="1.5" 
                                        stroke-dasharray="4,4" 
                                        opacity="0.85" 
                                    />
                                    <text 
                                        x="{{ $uptimeStats['plot_right'] }}" 
                                        y="{{ max(14, $uptimeStats['sla_target_y'] - 6) }}" 
                                        text-anchor="end" 
                                        fill="#d97706" 
                                        font-size="9" 
                                        font-weight="700" 
                                        font-family="ui-monospace, SFMono-Regular, monospace"
                                    >Batas SLA Target: {{ $uptimeStats['sla_target'] }}</text>

                                    <!-- Area Gradient Fill -->
                                    <path 
                                        d="{{ $uptimeStats['area_path'] }}" 
                                        fill="url(#slaGradient)" 
                                    />

                                    <!-- Spline Curve Line -->
                                    <path 
                                        d="{{ $uptimeStats['stroke_path'] }}" 
                                        fill="none" 
                                        stroke="#2563eb" 
                                        stroke-width="2.5" 
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        filter="url(#lineGlow)"
                                    />

                                    <!-- Interactive Points with Smooth Hover Tooltips (Stably Locked to Curve) -->
                                    @foreach ($uptimeStats['all_points'] as $pt)
                                        <g class="cursor-pointer group">
                                            <!-- Invisible Wide Hit Area for Smooth Interaction -->
                                            <circle 
                                                cx="{{ $pt['x'] }}" 
                                                cy="{{ $pt['y'] }}" 
                                                r="12" 
                                                fill="transparent" 
                                            />

                                            <!-- Outer Halo on Hover (Never Detaches) -->
                                            <circle 
                                                cx="{{ $pt['x'] }}" 
                                                cy="{{ $pt['y'] }}" 
                                                r="8" 
                                                fill="#3b82f6" 
                                                fill-opacity="0.22" 
                                                class="opacity-0 group-hover:opacity-100 transition-opacity duration-150" 
                                            />

                                            <!-- Dot (Milestone or Incident Point) -->
                                            @if ($pt['is_milestone'] || $pt['uptime'] < 99.7)
                                                <circle 
                                                    cx="{{ $pt['x'] }}" 
                                                    cy="{{ $pt['y'] }}" 
                                                    r="4" 
                                                    fill="#ffffff" 
                                                    stroke="{{ $pt['uptime'] < 99.7 ? '#ef4444' : '#2563eb' }}" 
                                                    stroke-width="2.5" 
                                                    class="transition-colors group-hover:fill-blue-100" 
                                                />
                                            @else
                                                <circle 
                                                    cx="{{ $pt['x'] }}" 
                                                    cy="{{ $pt['y'] }}" 
                                                    r="2.5" 
                                                    fill="#2563eb" 
                                                    class="opacity-0 group-hover:opacity-100 transition-opacity duration-150" 
                                                />
                                            @endif

                                            <!-- Tooltip Popover (Floating cleanly ABOVE the peak) -->
                                            <g class="opacity-0 group-hover:opacity-100 transition-opacity duration-150 pointer-events-none">
                                                @php
                                                    $tooltipX = max(68, min(692, $pt['x']));
                                                    $tooltipY = max(6, $pt['y'] - 38);
                                                @endphp
                                                <rect 
                                                    x="{{ $tooltipX - 58 }}" 
                                                    y="{{ $tooltipY }}" 
                                                    width="116" 
                                                    height="32" 
                                                    rx="6" 
                                                    fill="#0f172a" 
                                                    fill-opacity="0.95" 
                                                />
                                                <text 
                                                    x="{{ $tooltipX }}" 
                                                    y="{{ $tooltipY + 13 }}" 
                                                    text-anchor="middle" 
                                                    fill="#ffffff" 
                                                    font-size="9.5" 
                                                    font-weight="700" 
                                                    font-family="system-ui, -apple-system, sans-serif"
                                                >{{ $pt['uptime'] }}% • {{ $pt['date'] }}</text>
                                                <text 
                                                    x="{{ $tooltipX }}" 
                                                    y="{{ $tooltipY + 24 }}" 
                                                    text-anchor="middle" 
                                                    fill="{{ $pt['downtime'] > 0 ? '#fca5a5' : '#86efac' }}" 
                                                    font-size="8.5" 
                                                    font-weight="600" 
                                                    font-family="system-ui, -apple-system, sans-serif"
                                                >{{ $pt['downtime'] > 0 ? $pt['downtime'] . 'm Padam (' . $pt['status'] . ')' : '100% Normal Operasional' }}</text>
                                            </g>

                                            <!-- Native Browser Title Accessibility -->
                                            <title>{{ $pt['full_date'] }}&#10;Uptime: {{ $pt['uptime'] }}%&#10;Downtime: {{ $pt['downtime'] }} Menit&#10;Status: {{ $pt['status'] }}</title>
                                        </g>
                                    @endforeach

                                    <!-- X-Axis Milestone Date Labels -->
                                    @foreach ($uptimeStats['sample_points'] as $pt)
                                        <text 
                                            x="{{ $pt['x'] }}" 
                                            y="228" 
                                            text-anchor="middle" 
                                            fill="#94a3b8" 
                                            font-size="10" 
                                            font-weight="500" 
                                            font-family="ui-monospace, SFMono-Regular, monospace"
                                        >{{ $pt['date'] }}</text>
                                    @endforeach
                                </svg>
                            </div>

                            <!-- Legend Footer Subrow -->
                            <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-[11px] text-slate-500">
                                <div class="flex items-center gap-4">
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="w-3.5 h-1 bg-blue-600 rounded-full inline-block"></span>
                                        <span>Realisasi Uptime Harian</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="w-3.5 h-0.5 border-t-2 border-dashed border-amber-500 inline-block"></span>
                                        <span>Target Kontrak ({{ $uptimeStats['sla_target'] }})</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full border-2 border-blue-600 bg-white inline-block"></span>
                                        <span>Milestone Telemetri</span>
                                    </span>
                                </div>
                                <div class="text-[10px] font-mono text-slate-400">
                                    Sampel 30 Hari Aktif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Live Alert Mini-Board -->
                    <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-2xs flex flex-col justify-between">
                        <div>
                            <!-- Header -->
                            <div class="flex items-center justify-between mb-1">
                                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                                    <span class="w-2 h-2 rounded-full bg-rose-500 -ml-3.5"></span>
                                    <span>Live Alert Mini-Board</span>
                                </h3>
                                <span class="px-2 py-0.5 text-[10px] font-semibold text-slate-500 bg-slate-100 rounded-full border border-slate-200/60">
                                    Realtime
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mb-4">
                                Notifikasi insiden perangkat dan anomali jaringan saat ini:
                            </p>

                            <!-- Alerts List (Dinamis) -->
                            <div class="space-y-3">
                                @forelse ($alerts as $alert)
                                    @php
                                        $bgClass = match ($alert['badge_type']) {
                                            'danger' => 'border-rose-100 bg-rose-50/40 hover:bg-rose-50/70',
                                            'warning' => 'border-amber-100 bg-amber-50/40 hover:bg-amber-50/70',
                                            default => 'border-indigo-100 bg-indigo-50/40 hover:bg-indigo-50/70',
                                        };
                                        $badgeClass = match ($alert['badge_type']) {
                                            'danger' => 'bg-rose-100 text-rose-700',
                                            'warning' => 'bg-amber-100 text-amber-800',
                                            default => 'bg-indigo-100 text-indigo-700',
                                        };
                                    @endphp
                                    <div class="p-3 rounded-lg border {{ $bgClass }} transition-colors">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase rounded {{ $badgeClass }} tracking-wide">
                                                {{ $alert['badge'] }}
                                            </span>
                                            <span class="text-[10px] font-mono text-slate-400">{{ $alert['time'] }}</span>
                                        </div>
                                        <div class="text-xs font-bold text-slate-900 mt-1">
                                            {{ $alert['title'] }}
                                        </div>
                                        <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">
                                            {{ $alert['desc'] }}
                                        </p>
                                    </div>
                                @empty
                                    <div class="p-4 text-center text-xs text-slate-400 bg-slate-50 rounded-lg">
                                        Tidak ada anomali atau insiden aktif saat ini.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 mt-4">
                            <a href="{{ route('monitoring.index') }}" class="w-full block text-center py-2 text-xs font-semibold text-slate-700 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors">
                                Buka NOC Monitoring Center &rarr;
                            </a>
                        </div>
                    </div>

                </div>

                <!-- BOTTOM TABLE: WIDGET ANTREAN TIKET -->
                <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <!-- Table Header -->
                    <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-slate-900">
                                    Widget Antrean Tiket
                                </h3>
                                <span class="px-2 py-0.5 text-[10px] font-semibold text-slate-600 bg-slate-100 rounded-full border border-slate-200">
                                    {{ count($tickets) }} Membutuhkan Penanganan
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Daftar insiden gangguan jaringan aktif yang membutuhkan penanganan segera berdasarkan SLA.
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('tickets.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition-colors">
                                <span>Lihat Semua Tiket</span>
                                <span>&rsaquo;</span>
                            </a>
                        </div>
                    </div>

                    <!-- Table Data (Dinamis) -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/60 text-[10px] font-bold uppercase text-slate-400 tracking-wider">
                                    <th class="py-3 px-5">ID TIKET</th>
                                    <th class="py-3 px-5">CUSTOMER / PROYEK</th>
                                    <th class="py-3 px-5">DESKRIPSI GANGGUAN</th>
                                    <th class="py-3 px-5">PRIORITAS</th>
                                    <th class="py-3 px-5">STATUS PENANGANAN</th>
                                    <th class="py-3 px-5">DURASI PADAM</th>
                                    <th class="py-3 px-5 text-right">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                                @forelse ($tickets as $ticket)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="py-3.5 px-5 font-mono text-[11px] font-semibold text-slate-800">
                                            <span class="px-2 py-0.5 bg-slate-100 border border-slate-200/70 rounded">
                                                {{ $ticket['id'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-5">
                                            <div class="font-bold text-slate-900">{{ $ticket['customer'] }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $ticket['package'] }}</div>
                                        </td>
                                        <td class="py-3.5 px-5 text-slate-600 font-medium">
                                            {{ $ticket['issue'] }}
                                        </td>
                                        <td class="py-3.5 px-5">
                                            <span class="inline-block px-2 py-0.5 text-[10px] font-bold {{ $ticket['priority_type'] === 'critical' ? 'text-rose-700 bg-rose-50 border border-rose-200' : ($ticket['priority_type'] === 'high' ? 'text-amber-800 bg-amber-50 border border-amber-200' : 'text-slate-700 bg-slate-100 border border-slate-200') }} rounded">
                                                {{ $ticket['priority'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-5">
                                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $ticket['status_color'] === 'emerald' ? 'bg-emerald-500' : ($ticket['status_color'] === 'amber' ? 'bg-amber-500' : ($ticket['status_color'] === 'blue' ? 'bg-blue-500' : 'bg-slate-400')) }}"></span>
                                                {{ $ticket['status'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-5 font-mono text-slate-600">
                                            {{ $ticket['duration'] }}
                                        </td>
                                        <td class="py-3.5 px-5 text-right">
                                            <a href="{{ route('tickets.index') }}" class="inline-block px-3 py-1 {{ $ticket['action_type'] === 'primary' ? 'bg-slate-900 hover:bg-slate-800 text-white' : 'border border-slate-300 hover:bg-slate-50 text-slate-700' }} text-xs font-semibold rounded-md shadow-2xs transition-colors cursor-pointer">
                                                {{ $ticket['action_label'] }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-6 text-center text-xs text-slate-400">
                                            Tidak ada antrean tiket aktif saat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- FOOTER -->
                <footer class="pt-4 pb-2 text-center text-xs text-slate-400">
                    &copy; {{ date('Y') }} PT Netmedia Fiberoptik Lancarmulya • Network Operation Center
                </footer>

            </main>
        </div>

    </div>

</body>
</html>
