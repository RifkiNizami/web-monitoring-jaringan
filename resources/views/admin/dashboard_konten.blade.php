<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin Konten — Portal NetFly</title>
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
                    <button id="openSidebar" class="lg:hidden p-1.5 -ml-1 text-slate-600 hover:text-slate-900 rounded-md hover:bg-slate-100 cursor-pointer" aria-label="Buka Menu">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Breadcrumb -->
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                        <span class="tracking-wide uppercase">MANAJEMEN KONTEN</span>
                        <span>›</span>
                        <span class="text-slate-800 font-bold">Dashboard Admin Konten</span>
                    </div>
                </div>

                <!-- Right Header Tools -->
                <div class="flex items-center gap-3">
                    <!-- Tombol Lihat Landing Page Live -->
                    <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-netfly-blue bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors border border-blue-200">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        <span>Lihat Landing Page Live</span>
                    </a>

                    <!-- Realtime Digital Clock -->
                    <div class="hidden sm:flex items-center gap-1.5 text-xs font-mono text-slate-600 bg-slate-100/80 px-2.5 py-1 rounded-md border border-slate-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="liveClock">--:--:-- WIB</span>
                    </div>
                </div>
            </header>

            <!-- FLASH ALERTS -->
            @if(session('success'))
                <div class="mx-6 lg:mx-8 mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-emerald-800 text-sm">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mx-6 lg:mx-8 mt-6 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-between text-rose-800 text-sm">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- MAIN BODY PADDING -->
            <main class="p-6 lg:p-8 space-y-6">
                
                <!-- HEADER SECTION -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="text-[11px] font-semibold text-slate-400 mb-1 flex items-center gap-1.5">
                            <span class="text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded border border-blue-200">PORTAL ADMIN KONTEN</span>
                            <span>›</span>
                            <span>Ringkasan Publikasi & Pembaruan</span>
                        </div>
                        <h1 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight">
                            Pusat Pengelolaan Konten & Pemberitahuan
                        </h1>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Pantau konten logo klien yang baru diunggah untuk landing page publik serta status siaran pemberitahuan pemeliharaan/sistem.
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2.5 shrink-0">
                        <a href="{{ route('landing-clients.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-netfly-blue hover:bg-blue-700 rounded-lg shadow-sm transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Kelola Klien Landing</span>
                        </a>
                        <a href="{{ route('announcements.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 rounded-lg border border-slate-200 shadow-2xs transition-all">
                            <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                            <span>Buat Pemberitahuan</span>
                        </a>
                    </div>
                </div>

                <!-- 4 METRIC CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Card 1: Total Klien Landing Page -->
                    <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs hover:border-blue-300 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Klien Landing Page</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-50 text-netfly-blue border border-blue-200">
                                {{ $activeClients }} Aktif
                            </span>
                        </div>
                        <div class="mt-3 flex items-baseline gap-2">
                            <span class="text-2xl font-black text-slate-900">{{ $totalClients }}</span>
                            <span class="text-xs text-slate-500">Logo Instansi</span>
                        </div>
                        <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Tampil di Landing</span>
                            <span class="font-semibold text-emerald-600">{{ $totalClients > 0 ? round(($activeClients / $totalClients) * 100) : 0 }}% Terbit</span>
                        </div>
                    </div>

                    <!-- Card 2: Pemberitahuan & Broadcast -->
                    <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs hover:border-amber-300 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pemberitahuan</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                {{ $activeAnnouncements }} Sedang Siar
                            </span>
                        </div>
                        <div class="mt-3 flex items-baseline gap-2">
                            <span class="text-2xl font-black text-slate-900">{{ $totalAnnouncements }}</span>
                            <span class="text-xs text-slate-500">Total Siaran</span>
                        </div>
                        <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Kanal Informasi</span>
                            <span class="font-semibold text-slate-700">Maintenance & Info</span>
                        </div>
                    </div>

                    <!-- Card 3: Pembaruan Terakhir -->
                    <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs hover:border-blue-300 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pembaruan Terakhir</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        </div>
                        <div class="mt-3">
                            <p class="text-sm font-bold text-slate-800 truncate">
                                {{ $lastUpdatedClient ? $lastUpdatedClient->name : 'Belum ada konten' }}
                            </p>
                            <span class="text-[11px] text-slate-400 font-mono">
                                {{ $lastUpdatedClient ? $lastUpdatedClient->updated_at->diffForHumans() : '-' }}
                            </span>
                        </div>
                        <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Status Terakhir</span>
                            <span class="font-semibold text-blue-600">Tersinkronisasi</span>
                        </div>
                    </div>

                    <!-- Card 4: Status Integrasi Landing Page -->
                    <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs hover:border-emerald-300 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Landing Page Live</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                LIVE
                            </span>
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                            <span class="text-base font-bold text-slate-800">Carousel Siap</span>
                        </div>
                        <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Navigasi Publik</span>
                            <a href="{{ route('home') }}#klien" target="_blank" class="font-semibold text-netfly-blue hover:underline">Cek Bagian Klien ›</a>
                        </div>
                    </div>

                </div>

                <!-- 2-COLUMN SECTION: PEMBARUAN KONTEN & PEMBERITAHUAN -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- Kiri: Hal-hal yang baru diupload / diperbarui oleh admin konten -->
                    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden flex flex-col">
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-blue-50 text-netfly-blue flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-sm font-bold text-slate-900">Pembaruan Konten Klien Terkini</h2>
                                    <p class="text-[11px] text-slate-400">Daftar logo dan instansi mitra yang baru diunggah/diperbarui</p>
                                </div>
                            </div>
                            <a href="{{ route('landing-clients.index') }}" class="text-xs font-semibold text-netfly-blue hover:text-blue-700">
                                Lihat Semua ›
                            </a>
                        </div>

                        <div class="divide-y divide-slate-100 flex-1">
                            @forelse($latestClients as $client)
                                <div class="p-4 flex items-center justify-between gap-3 hover:bg-slate-50/80 transition-colors">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-12 h-10 rounded-lg bg-slate-100 border border-slate-200/80 p-1 flex items-center justify-center shrink-0">
                                            <img src="{{ $client->logo_url }}" alt="{{ $client->name }}" class="max-h-8 max-w-10 object-contain">
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-800 truncate">{{ $client->name }}</p>
                                            <p class="text-[11px] text-slate-400 flex items-center gap-1.5">
                                                <span>{{ $client->category }}</span>
                                                <span>&bull;</span>
                                                <span>{{ $client->location }}</span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 shrink-0 text-right">
                                        <div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $client->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                                                {{ $client->is_active ? 'Aktif' : 'Draft' }}
                                            </span>
                                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $client->updated_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-xs text-slate-400">
                                    Belum ada data klien yang diunggah.
                                </div>
                            @endforelse
                        </div>

                        <div class="p-3 bg-slate-50 border-t border-slate-100 text-center">
                            <a href="{{ route('landing-clients.index') }}" class="text-xs font-semibold text-netfly-blue hover:underline">
                                + Kelola & Unggah Logo Klien Baru
                            </a>
                        </div>
                    </div>

                    <!-- Kanan: Pemberitahuan (Maintenance, etc.) -->
                    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden flex flex-col">
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-sm font-bold text-slate-900">Pemberitahuan & Announcement Aktif</h2>
                                    <p class="text-[11px] text-slate-400">Siaran informasi maintenance, perbaikan, dan pengumuman untuk admin</p>
                                </div>
                            </div>
                            <a href="{{ route('announcements.index') }}" class="text-xs font-semibold text-netfly-blue hover:text-blue-700">
                                Kelola ›
                            </a>
                        </div>

                        <div class="divide-y divide-slate-100 flex-1">
                            @forelse($latestAnnouncements as $announcement)
                                @php
                                    $badge = $announcement->category_badge;
                                    $prioBadge = $announcement->priority_badge;
                                @endphp
                                <div class="p-4 hover:bg-slate-50/80 transition-colors">
                                    <div class="flex items-center justify-between gap-2 mb-1.5">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badge['bg'] }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                                {{ $badge['label'] }}
                                            </span>
                                            <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase rounded border {{ $prioBadge['class'] }}">
                                                {{ $prioBadge['label'] }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-mono">
                                            {{ $announcement->created_at->format('d M Y, H:i') }}
                                        </span>
                                    </div>
                                    <h3 class="text-xs font-bold text-slate-800 leading-snug">
                                        {{ $announcement->title }}
                                    </h3>
                                    <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">
                                        {{ $announcement->message }}
                                    </p>
                                </div>
                            @empty
                                <div class="p-8 text-center text-xs text-slate-400">
                                    Belum ada pemberitahuan yang dibuat.
                                </div>
                            @endforelse
                        </div>

                        <div class="p-3 bg-slate-50 border-t border-slate-100 text-center">
                            <a href="{{ route('announcements.index') }}" class="text-xs font-semibold text-netfly-blue hover:underline">
                                + Buat Pemberitahuan Maintenance / Info Baru
                            </a>
                        </div>
                    </div>

                </div>

            </main>

        </div>

    </div>

    <!-- Realtime Clock Script -->
    <script>
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

        // Mobile Sidebar Toggle
        const openSidebarBtn = document.getElementById('openSidebar');
        const closeSidebarBtn = document.getElementById('closeSidebar');
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        if (openSidebarBtn && sidebar && sidebarBackdrop) {
            openSidebarBtn.addEventListener('click', () => {
                sidebar.classList.remove('-translate-x-full');
                sidebarBackdrop.classList.remove('hidden');
            });
            if (closeSidebarBtn) {
                closeSidebarBtn.addEventListener('click', () => {
                    sidebar.classList.add('-translate-x-full');
                    sidebarBackdrop.classList.add('hidden');
                });
            }
            sidebarBackdrop.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                sidebarBackdrop.classList.add('hidden');
            });
        }
    </script>
</body>
</html>
