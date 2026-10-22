<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring & Alert — Portal Manajemen Jaringan NetFly</title>
    <link rel="icon" href="{{ asset('assets/images/netfly-logo.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    <div class="min-h-screen flex w-full">
        
        <!-- SIDEBAR (Include Partial) -->
        @include('partials.sidebar')

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            
            <!-- TOP NAVBAR / HEADER -->
            <header class="bg-white border-b border-slate-200/80 px-6 py-3.5 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger -->
                    <button id="openSidebar" class="lg:hidden p-1.5 -ml-1 text-slate-600 hover:text-slate-900 rounded-md hover:bg-slate-100 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Breadcrumb -->
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                        <span class="tracking-wide">OVERVIEW</span>
                        <span>›</span>
                        <span class="text-slate-800 font-bold">Monitoring & Alert</span>
                    </div>
                </div>

                <!-- Right Header Tools -->
                <div class="flex items-center gap-4">
                    <!-- Search Bar -->
                    <div class="relative hidden sm:block">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            placeholder="Go to..." 
                            class="pl-8 pr-12 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 w-44 lg:w-56 transition-all"
                        >
                        <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none">
                            <kbd class="px-1.5 py-0.5 text-[10px] font-mono text-slate-400 bg-white border border-slate-200 rounded shadow-2xs">⌘K</kbd>
                        </span>
                    </div>

                    <!-- Notification Bell -->
                    <button class="relative p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
                    </button>

                    <!-- Clock (Live WIB) -->
                    <div class="hidden md:flex items-center gap-1.5 text-xs font-mono text-slate-600 bg-slate-100/80 px-2.5 py-1 rounded-md border border-slate-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="liveClock">--:--:-- WIB</span>
                    </div>
                </div>
            </header>

            <!-- MAIN BODY CONTENT -->
            <main class="flex-1 p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">
                
                <!-- Page Title & Header Status -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold text-blue-600 tracking-wide uppercase mb-1">
                            NetFly NMS › Telemetri & Operasional NOC
                        </p>
                        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                            Monitoring & Live Alert
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Pemantauan telemetri real-time status perangkat, utilisasi link, dan log insiden otomatis.
                        </p>
                    </div>

                    <!-- Poller Status Badge & Trigger -->
                    <div>
                        <button 
                            id="btnPollAllNodes" 
                            type="button" 
                            title="Klik untuk sinkronisasi telemetri semua perangkat sekarang"
                            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 hover:border-emerald-300 transition-all cursor-pointer shadow-2xs active:scale-95"
                        >
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            <span id="pollerStatusText">SNMP / ICMP Poller Aktif (30s)</span>
                        </button>
                    </div>
                </div>

                <!-- 4 SUMMARY METRIC CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Card 1: Status Node Fisik -->
                    <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                STATUS NODE FISIK
                            </span>
                            <span class="text-xs font-bold text-slate-600 font-mono">
                                {{ $summary['nodes']['total'] }} Unit
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ $summary['nodes']['online'] }} Online
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/80">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                {{ $summary['nodes']['offline'] }} Padam
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500">
                            {{ $summary['nodes']['subtext'] }}
                        </p>
                    </div>

                    <!-- Card 2: Agregat Throughput -->
                    <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                AGREGAT THROUGHPUT
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $summary['throughput']['badge'] }}
                            </span>
                        </div>
                        <div class="flex items-baseline gap-6">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono">INBOUND</p>
                                <p class="text-2xl font-extrabold text-slate-900 tracking-tight font-mono">
                                    {{ $summary['throughput']['inbound'] }}
                                    <span class="text-xs font-bold text-slate-500">Mbps</span>
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono">OUTBOUND</p>
                                <p class="text-2xl font-extrabold text-slate-900 tracking-tight font-mono">
                                    {{ $summary['throughput']['outbound'] }}
                                    <span class="text-xs font-bold text-slate-500">Mbps</span>
                                </p>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-400">
                            {{ $summary['throughput']['subtext'] }}
                        </p>
                    </div>

                    <!-- Card 3: Rata-rata CPU Load -->
                    <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                RATA-RATA CPU LOAD
                            </span>
                            <span class="text-xs font-extrabold text-slate-900 font-mono">
                                {{ $summary['cpu']['average_load'] }}%
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $summary['cpu']['average_load'] }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-xs pt-0.5">
                            <span class="text-slate-500 text-[11px]">Rata-rata Packet Loss:</span>
                            <span class="font-bold text-slate-800 font-mono text-xs">
                                {{ $summary['cpu']['average_packet_loss'] }}%
                            </span>
                        </div>
                    </div>

                    <!-- Card 4: Insiden Terdeteksi -->
                    <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-rose-600">
                                INSIDEN TERDETEKSI
                            </span>
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                            </span>
                        </div>
                        <div>
                            <p class="text-2xl font-extrabold text-slate-900 tracking-tight font-mono">
                                <span id="cardIncidentCount">{{ $summary['incidents']['active_count'] }}</span> 
                                <span class="text-base font-bold text-rose-600 font-sans">Insiden Aktif</span>
                            </p>
                        </div>
                        <p class="text-[11px] text-rose-600 font-medium">
                            {{ $summary['incidents']['subtext'] }}
                        </p>
                    </div>

                </div>

                <!-- PAPAN LIVE ALERT (LOG INSIDEN OTOMATIS) -->
                <div class="rounded-xl border border-slate-200/80 bg-white overflow-hidden shadow-2xs">
                    
                    <!-- Alert Header Dark Banner -->
                    <div class="bg-slate-900 text-white px-5 py-4 flex items-center justify-between flex-wrap gap-3">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/20 shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-white">
                                    Papan Live Alert (Log Insiden Otomatis)
                                </h3>
                                <p class="text-xs text-slate-400">
                                    Deteksi insiden otomatis saat perangkat padam, CPU memuncak, atau traffic saturasi
                                </p>
                            </div>
                        </div>

                        <div>
                            <span id="unresolvedAlertBadge" class="px-3 py-1 rounded-md text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700/80">
                                <span id="unresolvedCountText">2</span> Belum Terselesaikan
                            </span>
                        </div>
                    </div>

                    <!-- Alert Cards List -->
                    <div id="alertContainer" class="divide-y divide-slate-100">
                        @foreach ($alerts as $alert)
                            <div id="alert-item-{{ $alert['id'] }}" class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors hover:bg-slate-50/50">
                                <div class="flex items-start gap-3.5">
                                    <!-- Severity Badge -->
                                    @if ($alert['severity'] === 'warning')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold tracking-wide uppercase bg-amber-500 text-white shrink-0 mt-0.5">
                                            WARNING
                                        </span>
                                    @elseif ($alert['severity'] === 'critical')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold tracking-wide uppercase bg-rose-500 text-white shrink-0 mt-0.5">
                                            CRITICAL
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold tracking-wide uppercase bg-blue-500 text-white shrink-0 mt-0.5">
                                            INFO
                                        </span>
                                    @endif

                                    <!-- Content -->
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-bold text-slate-900 text-xs">{{ $alert['host'] }}</span>
                                            <span class="text-slate-300 text-xs">•</span>
                                            <span class="text-blue-600 text-xs font-semibold">{{ $alert['customer'] }}</span>
                                        </div>
                                        <p class="text-xs text-slate-800 font-medium leading-relaxed">
                                            {{ $alert['message'] }}
                                        </p>
                                        <p class="text-[11px] text-slate-400 font-mono">
                                            Tipe: {{ $alert['type'] }} <span class="text-slate-300">•</span> {{ $alert['time_ago'] }} ({{ $alert['time_wib'] }})
                                        </p>
                                    </div>
                                </div>

                                <!-- Action Button / Status -->
                                <div id="alert-action-container-{{ $alert['id'] }}" class="shrink-0 self-end sm:self-center">
                                    @if ($alert['is_resolved'])
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-400 text-xs font-semibold cursor-default">
                                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Resolved
                                        </span>
                                    @else
                                        <button 
                                            type="button" 
                                            data-alert-id="{{ $alert['id'] }}" 
                                            data-alert-type="{{ $alert['type'] }}"
                                            class="btn-resolve-alert inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold transition cursor-pointer shadow-2xs active:scale-95"
                                        >
                                            Tandai Selesai
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- TELEMETRI STATUS PERANGKAT REAL-TIME -->
                <div class="space-y-4">
                    
                    <!-- Table Section Header & Filters -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-sm text-slate-900">
                                Telemetri Status Perangkat Real-Time
                            </h3>
                            <p class="text-xs text-slate-500">
                                Parameter status fisik, Uptime, Bandwidth In/Out, CPU %, RAM %, dan Packet Loss %
                            </p>
                        </div>

                        <!-- Dropdown Filter Controls -->
                        <div class="flex items-center gap-2.5">
                            <select 
                                id="filterStatus" 
                                class="text-xs bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-slate-700 focus:outline-none focus:border-blue-500 cursor-pointer shadow-2xs"
                            >
                                <option value="all">Semua Status</option>
                                <option value="online">Online</option>
                                <option value="warning">Warning</option>
                                <option value="offline">Offline</option>
                            </select>

                            <select 
                                id="filterUnit" 
                                class="text-xs bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-slate-700 focus:outline-none focus:border-blue-500 cursor-pointer shadow-2xs"
                            >
                                <option value="all">Semua Jenis Unit</option>
                                <option value="Access Point">Access Point</option>
                                <option value="Router">Router</option>
                                <option value="Switch">Switch</option>
                                <option value="Firewall">Firewall</option>
                            </select>
                        </div>
                    </div>

                    <!-- Telemetry Data Table -->
                    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                        <th class="py-3 px-5">PERANGKAT & IP ADDRESS</th>
                                        <th class="py-3 px-5">STATUS</th>
                                        <th class="py-3 px-5">UPTIME</th>
                                        <th class="py-3 px-5">THROUGHPUT (IN / OUT)</th>
                                        <th class="py-3 px-5">CPU LOAD</th>
                                        <th class="py-3 px-5">RAM LOAD</th>
                                        <th class="py-3 px-5">PACKET LOSS</th>
                                        <th class="py-3 px-5 text-right">POLLER</th>
                                    </tr>
                                </thead>
                                <tbody id="telemetryTableBody" class="divide-y divide-slate-100 text-xs">
                                    @foreach ($telemetryDevices as $device)
                                        <tr 
                                            class="telemetry-row hover:bg-slate-50/80 transition-colors" 
                                            id="row-device-{{ $device['id'] }}"
                                            data-id="{{ $device['id'] }}"
                                            data-status="{{ $device['status'] }}"
                                            data-unit-type="{{ $device['unit_type'] }}"
                                            data-hostname="{{ $device['hostname'] }}"
                                            data-ip="{{ $device['ip_address'] }}"
                                        >
                                            <!-- Perangkat & IP Address -->
                                            <td class="py-4 px-5 align-middle">
                                                <div class="space-y-1">
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="font-bold text-slate-900 text-xs">{{ $device['hostname'] }}</span>
                                                        <span class="text-slate-400 text-xs">({{ $device['unit_type'] }})</span>
                                                    </div>
                                                    <p class="text-[11px] text-slate-500 font-mono">
                                                        {{ $device['ip_address'] }} <span class="text-slate-300">•</span> {{ $device['customer'] }}
                                                    </p>
                                                </div>
                                            </td>

                                            <!-- Status -->
                                            <td class="py-4 px-5 align-middle whitespace-nowrap">
                                                <div id="status-badge-{{ $device['id'] }}">
                                                    @if ($device['status'] === 'warning')
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200/80 rounded-full">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                            WARNING
                                                        </span>
                                                    @elseif ($device['status'] === 'online')
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/80 rounded-full">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                            ONLINE
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-rose-700 bg-rose-50 border border-rose-200/80 rounded-full">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                            OFFLINE
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Uptime -->
                                            <td class="py-4 px-5 align-middle whitespace-nowrap font-mono text-slate-700 font-medium">
                                                {{ $device['uptime'] }}
                                            </td>

                                            <!-- Throughput (IN / OUT) -->
                                            <td class="py-4 px-5 align-middle whitespace-nowrap font-mono text-emerald-600 font-semibold">
                                                ↓ {{ $device['rx_mbps'] }} <span class="text-slate-300">/</span> ↑ {{ $device['tx_mbps'] }}
                                            </td>

                                            <!-- CPU Load -->
                                            <td class="py-4 px-5 align-middle whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-12 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                                        <div class="{{ $device['cpu_load'] > 70 ? 'bg-rose-500' : 'bg-emerald-500' }} h-1.5 rounded-full" style="width: {{ $device['cpu_load'] }}%"></div>
                                                    </div>
                                                    <span class="font-bold text-slate-800 font-mono text-xs">{{ $device['cpu_load'] }}%</span>
                                                </div>
                                            </td>

                                            <!-- RAM Load -->
                                            <td class="py-4 px-5 align-middle whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-12 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                                        <div class="{{ $device['ram_load'] > 75 ? 'bg-rose-500' : 'bg-indigo-500' }} h-1.5 rounded-full" style="width: {{ $device['ram_load'] }}%"></div>
                                                    </div>
                                                    <span class="font-bold text-slate-800 font-mono text-xs">{{ $device['ram_load'] }}%</span>
                                                </div>
                                            </td>

                                            <!-- Packet Loss -->
                                            <td class="py-4 px-5 align-middle whitespace-nowrap font-mono">
                                                @if ($device['packet_loss'] > 5.0)
                                                    <span class="font-bold text-rose-600 text-xs">
                                                        {{ $device['packet_loss'] }}% LOSS
                                                    </span>
                                                @else
                                                    <span class="font-medium text-slate-600 text-xs">
                                                        {{ $device['packet_loss'] }}%
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- Poller Action -->
                                            <td class="py-4 px-5 align-middle text-right whitespace-nowrap">
                                                <button 
                                                    type="button" 
                                                    data-device-id="{{ $device['id'] }}"
                                                    data-device-hostname="{{ $device['hostname'] }}"
                                                    data-device-ip="{{ $device['ip_address'] }}"
                                                    class="btn-polling-ping inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-blue-200 bg-blue-50/70 text-blue-700 hover:bg-blue-100 text-xs font-semibold transition cursor-pointer active:scale-95 shadow-2xs"
                                                >
                                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                    </svg>
                                                    <span>Polling Ping</span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <footer class="pt-4 pb-6 text-center text-xs text-slate-400">
                    <p>© 2026 PT Netmedia Fiberoptik Lancarmulya • Network Operation Center</p>
                </footer>

            </main>

        </div>
    </div>

    <!-- MODAL: LIVE ICMP PING & DIAGNOSTIC RESULT -->
    <div id="pingDiagnosticModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200/80 w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-900 text-white">
                <div class="flex items-center gap-2.5">
                    <div class="p-1.5 bg-blue-500/20 text-blue-400 border border-blue-500/30 rounded-lg">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-white">Diagnostik Poller Real-Time</h4>
                        <p class="text-[11px] text-slate-400 font-mono">ICMP Echo & SNMPv2 Polling</p>
                    </div>
                </div>
                <button type="button" class="close-ping-modal text-slate-400 hover:text-white p-1 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6 space-y-4">
                
                <!-- Target Device Info Banner -->
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Perangkat Target</p>
                        <h5 id="modalPingHostname" class="font-extrabold text-slate-900 text-sm">--</h5>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">IP Address</p>
                        <p id="modalPingIp" class="font-mono text-xs font-bold text-blue-600">--</p>
                    </div>
                </div>

                <!-- Ping Stats Grid -->
                <div class="grid grid-cols-3 gap-2.5">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-center">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Avg Latency</p>
                        <p id="modalPingRttAvg" class="text-base font-extrabold text-slate-900 font-mono mt-0.5">-- ms</p>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-center">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Packet Loss</p>
                        <p id="modalPingLoss" class="text-base font-extrabold text-emerald-600 font-mono mt-0.5">0.0%</p>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-center">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Status Link</p>
                        <p id="modalPingQuality" class="text-xs font-bold text-slate-800 mt-1">Normal</p>
                    </div>
                </div>

                <!-- Raw Shell Simulation Box -->
                <div class="rounded-xl bg-slate-950 p-3.5 border border-slate-800 text-slate-300 font-mono text-[11px] space-y-1.5 overflow-x-auto">
                    <p class="text-slate-500"># icmp_ping_diagnostic --count=4</p>
                    <p id="modalPingRaw1">64 bytes from target: icmp_seq=1 ttl=64 time=-- ms</p>
                    <p id="modalPingRaw2">64 bytes from target: icmp_seq=2 ttl=64 time=-- ms</p>
                    <p id="modalPingRaw3">64 bytes from target: icmp_seq=3 ttl=64 time=-- ms</p>
                    <p id="modalPingRaw4">64 bytes from target: icmp_seq=4 ttl=64 time=-- ms</p>
                    <p id="modalPingRawSnmp" class="text-amber-400/90 pt-1 border-t border-slate-800/80">SNMPv2-MIB::sysUpTime: OK</p>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                <button 
                    type="button" 
                    id="btnCopyPingLog"
                    class="text-xs font-semibold text-slate-600 hover:text-slate-900 cursor-pointer inline-flex items-center gap-1.5"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span>Salin Hasil Diagnostik</span>
                </button>

                <div class="flex items-center gap-2">
                    <button 
                        type="button" 
                        class="close-ping-modal px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition cursor-pointer"
                    >
                        Tutup
                    </button>
                    <button 
                        type="button" 
                        id="btnRePing"
                        class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition cursor-pointer shadow-xs"
                    >
                        Re-Ping
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

</body>
</html>
