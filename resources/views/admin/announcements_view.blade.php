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
                    <!-- Mobile Hamburger -->
                    <button id="openSidebar" class="lg:hidden p-1.5 -ml-1 text-slate-600 hover:text-slate-900 rounded-md hover:bg-slate-100 cursor-pointer" aria-label="Buka Menu">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
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
                
                <!-- HEADER SECTION -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="text-[11px] font-semibold text-slate-400 mb-1 flex items-center gap-1.5">
                            <span class="text-slate-500 font-medium">NetFly NMS</span>
                            <span>›</span>
                            <span>Pemberitahuan & Broadcast Sistem</span>
                        </div>
                        <h1 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight">
                            Pusat Pemberitahuan & Jadwal Pemeliharaan
                        </h1>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Informasi resmi jadwal pemeliharaan (maintenance), pembaruan sistem infrastruktur, dan pemberitahuan operasional dari Admin Konten.
                        </p>
                    </div>

                    <!-- Status Info Tag -->
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-netfly-blue border border-blue-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>{{ $activeCount }} Siaran Aktif</span>
                        </span>
                    </div>
                </div>

                <!-- 3 STATS CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Jadwal Maintenance</span>
                            <p class="text-2xl font-black text-amber-600 mt-1">{{ $maintenanceCount }}</p>
                            <span class="text-[11px] text-slate-400">Pemeliharaan Terjadwal</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pembaruan Sistem</span>
                            <p class="text-2xl font-black text-blue-600 mt-1">{{ $systemUpdateCount }}</p>
                            <span class="text-[11px] text-slate-400">Update Patch & Optimalisasi</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Siaran Diterima</span>
                            <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalCount }}</p>
                            <span class="text-[11px] text-slate-400">Arsip Pengumuman</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- FILTER & SEARCH BAR -->
                <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs">
                    <form method="GET" action="{{ route('announcements.index') }}" class="flex flex-col sm:flex-row gap-3">
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
                                placeholder="Cari judul atau isi pemberitahuan..." 
                                class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 transition-all"
                            >
                        </div>

                        <div class="flex gap-2">
                            <select name="category" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:bg-white focus:border-blue-500">
                                <option value="all">Semua Kategori</option>
                                <option value="maintenance" {{ request('category') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="pembaruan" {{ request('category') === 'pembaruan' ? 'selected' : '' }}>Pembaruan Sistem</option>
                                <option value="gangguan" {{ request('category') === 'gangguan' ? 'selected' : '' }}>Gangguan</option>
                                <option value="info" {{ request('category') === 'info' ? 'selected' : '' }}>Pengumuman Info</option>
                            </select>

                            <select name="status" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:bg-white focus:border-blue-500">
                                <option value="all">Semua Status</option>
                                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>

                            @if(request('search') || request('category') || request('status'))
                                <a href="{{ route('announcements.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span>Reset</span>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- CARDS GRID VIEW PEMBERITAHUAN -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($announcements as $item)
                        @php
                            $badge = $item->category_badge;
                            $prioBadge = $item->priority_badge;
                        @endphp
                        <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs p-5 hover:border-blue-300 transition-all flex flex-col justify-between group">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $badge['bg'] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                            {{ $badge['label'] }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border {{ $prioBadge['class'] }}">
                                            {{ $prioBadge['label'] }}
                                        </span>
                                    </div>

                                    <span class="text-[10px] text-slate-400 font-mono">
                                        {{ $item->created_at->format('d M Y, H:i') }} WIB
                                    </span>
                                </div>

                                <div>
                                    <h2 class="text-sm font-bold text-slate-900 group-hover:text-netfly-blue transition-colors">
                                        {{ $item->title }}
                                    </h2>
                                    <p class="text-xs text-slate-600 mt-1.5 line-clamp-3 leading-relaxed">
                                        {{ $item->message }}
                                    </p>
                                </div>

                                @if($item->scheduled_at)
                                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/70 text-[11px] text-slate-600 space-y-1">
                                        <div class="flex items-center gap-1.5 font-medium">
                                            <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>Mulai: <strong class="text-slate-800">{{ $item->scheduled_at->format('d M Y, H:i') }} WIB</strong></span>
                                        </div>
                                        @if($item->expires_at)
                                            <div class="flex items-center gap-1.5 text-slate-500 pl-5">
                                                <span>Selesai: {{ $item->expires_at->format('d M Y, H:i') }} WIB</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                <span class="text-slate-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>Penyiar: {{ $item->creator ? $item->creator->name : 'Admin Konten' }}</span>
                                </span>

                                <button onclick="openDetailModal({{ json_encode($item) }})" class="font-bold text-netfly-blue hover:text-blue-700 cursor-pointer">
                                    Baca Lengkap ›
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white rounded-xl border border-slate-200/80 p-12 text-center text-slate-400">
                            <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                            <p class="font-bold text-slate-700">Tidak ada pemberitahuan yang ditemukan.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $announcements->links() }}
                </div>

            </main>

        </div>

    </div>

    <!-- ==========================================
         MODAL: DETAIL PEMBERITAHUAN
         ========================================== -->
    <div id="detailModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl w-full max-w-lg overflow-hidden animate-scale">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span id="detail_category_badge" class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border"></span>
                    <span id="detail_priority_badge" class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border"></span>
                </div>
                <button onclick="closeModal('detailModal')" class="text-slate-400 hover:text-slate-600 p-1 rounded-md">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <h3 id="detail_title" class="text-base font-bold text-slate-900 leading-snug"></h3>

                <div id="detail_schedule_box" class="p-3 rounded-xl bg-amber-50/70 border border-amber-200 text-xs text-amber-900 space-y-1">
                    <p class="font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Jadwal Pelaksanaan:</span>
                    </p>
                    <p id="detail_schedule_text" class="text-[11px] font-mono pl-5.5"></p>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Detail Informasi</label>
                    <div id="detail_message" class="text-xs text-slate-700 leading-relaxed whitespace-pre-line p-3 bg-slate-50 rounded-xl border border-slate-200/70 max-h-60 overflow-y-auto"></div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                    <span id="detail_meta"></span>
                    <button type="button" onclick="closeModal('detailModal')" class="px-4 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors cursor-pointer">
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
            catBadge.textContent = item.category ? item.category.toUpperCase() : 'INFO';
            catBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-bold border ' + 
                (item.category === 'maintenance' ? 'bg-amber-50 text-amber-700 border-amber-200' : 
                (item.category === 'pembaruan' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-slate-100 text-slate-700 border-slate-200'));

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
                let text = `Mulai: ${new Date(item.scheduled_at).toLocaleString('id-ID')}`;
                if (item.expires_at) {
                    text += ` s/d ${new Date(item.expires_at).toLocaleString('id-ID')}`;
                }
                scheduleText.textContent = text;
            } else {
                scheduleBox.classList.add('hidden');
            }

            // Meta
            document.getElementById('detail_meta').textContent = `Diterbitkan pada: ${new Date(item.created_at).toLocaleDateString('id-ID')}`;

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
