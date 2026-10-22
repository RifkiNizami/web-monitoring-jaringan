<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventaris Perangkat Jaringan — Portal Manajemen Jaringan NetFly</title>
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
                    <button id="openSidebar" class="lg:hidden p-1.5 -ml-1 text-slate-600 hover:text-slate-900 rounded-md hover:bg-slate-100 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Breadcrumb -->
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                        <span class="tracking-wide uppercase">OVERVIEW</span>
                        <span>›</span>
                        <span class="text-slate-800 font-bold">Network Devices</span>
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
                    <button class="relative p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1 right-1 w-3.5 h-3.5 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center border-2 border-white">
                            3
                        </span>
                    </button>

                    <!-- Realtime Digital Clock -->
                    <div class="text-xs font-semibold text-slate-700 bg-slate-100/80 px-2.5 py-1 rounded-md border border-slate-200/60 font-mono tracking-tight" id="liveClock">
                        11.38.57 WIB
                    </div>
                </div>
            </header>

            <!-- MAIN BODY PADDING -->
            <main class="p-6 lg:p-8 space-y-6">
                
                <!-- HEADER SECTION: CATEGORY, TITLE, & ACTION BUTTONS -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="text-[11px] font-semibold text-slate-400 mb-1 flex items-center gap-1.5">
                            <span class="text-slate-500 font-medium">NetFly NMS</span>
                            <span>›</span>
                            <span>Infrastruktur & Inventaris Jaringan</span>
                        </div>
                        <h1 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight">
                            Inventaris Perangkat Jaringan
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Kelola konfigurasi perangkat jaringan, pemetaan IP manajemen, penempatan rak fisik, dan parameter monitoring SNMP.
                        </p>
                    </div>

                    <!-- Top Action Button: Tambah Perangkat Baru -->
                    <div class="flex items-center gap-2.5 shrink-0">
                        <button 
                            id="btnOpenCreateDevice" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Tambah Perangkat Baru</span>
                        </button>
                    </div>
                </div>

                <!-- 4 SUMMARY STATS CARDS -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    
                    <!-- Card 1: TOTAL PERANGKAT -->
                    <div class="bg-white p-4.5 rounded-xl border border-slate-200/80 shadow-2xs">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tight mb-2">
                            TOTAL PERANGKAT
                        </p>
                        <p class="text-2xl font-black text-slate-900 tracking-tight" id="countTotal">
                            {{ $summary['total'] ?? 4 }}
                        </p>
                    </div>

                    <!-- Card 2: ONLINE -->
                    <div class="bg-white p-4.5 rounded-xl border border-slate-200/80 shadow-2xs">
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">
                                BEROPERASI NORMAL (ONLINE)
                            </p>
                        </div>
                        <p class="text-2xl font-black text-emerald-600 tracking-tight" id="countOnline">
                            {{ $summary['online'] ?? 3 }}
                        </p>
                    </div>

                    <!-- Card 3: OFFLINE -->
                    <div class="bg-white p-4.5 rounded-xl border border-slate-200/80 shadow-2xs">
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">
                                PADAM / GANGGUAN (OFFLINE)
                            </p>
                        </div>
                        <p class="text-2xl font-black text-rose-600 tracking-tight" id="countOffline">
                            {{ $summary['offline'] ?? 1 }}
                        </p>
                    </div>

                    <!-- Card 4: ROUTER & SWITCH -->
                    <div class="bg-white p-4.5 rounded-xl border border-slate-200/80 shadow-2xs">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tight mb-2">
                            ROUTER & SWITCH UTAMA
                        </p>
                        <p class="text-2xl font-black text-blue-600 tracking-tight">
                            {{ ($summary['routers'] ?? 2) + ($summary['switches'] ?? 1) }} Unit
                        </p>
                    </div>

                </div>

                <!-- FILTER BAR (SEARCH & TYPE SELECT) -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    
                    <!-- Search Input -->
                    <div class="relative flex-1 max-w-lg">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            id="deviceSearch" 
                            placeholder="Cari hostname, IP manajemen, lokasi rak, atau customer..." 
                            class="w-full pl-10 pr-4 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs transition-all"
                        >
                    </div>

                    <!-- Type Filter Dropdown -->
                    <div class="relative shrink-0">
                        <select 
                            id="typeFilter" 
                            class="appearance-none pl-3.5 pr-8 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs cursor-pointer"
                        >
                            <option value="all">Semua Jenis Perangkat</option>
                            @foreach ($deviceTypes as $type)
                                <option value="{{ strtolower($type) }}">{{ $type }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                </div>

                <!-- TABLE: INVENTARIS PERANGKAT JARINGAN -->
                <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse" id="devicesTable">
                            
                            <!-- Table Header -->
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/60 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    <th scope="col" class="py-3 px-5 whitespace-nowrap">HOSTNAME & JENIS</th>
                                    <th scope="col" class="py-3 px-5 whitespace-nowrap">IP ADDRESS MANAJEMEN</th>
                                    <th scope="col" class="py-3 px-5 whitespace-nowrap">STATUS FISIK</th>
                                    <th scope="col" class="py-3 px-5 whitespace-nowrap">MITRA CUSTOMER / PROJECT</th>
                                    <th scope="col" class="py-3 px-5 whitespace-nowrap">LOKASI RAK / FISIK</th>
                                    <th scope="col" class="py-3 px-5 whitespace-nowrap">KOMUNITAS SNMP</th>
                                    <th scope="col" class="py-3 px-5 text-right whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>

                            <!-- Table Body -->
                            <tbody class="divide-y divide-slate-100 text-xs text-slate-700" id="devicesTableBody">
                                
                                @forelse ($devices as $device)
                                    <tr 
                                        class="hover:bg-slate-50/80 transition-colors device-row" 
                                        data-id="{{ $device['id'] }}"
                                        data-hostname="{{ $device['hostname'] }}"
                                        data-type="{{ $device['type'] }}"
                                        data-ip="{{ $device['ip_address'] }}"
                                        data-status="{{ $device['status'] }}"
                                        data-customer="{{ $device['customer'] }}"
                                        data-project="{{ $device['project'] }}"
                                        data-rack="{{ $device['rack_location'] }}"
                                        data-snmp="{{ $device['snmp_community'] }}"
                                    >
                                        
                                        <!-- 1. HOSTNAME & JENIS -->
                                        <td class="py-4 px-5 align-middle">
                                            <div class="space-y-1">
                                                <span class="font-bold text-slate-900 font-mono tracking-tight text-xs block">
                                                    {{ $device['hostname'] }}
                                                </span>
                                                <span class="inline-block px-2 py-0.5 text-[9px] font-semibold rounded bg-slate-100 text-slate-700 border border-slate-200">
                                                    {{ $device['type'] }}
                                                </span>
                                            </div>
                                        </td>

                                        <!-- 2. IP ADDRESS MANAJEMEN -->
                                        <td class="py-4 px-5 align-middle font-mono text-xs font-semibold text-blue-600 whitespace-nowrap">
                                            {{ $device['ip_address'] }}
                                        </td>

                                        <!-- 3. STATUS FISIK -->
                                        <td class="py-4 px-5 align-middle whitespace-nowrap">
                                            @if ($device['status_state'] === 'online')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/80 rounded-full">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Online
                                                </span>
                                            @elseif ($device['status_state'] === 'offline')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-rose-700 bg-rose-50 border border-rose-200/80 rounded-full">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                                    Offline
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-amber-700 bg-amber-50 border border-amber-200/80 rounded-full">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Maintenance
                                                </span>
                                            @endif
                                        </td>

                                        <!-- 4. MITRA CUSTOMER / PROJECT -->
                                        <td class="py-4 px-5 align-middle">
                                            <div class="space-y-0.5 max-w-xs">
                                                <p class="font-bold text-slate-800 text-xs">
                                                    {{ $device['customer'] }}
                                                </p>
                                                <p class="text-slate-500 text-[11px]">
                                                    {{ $device['project'] }}
                                                </p>
                                            </div>
                                        </td>

                                        <!-- 5. LOKASI RAK / FISIK -->
                                        <td class="py-4 px-5 align-middle font-medium text-slate-700 text-xs whitespace-nowrap">
                                            {{ $device['rack_location'] }}
                                        </td>

                                        <!-- 6. KOMUNITAS SNMP -->
                                        <td class="py-4 px-5 align-middle font-mono text-[11px] text-slate-500 whitespace-nowrap">
                                            {{ $device['snmp_community'] }}
                                        </td>

                                        <!-- 7. AKSI (EDIT & HAPUS) -->
                                        <td class="py-4 px-5 align-middle text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <!-- Tombol Edit -->
                                                <button 
                                                    type="button" 
                                                    title="Edit Data Perangkat"
                                                    class="btn-edit inline-flex items-center justify-center w-7 h-7 rounded-lg border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-100 active:scale-95 transition-all cursor-pointer"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>

                                                <!-- Tombol Hapus -->
                                                <button 
                                                    type="button" 
                                                    title="Hapus Perangkat Jaringan"
                                                    class="btn-delete inline-flex items-center justify-center w-7 h-7 rounded-lg border border-rose-200/90 text-rose-500 hover:text-rose-600 hover:bg-rose-50 active:scale-95 transition-all cursor-pointer"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-xs text-slate-400">
                                            Tidak ada inventaris perangkat jaringan yang terdaftar.
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>

                        </table>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="pt-4 pb-2 text-center text-xs text-slate-400 border-t border-slate-200/60">
                    &copy; {{ date('Y') }} PT Netmedia Fiberoptik Lancarmulya &bull; Network Operation Center
                </div>

            </main>

        </div>

    </div>

    <!-- ========================================== -->
    <!-- MODAL 1: TAMBAH INVENTARIS PERANGKAT BARU  -->
    <!-- ========================================== -->
    <div id="createDeviceModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs items-center justify-center p-4 hidden [&:not(.hidden)]:flex">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm lg:text-base font-bold text-slate-900">
                    Tambah Inventaris Perangkat Baru
                </h3>
                <button type="button" class="close-create-modal text-slate-400 hover:text-slate-600 p-1 rounded-md cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Form (2-Column Grid as in Figma) -->
            <form id="formCreateDevice" class="p-6 space-y-4">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    <!-- Row 1: Left - HOSTNAME PERANGKAT * -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                            HOSTNAME PERANGKAT <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="create_hostname"
                            required 
                            placeholder="Contoh: RTR-CORE-01" 
                            class="w-full px-3.5 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-mono"
                        >
                    </div>

                    <!-- Row 1: Right - JENIS PERANGKAT * -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                            JENIS PERANGKAT <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select 
                                id="create_type"
                                required
                                class="w-full appearance-none pl-3.5 pr-8 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-pointer transition-all"
                            >
                                @foreach ($deviceTypes as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Left - IP ADDRESS MANAJEMEN * -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                            IP ADDRESS MANAJEMEN <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="create_ip"
                            required 
                            placeholder="Contoh: 10.200.45.1" 
                            class="w-full px-3.5 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-mono"
                        >
                    </div>

                    <!-- Row 2: Right - STATUS FISIK AWAL * -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                            STATUS FISIK AWAL <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select 
                                id="create_status"
                                required
                                class="w-full appearance-none pl-3.5 pr-8 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-pointer transition-all"
                            >
                                @foreach ($statusOptions as $st)
                                    <option value="{{ $st }}">{{ $st }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Left - MITRA CUSTOMER * -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                            MITRA CUSTOMER <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select 
                                id="create_customer"
                                required
                                class="w-full appearance-none pl-3.5 pr-8 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-pointer transition-all"
                            >
                                <option value="">-- Pilih Customer --</option>
                                @foreach ($customers as $c)
                                    <option value="{{ $c }}">{{ $c }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Right - PROJECT TERHUBUNG (No red asterisk) -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                            PROJECT TERHUBUNG
                        </label>
                        <div class="relative">
                            <select 
                                id="create_project"
                                class="w-full appearance-none pl-3.5 pr-8 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-pointer transition-all"
                            >
                                @foreach ($projects as $proj)
                                    <option value="{{ $proj }}">{{ $proj }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: Left - LOKASI RAK / POSISI FISIK * -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                            LOKASI RAK / POSISI FISIK <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="create_rack"
                            required 
                            placeholder="Contoh: Rack A-01, Slot 42U" 
                            class="w-full px-3.5 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all"
                        >
                    </div>

                    <!-- Row 4: Right - KOMUNITAS SNMP * -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                            KOMUNITAS SNMP <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="create_snmp"
                            required 
                            value="netfly-ro-noc" 
                            class="w-full px-3.5 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-mono"
                        >
                    </div>

                </div>

                <!-- Modal Footer (Batal & Simpan Perangkat) -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button 
                        type="button" 
                        class="close-create-modal px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors cursor-pointer"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer"
                    >
                        Simpan Perangkat
                    </button>
                </div>

            </form>

        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 2: EDIT DATA PERANGKAT               -->
    <!-- ========================================== -->
    <div id="editDeviceModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs items-center justify-center p-4 hidden [&:not(.hidden)]:flex">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm lg:text-base font-bold text-slate-900">
                    Edit Data Perangkat
                </h3>
                <button type="button" class="close-edit-modal text-slate-400 hover:text-slate-600 p-1 rounded-md cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Form (Prefilled with Device Data) -->
            <form id="formEditDevice" class="p-6 space-y-4">
                <input type="hidden" id="edit_device_id" value="">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    <!-- Row 1: Left - HOSTNAME PERANGKAT * -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                            HOSTNAME PERANGKAT <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="edit_hostname"
                            required 
                            value="RTR-CORE-MANDIRI-01" 
                            class="w-full px-3.5 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-mono"
                        >
                    </div>

                    <!-- Row 1: Right - JENIS PERANGKAT * -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                            JENIS PERANGKAT <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select 
                                id="edit_type"
                                required
                                class="w-full appearance-none pl-3.5 pr-8 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-pointer transition-all"
                            >
                                @foreach ($deviceTypes as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Left - IP ADDRESS MANAJEMEN * -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                            IP ADDRESS MANAJEMEN <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="edit_ip"
                            required 
                            value="10.200.45.1" 
                            class="w-full px-3.5 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-mono"
                        >
                    </div>

                    <!-- Row 2: Right - STATUS FISIK AWAL * -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                            STATUS FISIK AWAL <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select 
                                id="edit_status"
                                required
                                class="w-full appearance-none pl-3.5 pr-8 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-pointer transition-all"
                            >
                                @foreach ($statusOptions as $st)
                                    <option value="{{ $st }}">{{ $st }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Left - MITRA CUSTOMER * -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                            MITRA CUSTOMER <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select 
                                id="edit_customer"
                                required
                                class="w-full appearance-none pl-3.5 pr-8 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-pointer transition-all"
                            >
                                @foreach ($customers as $c)
                                    <option value="{{ $c }}">{{ $c }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Right - PROJECT TERHUBUNG -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                            PROJECT TERHUBUNG
                        </label>
                        <div class="relative">
                            <select 
                                id="edit_project"
                                class="w-full appearance-none pl-3.5 pr-8 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-pointer transition-all"
                            >
                                @foreach ($projects as $proj)
                                    <option value="{{ $proj }}">{{ $proj }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: Left - LOKASI RAK / POSISI FISIK * -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                            LOKASI RAK / POSISI FISIK <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="edit_rack"
                            required 
                            value="Rack Core-A, U-42" 
                            class="w-full px-3.5 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all"
                        >
                    </div>

                    <!-- Row 4: Right - KOMUNITAS SNMP * -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                            KOMUNITAS SNMP <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="edit_snmp"
                            required 
                            value="mandiri-ro-noc" 
                            class="w-full px-3.5 py-2 text-xs bg-slate-50/50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-mono"
                        >
                    </div>

                </div>

                <!-- Modal Footer (Batal & Perbarui Perangkat) -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button 
                        type="button" 
                        class="close-edit-modal px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors cursor-pointer"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer"
                    >
                        Perbarui Perangkat
                    </button>
                </div>

            </form>

        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 3: HAPUS PERANGKAT JARINGAN          -->
    <!-- ========================================== -->
    <div id="deleteDeviceModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs items-center justify-center p-4 hidden [&:not(.hidden)]:flex">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            
            <!-- Modal Header (Rose trash badge, Title, Subtitle, Close X) -->
            <div class="p-5 border-b border-slate-100 flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-500 border border-rose-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 leading-tight">
                            Hapus Perangkat Jaringan
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            Tindakan ini memerlukan konfirmasi
                        </p>
                    </div>
                </div>

                <button type="button" class="close-delete-modal text-slate-400 hover:text-slate-600 p-1 rounded-md cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body (Question message with dynamic hostname & IP) -->
            <div class="p-6">
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 text-xs text-slate-600 leading-relaxed">
                    Apakah Anda yakin ingin menghapus perangkat <span id="delDeviceTarget" class="font-bold text-slate-900 font-mono">RTR-CORE-MANDIRI-01 (10.200.45.1)</span>?
                </div>
            </div>

            <!-- Modal Footer (Batal & Ya, Hapus Data) -->
            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end gap-3">
                <button 
                    type="button" 
                    class="close-delete-modal px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-900 transition-colors cursor-pointer"
                >
                    Batal
                </button>
                <button 
                    type="button" 
                    id="btnConfirmDelete"
                    class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer"
                >
                    Ya, Hapus Data
                </button>
            </div>

        </div>
    </div>

</body>
</html>
