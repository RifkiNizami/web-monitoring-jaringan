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

    <div class="min-h-screen flex w-full">
        
        <!-- SIDEBAR (Include Partial) -->
        @include('partials.sidebar')

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            
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
                        <span class="tracking-wide">OVERVIEW</span>
                        <span>›</span>
                        <span class="text-slate-800 font-bold">Dashboard</span>
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
                    <button class="relative p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1 right-1 w-3.5 h-3.5 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center border-2 border-white">
                            3
                        </span>
                    </button>

                    <!-- Realtime Digital Clock -->
                    <div class="text-xs font-semibold text-slate-700 bg-slate-100/80 px-2.5 py-1 rounded-md border border-slate-200/60 font-mono tracking-tight" id="liveClock">
                        10.43.26 WIB
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
                                <span>CUSTOMER AKTIF</span>
                            </div>
                            <span class="px-2 py-0.5 text-[10px] font-medium bg-slate-100 text-slate-600 rounded-full border border-slate-200/60">
                                Terverifikasi
                            </span>
                        </div>
                        <div class="flex items-baseline gap-2 mb-4">
                            <span class="text-2xl font-black text-slate-900 tracking-tight">1, 248</span>
                            <span class="text-xs font-medium text-emerald-600">+12 bln ini</span>
                        </div>
                        <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                            <span>Corporate & Retail</span>
                            <span class="text-slate-600 font-semibold">99.4% Aktif</span>
                        </div>
                    </div>

                    <!-- Card 2: Project Aktif -->
                    <div class="bg-white p-4.5 rounded-xl border border-slate-200/80 shadow-2xs flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                                <span>PROJECT AKTIF</span>
                            </div>
                            <span class="px-2 py-0.5 text-[10px] font-medium bg-slate-100 text-slate-600 rounded-full border border-slate-200/60">
                                On Track
                            </span>
                        </div>
                        <div class="flex items-baseline gap-2 mb-4">
                            <span class="text-2xl font-black text-slate-900 tracking-tight">42</span>
                            <span class="text-xs font-medium text-slate-500">Instalasi Baru</span>
                        </div>
                        <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                            <span>Target Selesai</span>
                            <span class="text-slate-600 font-semibold">5 Minggu Ini</span>
                        </div>
                    </div>

                    <!-- Card 3: Perangkat Online -->
                    <div class="bg-white p-4.5 rounded-xl border border-slate-200/80 shadow-2xs flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                </svg>
                                <span>PERANGKAT ONLINE</span>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-medium bg-emerald-50 text-emerald-600 rounded-full border border-emerald-200/60">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Online
                            </span>
                        </div>
                        <div class="flex items-baseline gap-2 mb-4">
                            <span class="text-2xl font-black text-slate-900 tracking-tight">8, 432</span>
                            <span class="text-xs font-medium text-slate-400">/ 8,444 unit</span>
                        </div>
                        <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                            <span>Ketersediaan Fisik</span>
                            <span class="text-emerald-600 font-semibold">99.86% Sehat</span>
                        </div>
                    </div>

                    <!-- Card 4: Perangkat Offline -->
                    <div class="bg-white p-4.5 rounded-xl border border-slate-200/80 shadow-2xs flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-tight">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>PERANGKAT OFFLINE</span>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-medium bg-rose-50 text-rose-600 rounded-full border border-rose-200/60">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                Offline
                            </span>
                        </div>
                        <div class="flex items-baseline gap-2 mb-4">
                            <span class="text-2xl font-black text-rose-600 tracking-tight">12</span>
                            <span class="text-xs font-medium text-slate-400">Unit Padam</span>
                        </div>
                        <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                            <span>Status Tindakan</span>
                            <span class="text-rose-600 font-semibold">Investigasi Aktif</span>
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
                                        Statistik ketersediaan transmisi backbone dan link pelanggan (30 Hari Terakhir)
                                    </p>
                                </div>
                                <div>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200/60 rounded-md">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                        Rata-rata: 99.82%
                                    </span>
                                </div>
                            </div>

                            <!-- Metrics Summary Subrow -->
                            <div class="grid grid-cols-3 gap-3 p-3 bg-slate-50/80 rounded-lg border border-slate-100 text-center mb-6">
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-tight">UPTIME TERTINGGI</div>
                                    <div class="text-sm font-bold text-slate-800 mt-0.5">100.0%</div>
                                </div>
                                <div class="border-x border-slate-200/60">
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-tight">TITIK TERENDAH</div>
                                    <div class="text-sm font-bold text-slate-800 mt-0.5">99.50%</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-tight">STATUS SLA BULANAN</div>
                                    <div class="text-sm font-bold text-emerald-600 mt-0.5">Tercapai (99.5%)</div>
                                </div>
                            </div>
                        </div>

                        <!-- SVG Line Chart (Matching image spline & labels) -->
                        <div class="w-full relative pt-2">
                            <div class="flex">
                                <!-- Y Axis Labels -->
                                <div class="flex flex-col justify-between text-[10px] font-mono text-slate-400 pr-3 pb-6 text-right h-44 select-none shrink-0">
                                    <span>100.1%</span>
                                    <span>100.0%</span>
                                    <span>99.8%</span>
                                    <span>99.6%</span>
                                    <span>99.4%</span>
                                    <span>99.2%</span>
                                </div>

                                <!-- Chart Area -->
                                <div class="flex-1 relative pb-6 h-44">
                                    <!-- Horizontal Grid Lines -->
                                    <div class="absolute inset-x-0 top-0 h-full flex flex-col justify-between pointer-events-none pb-6">
                                        <div class="border-b border-dashed border-slate-200/70 w-full"></div>
                                        <div class="border-b border-dashed border-slate-200/70 w-full"></div>
                                        <div class="border-b border-dashed border-slate-200/70 w-full"></div>
                                        <div class="border-b border-dashed border-slate-200/70 w-full"></div>
                                        <div class="border-b border-dashed border-slate-200/70 w-full"></div>
                                        <div class="border-b border-slate-200 w-full"></div>
                                    </div>

                                    <!-- Spline Path SVG -->
                                    <svg class="w-full h-[calc(100%-1.5rem)] overflow-visible" viewBox="0 0 600 150" preserveAspectRatio="none">
                                        <defs>
                                            <linearGradient id="slaGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                                <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.25" />
                                                <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.0" />
                                            </linearGradient>
                                        </defs>

                                        <!-- Area Fill -->
                                        <path 
                                            d="M 10,48 C 60,52 80,56 120,53 C 160,50 170,44 210,45 C 240,46 270,95 310,95 C 350,95 380,50 420,50 C 460,50 480,44 520,44 C 550,44 570,54 590,56 L 590,150 L 10,150 Z" 
                                            fill="url(#slaGradient)" 
                                        />

                                        <!-- Stroke Line -->
                                        <path 
                                            d="M 10,48 C 60,52 80,56 120,53 C 160,50 170,44 210,45 C 240,46 270,95 310,95 C 350,95 380,50 420,50 C 460,50 480,44 520,44 C 550,44 570,54 590,56" 
                                            fill="none" 
                                            stroke="#2563eb" 
                                            stroke-width="2.5" 
                                            stroke-linecap="round"
                                        />

                                        <!-- Interactive Points -->
                                        <circle cx="10" cy="48" r="4" fill="#ffffff" stroke="#2563eb" stroke-width="2" />
                                        <circle cx="120" cy="53" r="3.5" fill="#ffffff" stroke="#2563eb" stroke-width="2" />
                                        <circle cx="210" cy="45" r="4" fill="#ffffff" stroke="#2563eb" stroke-width="2" />
                                        <circle cx="310" cy="95" r="4" fill="#ffffff" stroke="#2563eb" stroke-width="2" />
                                        <circle cx="420" cy="50" r="4" fill="#ffffff" stroke="#2563eb" stroke-width="2" />
                                        <circle cx="520" cy="44" r="4" fill="#ffffff" stroke="#2563eb" stroke-width="2" />
                                        <circle cx="590" cy="56" r="3.5" fill="#ffffff" stroke="#2563eb" stroke-width="2" />
                                    </svg>

                                    <!-- X Axis Labels -->
                                    <div class="absolute bottom-0 inset-x-0 flex justify-between text-[10px] font-mono text-slate-400 select-none">
                                        <span>01 Okt</span>
                                        <span>05 Okt</span>
                                        <span>10 Okt</span>
                                        <span>15 Okt</span>
                                        <span>20 Okt</span>
                                        <span>25 Okt</span>
                                        <span>30 Okt</span>
                                    </div>
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

                            <!-- Alerts List -->
                            <div class="space-y-3">
                                
                                <!-- Alert 1 -->
                                <div class="p-3 rounded-lg border border-rose-100 bg-rose-50/40 hover:bg-rose-50/70 transition-colors">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase rounded bg-rose-100 text-rose-700 tracking-wide">
                                            PADAM
                                        </span>
                                        <span class="text-[10px] font-mono text-slate-400">10:42:32 WIB</span>
                                    </div>
                                    <div class="text-xs font-bold text-slate-900 mt-1">
                                        Switch Agg-Bks_04 Terputus
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">
                                        Koneksi padam di POP Bekasi. Indikasi kehilangan catu daya listrik AC.
                                    </p>
                                </div>

                                <!-- Alert 2 -->
                                <div class="p-3 rounded-lg border border-amber-100 bg-amber-50/40 hover:bg-amber-50/70 transition-colors">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase rounded bg-amber-100 text-amber-800 tracking-wide">
                                            BEBAN TINGGI
                                        </span>
                                        <span class="text-[10px] font-mono text-slate-400">10:45:00 WIB</span>
                                    </div>
                                    <div class="text-xs font-bold text-slate-900 mt-1">
                                        Router Core-East_01 CPU 92%
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">
                                        Utilisasi CPU melebihi ambang batas aman selama lebih dari 5 menit.
                                    </p>
                                </div>

                                <!-- Alert 3 -->
                                <div class="p-3 rounded-lg border border-indigo-100 bg-indigo-50/40 hover:bg-indigo-50/70 transition-colors">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase rounded bg-indigo-100 text-indigo-700 tracking-wide">
                                            ANOMALI
                                        </span>
                                        <span class="text-[10px] font-mono text-slate-400">09:32:45 WIB</span>
                                    </div>
                                    <div class="text-xs font-bold text-slate-900 mt-1">
                                        Backbone JKT-SBY Latensi Naik
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">
                                        RTT terdeteksi 120ms (Batas Normal transmisi: ≤15ms).
                                    </p>
                                </div>

                            </div>
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
                                    3 Membutuhkan Penanganan
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Daftar insiden gangguan jaringan aktif yang membutuhkan penanganan segera berdasarkan SLA.
                            </p>
                        </div>
                        <div>
                            <a href="#semua-tiket" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition-colors">
                                <span>Lihat Semua Tiket</span>
                                <span>›</span>
                            </a>
                        </div>
                    </div>

                    <!-- Table Data -->
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
                                
                                <!-- Row 1 -->
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 px-5 font-mono text-[11px] font-semibold text-slate-800">
                                        <span class="px-2 py-0.5 bg-slate-100 border border-slate-200/70 rounded">
                                            INC-2026-1023
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <div class="font-bold text-slate-900">PT. XYZ Teknologi</div>
                                        <div class="text-[11px] text-slate-400">Dedicated Fiber 1Gbps</div>
                                    </td>
                                    <td class="py-3.5 px-5 text-slate-600 font-medium">
                                        Link Down (Kabel Putus / FO Cut)
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <span class="inline-block px-2 py-0.5 text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-200 rounded">
                                            P1 - Critical
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Menunggu Dispatch
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5 font-mono text-slate-600">
                                        45m 12s
                                    </td>
                                    <td class="py-3.5 px-5 text-right">
                                        <button class="px-3 py-1 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-md shadow-2xs transition-colors cursor-pointer">
                                            Tangani
                                        </button>
                                    </td>
                                </tr>

                                <!-- Row 2 -->
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 px-5 font-mono text-[11px] font-semibold text-slate-800">
                                        <span class="px-2 py-0.5 bg-slate-100 border border-slate-200/70 rounded">
                                            INC-2026-1024
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <div class="font-bold text-slate-900">Dinas Pendidikan Kota</div>
                                        <div class="text-[11px] text-slate-400">Project Smart City Jaringan FO</div>
                                    </td>
                                    <td class="py-3.5 px-5 text-slate-600 font-medium">
                                        Packet Loss Tinggi (>15%)
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <span class="inline-block px-2 py-0.5 text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded">
                                            P2 - High
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            Dianalisis NOC
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5 font-mono text-slate-600">
                                        12m 05s
                                    </td>
                                    <td class="py-3.5 px-5 text-right">
                                        <button class="px-3 py-1 border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-medium rounded-md transition-colors cursor-pointer">
                                            Detail
                                        </button>
                                    </td>
                                </tr>

                                <!-- Row 3 -->
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 px-5 font-mono text-[11px] font-semibold text-slate-800">
                                        <span class="px-2 py-0.5 bg-slate-100 border border-slate-200/70 rounded">
                                            REQ-2026-0882
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <div class="font-bold text-slate-900">RS Medika Center</div>
                                        <div class="text-[11px] text-slate-400">Dedicated Internet Broadband</div>
                                    </td>
                                    <td class="py-3.5 px-5 text-slate-600 font-medium">
                                        Permintaan Bandwidth On-Demand (+50M)
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <span class="inline-block px-2 py-0.5 text-[10px] font-bold text-slate-700 bg-slate-100 border border-slate-200 rounded">
                                            P3 - Medium
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            Menunggu Persetujuan
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5 font-mono text-slate-600">
                                        2j 15m
                                    </td>
                                    <td class="py-3.5 px-5 text-right">
                                        <button class="px-3 py-1 border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-medium rounded-md transition-colors cursor-pointer">
                                            Proses
                                        </button>
                                    </td>
                                </tr>

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
