<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktivitas Sistem — Portal Manajemen Jaringan NetFly</title>
    <link rel="icon" href="{{ asset('assets/images/netfly-logo.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

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
                    <button id="openSidebar" class="lg:hidden p-1.5 -ml-1 text-slate-600 hover:text-slate-900 rounded-md hover:bg-slate-100 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Breadcrumb -->
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                        <span class="tracking-wide uppercase">LAPORAN & AUDIT</span>
                        <span>›</span>
                        <span class="text-slate-800 font-bold">Log Aktivitas</span>
                    </div>
                </div>

                <!-- Right Header Tools -->
                <div class="flex items-center gap-4">
                    <!-- Realtime Digital Clock -->
                    <div class="hidden sm:flex items-center gap-1.5 text-xs font-mono text-slate-600 bg-slate-100/80 px-2.5 py-1 rounded-md border border-slate-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="liveClock">--:--:-- WIB</span>
                    </div>
                </div>
            </header>

            <!-- MAIN BODY PADDING -->
            <main class="p-6 lg:p-8 space-y-6">
                
                <!-- HEADER SECTION: CATEGORY & TITLE -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="text-[11px] font-semibold text-slate-400 mb-1 flex items-center gap-1.5">
                            <span class="text-slate-500 font-medium">NetFly NMS</span>
                            <span>›</span>
                            <span>Laporan Kinerja SLA & Audit</span>
                        </div>
                        <h1 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                            <span>Log Aktivitas Sistem</span>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-netfly-blue border border-blue-200">Audit Trail</span>
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Rekaman audit komprehensif seluruh tindakan pengguna, autentikasi, dan perubahan inventaris & telemetri secara real-time.
                        </p>
                    </div>

                    <div class="flex items-center gap-2 text-xs text-slate-500 bg-white px-3 py-2 rounded-lg border border-slate-200 shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span class="font-medium text-slate-700">Audit Immutability:</span>
                        <span class="text-slate-500">Read-Only Enforced</span>
                    </div>
                </div>

                <!-- KARTU RINGKASAN AKTIVITAS HARI INI -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Total Hari Ini -->
                    <div class="bg-white rounded-xl border border-slate-200/80 p-4 shadow-2xs hover:shadow-xs transition-shadow">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-500">Total Aktivitas Hari Ini</span>
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-netfly-blue flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2.5 flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-slate-900 tracking-tight">{{ number_format($summary['today_total']) }}</span>
                            <span class="text-xs text-slate-400">peristiwa</span>
                        </div>
                    </div>

                    <!-- Login Gagal Hari Ini -->
                    <div class="bg-white rounded-xl border border-slate-200/80 p-4 shadow-2xs hover:shadow-xs transition-shadow">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-500">Login Gagal Hari Ini</span>
                            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2.5 flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-amber-600 tracking-tight">{{ number_format($summary['today_failed_login']) }}</span>
                            <span class="text-xs text-slate-400">percobaan mencurigakan</span>
                        </div>
                    </div>

                    <!-- Aksi Hapus Hari Ini -->
                    <div class="bg-white rounded-xl border border-slate-200/80 p-4 shadow-2xs hover:shadow-xs transition-shadow">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-500">Aksi Hapus Data Hari Ini</span>
                            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2.5 flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-rose-600 tracking-tight">{{ number_format($summary['today_deletions']) }}</span>
                            <span class="text-xs text-slate-400">data terhapus</span>
                        </div>
                    </div>
                </div>

                <!-- FILTER CARD -->
                <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-2xs">
                    <form id="filterForm" method="GET" action="{{ route('activity-logs.index') }}" class="space-y-4">
                        
                        <!-- Baris 1: Filter Kategori & Pencarian (4 Kolom Rata & Seimbang) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <!-- 1. Pencarian -->
                            <div>
                                <label for="search" class="block text-[10px] font-bold text-slate-500 uppercase tracking-tight mb-1.5">
                                    Pencarian
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input 
                                        type="text" 
                                        name="search" 
                                        id="search" 
                                        value="{{ $search }}" 
                                        placeholder="Cari keterangan, subjek, IP..." 
                                        class="w-full pl-9 pr-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs transition-all"
                                    >
                                </div>
                            </div>

                            <!-- 2. Modul -->
                            <div>
                                <label for="module" class="block text-[10px] font-bold text-slate-500 uppercase tracking-tight mb-1.5">
                                    Modul
                                </label>
                                <div class="relative">
                                    <select 
                                        name="module" 
                                        id="module" 
                                        class="w-full text-xs bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-700 font-medium focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 appearance-none pr-8 cursor-pointer shadow-2xs transition-all"
                                    >
                                        <option value="all">Semua Modul</option>
                                        @foreach($modules as $modKey => $modLabel)
                                            <option value="{{ $modKey }}" {{ $module === $modKey ? 'selected' : '' }}>{{ $modLabel }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Aksi -->
                            <div>
                                <label for="action" class="block text-[10px] font-bold text-slate-500 uppercase tracking-tight mb-1.5">
                                    Aksi
                                </label>
                                <div class="relative">
                                    <select 
                                        name="action" 
                                        id="action" 
                                        class="w-full text-xs bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-700 font-medium focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 appearance-none pr-8 cursor-pointer shadow-2xs transition-all"
                                    >
                                        <option value="all">Semua Aksi</option>
                                        @foreach($actions as $actKey => $actLabel)
                                            <option value="{{ $actKey }}" {{ $action === $actKey ? 'selected' : '' }}>{{ $actLabel }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. Pengguna -->
                            <div>
                                <label for="user_id" class="block text-[10px] font-bold text-slate-500 uppercase tracking-tight mb-1.5">
                                    Pengguna
                                </label>
                                <div class="relative">
                                    <select 
                                        name="user_id" 
                                        id="user_id" 
                                        class="w-full text-xs bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-700 font-medium focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 appearance-none pr-8 cursor-pointer shadow-2xs transition-all"
                                    >
                                        <option value="all">Semua Pengguna</option>
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}" {{ (string)$userId === (string)$u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->role }})</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Baris 2: Filter Rentang Tanggal, Pintasan Cepat & Tombol Aksi -->
                        @php
                            $todayDateStr = \Carbon\Carbon::now('Asia/Jakarta')->toDateString();
                            $sevenDaysDateStr = \Carbon\Carbon::now('Asia/Jakarta')->subDays(6)->toDateString();
                            $thirtyDaysDateStr = \Carbon\Carbon::now('Asia/Jakarta')->subDays(29)->toDateString();
                            $thisMonthDateStr = \Carbon\Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString();

                            $isTodayActive = ($dateFrom === $todayDateStr && $dateTo === $todayDateStr);
                            $is7DaysActive = ($dateFrom === $sevenDaysDateStr && $dateTo === $todayDateStr);
                            $is30DaysActive = ($dateFrom === $thirtyDaysDateStr && $dateTo === $todayDateStr);
                            $isThisMonthActive = ($dateFrom === $thisMonthDateStr && $dateTo === $todayDateStr);
                        @endphp
                        <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3 pt-3.5 border-t border-slate-100">
                            <!-- Kiri: Rentang Tanggal & Pintasan Cepat -->
                            <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                                <!-- Group Rentang Tanggal -->
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-1.5">
                                        <label for="date_from" class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">Dari:</label>
                                        <input 
                                            type="date" 
                                            name="date_from" 
                                            id="date_from" 
                                            value="{{ $dateFrom }}" 
                                            class="px-2.5 py-1.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-700 font-medium focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs cursor-pointer"
                                        >
                                    </div>
                                    <span class="text-xs font-semibold text-slate-400">s/d</span>
                                    <div class="flex items-center gap-1.5">
                                        <label for="date_to" class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">Sampai:</label>
                                        <input 
                                            type="date" 
                                            name="date_to" 
                                            id="date_to" 
                                            value="{{ $dateTo }}" 
                                            class="px-2.5 py-1.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-700 font-medium focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs cursor-pointer"
                                        >
                                    </div>
                                </div>

                                <div class="h-4 w-px bg-slate-200 hidden sm:block"></div>

                                <!-- Pintasan Tanggal Cepat -->
                                <div class="flex flex-wrap items-center gap-1">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tight mr-0.5">Pintasan:</span>
                                    <button 
                                        type="button" 
                                        onclick="setDateShortcut('today')"
                                        class="px-2.5 py-1 text-[11px] rounded-md border transition-all cursor-pointer {{ $isTodayActive ? 'bg-blue-600 text-white border-blue-600 font-semibold shadow-2xs' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 hover:text-blue-600' }}"
                                    >
                                        Hari Ini
                                    </button>
                                    <button 
                                        type="button" 
                                        onclick="setDateShortcut('7days')"
                                        class="px-2.5 py-1 text-[11px] rounded-md border transition-all cursor-pointer {{ $is7DaysActive ? 'bg-blue-600 text-white border-blue-600 font-semibold shadow-2xs' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 hover:text-blue-600' }}"
                                    >
                                        7 Hari Terakhir
                                    </button>
                                    <button 
                                        type="button" 
                                        onclick="setDateShortcut('30days')"
                                        class="px-2.5 py-1 text-[11px] rounded-md border transition-all cursor-pointer {{ $is30DaysActive ? 'bg-blue-600 text-white border-blue-600 font-semibold shadow-2xs' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 hover:text-blue-600' }}"
                                    >
                                        30 Hari Terakhir
                                    </button>
                                    <button 
                                        type="button" 
                                        onclick="setDateShortcut('this_month')"
                                        class="px-2.5 py-1 text-[11px] rounded-md border transition-all cursor-pointer {{ $isThisMonthActive ? 'bg-blue-600 text-white border-blue-600 font-semibold shadow-2xs' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 hover:text-blue-600' }}"
                                    >
                                        Bulan Ini
                                    </button>
                                </div>
                            </div>

                            <!-- Kanan: Tombol Reset & Terapkan Filter -->
                            <div class="flex items-center gap-2 justify-end shrink-0 pt-2 lg:pt-0">
                                <a 
                                    href="{{ route('activity-logs.index') }}" 
                                    class="px-3.5 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors"
                                >
                                    Reset Filter
                                </a>
                                <button 
                                    type="submit" 
                                    class="inline-flex items-center gap-1.5 px-4 py-1.5 text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-lg shadow-2xs active:scale-95 transition-all cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                    <span>Terapkan Filter</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- TABEL LOG AKTIVITAS -->
                <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/60 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    <th scope="col" class="py-3.5 px-5 whitespace-nowrap">Waktu</th>
                                    <th scope="col" class="py-3.5 px-5 whitespace-nowrap">Pengguna</th>
                                    <th scope="col" class="py-3.5 px-5 whitespace-nowrap">Modul</th>
                                    <th scope="col" class="py-3.5 px-5 whitespace-nowrap">Aksi</th>
                                    <th scope="col" class="py-3.5 px-5">Keterangan Aktivitas</th>
                                    <th scope="col" class="py-3.5 px-5 whitespace-nowrap">Alamat IP</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($logs as $log)
                                    @php
                                        // Tentukan kelas warna badge aksi
                                        $actionBadgeClass = match($log->action) {
                                            'create' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20 border-emerald-200',
                                            'update' => 'bg-blue-50 text-blue-700 ring-1 ring-blue-600/20 border-blue-200',
                                            'delete' => 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/20 border-rose-200',
                                            'upload' => 'bg-purple-50 text-purple-700 ring-1 ring-purple-600/20 border-purple-200',
                                            'login' => 'bg-slate-100 text-slate-700 ring-1 ring-slate-500/20 border-slate-200',
                                            'logout' => 'bg-slate-100 text-slate-600 ring-1 ring-slate-400/20 border-slate-200',
                                            'login_failed' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20 border-amber-200',
                                            default => 'bg-slate-100 text-slate-700 ring-1 ring-slate-400/20 border-slate-200',
                                        };

                                        // Badge Role
                                        $roleBadgeClass = match($log->user_role) {
                                            'admin' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                            'admin_konten' => 'bg-teal-50 text-teal-700 border-teal-200',
                                            'customer_representative', 'customer_residential' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                            default => 'bg-slate-100 text-slate-600 border-slate-200',
                                        };

                                        $moduleLabel = match($log->module) {
                                            'inventaris' => 'Inventaris',
                                            'monitoring' => 'Monitoring',
                                            'tiket' => 'Tiket',
                                            'akun' => 'Akun',
                                            'konten' => 'Konten',
                                            'auth' => 'Keamanan',
                                            default => ucfirst($log->module),
                                        };
                                    @endphp
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <!-- Waktu -->
                                        <td class="py-3.5 px-5 whitespace-nowrap">
                                            <div class="font-semibold text-slate-900">{{ $log->created_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} <span class="text-[10px] text-slate-400 font-normal">WIB</span></div>
                                            <div class="text-[10px] text-slate-400">{{ $log->created_at->setTimezone('Asia/Jakarta')->locale('id')->diffForHumans() }}</div>
                                        </td>

                                        <!-- Pengguna + Role Badge -->
                                        <td class="py-3.5 px-5">
                                            <div class="font-medium text-slate-900 leading-tight">{{ $log->user_name }}</div>
                                            <span class="inline-block mt-0.5 px-1.5 py-0.2 rounded text-[10px] font-semibold uppercase tracking-wider border {{ $roleBadgeClass }}">
                                                {{ $log->user_role }}
                                            </span>
                                        </td>

                                        <!-- Modul -->
                                        <td class="py-3.5 px-5 whitespace-nowrap">
                                            <span class="font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded text-[11px] border border-slate-200/80">
                                                {{ $moduleLabel }}
                                            </span>
                                        </td>

                                        <!-- Aksi -->
                                        <td class="py-3.5 px-5 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $actionBadgeClass }}">
                                                {{ strtoupper(str_replace('_', ' ', $log->action)) }}
                                            </span>
                                        </td>

                                        <!-- Keterangan -->
                                        <td class="py-3.5 px-5">
                                            <div class="text-slate-800 leading-snug">{{ $log->description }}</div>
                                            @if($log->subject_label)
                                                <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1">
                                                    <span class="text-slate-400">Target:</span>
                                                    <span class="font-mono bg-slate-100 px-1 py-0.2 rounded text-[10px] text-slate-700 font-semibold">{{ $log->subject_label }}</span>
                                                    @if($log->subject_id)
                                                        <span class="text-[10px] text-slate-400">(ID: {{ $log->subject_id }})</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>

                                        <!-- IP Address -->
                                        <td class="py-3.5 px-5 whitespace-nowrap font-mono text-[11px] text-slate-500">
                                            {{ $log->ip_address ?? '—' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <p class="text-sm font-bold text-slate-700">Belum ada catatan log aktivitas</p>
                                                <p class="text-xs text-slate-400 mt-1 max-w-sm">
                                                    Tidak ditemukan catatan audit trail yang cocok dengan kriteria filter saat ini.
                                                </p>
                                                @if($search || $module !== 'all' || $action !== 'all' || $userId !== 'all' || $dateFrom || $dateTo)
                                                    <a 
                                                        href="{{ route('activity-logs.index') }}" 
                                                        class="mt-3 px-3.5 py-1.5 text-xs font-semibold text-netfly-blue hover:underline"
                                                    >
                                                        Bersihkan Semua Filter
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINATION BAR -->
                    @if($logs->hasPages())
                        <div class="p-4 border-t border-slate-200/80 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                            <div>
                                Menampilkan <span class="font-semibold text-slate-700">{{ $logs->firstItem() ?? 0 }}</span> sampai <span class="font-semibold text-slate-700">{{ $logs->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-slate-700">{{ $logs->total() }}</span> catatan log
                            </div>
                            <div>
                                {{ $logs->links() }}
                            </div>
                        </div>
                    @endif
                </div>

            </main>
        </div>
    </div>

    <!-- Script Jam Digital & Interaktivitas Filter Tanggal -->
    <script>
        function updateLiveClock() {
            const clockEl = document.getElementById('liveClock');
            if (clockEl) {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                clockEl.textContent = `${hours}:${minutes}:${seconds} WIB`;
            }
        }
        setInterval(updateLiveClock, 1000);
        updateLiveClock();

        // Fungsi pintasan rentang tanggal cepat
        window.setDateShortcut = function(preset) {
            const now = new Date();
            const pad = (n) => String(n).padStart(2, '0');
            const formatDate = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

            const dateFromInput = document.getElementById('date_from');
            const dateToInput = document.getElementById('date_to');
            const todayStr = formatDate(now);

            if (preset === 'today') {
                dateFromInput.value = todayStr;
                dateToInput.value = todayStr;
            } else if (preset === '7days') {
                const past7 = new Date();
                past7.setDate(past7.getDate() - 6);
                dateFromInput.value = formatDate(past7);
                dateToInput.value = todayStr;
            } else if (preset === '30days') {
                const past30 = new Date();
                past30.setDate(past30.getDate() - 29);
                dateFromInput.value = formatDate(past30);
                dateToInput.value = todayStr;
            } else if (preset === 'this_month') {
                const startMonth = new Date(now.getFullYear(), now.getMonth(), 1);
                dateFromInput.value = formatDate(startMonth);
                dateToInput.value = todayStr;
            }

            const form = document.getElementById('filterForm');
            if (form) {
                form.submit();
            }
        };

        // Penyesuaian otomatis jika tanggal awal melebihi tanggal akhir
        const dateFromInput = document.getElementById('date_from');
        const dateToInput = document.getElementById('date_to');

        if (dateFromInput && dateToInput) {
            dateFromInput.addEventListener('change', function() {
                if (dateToInput.value && this.value > dateToInput.value) {
                    dateToInput.value = this.value;
                }
            });
            dateToInput.addEventListener('change', function() {
                if (dateFromInput.value && this.value < dateFromInput.value) {
                    dateFromInput.value = this.value;
                }
            });
        }
    </script>
</body>
</html>
