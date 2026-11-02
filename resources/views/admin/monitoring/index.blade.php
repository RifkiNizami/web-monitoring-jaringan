<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Perangkat — Portal Manajemen Jaringan NetFly</title>
    <link rel="icon" href="{{ asset('assets/images/netfly-logo.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    <div class="h-screen flex w-full overflow-hidden">
        
        <!-- SIDEBAR (Include Partial) -->
        @include('partials.sidebar')

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0 h-full overflow-y-auto">
            
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
                        <span class="tracking-wide uppercase">MONITORING & KENDALI</span>
                        <span>›</span>
                        <span class="text-slate-800 font-bold">Monitoring</span>
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

                    <!-- Clock (Live WIB) -->
                    <div class="hidden sm:flex items-center gap-1.5 text-xs font-mono text-slate-600 bg-slate-100/80 px-2.5 py-1 rounded-md border border-slate-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="liveClock">--:--:-- WIB</span>
                    </div>
                </div>
            </header>

            <!-- MAIN BODY CONTENT -->
            <main class="flex-1 p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">
                @php
                    $deviceCollection = collect($telemetryDevices);
                    $totalDevices = $deviceCollection->count();
                    $onlineCount = $deviceCollection->where('status', 'online')->count();
                    $warningCount = $deviceCollection->where('status', 'warning')->count();
                    $offlineCount = $deviceCollection->where('status', 'offline')->count();
                    $attentionCount = $warningCount + $offlineCount;
                    $unitTypes = $deviceCollection->pluck('unit_type')->unique()->values();
                    $unresolvedAlerts = collect($alerts)->where('is_resolved', false)->count();

                    $statusStyles = [
                        'online' => ['label' => 'Online', 'dot' => 'bg-emerald-500', 'pill' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'],
                        'warning' => ['label' => 'Warning', 'dot' => 'bg-amber-500', 'pill' => 'bg-amber-50 text-amber-700 ring-amber-600/20'],
                        'offline' => ['label' => 'Offline', 'dot' => 'bg-rose-500', 'pill' => 'bg-rose-50 text-rose-700 ring-rose-600/20'],
                    ];

                    $severityStyles = [
                        'critical' => ['border' => 'border-rose-500', 'text' => 'text-rose-600'],
                        'warning' => ['border' => 'border-amber-500', 'text' => 'text-amber-600'],
                        'info' => ['border' => 'border-blue-500', 'text' => 'text-blue-600'],
                    ];

                    $filterTabs = [
                        ['key' => 'all', 'label' => 'Semua', 'count' => $totalDevices],
                        ['key' => 'online', 'label' => 'Online', 'count' => $onlineCount],
                        ['key' => 'warning', 'label' => 'Warning', 'count' => $warningCount],
                        ['key' => 'offline', 'label' => 'Offline', 'count' => $offlineCount],
                    ];
                @endphp

                @if(session('success'))
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-emerald-800 text-xs font-semibold">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-between text-rose-800 text-xs font-semibold">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <!-- PAGE HEADER -->
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div>
                        <div class="text-[11px] font-semibold text-slate-400 mb-1 flex items-center gap-1.5">
                            <span class="text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded border border-blue-200">NOC MONITORING</span>
                            <span>›</span>
                            <span>Live Telemetri & Target Node</span>
                        </div>
                        <h1 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight">Monitoring Perangkat</h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Kelola dan pantau telemetri CPU, RAM, bandwidth, dan latensi node secara mandiri tanpa bergantung ke inventaris.
                        </p>
                    </div>
                    <div class="flex items-center gap-2.5 shrink-0">
                        <button
                            id="btnPollAllNodes"
                            type="button"
                            title="Sinkronisasi telemetri semua perangkat sekarang"
                            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 transition cursor-pointer shadow-2xs active:scale-95"
                        >
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span id="pollerStatusText">Sinkronisasi Semua</span>
                        </button>

                        <a
                            href="{{ route('monitoring.devices.create') }}"
                            id="btnOpenCreateMonitoringNode"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer"
                        >
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Tambah Perangkat Monitoring</span>
                        </a>
                    </div>
                </div>

                <!-- FLASH ALERTS -->
                @if (session('success'))
                    <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif
                @if (session('error'))
                    <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-xs flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <!-- SUMMARY STRIP -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl border border-slate-200 p-4">
                        <p class="text-xs font-medium text-slate-500">Perangkat online</p>
                        <p class="mt-1 text-2xl font-semibold text-slate-900 tabular-nums">
                            {{ $onlineCount }}<span class="text-base font-medium text-slate-400">/{{ $totalDevices }}</span>
                        </p>
                        <p class="mt-1 text-xs text-slate-400">unit merespons normal</p>
                    </div>

                    <div class="bg-white rounded-xl border border-slate-200 p-4">
                        <p class="text-xs font-medium text-slate-500">Perlu perhatian</p>
                        <p class="mt-1 text-2xl font-semibold tabular-nums {{ $attentionCount > 0 ? 'text-amber-600' : 'text-slate-900' }}">
                            {{ $attentionCount }}
                        </p>
                        <p class="mt-1 text-xs text-slate-400">{{ $warningCount }} warning · {{ $offlineCount }} offline</p>
                    </div>

                    <div class="bg-white rounded-xl border border-slate-200 p-4">
                        <p class="text-xs font-medium text-slate-500">Insiden aktif</p>
                        <p class="mt-1 text-2xl font-semibold tabular-nums {{ $unresolvedAlerts > 0 ? 'text-rose-600' : 'text-slate-900' }}">
                            <span id="cardIncidentCount">{{ $unresolvedAlerts }}</span>
                        </p>
                        <p class="mt-1 text-xs text-slate-400">menunggu tindakan NOC</p>
                    </div>

                    <div class="bg-white rounded-xl border border-slate-200 p-4">
                        <p class="text-xs font-medium text-slate-500">Total throughput</p>
                        <p class="mt-1 text-lg font-semibold text-slate-900 tabular-nums leading-8">
                            <span class="text-blue-600">↓</span> {{ $summary['throughput']['inbound'] }}
                            <span class="text-emerald-600 ml-1">↑</span> {{ $summary['throughput']['outbound'] }}
                        </p>
                        <p class="mt-1 text-xs text-slate-400">{{ $summary['throughput']['unit'] }} · gateway NOC</p>
                    </div>
                </div>

                <!-- CONTENT: DEVICE GRID + ALERT PANEL -->
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">

                    <!-- DEVICE LIST -->
                    <section class="xl:col-span-2 space-y-4">

                        <!-- Toolbar -->
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                            <div class="inline-flex p-1 bg-slate-100 rounded-lg self-start" role="tablist" aria-label="Filter status perangkat">
                                @foreach ($filterTabs as $tab)
                                    <button
                                        type="button"
                                        role="tab"
                                        data-filter="{{ $tab['key'] }}"
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                        class="filter-tab px-3 py-1.5 text-xs font-medium rounded-md transition cursor-pointer {{ $loop->first ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}"
                                    >
                                        {{ $tab['label'] }}
                                        <span class="ml-1 tabular-nums text-slate-400">{{ $tab['count'] }}</span>
                                    </button>
                                @endforeach
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="relative flex-1 lg:flex-none">
                                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <input
                                        id="deviceSearch"
                                        type="search"
                                        placeholder="Cari hostname, IP, klien..."
                                        class="w-full lg:w-60 pl-8 pr-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 shadow-2xs"
                                    >
                                </div>
                                <select
                                    id="filterUnit"
                                    aria-label="Filter jenis perangkat"
                                    class="text-xs bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-700 focus:outline-none focus:border-blue-500 cursor-pointer shadow-2xs"
                                >
                                    <option value="all">Semua jenis</option>
                                    @foreach ($unitTypes as $unitType)
                                        <option value="{{ $unitType }}">{{ $unitType }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Device Cards -->
                        @if (empty($telemetryDevices))
                            <div class="bg-white rounded-xl border border-dashed border-slate-300 p-12 text-center">
                                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-semibold text-slate-900 mb-4">Belum Ada Perangkat Monitoring</h3>
                                <a href="{{ route('monitoring.devices.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition shadow-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span>Tambah Perangkat Monitoring</span>
                                </a>
                            </div>
                        @endif

                        <div id="deviceGrid" class="{{ empty($telemetryDevices) ? 'hidden ' : '' }}grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($telemetryDevices as $device)
                                @php
                                    $devCpu = (float) ($device['cpu_load'] ?? 0);
                                    $devRam = (float) ($device['ram_load'] ?? 0);
                                    $st = $statusStyles[$device['status'] ?? 'online'] ?? $statusStyles['offline'];
                                    $cpuColor = $devCpu > 85 ? 'bg-rose-500' : ($devCpu > 70 ? 'bg-amber-500' : 'bg-emerald-500');
                                    $ramColor = $devRam > 85 ? 'bg-rose-500' : ($devRam > 75 ? 'bg-amber-500' : 'bg-blue-500');
                                @endphp
                                <article
                                    id="row-device-{{ $device['id'] }}"
                                    class="telemetry-row group relative bg-white rounded-xl border border-slate-200 p-4 transition hover:border-blue-300 hover:shadow-md"
                                    data-id="{{ $device['id'] }}"
                                    data-status="{{ $device['status'] }}"
                                    data-unit-type="{{ $device['unit_type'] }}"
                                    data-hostname="{{ $device['hostname'] }}"
                                    data-ip="{{ $device['ip_address'] }}"
                                    data-customer="{{ $device['customer'] }}"
                                >
                                    <!-- Seluruh kartu bisa diklik menuju halaman detail -->
                                    <a
                                        href="{{ route('monitoring.show', $device['id']) }}"
                                        class="absolute inset-0 rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                                        aria-label="Pantau detail {{ $device['hostname'] }}"
                                    ></a>

                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full shrink-0 {{ $st['dot'] }} {{ $device['status'] === 'offline' ? 'animate-pulse' : '' }}"></span>
                                                <h3 class="text-sm font-semibold text-slate-900 truncate group-hover:text-blue-600 transition-colors">
                                                    {{ $device['hostname'] }}
                                                </h3>
                                            </div>
                                            <p class="mt-1 text-xs text-slate-500 truncate">{{ $device['customer'] }}</p>
                                        </div>

                                        <div class="relative z-10 flex items-center gap-1.5 shrink-0">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium ring-1 ring-inset {{ $st['pill'] }}">
                                                {{ $st['label'] }}
                                            </span>

                                            <!-- Menu aksi (⋯) -->
                                            <div class="relative">
                                                <button
                                                    type="button"
                                                    class="btn-card-menu p-1 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer"
                                                    aria-label="Aksi lain untuk {{ $device['hostname'] }}"
                                                    aria-haspopup="true"
                                                    aria-expanded="false"
                                                >
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                </button>
                                                <div class="card-menu hidden absolute right-0 top-full mt-1 w-48 bg-white rounded-lg border border-slate-200 shadow-lg py-1">
                                                    <a href="{{ route('monitoring.show', $device['id']) }}" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50">
                                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        Pantau detail
                                                    </a>
                                                    <a
                                                        href="{{ route('monitoring.devices.edit', $device['id']) }}"
                                                        class="w-full flex items-center gap-2 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 cursor-pointer text-left"
                                                    >
                                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                        Edit perangkat
                                                    </a>
                                                    <button
                                                        type="button"
                                                        data-device-id="{{ $device['id'] }}"
                                                        data-device-hostname="{{ $device['hostname'] }}"
                                                        data-device-ip="{{ $device['ip_address'] }}"
                                                        class="btn-polling-ping w-full flex items-center gap-2 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 cursor-pointer text-left"
                                                    >
                                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                        </svg>
                                                        Tes ping
                                                    </button>
                                                    <div class="my-1 border-t border-slate-100"></div>
                                                    <button
                                                        type="button"
                                                        data-device-id="{{ $device['id'] }}"
                                                        data-device-hostname="{{ $device['hostname'] }}"
                                                        class="btn-delete-node w-full flex items-center gap-2 px-3 py-2 text-xs text-rose-600 hover:bg-rose-50 cursor-pointer text-left font-medium"
                                                    >
                                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Hapus dari monitoring
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <p class="mt-3 text-[11px] text-slate-500">
                                        <span class="font-mono text-slate-600">{{ $device['ip_address'] }}</span>
                                        <span class="text-slate-300 mx-1">·</span>
                                        {{ $device['unit_type'] }}
                                        <span class="text-slate-300 mx-1">·</span>
                                        Uptime {{ $device['uptime'] }}
                                    </p>

                                    @if ($device['status'] === 'offline')
                                        <div class="mt-4 rounded-lg bg-rose-50 px-3 py-2.5 text-xs text-rose-700">
                                            Perangkat tidak merespons. Periksa catu daya atau jalur koneksi.
                                        </div>
                                    @else
                                        <div class="mt-4 grid grid-cols-2 gap-4">
                                            <div>
                                                <div class="flex items-center justify-between text-xs">
                                                    <span class="text-slate-500">CPU</span>
                                                    <span class="font-semibold tabular-nums {{ $devCpu > 70 ? 'text-rose-600' : 'text-slate-900' }}">{{ $devCpu }}%</span>
                                                </div>
                                                <div class="mt-1.5 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                                    <div class="h-full rounded-full {{ $cpuColor }}" data-progress-width="{{ $devCpu }}"></div>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="flex items-center justify-between text-xs">
                                                    <span class="text-slate-500">RAM</span>
                                                    <span class="font-semibold tabular-nums {{ $devRam > 75 ? 'text-rose-600' : 'text-slate-900' }}">{{ $devRam }}%</span>
                                                </div>
                                                <div class="mt-1.5 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                                    <div class="h-full rounded-full {{ $ramColor }}" data-progress-width="{{ $devRam }}"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-3 gap-2 text-xs">
                                            <div>
                                                <p class="text-slate-400">Traffic</p>
                                                <p class="mt-0.5 font-medium text-slate-700 tabular-nums whitespace-nowrap">↓{{ $device['rx_mbps'] ?? '0 Mbps' }} ↑{{ $device['tx_mbps'] ?? '0 Mbps' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-slate-400">Latensi</p>
                                                <p class="mt-0.5 font-medium tabular-nums {{ ($device['last_ping_ms'] ?? 0) > 30 ? 'text-amber-600' : 'text-slate-700' }}">{{ $device['last_ping_ms'] ?? 0 }} ms</p>
                                            </div>
                                            <div>
                                                <p class="text-slate-400">Packet loss</p>
                                                <p class="mt-0.5 font-medium tabular-nums {{ ($device['packet_loss'] ?? 0) > 5 ? 'text-rose-600' : 'text-slate-700' }}">{{ $device['packet_loss'] ?? 0 }}%</p>
                                            </div>
                                        </div>
                                    @endif
                                </article>
                            @endforeach
                        </div>

                        <!-- Empty state (ditampilkan oleh JS saat filter tidak menemukan hasil) -->
                        <div id="emptyDeviceState" class="hidden bg-white rounded-xl border border-dashed border-slate-300 p-10 text-center">
                            <p class="text-sm font-medium text-slate-700">Tidak ada perangkat yang cocok</p>
                            <p class="mt-1 text-xs text-slate-500">Coba ubah kata kunci pencarian atau filter status.</p>
                        </div>
                    </section>

                    <!-- PANEL PERINGATAN -->
                    <aside class="bg-white rounded-xl border border-slate-200 overflow-hidden xl:sticky xl:top-20">
                        <div class="px-4 py-3.5 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h2 class="text-sm font-semibold text-slate-900">Peringatan terbaru</h2>
                                <p class="text-xs text-slate-500">Deteksi otomatis dari poller</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-600/20">
                                <span id="unresolvedCountText" class="tabular-nums mr-1">{{ $unresolvedAlerts }}</span> belum selesai
                            </span>
                        </div>

                        <div id="alertContainer" class="divide-y divide-slate-100">
                            @forelse ($alerts as $alert)
                                @php $sv = $severityStyles[$alert['severity']] ?? $severityStyles['info']; @endphp
                                <div id="alert-item-{{ $alert['id'] }}" class="px-4 py-3.5 border-l-[3px] {{ $sv['border'] }} {{ $alert['is_resolved'] ? 'opacity-60' : '' }}">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[11px] font-semibold uppercase tracking-wide {{ $sv['text'] }}">{{ $alert['type'] }}</span>
                                        <span class="text-[11px] text-slate-400 whitespace-nowrap" title="{{ $alert['time_wib'] }}">{{ $alert['time_ago'] }}</span>
                                    </div>
                                    <p class="mt-1 text-xs font-semibold text-slate-900">{{ $alert['host'] }}</p>
                                    <p class="mt-0.5 text-xs text-slate-600 leading-relaxed">{{ $alert['message'] }}</p>
                                    <div class="mt-2.5 flex items-center justify-between gap-2">
                                        <span class="text-[11px] text-slate-400 truncate">{{ $alert['customer'] }}</span>
                                        <div id="alert-action-container-{{ $alert['id'] }}" class="shrink-0">
                                            @if ($alert['is_resolved'])
                                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-400">
                                                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Selesai
                                                </span>
                                            @else
                                                <button
                                                    type="button"
                                                    data-alert-id="{{ $alert['id'] }}"
                                                    data-alert-type="{{ $alert['type'] }}"
                                                    class="btn-resolve-alert px-2.5 py-1 rounded-md border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-[11px] font-semibold transition cursor-pointer active:scale-95"
                                                >
                                                    Tandai selesai
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-8 text-center text-slate-400">
                                    <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-700">Semua Jaringan Normal</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Tidak ada insiden atau alert aktif.</p>
                                </div>
                            @endforelse
                        </div>
                    </aside>
                </div>

                <!-- FOOTER -->
                <footer class="pt-4 pb-6 text-center text-xs text-slate-400">
                    <p>© 2026 PT Netmedia Fiberoptik Lancarmulya • Network Operation Center</p>
                </footer>

            </main>

        </div>
    </div>

    <!-- MODAL: LIVE ICMP PING & DIAGNOSTIC RESULT -->
    <div id="pingDiagnosticModal" class="fixed inset-0 z-50 items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs hidden [&:not(.hidden)]:flex">
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

    {{-- Modal Konfirmasi Hapus Target Monitoring --}}
    @include('admin.monitoring.delete_modal')

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>


</body>
</html>
