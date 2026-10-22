<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer & Project Management — Portal Manajemen Jaringan NetFly</title>
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
                        <span class="tracking-wide uppercase">OVERVIEW</span>
                        <span>›</span>
                        <span class="text-slate-800 font-bold">Customer & Project Management</span>
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
                            id="topNavSearch"
                            placeholder="Cari PT, PIC, project..." 
                            class="pl-8 pr-12 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 w-44 lg:w-56 transition-all"
                        >
                        <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none">
                            <kbd class="px-1.5 py-0.5 text-[10px] font-mono text-slate-400 bg-white border border-slate-200 rounded shadow-2xs">⌘K</kbd>
                        </span>
                    </div>

                    <!-- Notification Bell -->
                    <button class="relative p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer" title="Notifikasi">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1 right-1 w-3.5 h-3.5 bg-blue-600 text-white text-[9px] font-bold rounded-full flex items-center justify-center border-2 border-white">
                            {{ count($customers) }}
                        </span>
                    </button>

                    <!-- Realtime Digital Clock -->
                    <div class="text-xs font-semibold text-slate-700 bg-slate-100/80 px-2.5 py-1 rounded-md border border-slate-200/60 font-mono tracking-tight" id="liveClock">
                        10.44.26 WIB
                    </div>
                </div>
            </header>

            <!-- MAIN BODY PADDING -->
            <main class="p-6 lg:p-8 space-y-6">
                
                <!-- HEADER SECTION: SUB-TITLE, TITLE & TOP ACTION BUTTONS -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="text-[11px] font-semibold text-slate-400 mb-1 flex items-center gap-1.5">
                            <span class="text-slate-500 font-medium">NetFly NMS</span>
                            <span>›</span>
                            <span>Kemitraan & Infrastruktur</span>
                        </div>
                        <h1 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight">
                            Customer & Project Management
                        </h1>
                    </div>

                    <!-- Top 2 Action Buttons (As shown in Figma) -->
                    <div class="flex items-center gap-2.5 shrink-0">
                        <!-- 1. Tambah Customer (Dark Button) -->
                        <button 
                            type="button" 
                            id="btnOpenCreateCustomer"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Tambah Customer</span>
                        </button>

                        <!-- 2. Tambah Project (White Border Button) -->
                        <button 
                            type="button" 
                            id="btnOpenCreateProject"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-800 border border-slate-200 text-xs font-bold rounded-lg shadow-2xs active:scale-95 transition-all cursor-pointer"
                        >
                            <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Tambah Project</span>
                        </button>
                    </div>
                </div>

                <!-- TABS CONTAINER & REALTIME FILTER ROW -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-1">
                    
                    <!-- 2 TABS: DAFTAR MITRA CUSTOMER vs PROJECT TERHUBUNG -->
                    <div class="flex items-center gap-2">
                        <!-- Tab 1: Daftar Mitra Customer -->
                        <button 
                            type="button" 
                            id="tabCustomerBtn"
                            onclick="switchTab('customer')"
                            class="tab-btn inline-flex items-center gap-2.5 px-4 py-2 text-xs font-bold rounded-xl transition-all cursor-pointer border {{ ($activeTab ?? 'customer') === 'customer' ? 'bg-white border-slate-200/90 shadow-2xs text-slate-900' : 'bg-transparent border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-100/60' }}"
                        >
                            <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Daftar Mitra Customer</span>
                            <span class="inline-flex items-center justify-center min-w-5 h-5 px-1.5 text-[10px] font-extrabold rounded-full {{ ($activeTab ?? 'customer') === 'customer' ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-700' }}" id="badgeCustomerCount">
                                {{ count($customers) }}
                            </span>
                        </button>

                        <!-- Tab 2: Project Terhubung -->
                        <button 
                            type="button" 
                            id="tabProjectBtn"
                            onclick="switchTab('project')"
                            class="tab-btn inline-flex items-center gap-2.5 px-4 py-2 text-xs font-bold rounded-xl transition-all cursor-pointer border {{ ($activeTab ?? 'customer') === 'project' ? 'bg-white border-slate-200/90 shadow-2xs text-slate-900' : 'bg-transparent border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-100/60' }}"
                        >
                            <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                            </svg>
                            <span>Project Terhubung</span>
                            <span class="inline-flex items-center justify-center min-w-5 h-5 px-1.5 text-[10px] font-extrabold rounded-full {{ ($activeTab ?? 'customer') === 'project' ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-700' }}" id="badgeProjectCount">
                                {{ count($projects) }}
                            </span>
                        </button>
                    </div>

                    <!-- SEARCH & FILTER -->
                    <div class="flex items-center gap-2.5">
                        <!-- Search Input -->
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                id="tableSearch" 
                                placeholder="Cari nama PT, PIC, project..." 
                                class="w-full pl-9 pr-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 shadow-2xs"
                            >
                        </div>

                        <!-- Status Filter Dropdown -->
                        <div class="relative">
                            <select 
                                id="statusFilter" 
                                class="appearance-none pl-3 pr-8 py-1.5 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:border-blue-500 shadow-2xs cursor-pointer"
                            >
                                <option value="all">Semua Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="negosiasi">Negosiasi</option>
                                <option value="trial">Trial / PoC</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- NOTIFICATION ALERT (IF FLASH SESSION MESSAGE) -->
                @if (session('success'))
                    <div id="flashAlert" class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-xs text-emerald-800">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" onclick="document.getElementById('flashAlert').remove()" class="text-emerald-600 hover:text-emerald-900 font-bold p-1">✕</button>
                    </div>
                @endif

                <!-- ========================================================================= -->
                <!-- VIEW 1: TAB DAFTAR MITRA CUSTOMER (TABLE FIGMA SCREENSHOT 1)              -->
                <!-- ========================================================================= -->
                <div id="viewCustomerTable" class="{{ ($activeTab ?? 'customer') === 'customer' ? '' : 'hidden' }}">
                    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse" id="customersTable">
                                <thead>
                                    <tr class="border-b border-slate-100 bg-slate-50/60 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        <th scope="col" class="py-3 px-5 whitespace-nowrap">NAMA PERUSAHAAN / MITRA</th>
                                        <th scope="col" class="py-3 px-5 whitespace-nowrap">PIC & KONTAK</th>
                                        <th scope="col" class="py-3 px-5 whitespace-nowrap">ALAMAT KANTOR</th>
                                        <th scope="col" class="py-3 px-5 whitespace-nowrap">STATUS KONTRAK</th>
                                        <th scope="col" class="py-3 px-5 whitespace-nowrap">PROJECT AKTIF</th>
                                        <th scope="col" class="py-3 px-5 text-right whitespace-nowrap">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs text-slate-700" id="customersTableBody">
                                    @forelse ($customers as $c)
                                        <tr 
                                            class="hover:bg-slate-50/80 transition-colors customer-row" 
                                            id="row-customer-{{ $c['id'] }}"
                                            data-id="{{ $c['id'] }}"
                                            data-name="{{ $c['name'] }}"
                                            data-pic="{{ $c['pic_name'] }}"
                                            data-phone="{{ $c['phone'] }}"
                                            data-email="{{ $c['email'] }}"
                                            data-address="{{ $c['address'] }}"
                                            data-note="{{ $c['note'] }}"
                                            data-status="{{ $c['status'] }}"
                                        >
                                            <!-- 1. NAMA PERUSAHAAN / MITRA -->
                                            <td class="py-4 px-5 align-middle">
                                                <div class="space-y-1">
                                                    <span class="font-bold text-slate-900 text-xs block">
                                                        {{ $c['name'] }}
                                                    </span>
                                                    <span class="text-[10px] font-mono text-slate-400 block">
                                                        ID: #{{ $c['code'] }}
                                                    </span>
                                                    @if (!empty($c['note']))
                                                        <span class="inline-block px-2 py-0.5 text-[10px] font-medium rounded bg-blue-50/90 text-blue-700 border border-blue-100/80 max-w-sm truncate">
                                                            {{ $c['note'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- 2. PIC & KONTAK -->
                                            <td class="py-4 px-5 align-middle">
                                                <div class="space-y-0.5">
                                                    <p class="font-semibold text-slate-800 text-xs">{{ $c['pic_name'] }}</p>
                                                    <p class="text-[11px] text-slate-500 font-mono">{{ $c['phone'] }}</p>
                                                    <a href="mailto:{{ $c['email'] }}" class="text-[11px] text-blue-600 hover:text-blue-800 hover:underline block truncate max-w-50">
                                                        {{ $c['email'] }}
                                                    </a>
                                                </div>
                                            </td>

                                            <!-- 3. ALAMAT KANTOR -->
                                            <td class="py-4 px-5 align-middle">
                                                <p class="text-xs text-slate-600 max-w-xs leading-relaxed">
                                                    {{ $c['address'] }}
                                                </p>
                                            </td>

                                            <!-- 4. STATUS KONTRAK -->
                                            <td class="py-4 px-5 align-middle whitespace-nowrap">
                                                @if ($c['status'] === 'Aktif')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/80 rounded-full">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        Aktif
                                                    </span>
                                                @elseif ($c['status'] === 'Negosiasi')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-amber-700 bg-amber-50 border border-amber-200/80 rounded-full">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                        Negosiasi
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-slate-600 bg-slate-100 border border-slate-200 rounded-full">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                        {{ $c['status'] }}
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- 5. PROJECT AKTIF -->
                                            <td class="py-4 px-5 align-middle font-medium text-slate-800 whitespace-nowrap">
                                                {{ $c['active_projects'] ?? 0 }} Project
                                            </td>

                                            <!-- 6. AKSI -->
                                            <td class="py-4 px-5 align-middle text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <!-- Edit Button (Blue Border Pencil) -->
                                                    <button 
                                                        type="button" 
                                                        title="Edit Data Mitra Customer" 
                                                        class="btn-edit-customer inline-flex items-center justify-center w-7 h-7 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 active:scale-95 transition-all cursor-pointer"
                                                        data-id="{{ $c['id'] }}"
                                                        data-name="{{ $c['name'] }}"
                                                        data-pic="{{ $c['pic_name'] }}"
                                                        data-phone="{{ $c['phone'] }}"
                                                        data-email="{{ $c['email'] }}"
                                                        data-address="{{ $c['address'] }}"
                                                        data-note="{{ $c['note'] }}"
                                                        data-status="{{ $c['status'] }}"
                                                    >
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </button>

                                                    <!-- Delete Button (Red Border Trash) -->
                                                    <button 
                                                        type="button" 
                                                        title="Hapus Mitra Customer" 
                                                        class="btn-delete-customer inline-flex items-center justify-center w-7 h-7 rounded-lg border border-rose-200 text-rose-500 hover:bg-rose-50 active:scale-95 transition-all cursor-pointer"
                                                        data-id="{{ $c['id'] }}"
                                                        data-name="{{ $c['name'] }}"
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
                                            <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                                Belum ada mitra customer terdaftar.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- VIEW 2: TAB PROJECT TERHUBUNG (TABLE FIGMA SCREENSHOT 2)                  -->
                <!-- ========================================================================= -->
                <div id="viewProjectTable" class="{{ ($activeTab ?? 'customer') === 'project' ? '' : 'hidden' }}">
                    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse" id="projectsTable">
                                <thead>
                                    <tr class="border-b border-slate-100 bg-slate-50/60 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        <th scope="col" class="py-3 px-5 whitespace-nowrap">NAMA PROJECT</th>
                                        <th scope="col" class="py-3 px-5 whitespace-nowrap">CUSTOMER TERHUBUNG</th>
                                        <th scope="col" class="py-3 px-5 whitespace-nowrap">LOKASI CABANG & KOORDINAT GPS</th>
                                        <th scope="col" class="py-3 px-5 whitespace-nowrap">JENIS LAYANAN</th>
                                        <th scope="col" class="py-3 px-5 whitespace-nowrap">STATUS KERJASAMA</th>
                                        <th scope="col" class="py-3 px-5 text-right whitespace-nowrap">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs text-slate-700" id="projectsTableBody">
                                    @forelse ($projects as $p)
                                        <tr 
                                            class="hover:bg-slate-50/80 transition-colors project-row" 
                                            id="row-project-{{ $p['id'] }}"
                                            data-id="{{ $p['id'] }}"
                                            data-customer-id="{{ $p['customer_id'] }}"
                                            data-customer-name="{{ $p['customer_name'] }}"
                                            data-name="{{ $p['name'] }}"
                                            data-location="{{ $p['branch_location'] }}"
                                            data-service="{{ $p['service_type'] }}"
                                            data-lat="{{ $p['gps_lat'] }}"
                                            data-long="{{ $p['gps_long'] }}"
                                            data-status="{{ $p['status'] }}"
                                            data-desc="{{ $p['description'] ?? $p['subtitle'] }}"
                                        >
                                            <!-- 1. NAMA PROJECT -->
                                            <td class="py-4 px-5 align-middle">
                                                <div class="space-y-0.5">
                                                    <span class="font-bold text-slate-900 text-xs block">
                                                        {{ $p['name'] }}
                                                    </span>
                                                    <span class="text-[11px] text-slate-400 block">
                                                        {{ $p['subtitle'] ?? $p['description'] }}
                                                    </span>
                                                </div>
                                            </td>

                                            <!-- 2. CUSTOMER TERHUBUNG -->
                                            <td class="py-4 px-5 align-middle">
                                                <div class="space-y-0.5">
                                                    <a href="javascript:void(0)" class="font-semibold text-blue-600 hover:text-blue-800 hover:underline text-xs block">
                                                        {{ $p['customer_name'] }}
                                                    </a>
                                                    <span class="text-[11px] text-slate-400 block">
                                                        PIC: {{ $p['pic_name'] ?? 'PIC Customer' }}
                                                    </span>
                                                </div>
                                            </td>

                                            <!-- 3. LOKASI CABANG & KOORDINAT GPS -->
                                            <td class="py-4 px-5 align-middle">
                                                <div class="space-y-1">
                                                    <div class="flex items-center gap-1.5 text-slate-800 font-semibold text-xs">
                                                        <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                                        </svg>
                                                        <span>{{ $p['branch_location'] }}</span>
                                                    </div>
                                                    <a 
                                                        href="https://www.google.com/maps?q={{ $p['gps_lat'] }},{{ $p['gps_long'] }}" 
                                                        target="_blank" 
                                                        rel="noopener noreferrer"
                                                        class="inline-flex items-center gap-1 text-[11px] font-mono text-blue-600 hover:text-blue-800 hover:underline"
                                                    >
                                                        <span>{{ $p['gps_lat'] }}, {{ $p['gps_long'] }}</span>
                                                    </a>
                                                </div>
                                            </td>

                                            <!-- 4. JENIS LAYANAN -->
                                            <td class="py-4 px-5 align-middle whitespace-nowrap">
                                                <span class="inline-block px-2.5 py-1 text-xs font-medium rounded bg-slate-100 text-slate-700 border border-slate-200">
                                                    {{ $p['service_type'] }}
                                                </span>
                                            </td>

                                            <!-- 5. STATUS KERJASAMA -->
                                            <td class="py-4 px-5 align-middle whitespace-nowrap">
                                                @if ($p['status'] === 'Aktif')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/80 rounded-full">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        Aktif
                                                    </span>
                                                @elseif ($p['status'] === 'Trial / PoC')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-amber-700 bg-amber-50 border border-amber-200/80 rounded-full">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                        Trial / PoC
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-slate-600 bg-slate-100 border border-slate-200 rounded-full">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                        {{ $p['status'] }}
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- 6. AKSI -->
                                            <td class="py-4 px-5 align-middle text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <!-- Edit Button (Blue Border Pencil) -->
                                                    <button 
                                                        type="button" 
                                                        title="Edit Data Project" 
                                                        class="btn-edit-project inline-flex items-center justify-center w-7 h-7 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 active:scale-95 transition-all cursor-pointer"
                                                        data-id="{{ $p['id'] }}"
                                                        data-customer-id="{{ $p['customer_id'] }}"
                                                        data-name="{{ $p['name'] }}"
                                                        data-location="{{ $p['branch_location'] }}"
                                                        data-service="{{ $p['service_type'] }}"
                                                        data-lat="{{ $p['gps_lat'] }}"
                                                        data-long="{{ $p['gps_long'] }}"
                                                        data-status="{{ $p['status'] }}"
                                                        data-desc="{{ $p['description'] ?? $p['subtitle'] }}"
                                                    >
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </button>

                                                    <!-- Delete Button (Red Border Trash) -->
                                                    <button 
                                                        type="button" 
                                                        title="Hapus Project" 
                                                        class="btn-delete-project inline-flex items-center justify-center w-7 h-7 rounded-lg border border-rose-200 text-rose-500 hover:bg-rose-50 active:scale-95 transition-all cursor-pointer"
                                                        data-id="{{ $p['id'] }}"
                                                        data-name="{{ $p['name'] }}"
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
                                            <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                                Belum ada project terhubung.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- FOOTER COPY AS IN FIGMA -->
                <div class="py-6 text-center">
                    <p class="text-[11px] text-slate-400">
                        © 2026 PT Netmedia Fiberoptik Lancarmulya • Network Operation Center
                    </p>
                </div>

            </main>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 1: TAMBAH MITRA CUSTOMER BARU (FIGMA MODAL 1)                       -->
    <!-- ========================================================================= -->
    <div id="modalCreateCustomer" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs items-center justify-center p-4 hidden [&:not(.hidden)]:flex">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-lg w-full overflow-hidden transform transition-all animate-in fade-in zoom-in duration-200 my-8">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Tambah Mitra Customer Baru</h3>
                <button type="button" class="close-customer-modal text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="formCreateCustomer" action="{{ route('customers.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4 max-h-[72vh] overflow-y-auto">
                    <!-- NAMA PERUSAHAAN / PT * -->
                    <div>
                        <label for="create_cust_name" class="block text-xs font-bold text-slate-700 mb-1">
                            NAMA PERUSAHAAN / PT <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="create_cust_name" 
                            name="name" 
                            required 
                            placeholder="Contoh: PT. Bank Mandiri (Persero) Tbk"
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                        >
                    </div>

                    <!-- ROW: NAMA PIC & STATUS KONTRAK -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="create_cust_pic" class="block text-xs font-bold text-slate-700 mb-1">
                                NAMA PIC (PENANGGUNG JAWAB) <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="create_cust_pic" 
                                name="pic_name" 
                                required 
                                placeholder="Contoh: Bambang Pratama"
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                            >
                        </div>
                        <div>
                            <label for="create_cust_status" class="block text-xs font-bold text-slate-700 mb-1">
                                STATUS KONTRAK <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select 
                                    id="create_cust_status" 
                                    name="status" 
                                    required 
                                    class="w-full appearance-none pl-3 pr-8 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs cursor-pointer"
                                >
                                    <option value="Aktif" selected>Aktif</option>
                                    <option value="Negosiasi">Negosiasi</option>
                                    <option value="Nonaktif">Nonaktif</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ROW: EMAIL RESMI & KONTAK TELEPON / WA -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="create_cust_email" class="block text-xs font-bold text-slate-700 mb-1">
                                EMAIL RESMI <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="email" 
                                id="create_cust_email" 
                                name="email" 
                                required 
                                placeholder="pic@perusahaan.co.id"
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                            >
                        </div>
                        <div>
                            <label for="create_cust_phone" class="block text-xs font-bold text-slate-700 mb-1">
                                KONTAK TELEPON / WA <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="create_cust_phone" 
                                name="phone" 
                                required 
                                placeholder="021-xxxx atau 0812xxxx"
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                            >
                        </div>
                    </div>

                    <!-- ALAMAT KANTOR PUSAT / CABANG * -->
                    <div>
                        <label for="create_cust_address" class="block text-xs font-bold text-slate-700 mb-1">
                            ALAMAT KANTOR PUSAT / CABANG <span class="text-rose-500">*</span>
                        </label>
                        <textarea 
                            id="create_cust_address" 
                            name="address" 
                            rows="2" 
                            required 
                            placeholder="Alamat lengkap lokasi gedung/kantor..."
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                        ></textarea>
                    </div>

                    <!-- CATATAN TAMBAHAN SLA / LAYANAN -->
                    <div>
                        <label for="create_cust_note" class="block text-xs font-bold text-slate-700 mb-1">
                            CATATAN TAMBAHAN SLA / LAYANAN
                        </label>
                        <input 
                            type="text" 
                            id="create_cust_note" 
                            name="note" 
                            placeholder="Catatan internal tim NOC terkait kontrak..."
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                        >
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" class="close-customer-modal px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer">
                        Simpan Customer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 2: EDIT DATA MITRA CUSTOMER (FIGMA MODAL 2)                         -->
    <!-- ========================================================================= -->
    <div id="modalEditCustomer" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs items-center justify-center p-4 hidden [&:not(.hidden)]:flex">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-lg w-full overflow-hidden transform transition-all animate-in fade-in zoom-in duration-200 my-8">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Edit Data Mitra Customer</h3>
                <button type="button" class="close-customer-edit-modal text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="formEditCustomer" method="POST">
                @csrf
                <input type="hidden" id="edit_cust_id">
                <div class="p-6 space-y-4 max-h-[72vh] overflow-y-auto">
                    <!-- NAMA PERUSAHAAN / PT * -->
                    <div>
                        <label for="edit_cust_name" class="block text-xs font-bold text-slate-700 mb-1">
                            NAMA PERUSAHAAN / PT <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="edit_cust_name" 
                            name="name" 
                            required 
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                        >
                    </div>

                    <!-- ROW: NAMA PIC & STATUS KONTRAK -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="edit_cust_pic" class="block text-xs font-bold text-slate-700 mb-1">
                                NAMA PIC (PENANGGUNG JAWAB) <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="edit_cust_pic" 
                                name="pic_name" 
                                required 
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                            >
                        </div>
                        <div>
                            <label for="edit_cust_status" class="block text-xs font-bold text-slate-700 mb-1">
                                STATUS KONTRAK <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select 
                                    id="edit_cust_status" 
                                    name="status" 
                                    required 
                                    class="w-full appearance-none pl-3 pr-8 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs cursor-pointer"
                                >
                                    <option value="Aktif">Aktif</option>
                                    <option value="Negosiasi">Negosiasi</option>
                                    <option value="Nonaktif">Nonaktif</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ROW: EMAIL RESMI & KONTAK TELEPON / WA -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="edit_cust_email" class="block text-xs font-bold text-slate-700 mb-1">
                                EMAIL RESMI <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="email" 
                                id="edit_cust_email" 
                                name="email" 
                                required 
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                            >
                        </div>
                        <div>
                            <label for="edit_cust_phone" class="block text-xs font-bold text-slate-700 mb-1">
                                KONTAK TELEPON / WA <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="edit_cust_phone" 
                                name="phone" 
                                required 
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                            >
                        </div>
                    </div>

                    <!-- ALAMAT KANTOR PUSAT / CABANG * -->
                    <div>
                        <label for="edit_cust_address" class="block text-xs font-bold text-slate-700 mb-1">
                            ALAMAT KANTOR PUSAT / CABANG <span class="text-rose-500">*</span>
                        </label>
                        <textarea 
                            id="edit_cust_address" 
                            name="address" 
                            rows="2" 
                            required 
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                        ></textarea>
                    </div>

                    <!-- CATATAN TAMBAHAN SLA / LAYANAN -->
                    <div>
                        <label for="edit_cust_note" class="block text-xs font-bold text-slate-700 mb-1">
                            CATATAN TAMBAHAN SLA / LAYANAN
                        </label>
                        <input 
                            type="text" 
                            id="edit_cust_note" 
                            name="note" 
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                        >
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" class="close-customer-edit-modal px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer">
                        Perbarui Customer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 3: TAMBAH PROJECT BARU (FIGMA MODAL 3)                              -->
    <!-- ========================================================================= -->
    <div id="modalCreateProject" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs items-center justify-center p-4 hidden [&:not(.hidden)]:flex">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-lg w-full overflow-hidden transform transition-all animate-in fade-in zoom-in duration-200 my-8">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Tambah Project Baru</h3>
                <button type="button" class="close-project-modal text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="formCreateProject" action="{{ route('projects.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4 max-h-[72vh] overflow-y-auto">
                    <!-- HUBUNGKAN KE MITRA CUSTOMER * -->
                    <div>
                        <label for="create_proj_customer_id" class="block text-xs font-bold text-slate-700 mb-1">
                            HUBUNGKAN KE MITRA CUSTOMER <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select 
                                id="create_proj_customer_id" 
                                name="customer_id" 
                                required 
                                class="w-full appearance-none pl-3 pr-8 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs cursor-pointer"
                            >
                                <option value="" disabled selected>-- Pilih Customer --</option>
                                @foreach ($customers as $c)
                                    <option value="{{ $c['id'] }}">{{ $c['name'] }} ({{ $c['pic_name'] }})</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- NAMA PROJECT * -->
                    <div>
                        <label for="create_proj_name" class="block text-xs font-bold text-slate-700 mb-1">
                            NAMA PROJECT <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="create_proj_name" 
                            name="name" 
                            required 
                            placeholder="Contoh: MPLS Core Interconnect HO Plaza Mandiri"
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                        >
                    </div>

                    <!-- ROW: LOKASI FISIK CABANG & JENIS LAYANAN JARINGAN -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="create_proj_location" class="block text-xs font-bold text-slate-700 mb-1">
                                LOKASI FISIK CABANG <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="create_proj_location" 
                                name="branch_location" 
                                required 
                                placeholder="Contoh: Gedung Plaza Mandiri Lt. 12"
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                            >
                        </div>
                        <div>
                            <label for="create_proj_service" class="block text-xs font-bold text-slate-700 mb-1">
                                JENIS LAYANAN JARINGAN <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="create_proj_service" 
                                name="service_type" 
                                required 
                                placeholder="Dedicated Fiber, MPLS, Metro-E..."
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                            >
                        </div>
                    </div>

                    <!-- KOORDINAT GPS CABANG -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            KOORDINAT GPS CABANG
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <input 
                                type="text" 
                                id="create_proj_lat" 
                                name="gps_lat" 
                                placeholder="Lat (Lintang) : contoh: -6.229748"
                                class="w-full px-3 py-2 text-xs font-mono bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                            >
                            <input 
                                type="text" 
                                id="create_proj_long" 
                                name="gps_long" 
                                placeholder="Long (Bujur) : contoh: 106.816458"
                                class="w-full px-3 py-2 text-xs font-mono bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                            >
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">
                            Dapat disalin langsung dari Google Maps untuk pemetaan jaringan
                        </p>
                    </div>

                    <!-- STATUS KERJA SAMA * -->
                    <div>
                        <label for="create_proj_status" class="block text-xs font-bold text-slate-700 mb-1">
                            STATUS KERJA SAMA <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select 
                                id="create_proj_status" 
                                name="status" 
                                required 
                                class="w-full appearance-none pl-3 pr-8 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs cursor-pointer"
                            >
                                <option value="Aktif" selected>Aktif</option>
                                <option value="Trial / PoC">Trial / PoC</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- DESKRIPSI & SPESIFIKASI LINK -->
                    <div>
                        <label for="create_proj_desc" class="block text-xs font-bold text-slate-700 mb-1">
                            DESKRIPSI & SPESIFIKASI LINK
                        </label>
                        <textarea 
                            id="create_proj_desc" 
                            name="description" 
                            rows="2" 
                            placeholder="Kapasitas bandwidth, interface router, atau catatan teknis..."
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                        ></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" class="close-project-modal px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer">
                        Simpan Project
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 4: EDIT DATA PROJECT (FIGMA MODAL 4)                                -->
    <!-- ========================================================================= -->
    <div id="modalEditProject" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs items-center justify-center p-4 hidden [&:not(.hidden)]:flex">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-lg w-full overflow-hidden transform transition-all animate-in fade-in zoom-in duration-200 my-8">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Edit Data Project</h3>
                <button type="button" class="close-project-edit-modal text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="formEditProject" method="POST">
                @csrf
                <input type="hidden" id="edit_proj_id">
                <div class="p-6 space-y-4 max-h-[72vh] overflow-y-auto">
                    <!-- HUBUNGKAN KE MITRA CUSTOMER * -->
                    <div>
                        <label for="edit_proj_customer_id" class="block text-xs font-bold text-slate-700 mb-1">
                            HUBUNGKAN KE MITRA CUSTOMER <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select 
                                id="edit_proj_customer_id" 
                                name="customer_id" 
                                required 
                                class="w-full appearance-none pl-3 pr-8 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs cursor-pointer"
                            >
                                @foreach ($customers as $c)
                                    <option value="{{ $c['id'] }}">{{ $c['name'] }} ({{ $c['pic_name'] }})</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- NAMA PROJECT * -->
                    <div>
                        <label for="edit_proj_name" class="block text-xs font-bold text-slate-700 mb-1">
                            NAMA PROJECT <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="edit_proj_name" 
                            name="name" 
                            required 
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                        >
                    </div>

                    <!-- ROW: LOKASI FISIK CABANG & JENIS LAYANAN JARINGAN -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="edit_proj_location" class="block text-xs font-bold text-slate-700 mb-1">
                                LOKASI FISIK CABANG <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="edit_proj_location" 
                                name="branch_location" 
                                required 
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                            >
                        </div>
                        <div>
                            <label for="edit_proj_service" class="block text-xs font-bold text-slate-700 mb-1">
                                JENIS LAYANAN JARINGAN <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="edit_proj_service" 
                                name="service_type" 
                                required 
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                            >
                        </div>
                    </div>

                    <!-- KOORDINAT GPS CABANG -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            KOORDINAT GPS CABANG
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <input 
                                type="text" 
                                id="edit_proj_lat" 
                                name="gps_lat" 
                                placeholder="-6.229748"
                                class="w-full px-3 py-2 text-xs font-mono bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                            >
                            <input 
                                type="text" 
                                id="edit_proj_long" 
                                name="gps_long" 
                                placeholder="106.816458"
                                class="w-full px-3 py-2 text-xs font-mono bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                            >
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">
                            Dapat disalin langsung dari Google Maps untuk pemetaan jaringan
                        </p>
                    </div>

                    <!-- STATUS KERJA SAMA * -->
                    <div>
                        <label for="edit_proj_status" class="block text-xs font-bold text-slate-700 mb-1">
                            STATUS KERJA SAMA <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select 
                                id="edit_proj_status" 
                                name="status" 
                                required 
                                class="w-full appearance-none pl-3 pr-8 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs cursor-pointer"
                            >
                                <option value="Aktif">Aktif</option>
                                <option value="Trial / PoC">Trial / PoC</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- DESKRIPSI & SPESIFIKASI LINK -->
                    <div>
                        <label for="edit_proj_desc" class="block text-xs font-bold text-slate-700 mb-1">
                            DESKRIPSI & SPESIFIKASI LINK
                        </label>
                        <textarea 
                            id="edit_proj_desc" 
                            name="description" 
                            rows="2" 
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"
                        ></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" class="close-project-edit-modal px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer">
                        Perbarui Project
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 5: HAPUS CUSTOMER (FIGMA MODAL 5)                                   -->
    <!-- ========================================================================= -->
    <div id="modalDeleteCustomer" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs items-center justify-center p-4 hidden [&:not(.hidden)]:flex">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-sm w-full overflow-hidden transform transition-all animate-in fade-in zoom-in duration-200">
            <div class="p-6 text-center space-y-4">
                <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center mx-auto border border-rose-100">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">
                        Hapus Customer
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        Tindakan ini memerlukan konfirmasi
                    </p>
                    <p class="text-xs text-slate-700 mt-3 font-medium">
                        Apakah Anda yakin ingin menghapus <span class="font-bold text-slate-900" id="delCustomerName">PT. Customer</span> ?
                    </p>
                    <p class="text-[11px] text-rose-500 mt-1 font-semibold">
                        * Data akan dihapus permanen.
                    </p>
                </div>
                <div class="flex items-center gap-2 pt-2">
                    <button 
                        type="button" 
                        class="close-customer-del-modal flex-1 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer"
                    >
                        Batal
                    </button>
                    <button 
                        type="button" 
                        id="btnConfirmDeleteCustomer"
                        class="flex-1 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors cursor-pointer"
                    >
                        Hapus Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 6: HAPUS PROJECT (FIGMA MODAL 6)                                    -->
    <!-- ========================================================================= -->
    <div id="modalDeleteProject" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs items-center justify-center p-4 hidden [&:not(.hidden)]:flex">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-sm w-full overflow-hidden transform transition-all animate-in fade-in zoom-in duration-200">
            <div class="p-6 text-center space-y-4">
                <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center mx-auto border border-rose-100">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">
                        Hapus Project
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        Tindakan ini memerlukan konfirmasi
                    </p>
                    <p class="text-xs text-slate-700 mt-3 font-medium">
                        Apakah Anda yakin ingin menghapus <span class="font-bold text-slate-900" id="delProjectName">Project</span> ?
                    </p>
                    <p class="text-[11px] text-rose-500 mt-1 font-semibold">
                        * Data akan dihapus permanen.
                    </p>
                </div>
                <div class="flex items-center gap-2 pt-2">
                    <button 
                        type="button" 
                        class="close-project-del-modal flex-1 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer"
                    >
                        Batal
                    </button>
                    <button 
                        type="button" 
                        id="btnConfirmDeleteProject"
                        class="flex-1 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors cursor-pointer"
                    >
                        Hapus Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

</body>
</html>
