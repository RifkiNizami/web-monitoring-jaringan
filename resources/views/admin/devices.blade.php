<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventaris Perangkat - NetFly Admin</title>
    <link rel="icon" href="{{ asset('assets/images/netfly-logo.png') }}" type="image/png">

    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full font-sans antialiased text-slate-800 bg-slate-50 flex overflow-hidden">

    <!-- ========================================== -->
    <!-- MAIN WRAPPER (SIDEBAR + CONTENT)           -->
    <!-- ========================================== -->
    <div class="flex w-full h-full overflow-hidden">

        <!-- SIDEBAR -->
        @include('partials.sidebar')

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col h-full overflow-y-auto overflow-x-hidden min-w-0">

            <!-- TOP HEADER BAR -->
            <header
                class="bg-white/80 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 px-6 py-3.5 flex items-center justify-between shrink-0">

                <!-- Left: Hamburger + Breadcrumb -->
                <div class="flex items-center gap-3">
                    <button type="button" id="openSidebar"
                        class="lg:hidden p-2 -ml-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
                        <span>Manajemen Aset</span>
                        <span>/</span>
                        <span class="text-slate-700 font-semibold">Inventaris Perangkat</span>
                    </div>
                </div>

                <!-- Right: Live Clock -->
                <div class="flex items-center gap-3">
                    <div
                        class="hidden sm:flex items-center gap-1.5 text-xs font-mono text-slate-600 bg-slate-100/80 px-2.5 py-1 rounded-md border border-slate-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="liveClock">--:--:-- WIB</span>
                    </div>
                </div>
            </header>

            <!-- MAIN BODY PADDING -->
            <main class="p-6 lg:p-8 space-y-6">

                <!-- FLASH ALERTS -->
                @if (session('success'))
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-emerald-800 text-xs font-semibold">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-between text-rose-800 text-xs font-semibold">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <!-- HEADER SECTION: CATEGORY, TITLE, & ACTION BUTTONS -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="text-[11px] font-semibold text-slate-400 mb-1 flex items-center gap-1.5">
                            <span class="text-slate-500 font-medium">NetFly Asset</span>
                            <span>›</span>
                            <span>Manajemen Inventaris Barang</span>
                        </div>
                        <h1 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight">
                            Inventaris Perangkat & Barang
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Pendataan fisik perangkat keras, spesifikasi merk/model, nomor seri (SN), jumlah stok, dan status ketersediaan barang.
                        </p>
                    </div>

                    <!-- Top Action Button: Export Excel & Tambah Barang Baru -->
                    <div class="flex items-center gap-2.5 shrink-0">
                        <a href="{{ route('devices.exportExcel') }}" id="btnExportDevicesExcel"
                            class="inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer"
                            title="Unduh seluruh data inventaris perangkat ke Excel (.xlsx)">
                            <svg class="w-3.5 h-3.5 text-emerald-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Export ke Excel</span>
                        </a>

                        <a href="{{ route('devices.create') }}" id="btnOpenCreateDevice"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Tambah Barang Baru</span>
                        </a>
                    </div>
                </div>

                <!-- 4 SUMMARY STATS CARDS -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

                    <!-- Card 1: TOTAL DAFTAR BARANG -->
                    <div class="bg-white p-4.5 rounded-xl border border-slate-200/80 shadow-2xs">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tight mb-2">
                            TOTAL DAFTAR BARANG
                        </p>
                        <p class="text-2xl font-black text-slate-900 tracking-tight" id="countTotal">
                            {{ $summary['total_items'] ?? 0 }}
                        </p>
                    </div>

                    <!-- Card 2: TOTAL SELURUH UNIT -->
                    <div class="bg-white p-4.5 rounded-xl border border-slate-200/80 shadow-2xs">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tight mb-2">
                            TOTAL SELURUH UNIT
                        </p>
                        <p class="text-2xl font-black text-blue-600 tracking-tight">
                            <span id="countUnits">{{ $summary['total_units'] ?? 0 }}</span> <span class="text-xs font-semibold text-slate-400">Unit</span>
                        </p>
                    </div>

                    <!-- Card 3: STATUS TERSEDIA -->
                    <div class="bg-white p-4.5 rounded-xl border border-slate-200/80 shadow-2xs">
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">
                                STATUS TERSEDIA (READY)
                            </p>
                        </div>
                        <p class="text-2xl font-black text-emerald-600 tracking-tight" id="countAvailable">
                            {{ $summary['available'] ?? 0 }}
                        </p>
                    </div>

                    <!-- Card 4: TERPAKAI / HABIS -->
                    <div class="bg-white p-4.5 rounded-xl border border-slate-200/80 shadow-2xs">
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">
                                TERPAKAI / STOK HABIS
                            </p>
                        </div>
                        <p class="text-2xl font-black text-slate-800 tracking-tight" id="countInUse">
                            {{ $summary['in_use_or_empty'] ?? 0 }}
                        </p>
                    </div>

                </div>

                <!-- FILTER BAR (SEARCH & TYPE SELECT) -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">

                    <!-- Search Input -->
                    <div class="relative flex-1 max-w-lg">
                        <div
                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="deviceSearch"
                            placeholder="Cari nama barang, merk/tipe seri, atau nomor seri (SN)..."
                            class="w-full pl-10 pr-4 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs transition-all">
                    </div>

                    <!-- Type Filter Dropdown -->
                    <div class="relative shrink-0">
                        <select id="typeFilter"
                            class="appearance-none pl-3.5 pr-8 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs cursor-pointer">
                            <option value="all">Semua Kategori Barang</option>
                            @foreach ($deviceTypes as $type)
                                <option value="{{ strtolower($type) }}">{{ $type }}</option>
                            @endforeach
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

                <!-- TABLE: INVENTARIS BARANG & PERANGKAT -->
                <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse" id="devicesTable">

                            <!-- Table Header -->
                            <thead>
                                <tr
                                    class="border-b border-slate-100 bg-slate-50/60 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    <th scope="col" class="py-3.5 px-5 whitespace-nowrap">FOTO & NAMA BARANG</th>
                                    <th scope="col" class="py-3.5 px-5 whitespace-nowrap">MERK / TIPE SERI</th>
                                    <th scope="col" class="py-3.5 px-5 whitespace-nowrap">NOMOR SERI (SN)</th>
                                    <th scope="col" class="py-3.5 px-5 whitespace-nowrap">JUMLAH BARANG</th>
                                    <th scope="col" class="py-3.5 px-5 whitespace-nowrap">STATUS KETERSEDIAAN</th>
                                    <th scope="col" class="py-3.5 px-5 text-right whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>

                            <!-- Table Body -->
                            <tbody class="divide-y divide-slate-100 text-xs text-slate-700" id="devicesTableBody">

                                @forelse ($devices as $device)
                                    <tr class="hover:bg-slate-50/80 transition-colors device-row"
                                        data-id="{{ $device['id'] }}"
                                        data-hostname="{{ $device['hostname'] }}"
                                        data-type="{{ $device['type'] }}"
                                        data-brand="{{ $device['brand_model'] }}"
                                        data-sn="{{ $device['serial_number'] }}"
                                        data-qty="{{ $device['quantity'] }}"
                                        data-status="{{ $device['status'] }}"
                                        data-photo="{{ $device['photo'] ?? '' }}">

                                        <!-- 1. FOTO & NAMA BARANG -->
                                        <td class="py-3.5 px-5 align-middle">
                                            <div class="flex items-center gap-3.5">
                                                <div class="w-11 h-11 rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden shrink-0">
                                                    @if (!empty($device['photo']))
                                                        <img src="{{ $device['photo'] }}" alt="{{ $device['hostname'] }}" class="w-full h-full object-cover">
                                                    @else
                                                        <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="space-y-1">
                                                    <span class="font-bold text-slate-900 font-mono tracking-tight text-xs block">
                                                        {{ $device['hostname'] }}
                                                    </span>
                                                    <span class="inline-block px-2 py-0.5 text-[9px] font-semibold rounded bg-slate-100 text-slate-700 border border-slate-200">
                                                        {{ $device['type'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- 2. MERK / TIPE SERI -->
                                        <td class="py-3.5 px-5 align-middle font-semibold text-slate-800 text-xs whitespace-nowrap">
                                            {{ $device['brand_model'] }}
                                        </td>

                                        <!-- 3. NOMOR SERI (SN) -->
                                        <td class="py-3.5 px-5 align-middle font-mono text-xs font-medium text-blue-600 whitespace-nowrap">
                                            {{ $device['serial_number'] }}
                                        </td>

                                        <!-- 4. JUMLAH BARANG -->
                                        <td class="py-3.5 px-5 align-middle whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-800 tabular-nums">
                                                {{ $device['quantity'] }} Unit
                                            </span>
                                        </td>

                                        <!-- 5. STATUS KETERSEDIAAN -->
                                        <td class="py-3.5 px-5 align-middle whitespace-nowrap">
                                            @if ($device['status_state'] === 'available')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/80 rounded-full">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Tersedia
                                                </span>
                                            @elseif ($device['status_state'] === 'in_use')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-blue-700 bg-blue-50 border border-blue-200/80 rounded-full">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                    Terpakai
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-rose-700 bg-rose-50 border border-rose-200/80 rounded-full">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                    Habis
                                                </span>
                                            @endif
                                        </td>

                                        <!-- 6. AKSI (EDIT & HAPUS) -->
                                        <td class="py-3.5 px-5 align-middle text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <!-- Tombol Edit (Pindah ke halaman edit.blade.php) -->
                                                <a href="{{ route('devices.edit', $device['id']) }}" title="Edit Data Barang"
                                                    class="inline-flex items-center justify-center w-7 h-7 rounded-lg border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-100 active:scale-95 transition-all cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="1.8"
                                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </a>

                                                <!-- Tombol Hapus (Membuka konfirmasi delete_modal.blade.php) -->
                                                <button type="button" title="Hapus Data Barang"
                                                    data-device-id="{{ $device['id'] }}"
                                                    data-device-hostname="{{ $device['hostname'] }} ({{ $device['serial_number'] }})"
                                                    data-delete-url="{{ route('devices.destroy', $device['id']) }}"
                                                    class="btn-delete-device inline-flex items-center justify-center w-7 h-7 rounded-lg border border-rose-200/90 text-rose-500 hover:text-rose-600 hover:bg-rose-50 active:scale-95 transition-all cursor-pointer">
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
                                        <td colspan="6" class="py-10 text-center text-xs text-slate-400">
                                            Belum ada data barang perangkat yang terdaftar di inventaris.
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

    {{-- Modal Konfirmasi Hapus Barang Inventaris --}}
    @include('admin.devices.delete_modal')

</body>

</html>
