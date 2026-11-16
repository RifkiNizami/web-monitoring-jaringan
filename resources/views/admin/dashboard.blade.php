<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Portal Manajemen Jaringan NetFly</title>
    <link rel="icon" href="{{ asset('assets/images/netfly-logo.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Leaflet.js 1.9.4 CSS & JS (OpenStreetMap Interactive Map) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-100 font-sans text-slate-800 antialiased">

    <div class="h-screen flex w-full overflow-hidden">

        <!-- SIDEBAR (Include Partial) -->
        @include('partials.sidebar')

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0 h-full overflow-y-auto bg-slate-100">

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
            <main class="flex-1 p-6 lg:p-8 space-y-6 bg-slate-100 min-h-screen">

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

                <!-- PETA SEBARAN LOKASI CUSTOMER (DARI ACCOUNT MANAGEMENT) -->
                <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <!-- Map Header -->
                    <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3 bg-slate-50/50">
                        <div class="flex items-center gap-2.5">
                            <span class="p-2 rounded-lg bg-blue-600 text-white shadow-2xs">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-900">
                                        Peta Sebaran Customer Terdaftar
                                    </h3>
                                    <span class="px-2 py-0.5 text-[10px] font-semibold text-blue-700 bg-blue-50 rounded-full border border-blue-200/60">
                                        {{ count($customerAccounts) }} Titik Lokasi
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    Persebaran titik instalasi pelanggan B2B & perumahan dari modul Account Management.
                                </p>
                            </div>
                        </div>

                        <!-- Right Controls / Legend -->
                        <div class="flex flex-wrap items-center gap-2.5 text-xs font-semibold">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-200 text-[11px]">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                Korporat (B2B)
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px]">
                                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                Rumahan (Home)
                            </span>
                            <button
                                type="button"
                                id="btnResetDashboardMap"
                                class="px-2.5 py-1 rounded-md bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-[11px] font-semibold transition-colors cursor-pointer"
                            >
                                Reset Zoom
                            </button>
                            <a
                                href="{{ route('accounts.index') }}"
                                class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition-colors ml-1"
                            >
                                <span>Buka Account Management</span>
                                <span>&rsaquo;</span>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Customer Focus Bar -->
                    <div class="px-4 py-2.5 bg-slate-50/80 border-b border-slate-100 flex items-center gap-2 overflow-x-auto text-[11px]">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] shrink-0">Fokus Lokasi:</span>
                        @foreach ($customerAccounts as $acc)
                            @if (!empty($acc['latitude']) && !empty($acc['longitude']))
                                <button
                                    type="button"
                                    class="btn-dashboard-focus-customer shrink-0 px-2.5 py-1 rounded-md bg-white hover:bg-blue-50 border border-slate-200/80 hover:border-blue-200 text-slate-700 hover:text-blue-700 font-medium transition-colors cursor-pointer flex items-center gap-1.5 shadow-2xs"
                                    data-lat="{{ $acc['latitude'] }}"
                                    data-lng="{{ $acc['longitude'] }}"
                                    data-name="{{ $acc['customer_name'] }}"
                                    data-id="{{ $acc['id'] }}"
                                >
                                    <span class="w-1.5 h-1.5 rounded-full {{ ($acc['customer_type'] ?? '') === 'rumahan' ? 'bg-emerald-500' : 'bg-blue-600' }}"></span>
                                    <span>{{ \Illuminate\Support\Str::limit($acc['customer_name'], 22) }}</span>
                                </button>
                            @endif
                        @endforeach
                    </div>

                    <!-- Map Container -->
                    <div id="dashboardCustomerMap" class="w-full h-80 sm:h-96 z-10 bg-slate-100"></div>

                    <!-- Map Footer Subrow -->
                    <div class="p-3 bg-white border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-[11px] text-slate-500">
                        <div class="flex items-center gap-4">
                            <span class="text-slate-400">Total Terpetakan: <b class="text-slate-800">{{ count($customerAccounts) }} Titik</b></span>
                            <span class="text-slate-400">•</span>
                            <span class="text-slate-400">Peta: <b class="text-slate-700">OpenStreetMap Interactive</b></span>
                        </div>
                        <div class="text-[10px] text-slate-400">
                            Sinkronisasi data langsung dari modul Account Management
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

    <!-- Leaflet Interactive Customer Overview Map Script -->
    <script>
        (function() {
            let dashboardCustomerMapInstance = null;
            let dashboardMarkersLayer = null;
            const dashboardCustomerMarkers = {};

            function initDashboardCustomerMap() {
                const mapEl = document.getElementById('dashboardCustomerMap');
                if (!mapEl || typeof L === 'undefined') return;

                // Reset jika sudah pernah di-inisialisasi sebelumnya (misal navigasi Turbo)
                if (mapEl._leaflet_id) {
                    if (dashboardCustomerMapInstance) {
                        try {
                            dashboardCustomerMapInstance.remove();
                        } catch (e) {}
                        dashboardCustomerMapInstance = null;
                    }
                    mapEl._leaflet_id = null;
                }

                dashboardCustomerMapInstance = L.map('dashboardCustomerMap', {
                    zoomControl: true,
                    scrollWheelZoom: false,
                }).setView([-6.225, 106.825], 11);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>',
                }).addTo(dashboardCustomerMapInstance);

                dashboardMarkersLayer = L.layerGroup().addTo(dashboardCustomerMapInstance);

                const customerData = @json($customerAccounts);
                const bounds = [];

                function createPinIcon(type) {
                    const isHome = type === 'rumahan';
                    const bgColor = isHome ? '#059669' : '#2563eb';
                    const ringColor = isHome ? '#a7f3d0' : '#bfdbfe';
                    return L.divIcon({
                        className: 'custom-dashboard-map-pin',
                        html: `<div style="width:30px;height:30px;border-radius:9999px;background:${bgColor};border:3px solid ${ringColor};box-shadow:0 4px 10px rgba(15,23,42,0.35);display:flex;align-items:center;justify-content:center;color:#fff;cursor:pointer;">
                            <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>`,
                        iconSize: [30, 30],
                        iconAnchor: [15, 15],
                        popupAnchor: [0, -15],
                    });
                }

                customerData.forEach(acc => {
                    const lat = parseFloat(acc.latitude);
                    const lng = parseFloat(acc.longitude);
                    if (isNaN(lat) || isNaN(lng)) return;

                    const isHome = (acc.customer_type || '') === 'rumahan';
                    const badgeType = isHome ? 'CUSTOMER RUMAHAN' : 'KORPORAT B2B';
                    const badgeStyle = isHome
                        ? 'background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;'
                        : 'background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;';
                    const statusLabel = acc.is_active
                        ? '<span style="color:#059669;font-weight:700;">● Aktif</span>'
                        : '<span style="color:#e11d48;font-weight:700;">● Nonaktif</span>';

                    const popupContent = `
                        <div style="min-width:230px;font-family:'Plus Jakarta Sans',sans-serif;line-height:1.45;padding:2px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;margin-bottom:6px;">
                                <span style="display:inline-block;padding:2px 7px;border-radius:4px;font-size:9.5px;font-weight:800;${badgeStyle}">${badgeType}</span>
                                <span style="font-size:10px;">${statusLabel}</span>
                            </div>
                            <div style="font-size:12.5px;font-weight:800;color:#0f172a;">${acc.customer_name}</div>
                            <div style="font-size:11px;color:#334155;margin-top:3px;">
                                PIC: <b>${acc.name || '-'}</b> (${acc.username || '-'})
                            </div>
                            <div style="font-size:10.5px;color:#2563eb;font-weight:700;margin-top:2px;">
                                ${acc.package || '-'}
                            </div>
                            <div style="font-size:10px;color:#64748b;margin-top:4px;">
                                ${acc.address || '-'}
                            </div>
                            <div style="font-size:10px;font-family:monospace;color:#475569;margin-top:5px;padding-top:4px;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;">
                                <span>${lat.toFixed(6)}, ${lng.toFixed(6)}</span>
                                <a href="{{ route('accounts.index') }}" style="color:#2563eb;text-decoration:none;font-weight:700;">Kelola &rsaquo;</a>
                            </div>
                        </div>
                    `;

                    const marker = L.marker([lat, lng], {
                        icon: createPinIcon(acc.customer_type),
                    }).bindPopup(popupContent);

                    dashboardMarkersLayer.addLayer(marker);
                    dashboardCustomerMarkers[acc.id] = marker;
                    bounds.push([lat, lng]);
                });

                setTimeout(() => {
                    if (dashboardCustomerMapInstance) {
                        dashboardCustomerMapInstance.invalidateSize();
                        if (bounds.length > 0) {
                            dashboardCustomerMapInstance.fitBounds(bounds, { padding: [35, 35], maxZoom: 14 });
                        }
                    }
                }, 200);

                // Tombol Reset Zoom Peta
                const btnReset = document.getElementById('btnResetDashboardMap');
                if (btnReset) {
                    btnReset.onclick = function() {
                        if (dashboardCustomerMapInstance && bounds.length > 0) {
                            dashboardCustomerMapInstance.fitBounds(bounds, { padding: [35, 35], maxZoom: 14 });
                        }
                    };
                }

                // Tombol Fokus Pelanggan di Quick Bar
                document.querySelectorAll('.btn-dashboard-focus-customer').forEach(btn => {
                    btn.onclick = function() {
                        const lat = parseFloat(this.getAttribute('data-lat'));
                        const lng = parseFloat(this.getAttribute('data-lng'));
                        const id = this.getAttribute('data-id');
                        if (isNaN(lat) || isNaN(lng) || !dashboardCustomerMapInstance) return;

                        mapEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        dashboardCustomerMapInstance.flyTo([lat, lng], 16, { duration: 0.9 });

                        const marker = dashboardCustomerMarkers[id];
                        if (marker) {
                            setTimeout(() => marker.openPopup(), 500);
                        }
                    };
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initDashboardCustomerMap);
            } else {
                initDashboardCustomerMap();
            }
            document.addEventListener('turbo:load', initDashboardCustomerMap);
        })();
    </script>
</body>
</html>
