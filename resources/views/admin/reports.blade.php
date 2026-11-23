<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Report Generator — Portal Manajemen Jaringan NetFly</title>
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
                        <span class="tracking-wide uppercase">LAPORAN & AUDIT</span>
                        <span>›</span>
                        <span class="text-slate-800 font-bold">Report Generator</span>
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
                
                <!-- HEADER SECTION: CATEGORY, TITLE, & EXPORT ACTION BUTTON -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight">
                            Executive Report Generator
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Filter laporan berkala per mitra customer dan unduh dokumen evaluasi SLA resmi bertanda tangan.
                        </p>
                    </div>

                    @php
                        // Mempertahankan path URL aktual saat ini untuk mendukung subfolder lokal Laragon maupun root domain produksi
                        $currentReportUrl = request()->url();
                        $exportPdfUrl = rtrim($currentReportUrl, '/') . '/export-pdf';
                        $exportExcelUrl = rtrim($currentReportUrl, '/') . '/export-excel';
                        $importExcelUrl = route('reports.importExcel');
                    @endphp

                    <!-- Top Action Button: Export / Download PDF & Excel Resmi, serta Impor Excel -->
                    <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                        <!-- Tombol Impor Excel -->
                        <button 
                            type="button"
                            id="btnOpenImportModal"
                            onclick="document.getElementById('importModal').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer"
                            title="Unggah dan impor kembali file spreadsheet Excel yang telah diedit"
                        >
                            <svg class="w-3.5 h-3.5 text-indigo-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <span>Impor dari Excel</span>
                        </button>

                        <!-- Tombol Export Excel -->
                        <a 
                            id="btnExportExcel"
                            data-turbo="false"
                            data-base-url="{{ $exportExcelUrl }}"
                            href="{{ $exportExcelUrl . '?' . http_build_query(['customer' => $selectedCustomer, 'month' => $selectedMonth, 'year' => $selectedYear]) }}" 
                            class="inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer"
                            title="Unduh laporan data perangkat ke format spreadsheet Excel (.xlsx)"
                        >
                            <svg class="w-3.5 h-3.5 text-emerald-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Export ke Excel</span>
                        </a>

                        <!-- Tombol Preview Langsung di Tab Baru -->
                        <a 
                            id="btnPreviewPdf"
                            data-turbo="false"
                            data-base-url="{{ $exportPdfUrl }}"
                            href="{{ $exportPdfUrl . '?' . http_build_query(['customer' => $selectedCustomer, 'month' => $selectedMonth, 'year' => $selectedYear, 'preview' => '1']) }}" 
                            target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-white hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg border border-slate-200 shadow-2xs active:scale-95 transition-all cursor-pointer"
                            title="Buka pratinjau dokumen PDF di tab baru"
                        >
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span>Preview</span>
                        </a>

                        <!-- Tombol Utama Download PDF Resmi -->
                        <a 
                            id="btnExportPdf"
                            data-turbo="false"
                            data-base-url="{{ $exportPdfUrl }}"
                            href="{{ $exportPdfUrl . '?' . http_build_query(['customer' => $selectedCustomer, 'month' => $selectedMonth, 'year' => $selectedYear]) }}" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Export / Download PDF Resmi</span>
                        </a>
                    </div>
                </div>

                <!-- NOTIFICATION ALERT (IF FLASH SESSION MESSAGE) -->
                @if (session('success'))
                    <div id="flashSuccessAlert" class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-xs text-emerald-800 animate-in fade-in duration-200 shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="p-1 rounded-full bg-emerald-200 text-emerald-800">
                                <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                        <button type="button" onclick="document.getElementById('flashSuccessAlert').remove()" class="text-emerald-600 hover:text-emerald-900 font-bold p-1 cursor-pointer">✕</button>
                    </div>
                @endif

                @if (session('error'))
                    <div id="flashErrorAlert" class="p-4 bg-rose-50 border border-rose-200 rounded-xl flex items-center justify-between text-xs text-rose-800 animate-in fade-in duration-200 shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="p-1 rounded-full bg-rose-200 text-rose-800">
                                <svg class="w-4 h-4 text-rose-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </span>
                            <span class="font-medium">{{ session('error') }}</span>
                        </div>
                        <button type="button" onclick="document.getElementById('flashErrorAlert').remove()" class="text-rose-600 hover:text-rose-900 font-bold p-1 cursor-pointer">✕</button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800 animate-in fade-in duration-200 shadow-2xs">
                        <div class="font-bold mb-1">Terjadi kesalahan validasi:</div>
                        <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- FILTER FORM CARD -->
                <div class="bg-white p-4 lg:p-5 rounded-xl border border-slate-200/80 shadow-2xs">
                    <form id="reportFilterForm" action="{{ $currentReportUrl }}" method="GET" data-turbo="false" class="space-y-4">
                        
                        <div class="flex flex-col lg:flex-row items-stretch lg:items-end gap-4">
                            <!-- Field 1: Pilih Mitra Customer -->
                            <div class="flex-1">
                                <label for="filterCustomer" class="block text-[10px] font-bold text-slate-500 uppercase tracking-tight mb-1.5">
                                    PILIH MITRA CUSTOMER
                                </label>
                                <div class="relative">
                                    <select 
                                        name="customer" 
                                        id="filterCustomer"
                                        class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-slate-800 font-medium focus:outline-none focus:bg-white focus:border-blue-500 transition-colors appearance-none pr-8 cursor-pointer"
                                    >
                                        @foreach($customers as $key => $name)
                                            <option value="{{ $key }}" {{ (string) $selectedCustomer === (string) $key ? 'selected' : '' }}>
                                                {{ $key === 'all' ? '-- ' . $name . ' --' : $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Field 2: Bulan Pelaporan -->
                            <div class="w-full lg:w-48">
                                <label for="filterMonth" class="block text-[10px] font-bold text-slate-500 uppercase tracking-tight mb-1.5">
                                    BULAN PELAPORAN
                                </label>
                                <div class="relative">
                                    <select 
                                        name="month" 
                                        id="filterMonth"
                                        class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-slate-800 font-medium focus:outline-none focus:bg-white focus:border-blue-500 transition-colors appearance-none pr-8 cursor-pointer"
                                    >
                                        @foreach($months as $num => $name)
                                            <option value="{{ $num }}" {{ $selectedMonth === $num ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Field 3: Tahun -->
                            <div class="w-full lg:w-32">
                                <label for="filterYear" class="block text-[10px] font-bold text-slate-500 uppercase tracking-tight mb-1.5">
                                    TAHUN
                                </label>
                                <div class="relative">
                                    <select 
                                        name="year" 
                                        id="filterYear"
                                        class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-slate-800 font-medium focus:outline-none focus:bg-white focus:border-blue-500 transition-colors appearance-none pr-8 cursor-pointer"
                                    >
                                        @foreach($years as $yr)
                                            <option value="{{ $yr }}" {{ $selectedYear === $yr ? 'selected' : '' }}>
                                                {{ $yr }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Submit Button: Terapkan -->
                            <div class="shrink-0 flex items-center gap-2">
                                <button 
                                    type="submit" 
                                    class="w-full lg:w-auto px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer"
                                >
                                    Terapkan
                                </button>

                                @if($selectedCustomer !== 'all' || $selectedMonth !== (int) now()->month || $selectedYear !== (int) now()->year || request('issuer_name') || request('issuer_role'))
                                    <a 
                                        id="btnResetFilter"
                                        href="{{ $currentReportUrl }}" 
                                        data-turbo="false"
                                        class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors"
                                        title="Reset filter ke kondisi awal"
                                    >
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Secondary Row: Konfigurasi Penandatangan PDF (Semi-Dinamis) -->
                        <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <label for="filterIssuerName" class="block text-[10px] font-bold text-slate-500 uppercase tracking-tight">
                                        PENANDATANGAN PDF (NOC LEAD / PENERBIT)
                                    </label>
                                    <span class="text-[10px] text-slate-400">Default: {{ $defaultIssuerName }}</span>
                                </div>
                                <input 
                                    type="text" 
                                    name="issuer_name" 
                                    id="filterIssuerName"
                                    value="{{ request('issuer_name', '') }}"
                                    placeholder="Ketik untuk kustomisasi nama (misal: Ir. Hendra Wijaya)"
                                    class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 text-slate-800 placeholder-slate-400 font-medium focus:outline-none focus:bg-white focus:border-blue-500 transition-colors"
                                >
                            </div>

                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <label for="filterIssuerRole" class="block text-[10px] font-bold text-slate-500 uppercase tracking-tight">
                                        JABATAN / DIVISI PENERBIT
                                    </label>
                                    <span class="text-[10px] text-slate-400">Default: {{ $defaultIssuerRole }}</span>
                                </div>
                                <input 
                                    type="text" 
                                    name="issuer_role" 
                                    id="filterIssuerRole"
                                    value="{{ request('issuer_role', '') }}"
                                    placeholder="Ketik untuk kustomisasi jabatan (misal: Head of Operations)"
                                    class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 text-slate-800 placeholder-slate-400 font-medium focus:outline-none focus:bg-white focus:border-blue-500 transition-colors"
                                >
                            </div>
                        </div>
                    </form>
                </div>

                <!-- 3 SUMMARY METRIC CARDS -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <!-- Card 1: Total Insiden & Tiket -->
                    <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">
                                    TOTAL INSIDEN & TIKET
                                </span>
                                <span class="text-[11px] font-semibold text-slate-500">
                                    {{ $totalTickets }} Tiket
                                </span>
                            </div>
                            <div class="flex items-baseline gap-2 mb-2">
                                <span class="text-2xl font-black text-slate-900 tracking-tight">{{ $resolvedTickets }}</span>
                                <span class="text-xs font-medium text-slate-500">Terselesaikan</span>
                            </div>
                        </div>
                        <div class="pt-2.5 border-t border-slate-100 text-[11px] text-slate-500">
                            Rata-rata waktu penyelesaian: <span class="font-semibold text-slate-700">{{ $avgResolutionTime }}</span>
                        </div>
                    </div>

                    <!-- Card 2: Cakupan Node Hardware -->
                    <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">
                                    CAKUPAN NODE HARDWARE
                                </span>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span class="text-[11px] font-bold text-slate-600">Aktif</span>
                                </div>
                            </div>
                            <div class="flex items-baseline gap-2 mb-2">
                                <span class="text-2xl font-black text-slate-900 tracking-tight">{{ $totalDevices }}</span>
                                <span class="text-xs font-medium text-slate-500">Perangkat Terpantau</span>
                            </div>
                        </div>
                        <div class="pt-2.5 border-t border-slate-100 text-[11px] text-slate-500">
                            <span class="font-semibold text-emerald-600">{{ $normalDevices }} unit</span> beroperasi normal
                            @if($warningDevices > 0)
                                • <span class="font-semibold text-amber-600">{{ $warningDevices }} warning</span>
                            @endif
                            @if($offlineDevices > 0)
                                • <span class="font-semibold text-rose-600">{{ $offlineDevices }} offline</span>
                            @endif
                        </div>
                    </div>

                    <!-- Card 3: Mitra Yang Dievaluasi -->
                    <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">
                                    MITRA YANG DIEVALUASI
                                </span>
                                <span class="text-[10px] font-mono text-slate-400">
                                    {{ $reportDocumentNumber }}
                                </span>
                            </div>
                            <div class="mb-2">
                                <h3 class="text-base font-bold text-slate-900 tracking-tight leading-snug line-clamp-2" title="{{ $evaluatedPartner }}">
                                    {{ $evaluatedPartner }}
                                </h3>
                            </div>
                        </div>
                        <div class="pt-2.5 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                            <span>Periode: <strong class="text-slate-700 font-semibold">{{ $formattedPeriod }}</strong></span>
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                        </div>
                    </div>

                </div>

                <!-- TWO CONTENT PANELS: DAFTAR PERANGKAT & RIWAYAT TIKET -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- LEFT PANEL: DAFTAR PERANGKAT DALAM LAPORAN -->
                    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs flex flex-col overflow-hidden">
                        
                        <!-- Panel Header -->
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-tight">
                                Daftar Perangkat Dalam Laporan
                            </h3>
                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-slate-100 text-slate-600 rounded-full border border-slate-200/60">
                                {{ $totalDevices }} node
                            </span>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto flex-1">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-100 bg-slate-50/60 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        <th class="py-2.5 px-4">HOSTNAME</th>
                                        <th class="py-2.5 px-3">JENIS</th>
                                        <th class="py-2.5 px-3 font-mono">IP ADDRESS</th>
                                        <th class="py-2.5 px-3">LOKASI</th>
                                        <th class="py-2.5 px-4 text-right">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs">
                                    @forelse($devices as $device)
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            <td class="py-3 px-4 font-bold text-slate-900 font-mono text-[11px] tracking-tight">
                                                <a href="{{ route('devices.index', ['search' => $device['hostname']]) }}" class="hover:text-blue-600 hover:underline transition-colors" title="Lihat inventaris perangkat">
                                                    {{ $device['hostname'] }}
                                                </a>
                                            </td>
                                            <td class="py-3 px-3 text-slate-600 font-medium">
                                                {{ $device['type'] }}
                                            </td>
                                            <td class="py-3 px-3 font-mono text-[11px] text-slate-500">
                                                {{ $device['ip_address'] }}
                                            </td>
                                            <td class="py-3 px-3 text-[11px] text-slate-500">
                                                {{ $device['location'] }}
                                            </td>
                                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                                @if($device['status_state'] === 'online')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        Online
                                                    </span>
                                                @elseif($device['status_state'] === 'warning')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                        Warning
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200/60">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                        Offline
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-8 text-center text-xs text-slate-400">
                                                Tidak ada perangkat jaringan yang tercatat untuk filter ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer Info -->
                        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/40 text-[11px] text-slate-400 flex items-center justify-between">
                            <span>Menampilkan {{ count($devices) }} dari {{ $totalDevices }} entitas node</span>
                            <span class="text-slate-600 font-mono text-[10px]">ICMP / SNMP Polled</span>
                        </div>
                    </div>

                    <!-- RIGHT PANEL: RIWAYAT TIKET PENANGANAN PERIODE INI -->
                    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs flex flex-col overflow-hidden">
                        
                        <!-- Panel Header -->
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-tight">
                                Riwayat Tiket Penanganan Periode Ini
                            </h3>
                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-slate-100 text-slate-600 rounded-full border border-slate-200/60">
                                {{ $totalTickets }} tiket
                            </span>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto flex-1">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-100 bg-slate-50/60 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        <th class="py-2.5 px-4 font-mono">NOMOR TIKET</th>
                                        <th class="py-2.5 px-3">SUBJEK / RINGKASAN GANGGUAN</th>
                                        <th class="py-2.5 px-3">PRIORITAS</th>
                                        <th class="py-2.5 px-3">TEKNISI NOC</th>
                                        <th class="py-2.5 px-4 text-right">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs">
                                    @forelse($tickets as $ticket)
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            <td class="py-3 px-4 font-mono text-[11px] whitespace-nowrap">
                                                <a href="{{ route('tickets.index', ['search' => $ticket['ticket_number']]) }}" class="font-bold text-blue-600 hover:text-blue-800 hover:underline transition-colors" title="Buka tiket di antrean tiket">
                                                    {{ $ticket['ticket_number'] }}
                                                </a>
                                            </td>
                                            <td class="py-3 px-3 font-medium text-slate-800 text-[11px] leading-relaxed" title="{{ $ticket['full_subject'] }}">
                                                {{ $ticket['full_subject'] }}
                                            </td>
                                            <td class="py-3 px-3 whitespace-nowrap">
                                                <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200/60">
                                                    {{ $ticket['priority'] }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-3 text-slate-600 text-xs whitespace-nowrap">
                                                {{ $ticket['technician'] }}
                                            </td>
                                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                                @if($ticket['status_type'] === 'in_progress')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-900 text-white">
                                                        In Progress
                                                    </span>
                                                @elseif($ticket['status_type'] === 'assigned')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                        Assigned
                                                    </span>
                                                @elseif($ticket['status_type'] === 'open')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                        Open
                                                    </span>
                                                @elseif($ticket['status_type'] === 'resolved')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        Resolved
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">
                                                        {{ $ticket['status'] }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-8 text-center text-xs text-slate-400">
                                                Tidak ada riwayat tiket gangguan pada periode ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer Info -->
                        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/40 text-[11px] text-slate-400 flex items-center justify-between">
                            <span>{{ $resolvedTickets }} dari {{ $totalTickets }} insiden tertangani tuntas</span>
                            <a href="{{ route('tickets.index') }}" class="text-blue-600 hover:text-blue-700 font-semibold hover:underline">
                                Buka Antrean Tiket →
                            </a>
                        </div>
                    </div>

                </div>

                <!-- FOOTER BRANDING -->
                <footer class="pt-8 pb-4 text-center text-xs text-slate-400">
                    <p>© 2026 PT Netmedia Fiberoptik Lancarmulya • Network Operation Center</p>
                </footer>

            </main>

        </div>

    </div>

    <!-- MODAL IMPOR LAPORAN EXCEL -->
    <div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 animate-in fade-in zoom-in-95 duration-150">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Impor Laporan dari Excel</h3>
                        <p class="text-[11px] text-slate-500">Unggah file hasil ekspor Excel yang telah Anda edit secara manual</p>
                    </div>
                </div>
                <button 
                    type="button" 
                    onclick="document.getElementById('importModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Form -->
            <form action="{{ $importExcelUrl }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="customer" value="{{ $selectedCustomer }}">
                <input type="hidden" name="month" value="{{ $selectedMonth }}">
                <input type="hidden" name="year" value="{{ $selectedYear }}">

                <!-- Panduan Singkat -->
                <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-xl text-xs space-y-1.5 text-slate-600">
                    <div class="font-semibold text-slate-800 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Ketentuan Berkas Excel:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 pl-1 text-[11px] text-slate-500">
                        <li>Gunakan file hasil <strong>Export ke Excel</strong> dari sistem ini.</li>
                        <li>Format sheet yang didukung: <strong>Daftar Perangkat</strong> dan <strong>Riwayat Tiket & Insiden</strong>.</li>
                        <li>Sistem otomatis memperbarui data bila <em>Hostname</em> atau <em>Nomor Tiket</em> sudah ada, atau menambahkan data baru jika belum terdaftar.</li>
                    </ul>
                </div>

                <!-- File Input Drag-Drop Area -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Berkas Excel (.xlsx, .xls, .csv)</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-xl hover:border-indigo-400 hover:bg-indigo-50/20 transition-all cursor-pointer relative group">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-9 w-9 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div class="flex text-xs text-slate-600 justify-center">
                                <span class="font-medium text-indigo-600 group-hover:underline">Klik untuk memilih file</span>
                            </div>
                            <p id="selectedFileName" class="text-[11px] text-slate-400">XLSX, XLS, atau CSV hingga 10MB</p>
                        </div>
                        <input 
                            id="excelFileInput" 
                            name="file" 
                            type="file" 
                            accept=".xlsx, .xls, .csv" 
                            required
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            onchange="const f = this.files[0]; if(f) document.getElementById('selectedFileName').textContent = f.name + ' (' + (f.size/1024).toFixed(1) + ' KB)';"
                        >
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button 
                        type="button" 
                        onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg border border-slate-200 shadow-2xs transition-all cursor-pointer"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5 text-indigo-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span>Mulai Impor</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

            </main>

        </div>

    </div>

</body>
</html>
