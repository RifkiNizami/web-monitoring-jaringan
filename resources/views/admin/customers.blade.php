<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management — Portal Manajemen Jaringan NetFly</title>
    <link rel="icon" href="{{ asset('assets/images/netfly-logo.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap"
        rel="stylesheet">

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
            <header
                class="bg-white border-b border-slate-200/80 px-6 py-3.5 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger -->
                    <button id="openSidebar"
                        class="lg:hidden p-1.5 -ml-1 text-slate-600 hover:text-slate-900 rounded-md hover:bg-slate-100 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Breadcrumb -->
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                        <span class="tracking-wide uppercase">DATA & KEMITRAAN</span>
                        <span>›</span>
                        <span class="text-slate-800 font-bold">Customer Management</span>
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
                            Customer Management
                        </h1>
                    </div>

                    <!-- Top 2 Action Buttons -->
                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        <!-- 1. Tambah Customer Rumahan (Dark Button) -->
                        <button type="button" id="btnOpenCreateCustomer"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Tambah Customer Rumahan</span>
                        </button>

                        <!-- 2. Tambah Customer Perusahaan (White Border Button) -->
                        <button type="button" id="btnOpenCreateProject"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-800 border border-slate-200 text-xs font-bold rounded-lg shadow-2xs active:scale-95 transition-all cursor-pointer">
                            <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Tambah Customer Perusahaan</span>
                        </button>
                    </div>
                </div>

                <!-- TABS CONTAINER & REALTIME FILTER ROW -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-1">

                    <!-- 2 TABS: CUSTOMER RUMAHAN vs CUSTOMER PERUSAHAAN -->
                    <div class="flex items-center gap-2">
                        <!-- Tab 1: Customer Rumahan -->
                        <button type="button" id="tabCustomerBtn" onclick="switchTab('customer')"
                            class="tab-btn inline-flex items-center gap-2.5 px-4 py-2 text-xs font-bold rounded-xl transition-all cursor-pointer border {{ ($activeTab ?? 'customer') === 'customer' ? 'bg-white border-slate-200/90 shadow-2xs text-slate-900' : 'bg-transparent border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-100/60' }}">
                            <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Customer Rumahan</span>
                            <span
                                class="inline-flex items-center justify-center min-w-5 h-5 px-1.5 text-[10px] font-extrabold rounded-full {{ ($activeTab ?? 'customer') === 'customer' ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-700' }}"
                                id="badgeCustomerCount">
                                {{ count($customers) }}
                            </span>
                        </button>

                        <!-- Tab 2: Customer Perusahaan -->
                        <button type="button" id="tabProjectBtn" onclick="switchTab('project')"
                            class="tab-btn inline-flex items-center gap-2.5 px-4 py-2 text-xs font-bold rounded-xl transition-all cursor-pointer border {{ ($activeTab ?? 'customer') === 'project' ? 'bg-white border-slate-200/90 shadow-2xs text-slate-900' : 'bg-transparent border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-100/60' }}">
                            <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Customer Perusahaan</span>
                            <span
                                class="inline-flex items-center justify-center min-w-5 h-5 px-1.5 text-[10px] font-extrabold rounded-full {{ ($activeTab ?? 'customer') === 'project' ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-700' }}"
                                id="badgeProjectCount">
                                {{ count($projects) }}
                            </span>
                        </button>
                    </div>

                    <!-- SEARCH & FILTER -->
                    <div class="flex items-center gap-2.5">
                        <!-- Search Input -->
                        <div class="relative w-full sm:w-64">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" id="tableSearch" placeholder="Cari nama customer, PT, PIC..."
                                class="w-full pl-9 pr-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 shadow-2xs">
                        </div>

                        <!-- Status Filter Dropdown -->
                        <div class="relative">
                            <select id="statusFilter"
                                class="appearance-none pl-3 pr-8 py-1.5 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:border-blue-500 shadow-2xs cursor-pointer">
                                <option value="all">Semua Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="negosiasi">Negosiasi</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- NOTIFICATION ALERT (IF FLASH SESSION MESSAGE) -->
                @if (session('success'))
                    <div id="flashAlert"
                        class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-xs text-emerald-800">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" onclick="document.getElementById('flashAlert').remove()"
                            class="text-emerald-600 hover:text-emerald-900 font-bold p-1">✕</button>
                    </div>
                @endif

                <!-- ========================================================================= -->
                <!-- VIEW 1: TAB CUSTOMER RUMAHAN                                              -->
                <!-- ========================================================================= -->
                <div id="viewCustomerTable" class="{{ ($activeTab ?? 'customer') === 'customer' ? '' : 'hidden' }}">
                    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse" id="customersTable">
                                <thead>
                                    <tr
                                        class="border-b border-slate-100 bg-slate-50/60 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        <th scope="col" class="py-3 px-5 whitespace-nowrap">NAMA CUSTOMER</th>
                                        <th scope="col" class="py-3 px-5 whitespace-nowrap">KONTAK TELEPON / WA & EMAIL
                                        </th>
                                        <th scope="col" class="py-3 px-5 whitespace-nowrap">ALAMAT RUMAH</th>
                                        <th scope="col" class="py-3 px-5 whitespace-nowrap">STATUS KONTRAK</th>
                                        <th scope="col" class="py-3 px-5 whitespace-nowrap">CATATAN / LAYANAN</th>
                                        <th scope="col" class="py-3 px-5 text-right whitespace-nowrap">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs text-slate-700" id="customersTableBody">
                                    @forelse ($customers as $c)
                                        <tr class="hover:bg-slate-50/80 transition-colors customer-row"
                                            id="row-customer-{{ $c['id'] }}" data-id="{{ $c['id'] }}"
                                            data-name="{{ $c['name'] }}" data-phone="{{ $c['phone'] }}"
                                            data-email="{{ $c['email'] }}" data-address="{{ $c['address'] }}"
                                            data-note="{{ $c['note'] }}" data-status="{{ $c['status'] }}">
                                            <!-- 1. NAMA CUSTOMER -->
                                            <td class="py-4 px-5 align-middle">
                                                <div class="space-y-1">
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="font-bold text-slate-900 text-xs block">
                                                            {{ $c['name'] }}
                                                        </span>
                                                        <span
                                                            class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                            RUMAHAN
                                                        </span>
                                                    </div>
                                                    <span class="text-[10px] font-mono text-slate-400 block">
                                                        ID: #{{ $c['code'] }}
                                                    </span>
                                                </div>
                                            </td>

                                            <!-- 2. KONTAK TELEPON / WA & EMAIL (Tanpa Nama PIC) -->
                                            <td class="py-4 px-5 align-middle">
                                                <div class="space-y-0.5">
                                                    <p class="font-semibold text-slate-800 text-xs font-mono">
                                                        {{ $c['phone'] }}
                                                    </p>
                                                    <a href="mailto:{{ $c['email'] }}"
                                                        class="text-[11px] text-blue-600 hover:text-blue-800 hover:underline block truncate max-w-50">
                                                        {{ $c['email'] }}
                                                    </a>
                                                </div>
                                            </td>

                                            <!-- 3. ALAMAT RUMAH -->
                                            <td class="py-4 px-5 align-middle">
                                                <p class="text-xs text-slate-600 max-w-xs leading-relaxed">
                                                    {{ $c['address'] }}
                                                </p>
                                            </td>

                                            <!-- 4. STATUS KONTRAK -->
                                            <td class="py-4 px-5 align-middle whitespace-nowrap">
                                                @if ($c['status'] === 'Aktif')
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/80 rounded-full">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        Aktif
                                                    </span>
                                                @elseif ($c['status'] === 'Negosiasi')
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-amber-700 bg-amber-50 border border-amber-200/80 rounded-full">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                        Negosiasi
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-slate-600 bg-slate-100 border border-slate-200 rounded-full">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                        {{ $c['status'] }}
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- 5. CATATAN / LAYANAN -->
                                            <td class="py-4 px-5 align-middle">
                                                @if (!empty($c['note']))
                                                    <span
                                                        class="inline-block px-2.5 py-1 text-[11px] font-medium rounded-lg bg-blue-50/90 text-blue-700 border border-blue-100/80 max-w-xs">
                                                        {{ $c['note'] }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 text-xs">-</span>
                                                @endif
                                            </td>

                                            <!-- 6. AKSI -->
                                            <td class="py-4 px-5 align-middle text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <!-- Edit Button (Blue Border Pencil) -->
                                                    <button type="button" title="Edit Data Customer Rumahan"
                                                        class="btn-edit-customer inline-flex items-center justify-center w-7 h-7 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 active:scale-95 transition-all cursor-pointer"
                                                        data-id="{{ $c['id'] }}" data-name="{{ $c['name'] }}"
                                                        data-phone="{{ $c['phone'] }}" data-email="{{ $c['email'] }}"
                                                        data-address="{{ $c['address'] }}" data-note="{{ $c['note'] }}"
                                                        data-status="{{ $c['status'] }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="1.8"
                                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </button>

                                                    <!-- Delete Button (Red Border Trash) -->
                                                    <button type="button" title="Hapus Customer Rumahan"
                                                        class="btn-delete-customer inline-flex items-center justify-center w-7 h-7 rounded-lg border border-rose-200 text-rose-500 hover:bg-rose-50 active:scale-95 transition-all cursor-pointer"
                                                        data-id="{{ $c['id'] }}" data-name="{{ $c['name'] }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="1.8"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                                Belum ada customer rumahan terdaftar.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- VIEW 2: TAB CUSTOMER PERUSAHAAN                                           -->
                <!-- ========================================================================= -->
                <div id="viewProjectTable" class="{{ ($activeTab ?? 'customer') === 'project' ? '' : 'hidden' }}">
                    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse" id="projectsTable">
                                <thead>
                                    <tr
                                        class="border-b border-slate-100 bg-slate-50/60 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        <th scope="col" class="py-3 px-5 whitespace-nowrap">NAMA PERUSAHAAN / PT</th>
                                        <th scope="col" class="py-3 px-5 whitespace-nowrap">PIC (PENANGGUNG JAWAB) &
                                            KONTAK</th>
                                        <th scope="col" class="py-3 px-5 whitespace-nowrap">ALAMAT KANTOR PUSAT / CABANG
                                        </th>
                                        <th scope="col" class="py-3 px-5 whitespace-nowrap">STATUS KONTRAK</th>
                                        <th scope="col" class="py-3 px-5 whitespace-nowrap">CATATAN SLA / LAYANAN</th>
                                        <th scope="col" class="py-3 px-5 text-right whitespace-nowrap">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs text-slate-700" id="projectsTableBody">
                                    @forelse ($projects as $p)
                                        <tr class="hover:bg-slate-50/80 transition-colors project-row"
                                            id="row-project-{{ $p['id'] }}" data-id="{{ $p['id'] }}"
                                            data-name="{{ $p['name'] }}" data-pic="{{ $p['pic_name'] }}"
                                            data-phone="{{ $p['phone'] }}" data-email="{{ $p['email'] }}"
                                            data-address="{{ $p['address'] }}" data-note="{{ $p['note'] }}"
                                            data-status="{{ $p['status'] }}">
                                            <!-- 1. NAMA PERUSAHAAN / PT -->
                                            <td class="py-4 px-5 align-middle">
                                                <div class="space-y-1">
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="font-bold text-slate-900 text-xs block">
                                                            {{ $p['name'] }}
                                                        </span>
                                                        <span
                                                            class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                                            PERUSAHAAN
                                                        </span>
                                                    </div>
                                                    <span class="text-[10px] font-mono text-slate-400 block">
                                                        ID: #{{ $p['code'] ?? ('CUST-000' . $p['id']) }}
                                                    </span>
                                                </div>
                                            </td>

                                            <!-- 2. PIC & KONTAK -->
                                            <td class="py-4 px-5 align-middle">
                                                <div class="space-y-0.5">
                                                    <p class="font-semibold text-slate-800 text-xs">{{ $p['pic_name'] }}</p>
                                                    <p class="text-[11px] text-slate-500 font-mono">{{ $p['phone'] }}</p>
                                                    <a href="mailto:{{ $p['email'] }}"
                                                        class="text-[11px] text-blue-600 hover:text-blue-800 hover:underline block truncate max-w-50">
                                                        {{ $p['email'] }}
                                                    </a>
                                                </div>
                                            </td>

                                            <!-- 3. ALAMAT KANTOR PUSAT / CABANG -->
                                            <td class="py-4 px-5 align-middle">
                                                <p class="text-xs text-slate-600 max-w-xs leading-relaxed">
                                                    {{ $p['address'] }}
                                                </p>
                                            </td>

                                            <!-- 4. STATUS KONTRAK -->
                                            <td class="py-4 px-5 align-middle whitespace-nowrap">
                                                @if ($p['status'] === 'Aktif')
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/80 rounded-full">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        Aktif
                                                    </span>
                                                @elseif ($p['status'] === 'Negosiasi')
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-amber-700 bg-amber-50 border border-amber-200/80 rounded-full">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                        Negosiasi
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-slate-600 bg-slate-100 border border-slate-200 rounded-full">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                        {{ $p['status'] }}
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- 5. CATATAN SLA / LAYANAN -->
                                            <td class="py-4 px-5 align-middle">
                                                @if (!empty($p['note']))
                                                    <span
                                                        class="inline-block px-2.5 py-1 text-[11px] font-medium rounded-lg bg-blue-50/90 text-blue-700 border border-blue-100/80 max-w-xs">
                                                        {{ $p['note'] }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 text-xs">-</span>
                                                @endif
                                            </td>

                                            <!-- 6. AKSI -->
                                            <td class="py-4 px-5 align-middle text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <!-- Edit Button (Blue Border Pencil) -->
                                                    <button type="button" title="Edit Data Customer Perusahaan"
                                                        class="btn-edit-project inline-flex items-center justify-center w-7 h-7 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 active:scale-95 transition-all cursor-pointer"
                                                        data-id="{{ $p['id'] }}" data-name="{{ $p['name'] }}"
                                                        data-pic="{{ $p['pic_name'] }}" data-phone="{{ $p['phone'] }}"
                                                        data-email="{{ $p['email'] }}" data-address="{{ $p['address'] }}"
                                                        data-note="{{ $p['note'] }}" data-status="{{ $p['status'] }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="1.8"
                                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </button>

                                                    <!-- Delete Button (Red Border Trash) -->
                                                    <button type="button" title="Hapus Customer Perusahaan"
                                                        class="btn-delete-project inline-flex items-center justify-center w-7 h-7 rounded-lg border border-rose-200 text-rose-500 hover:bg-rose-50 active:scale-95 transition-all cursor-pointer"
                                                        data-id="{{ $p['id'] }}" data-name="{{ $p['name'] }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="1.8"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                                Belum ada customer perusahaan terdaftar.
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
    <!-- MODAL 1: TAMBAH CUSTOMER RUMAHAN                                          -->
    <!-- (Nama Perusahaan/PT -> Nama Customer, Nama PIC Dihilangkan,               -->
    <!--  Alamat Kantor -> Alamat Rumah, Kontak WA & Catatan Layanan Tetap)        -->
    <!-- ========================================================================= -->
    <div id="modalCreateCustomer"
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs items-center justify-center p-4 hidden [&:not(.hidden)]:flex">
        <div
            class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-lg w-full overflow-hidden transform transition-all animate-in fade-in zoom-in duration-200 my-8">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Tambah Customer Rumahan</h3>
                <button type="button"
                    class="close-customer-modal text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="formCreateCustomer" action="{{ route('customers.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4 max-h-[72vh] overflow-y-auto">
                    <!-- NAMA CUSTOMER * -->
                    <div>
                        <label for="create_cust_name" class="block text-xs font-bold text-slate-700 mb-1">
                            NAMA CUSTOMER <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="create_cust_name" name="name" required placeholder="Contoh: Budi Santoso"
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs">
                    </div>

                    <!-- STATUS KONTRAK * (Nama PIC Dihilangkan sesuai instruksi) -->
                    <div>
                        <label for="create_cust_status" class="block text-xs font-bold text-slate-700 mb-1">
                            STATUS KONTRAK <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="create_cust_status" name="status" required
                                class="w-full appearance-none pl-3 pr-8 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs cursor-pointer">
                                <option value="Aktif" selected>Aktif</option>
                                <option value="Negosiasi">Negosiasi</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- ROW: EMAIL RESMI & KONTAK TELEPON / WA -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="create_cust_email" class="block text-xs font-bold text-slate-700 mb-1">
                                EMAIL RESMI <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" id="create_cust_email" name="email" required
                                placeholder="pelanggan@email.com"
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs">
                        </div>
                        <div>
                            <label for="create_cust_phone" class="block text-xs font-bold text-slate-700 mb-1">
                                KONTAK TELEPON / WA <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="create_cust_phone" name="phone" required
                                placeholder="021-xxxx atau 0812xxxx"
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs">
                        </div>
                    </div>

                    <!-- ALAMAT RUMAH * -->
                    <div>
                        <label for="create_cust_address" class="block text-xs font-bold text-slate-700 mb-1">
                            ALAMAT RUMAH <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="create_cust_address" name="address" rows="2" required
                            placeholder="Alamat lengkap lokasi rumah..."
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"></textarea>
                    </div>

                    <!-- CATATAN TAMBAHAN SLA / LAYANAN -->
                    <div>
                        <label for="create_cust_note" class="block text-xs font-bold text-slate-700 mb-1">
                            CATATAN TAMBAHAN SLA / LAYANAN
                        </label>
                        <input type="text" id="create_cust_note" name="note"
                            placeholder="Catatan internal tim NOC terkait layanan..."
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs">
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button"
                        class="close-customer-modal px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer">
                        Simpan Customer Rumahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 2: EDIT DATA CUSTOMER RUMAHAN                                       -->
    <!-- ========================================================================= -->
    <div id="modalEditCustomer"
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs items-center justify-center p-4 hidden [&:not(.hidden)]:flex">
        <div
            class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-lg w-full overflow-hidden transform transition-all animate-in fade-in zoom-in duration-200 my-8">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Edit Data Customer Rumahan</h3>
                <button type="button"
                    class="close-customer-edit-modal text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="formEditCustomer" method="POST">
                @csrf
                <input type="hidden" id="edit_cust_id">
                <div class="p-6 space-y-4 max-h-[72vh] overflow-y-auto">
                    <!-- NAMA CUSTOMER * -->
                    <div>
                        <label for="edit_cust_name" class="block text-xs font-bold text-slate-700 mb-1">
                            NAMA CUSTOMER <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="edit_cust_name" name="name" required
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs">
                    </div>

                    <!-- STATUS KONTRAK * -->
                    <div>
                        <label for="edit_cust_status" class="block text-xs font-bold text-slate-700 mb-1">
                            STATUS KONTRAK <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="edit_cust_status" name="status" required
                                class="w-full appearance-none pl-3 pr-8 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs cursor-pointer">
                                <option value="Aktif">Aktif</option>
                                <option value="Negosiasi">Negosiasi</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- ROW: EMAIL RESMI & KONTAK TELEPON / WA -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="edit_cust_email" class="block text-xs font-bold text-slate-700 mb-1">
                                EMAIL RESMI <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" id="edit_cust_email" name="email" required
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs">
                        </div>
                        <div>
                            <label for="edit_cust_phone" class="block text-xs font-bold text-slate-700 mb-1">
                                KONTAK TELEPON / WA <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="edit_cust_phone" name="phone" required
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs">
                        </div>
                    </div>

                    <!-- ALAMAT RUMAH * -->
                    <div>
                        <label for="edit_cust_address" class="block text-xs font-bold text-slate-700 mb-1">
                            ALAMAT RUMAH <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="edit_cust_address" name="address" rows="2" required
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"></textarea>
                    </div>

                    <!-- CATATAN TAMBAHAN SLA / LAYANAN -->
                    <div>
                        <label for="edit_cust_note" class="block text-xs font-bold text-slate-700 mb-1">
                            CATATAN TAMBAHAN SLA / LAYANAN
                        </label>
                        <input type="text" id="edit_cust_note" name="note"
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs">
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button"
                        class="close-customer-edit-modal px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer">
                        Perbarui Customer Rumahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 3: TAMBAH CUSTOMER PERUSAHAAN                                       -->
    <!-- (Sesuai customer rumahan namun untuk perusahaan: Nama Perusahaan / PT,    -->
    <!--  Nama PIC (Penanggung Jawab) tidak dihilangkan, dan Alamat Kantor Pusat   -->
    <!--  / Cabang ada)                                                            -->
    <!-- ========================================================================= -->
    <div id="modalCreateProject"
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs items-center justify-center p-4 hidden [&:not(.hidden)]:flex">
        <div
            class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-lg w-full overflow-hidden transform transition-all animate-in fade-in zoom-in duration-200 my-8">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Tambah Customer Perusahaan</h3>
                <button type="button"
                    class="close-project-modal text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="formCreateProject" action="{{ route('projects.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4 max-h-[72vh] overflow-y-auto">
                    <!-- NAMA PERUSAHAAN / PT * -->
                    <div>
                        <label for="create_proj_name" class="block text-xs font-bold text-slate-700 mb-1">
                            NAMA PERUSAHAAN / PT <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="create_proj_name" name="name" required
                            placeholder="Contoh: PT. Bank Mandiri (Persero) Tbk"
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs">
                    </div>

                    <!-- ROW: NAMA PIC (PENANGGUNG JAWAB) & STATUS KONTRAK -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="create_proj_pic" class="block text-xs font-bold text-slate-700 mb-1">
                                NAMA PIC (PENANGGUNG JAWAB) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="create_proj_pic" name="pic_name" required
                                placeholder="Contoh: Bambang Pratama"
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs">
                        </div>
                        <div>
                            <label for="create_proj_status" class="block text-xs font-bold text-slate-700 mb-1">
                                STATUS KONTRAK <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="create_proj_status" name="status" required
                                    class="w-full appearance-none pl-3 pr-8 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs cursor-pointer">
                                    <option value="Aktif" selected>Aktif</option>
                                    <option value="Negosiasi">Negosiasi</option>
                                    <option value="Nonaktif">Nonaktif</option>
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ROW: EMAIL RESMI & KONTAK TELEPON / WA -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="create_proj_email" class="block text-xs font-bold text-slate-700 mb-1">
                                EMAIL RESMI <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" id="create_proj_email" name="email" required
                                placeholder="pic@perusahaan.co.id"
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs">
                        </div>
                        <div>
                            <label for="create_proj_phone" class="block text-xs font-bold text-slate-700 mb-1">
                                KONTAK TELEPON / WA <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="create_proj_phone" name="phone" required
                                placeholder="021-xxxx atau 0812xxxx"
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs">
                        </div>
                    </div>

                    <!-- ALAMAT KANTOR PUSAT / CABANG * -->
                    <div>
                        <label for="create_proj_address" class="block text-xs font-bold text-slate-700 mb-1">
                            ALAMAT KANTOR PUSAT / CABANG <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="create_proj_address" name="address" rows="2" required
                            placeholder="Alamat lengkap lokasi gedung/kantor pusat atau cabang..."
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"></textarea>
                    </div>

                    <!-- CATATAN TAMBAHAN SLA / LAYANAN -->
                    <div>
                        <label for="create_proj_note" class="block text-xs font-bold text-slate-700 mb-1">
                            CATATAN TAMBAHAN SLA / LAYANAN
                        </label>
                        <input type="text" id="create_proj_note" name="note"
                            placeholder="Catatan internal tim NOC terkait kontrak..."
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs">
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button"
                        class="close-project-modal px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer">
                        Simpan Customer Perusahaan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 4: EDIT DATA CUSTOMER PERUSAHAAN                                    -->
    <!-- ========================================================================= -->
    <div id="modalEditProject"
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs items-center justify-center p-4 hidden [&:not(.hidden)]:flex">
        <div
            class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-lg w-full overflow-hidden transform transition-all animate-in fade-in zoom-in duration-200 my-8">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Edit Data Customer Perusahaan</h3>
                <button type="button"
                    class="close-project-edit-modal text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="formEditProject" method="POST">
                @csrf
                <input type="hidden" id="edit_proj_id">
                <div class="p-6 space-y-4 max-h-[72vh] overflow-y-auto">
                    <!-- NAMA PERUSAHAAN / PT * -->
                    <div>
                        <label for="edit_proj_name" class="block text-xs font-bold text-slate-700 mb-1">
                            NAMA PERUSAHAAN / PT <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="edit_proj_name" name="name" required
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs">
                    </div>

                    <!-- ROW: NAMA PIC & STATUS KONTRAK -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="edit_proj_pic" class="block text-xs font-bold text-slate-700 mb-1">
                                NAMA PIC (PENANGGUNG JAWAB) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="edit_proj_pic" name="pic_name" required
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs">
                        </div>
                        <div>
                            <label for="edit_proj_status" class="block text-xs font-bold text-slate-700 mb-1">
                                STATUS KONTRAK <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="edit_proj_status" name="status" required
                                    class="w-full appearance-none pl-3 pr-8 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs cursor-pointer">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Negosiasi">Negosiasi</option>
                                    <option value="Nonaktif">Nonaktif</option>
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ROW: EMAIL RESMI & KONTAK TELEPON / WA -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="edit_proj_email" class="block text-xs font-bold text-slate-700 mb-1">
                                EMAIL RESMI <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" id="edit_proj_email" name="email" required
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs">
                        </div>
                        <div>
                            <label for="edit_proj_phone" class="block text-xs font-bold text-slate-700 mb-1">
                                KONTAK TELEPON / WA <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="edit_proj_phone" name="phone" required
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs">
                        </div>
                    </div>

                    <!-- ALAMAT KANTOR PUSAT / CABANG * -->
                    <div>
                        <label for="edit_proj_address" class="block text-xs font-bold text-slate-700 mb-1">
                            ALAMAT KANTOR PUSAT / CABANG <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="edit_proj_address" name="address" rows="2" required
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs"></textarea>
                    </div>

                    <!-- CATATAN TAMBAHAN SLA / LAYANAN -->
                    <div>
                        <label for="edit_proj_note" class="block text-xs font-bold text-slate-700 mb-1">
                            CATATAN TAMBAHAN SLA / LAYANAN
                        </label>
                        <input type="text" id="edit_proj_note" name="note"
                            class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs">
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button"
                        class="close-project-edit-modal px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer">
                        Perbarui Customer Perusahaan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 5: HAPUS CUSTOMER RUMAHAN                                           -->
    <!-- ========================================================================= -->
    <div id="modalDeleteCustomer"
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs items-center justify-center p-4 hidden [&:not(.hidden)]:flex">
        <div
            class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-sm w-full overflow-hidden transform transition-all animate-in fade-in zoom-in duration-200">
            <div class="p-6 text-center space-y-4">
                <div
                    class="w-12 h-12 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center mx-auto border border-rose-100">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">
                        Hapus Customer Rumahan
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        Tindakan ini memerlukan konfirmasi
                    </p>
                    <p class="text-xs text-slate-700 mt-3 font-medium">
                        Apakah Anda yakin ingin menghapus <span class="font-bold text-slate-900"
                            id="delCustomerName">Customer</span> ?
                    </p>
                    <p class="text-[11px] text-rose-500 mt-1 font-semibold">
                        * Data akan dihapus permanen.
                    </p>
                </div>
                <div class="flex items-center gap-2 pt-2">
                    <button type="button"
                        class="close-customer-del-modal flex-1 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="button" id="btnConfirmDeleteCustomer"
                        class="flex-1 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors cursor-pointer">
                        Hapus Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 6: HAPUS CUSTOMER PERUSAHAAN                                        -->
    <!-- ========================================================================= -->
    <div id="modalDeleteProject"
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs items-center justify-center p-4 hidden [&:not(.hidden)]:flex">
        <div
            class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-sm w-full overflow-hidden transform transition-all animate-in fade-in zoom-in duration-200">
            <div class="p-6 text-center space-y-4">
                <div
                    class="w-12 h-12 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center mx-auto border border-rose-100">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">
                        Hapus Customer Perusahaan
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        Tindakan ini memerlukan konfirmasi
                    </p>
                    <p class="text-xs text-slate-700 mt-3 font-medium">
                        Apakah Anda yakin ingin menghapus <span class="font-bold text-slate-900"
                            id="delProjectName">Perusahaan</span> ?
                    </p>
                    <p class="text-[11px] text-rose-500 mt-1 font-semibold">
                        * Data akan dihapus permanen.
                    </p>
                </div>
                <div class="flex items-center gap-2 pt-2">
                    <button type="button"
                        class="close-project-del-modal flex-1 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="button" id="btnConfirmDeleteProject"
                        class="flex-1 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors cursor-pointer">
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