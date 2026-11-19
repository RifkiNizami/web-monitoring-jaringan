<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Pemberitahuan — Portal Manajemen Jaringan NetFly</title>
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
                    <!-- Collapsible Sidebar Navbar Toggle Button -->
                    <button id="openSidebar" type="button" class="sidebar-toggle-btn p-1.5 -ml-1 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 cursor-pointer transition-colors" title="Buka / Tutup Menu Navigasi (Ctrl+B)" aria-label="Toggle menu navigasi" aria-expanded="true">
                        <svg class="w-5 h-5 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path class="bar-top" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16" />
                            <path class="bar-mid" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16" />
                            <path class="bar-bot" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 18h16" />
                        </svg>
                    </button>

                    <!-- Breadcrumb -->
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                        <span class="tracking-wide uppercase">INFORMASI & BROADCAST</span>
                        <span>›</span>
                        <span class="text-slate-800 font-bold">Pusat Pemberitahuan</span>
                    </div>
                </div>

                <!-- Right Header Tools -->
                <div class="flex items-center gap-3">
                    <!-- Realtime Digital Clock -->
                    <div class="hidden sm:flex items-center gap-1.5 text-xs font-mono text-slate-600 bg-slate-100/80 px-2.5 py-1 rounded-md border border-slate-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="liveClock">--:--:-- WIB</span>
                    </div>
                </div>
            </header>

            <!-- MAIN BODY PADDING -->
            <main class="p-6 lg:p-8 space-y-6">
                
                <!-- HEADER HERO BANNER -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2">
                    <div>
                        <h1 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight">
                            Pusat Pemberitahuan & Jadwal Pemeliharaan
                        </h1>
                        <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                            Pusat informasi resmi agenda pemeliharaan berkala (maintenance), insiden jaringan, update patch sistem, dan broadcast operasional dari Admin Konten.
                        </p>
                    </div>

                    <!-- Status Info Tag -->
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold bg-white text-netfly-blue border border-blue-200 shadow-2xs">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                            </span>
                            <span>{{ $activeCount }} Siaran Aktif</span>
                        </span>
                    </div>
                </div>

                <!-- 4 STATS OVERVIEW CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Total Siaran --}}
                    <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs flex items-center justify-between hover:border-slate-300 transition-all">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Siaran</span>
                            <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalCount }}</p>
                            <span class="text-[11px] text-slate-400">Arsip Pengumuman</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                    </div>

                    {{-- Sedang Siar (Aktif) --}}
                    <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs flex items-center justify-between hover:border-emerald-200 transition-all">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Sedang Siar</span>
                            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $activeCount }}</p>
                            <span class="text-[11px] text-emerald-600 font-medium">Aktif / Berlangsung</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Jadwal Maintenance --}}
                    <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs flex items-center justify-between hover:border-amber-200 transition-all">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Jadwal Maintenance</span>
                            <p class="text-2xl font-black text-amber-600 mt-1">{{ $maintenanceCount }}</p>
                            <span class="text-[11px] text-amber-600 font-medium">Pemeliharaan Berkala</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Pembaruan Sistem --}}
                    <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs flex items-center justify-between hover:border-blue-200 transition-all">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pembaruan Sistem</span>
                            <p class="text-2xl font-black text-blue-600 mt-1">{{ $systemUpdateCount }}</p>
                            <span class="text-[11px] text-blue-600 font-medium">Patch & Upgrade</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- FILTER, SEARCH & CATEGORY TABS -->
                <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs p-4 space-y-3.5">
                    {{-- Row 1: Category Filter Pill Tabs --}}
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none text-xs">
                        @php
                            $currentCat = request('category', 'all');
                        @endphp

                        <a href="{{ route('pemberitahuan.index', array_merge(request()->except(['category', 'page']), ['category' => 'all'])) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg font-semibold transition-all shrink-0 {{ $currentCat === 'all' || empty($currentCat) ? 'bg-slate-900 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200/80 hover:text-slate-900' }}">
                            <span>Semua Kategori</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentCat === 'all' || empty($currentCat) ? 'bg-slate-800 text-slate-200' : 'bg-white text-slate-600' }}">{{ $totalCount }}</span>
                        </a>

                        <a href="{{ route('pemberitahuan.index', array_merge(request()->except(['category', 'page']), ['category' => 'maintenance'])) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg font-semibold transition-all shrink-0 {{ $currentCat === 'maintenance' ? 'bg-amber-600 text-white shadow-2xs' : 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $currentCat === 'maintenance' ? 'bg-white' : 'bg-amber-500' }}"></span>
                            <span>Maintenance</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentCat === 'maintenance' ? 'bg-amber-700 text-amber-100' : 'bg-white text-amber-800' }}">{{ $maintenanceCount }}</span>
                        </a>

                        <a href="{{ route('pemberitahuan.index', array_merge(request()->except(['category', 'page']), ['category' => 'pembaruan'])) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg font-semibold transition-all shrink-0 {{ $currentCat === 'pembaruan' ? 'bg-blue-600 text-white shadow-2xs' : 'bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $currentCat === 'pembaruan' ? 'bg-white' : 'bg-blue-500' }}"></span>
                            <span>Pembaruan Sistem</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentCat === 'pembaruan' ? 'bg-blue-700 text-blue-100' : 'bg-white text-blue-800' }}">{{ $systemUpdateCount }}</span>
                        </a>

                        <a href="{{ route('pemberitahuan.index', array_merge(request()->except(['category', 'page']), ['category' => 'gangguan'])) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg font-semibold transition-all shrink-0 {{ $currentCat === 'gangguan' ? 'bg-rose-600 text-white shadow-2xs' : 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $currentCat === 'gangguan' ? 'bg-white' : 'bg-rose-500' }}"></span>
                            <span>Gangguan Jaringan</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentCat === 'gangguan' ? 'bg-rose-700 text-rose-100' : 'bg-white text-rose-800' }}">{{ $gangguanCount ?? 0 }}</span>
                        </a>

                        <a href="{{ route('pemberitahuan.index', array_merge(request()->except(['category', 'page']), ['category' => 'info'])) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg font-semibold transition-all shrink-0 {{ $currentCat === 'info' ? 'bg-purple-600 text-white shadow-2xs' : 'bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200/60' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $currentCat === 'info' ? 'bg-white' : 'bg-purple-500' }}"></span>
                            <span>Pengumuman Info</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentCat === 'info' ? 'bg-purple-700 text-purple-100' : 'bg-white text-purple-800' }}">{{ $infoCount ?? 0 }}</span>
                        </a>
                    </div>

                    {{-- Row 2: Search Input and Status Dropdown --}}
                    <form method="GET" action="{{ route('pemberitahuan.index') }}" class="flex flex-col sm:flex-row gap-2.5 pt-1 border-t border-slate-100">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif

                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Cari judul, kata kunci insiden, atau isi pemberitahuan..." 
                                class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 transition-all"
                            >
                        </div>

                        <div class="flex items-center gap-2">
                            <select name="status" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:bg-white focus:border-blue-500 cursor-pointer">
                                <option value="all">Semua Status</option>
                                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif (Sedang Siar)</option>
                                <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai / Pulih</option>
                            </select>

                            @if(request('search') || (request('category') && request('category') !== 'all') || (request('status') && request('status') !== 'all'))
                                <a href="{{ route('pemberitahuan.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors flex items-center gap-1.5 shrink-0" title="Reset Filter">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span>Reset</span>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- CARDS GRID VIEW PEMBERITAHUAN (3 COLUMNS BALANCED) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @forelse($announcements as $item)
                        @php
                            $badge = $item->category_badge;
                            $prioBadge = $item->priority_badge;
                        @endphp
                        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-blue-400 transition-all duration-200 flex flex-col justify-between overflow-hidden group">
                            
                            <!-- Card Content Area -->
                            <div class="p-5 space-y-3.5">
                                <!-- Top Bar: Category Pill & Priority Badge -->
                                <div class="flex items-center justify-between gap-2">
                                    {{-- Kategori Badge --}}
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $badge['bg'] }}">
                                        @if($item->category === 'maintenance')
                                            <svg class="w-3 h-3 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        @elseif($item->category === 'gangguan')
                                            <svg class="w-3 h-3 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        @elseif($item->category === 'pembaruan')
                                            <svg class="w-3 h-3 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                        @else
                                            <svg class="w-3 h-3 text-purple-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                        <span>{{ $badge['label'] }}</span>
                                    </span>

                                    {{-- Priority Badge --}}
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border {{ $prioBadge['class'] }}">
                                            {{ $prioBadge['label'] }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Title -->
                                <div>
                                    <h2 class="text-sm font-bold text-slate-900 group-hover:text-netfly-blue transition-colors line-clamp-2 leading-snug cursor-pointer" onclick="openDetailModal({{ json_encode($item) }})">
                                        {{ $item->title }}
                                    </h2>
                                    <p class="text-xs text-slate-600 mt-1.5 line-clamp-3 leading-relaxed">
                                        {{ $item->message }}
                                    </p>
                                </div>

                                <!-- Schedule Banner (Jika Ada Waktu Pelaksanaan) -->
                                @if($item->scheduled_at)
                                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/70 text-[11px] text-slate-600 space-y-1">
                                        <div class="flex items-center gap-1.5 font-semibold text-slate-800">
                                            <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>Mulai: <strong class="text-slate-800">{{ $item->scheduled_at->format('d M Y, H:i') }} WIB</strong></span>
                                        </div>
                                        @if($item->expires_at)
                                            <div class="flex items-center gap-1.5 text-slate-500 pl-5 text-[10px]">
                                                <span>Selesai: {{ $item->expires_at->format('d M Y, H:i') }} WIB</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Card Footer: Metadata & Action -->
                            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-[11px]">
                                <div class="flex items-center gap-2 text-slate-400">
                                    @if($item->status === 'aktif')
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-[10px] font-medium text-slate-400">
                                            Selesai
                                        </span>
                                    @endif
                                    <span>•</span>
                                    <span class="font-mono">{{ $item->created_at->format('d M Y') }}</span>
                                </div>

                                <button type="button" onclick="openDetailModal({{ json_encode($item) }})" class="inline-flex items-center gap-1 font-bold text-netfly-blue hover:text-blue-700 transition-colors cursor-pointer group-hover:translate-x-0.5">
                                    <span>Baca Lengkap</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white rounded-2xl border border-slate-200/80 p-12 text-center text-slate-400 space-y-3">
                            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-slate-700">Tidak ada pemberitahuan yang sesuai</h3>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto">
                                Coba ubah kata kunci pencarian atau bersihkan filter kategori untuk menampilkan seluruh data broadcast.
                            </p>
                            <div>
                                <a href="{{ route('pemberitahuan.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-netfly-blue bg-blue-50 hover:bg-blue-100 transition-colors">
                                    <span>Lihat Semua Pemberitahuan</span>
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- PAGINATION BAR -->
                <div class="pt-2">
                    {{ $announcements->links() }}
                </div>

            </main>

        </div>

    </div>

    <!-- ==========================================
         MODAL: DETAIL PEMBERITAHUAN
         ========================================== -->
    <div id="detailModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-lg overflow-hidden animate-scale">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span id="detail_category_badge" class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border"></span>
                    <span id="detail_priority_badge" class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border"></span>
                </div>
                <button onclick="closeModal('detailModal')" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <h3 id="detail_title" class="text-base font-black text-slate-900 leading-snug"></h3>

                <div id="detail_schedule_box" class="p-3.5 rounded-xl bg-amber-50/70 border border-amber-200 text-xs text-amber-900 space-y-1 hidden">
                    <p class="font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Jadwal Pelaksanaan / Jendela Waktu:</span>
                    </p>
                    <p id="detail_schedule_text" class="text-[11px] font-mono pl-5.5 text-amber-800"></p>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Detail Informasi</label>
                    <div id="detail_message" class="text-xs text-slate-700 leading-relaxed whitespace-pre-line p-3.5 bg-slate-50 rounded-xl border border-slate-200/70 max-h-60 overflow-y-auto"></div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                    <span id="detail_meta"></span>
                    <button type="button" onclick="closeModal('detailModal')" class="px-4 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function openDetailModal(item) {
            document.getElementById('detail_title').textContent = item.title || '';
            document.getElementById('detail_message').textContent = item.message || '';

            // Category badge
            const catBadge = document.getElementById('detail_category_badge');
            const catLabels = {
                maintenance: 'MAINTENANCE',
                pembaruan: 'PEMBARUAN SISTEM',
                gangguan: 'GANGGUAN',
                info: 'INFO'
            };
            catBadge.textContent = catLabels[item.category] || (item.category ? item.category.toUpperCase() : 'INFO');
            catBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-bold border ' + 
                (item.category === 'maintenance' ? 'bg-amber-50 text-amber-700 border-amber-200' : 
                (item.category === 'gangguan' ? 'bg-rose-50 text-rose-700 border-rose-200' :
                (item.category === 'pembaruan' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-purple-50 text-purple-700 border-purple-200')));

            // Priority badge
            const prioBadge = document.getElementById('detail_priority_badge');
            prioBadge.textContent = item.priority ? item.priority.toUpperCase() : 'NORMAL';
            prioBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold uppercase border ' + 
                (item.priority === 'urgent' ? 'bg-rose-100 text-rose-800 border-rose-300' : 
                (item.priority === 'tinggi' ? 'bg-orange-100 text-orange-800 border-orange-300' : 'bg-emerald-100 text-emerald-800 border-emerald-300'));

            // Schedule box
            const scheduleBox = document.getElementById('detail_schedule_box');
            const scheduleText = document.getElementById('detail_schedule_text');
            if (item.scheduled_at) {
                scheduleBox.classList.remove('hidden');
                const startStr = new Date(item.scheduled_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
                let text = `Mulai: ${startStr} WIB`;
                if (item.expires_at) {
                    const endStr = new Date(item.expires_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
                    text += ` s/d ${endStr} WIB`;
                }
                scheduleText.textContent = text;
            } else {
                scheduleBox.classList.add('hidden');
            }

            // Meta
            const pubDate = new Date(item.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            const author = item.creator ? item.creator.name : 'Admin Konten';
            document.getElementById('detail_meta').textContent = `Diterbitkan oleh ${author} pada ${pubDate}`;

            openModal('detailModal');
        }

        // Live Clock
        function updateLiveClock() {
            const clockEl = document.getElementById('liveClock');
            if (!clockEl) return;
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            clockEl.textContent = `${hours}:${minutes}:${seconds} WIB`;
        }
        setInterval(updateLiveClock, 1000);
        updateLiveClock();

    </script>
</body>
</html>
