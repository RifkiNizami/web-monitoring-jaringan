<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Management — Portal Manajemen Jaringan NetFly</title>
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
                        <span class="text-slate-800 font-bold">Account Management</span>
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
                            placeholder="Cari akun customer..." 
                            class="pl-8 pr-12 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 w-44 lg:w-56 transition-all"
                        >
                        <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none">
                            <kbd class="px-1.5 py-0.5 text-[10px] font-mono text-slate-400 bg-white border border-slate-200 rounded shadow-2xs">⌘K</kbd>
                        </span>
                    </div>

                    <!-- Notification Bell -->
                    <button class="relative p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer" title="Notifikasi Akun">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1 right-1 w-3.5 h-3.5 bg-blue-600 text-white text-[9px] font-bold rounded-full flex items-center justify-center border-2 border-white">
                            {{ $summary['inactive'] ?? 1 }}
                        </span>
                    </button>

                    <!-- Realtime Digital Clock -->
                    <div class="text-xs font-semibold text-slate-700 bg-slate-100/80 px-2.5 py-1 rounded-md border border-slate-200/60 font-mono tracking-tight" id="liveClock">
                        --.--.-- WIB
                    </div>
                </div>
            </header>

            <!-- MAIN BODY PADDING -->
            <main class="p-6 lg:p-8 space-y-6">
                
                <!-- HEADER SECTION: CATEGORY, TITLE, & ACTION BUTTONS -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="text-[11px] font-semibold text-slate-400 mb-1 flex items-center gap-1.5">
                            <span class="text-blue-600 font-bold uppercase tracking-wider">PUSAT KREDENSIAL B2B</span>
                            <span>•</span>
                            <span class="text-slate-500 font-medium">Access Control & Security</span>
                        </div>
                        <h1 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight">
                            Manajemen Akun Perwakilan Customer
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Penerbitan hak akses mandiri portal customer B2B, kontrol aktif/nonaktif akun secara instan, dan fitur pemulihan kata sandi.
                        </p>
                    </div>

                    <!-- Top Action Button: Buat Akun Baru (3-Step Workflow) -->
                    <div class="flex items-center gap-2.5 shrink-0">
                        <button 
                            type="button" 
                            id="btnOpenCreateAccount"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm hover:shadow active:scale-95 transition-all cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            <span>Buat Akun Customer Baru</span>
                        </button>
                    </div>
                </div>

                <!-- 4 SUMMARY METRIC CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Card 1: TOTAL AKUN -->
                    <div class="bg-white p-4.5 rounded-xl border border-slate-200/80 shadow-2xs">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">
                                TOTAL AKUN CUSTOMER
                            </p>
                            <span class="p-1 rounded bg-slate-100 text-slate-600">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </span>
                        </div>
                        <p class="text-2xl font-black text-slate-900 tracking-tight" id="metricTotalAccounts">
                            {{ $summary['total'] ?? 0 }} <span class="text-xs font-semibold text-slate-500">Akun</span>
                        </p>
                        <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
                            <span class="text-blue-600 font-semibold">B2B Portal</span>
                            <span>• Multi-Tenant</span>
                        </p>
                    </div>

                    <!-- Card 2: AKUN AKTIF -->
                    <div class="bg-white p-4.5 rounded-xl border border-slate-200/80 shadow-2xs">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">
                                AKUN STATUS AKTIF
                            </p>
                            <span class="p-1 rounded bg-emerald-50 text-emerald-600">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                        </div>
                        <p class="text-2xl font-black text-emerald-600 tracking-tight" id="metricActiveAccounts">
                            {{ $summary['active'] ?? 0 }} <span class="text-xs font-semibold text-slate-500">Aktif</span>
                        </p>
                        <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Akses portal diizinkan</span>
                        </p>
                    </div>

                    <!-- Card 3: AKUN NONAKTIF -->
                    <div class="bg-white p-4.5 rounded-xl border border-slate-200/80 shadow-2xs">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">
                                AKUN NONAKTIF / SUSPEND
                            </p>
                            <span class="p-1 rounded bg-rose-50 text-rose-600">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                            </span>
                        </div>
                        <p class="text-2xl font-black text-rose-600 tracking-tight" id="metricInactiveAccounts">
                            {{ $summary['inactive'] ?? 0 }} <span class="text-xs font-semibold text-slate-500">Ditangguhkan</span>
                        </p>
                        <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                            <span>Akses login diblokir</span>
                        </p>
                    </div>

                    <!-- Card 4: MITRA TERDAFTAR -->
                    <div class="bg-white p-4.5 rounded-xl border border-slate-200/80 shadow-2xs">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">
                                MITRA TERHUBUNG
                            </p>
                            <span class="p-1 rounded bg-blue-50 text-blue-600">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </span>
                        </div>
                        <p class="text-2xl font-black text-blue-600 tracking-tight">
                            {{ $summary['customers_count'] ?? 6 }} <span class="text-xs font-semibold text-slate-500">Perusahaan</span>
                        </p>
                        <p class="text-[11px] text-slate-500 mt-1">
                            Enterprise, Gov, & Retail
                        </p>
                    </div>

                </div>

                <!-- NOTIFICATION ALERT (IF FLASH SESSION MESSAGE) -->
                @if (session('success'))
                    <div id="flashAlert" class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-xs text-emerald-800">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" onclick="document.getElementById('flashAlert').remove()" class="text-emerald-600 hover:text-emerald-900 font-bold p-1">✕</button>
                    </div>
                @endif

                <!-- FILTER BAR (SEARCH, STATUS SELECT, CUSTOMER SELECT) -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    
                    <!-- Search Input -->
                    <div class="relative flex-1 max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            id="accountSearch" 
                            placeholder="Cari username, nama PIC, email, atau customer..." 
                            class="w-full pl-10 pr-4 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs transition-all"
                        >
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        <!-- Status Filter Dropdown -->
                        <div class="relative">
                            <select 
                                id="statusFilter" 
                                class="appearance-none pl-3.5 pr-8 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs cursor-pointer"
                            >
                                <option value="all">Semua Status Akun</option>
                                <option value="active">Hanya Aktif</option>
                                <option value="inactive">Hanya Nonaktif</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Customer Filter Dropdown -->
                        <div class="relative">
                            <select 
                                id="customerFilter" 
                                class="appearance-none pl-3.5 pr-8 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs cursor-pointer"
                            >
                                <option value="all">Semua Mitra Customer</option>
                                @foreach ($customers as $c)
                                    <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- TABLE: DAFTAR AKUN PERWAKILAN CUSTOMER -->
                <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse" id="accountsTable">
                            
                            <!-- Table Header -->
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    <th scope="col" class="py-3 px-5 whitespace-nowrap">MITRA CUSTOMER & PAKET</th>
                                    <th scope="col" class="py-3 px-5 whitespace-nowrap">PERWAKILAN (PIC) & USERNAME</th>
                                    <th scope="col" class="py-3 px-5 whitespace-nowrap">KONTAK EMAIL & WHATSAPP</th>
                                    <th scope="col" class="py-3 px-5 whitespace-nowrap">KONTROL STATUS AKUN</th>
                                    <th scope="col" class="py-3 px-5 whitespace-nowrap">LOG AKSES TERAKHIR</th>
                                    <th scope="col" class="py-3 px-5 text-right whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>

                            <!-- Table Body -->
                            <tbody class="divide-y divide-slate-100 text-xs text-slate-700" id="accountsTableBody">
                                
                                @forelse ($accounts as $account)
                                    <tr 
                                        class="hover:bg-slate-50/80 transition-colors account-row" 
                                        id="row-account-{{ $account['id'] }}"
                                        data-id="{{ $account['id'] }}"
                                        data-customer-id="{{ $account['customer_id'] }}"
                                        data-customer-name="{{ $account['customer_name'] }}"
                                        data-name="{{ $account['name'] }}"
                                        data-username="{{ $account['username'] }}"
                                        data-email="{{ $account['email'] }}"
                                        data-phone="{{ $account['phone'] }}"
                                        data-active="{{ $account['is_active'] ? '1' : '0' }}"
                                    >
                                        
                                        <!-- 1. CUSTOMER & PAKET -->
                                        <td class="py-4 px-5 align-middle">
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="font-bold text-slate-900 text-xs">
                                                        {{ $account['customer_name'] }}
                                                    </span>
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                                        {{ $account['customer_code'] ?? 'B2B' }}
                                                    </span>
                                                </div>
                                                <p class="text-[11px] text-slate-500 font-mono">
                                                    {{ $account['package'] ?? 'Dedicated Fiber Link' }}
                                                </p>
                                            </div>
                                        </td>

                                        <!-- 2. PERWAKILAN & USERNAME -->
                                        <td class="py-4 px-5 align-middle">
                                            <div class="space-y-1">
                                                <div class="font-semibold text-slate-800 text-xs flex items-center gap-1.5">
                                                    <span>{{ $account['name'] }}</span>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-800 font-mono text-[11px] font-semibold border border-slate-200">
                                                        <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                        <span class="font-bold">{{ $account['username'] }}</span>
                                                    </span>
                                                    <button 
                                                        type="button" 
                                                        title="Salin Username" 
                                                        class="text-slate-400 hover:text-slate-600 p-1 rounded hover:bg-slate-100 transition-colors cursor-pointer"
                                                        onclick="copyTextToClipboard('{{ $account['username'] }}', 'Username disalin!')"
                                                    >
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- 3. KONTAK & EMAIL -->
                                        <td class="py-4 px-5 align-middle">
                                            <div class="space-y-0.5 text-xs">
                                                <a href="mailto:{{ $account['email'] }}" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1 font-medium">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                    <span>{{ $account['email'] }}</span>
                                                </a>
                                                <p class="text-slate-500 text-[11px] font-mono flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                    </svg>
                                                    <span>{{ $account['phone'] ?? '-' }}</span>
                                                </p>
                                            </div>
                                        </td>

                                        <!-- 4. KONTROL STATUS AKUN (TOGGLE AKTIF / NONAKTIF) -->
                                        <td class="py-4 px-5 align-middle whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                
                                                <!-- Toggle Switch Button -->
                                                <button 
                                                    type="button" 
                                                    role="switch" 
                                                    aria-checked="{{ $account['is_active'] ? 'true' : 'false' }}"
                                                    data-account-id="{{ $account['id'] }}"
                                                    data-account-username="{{ $account['username'] }}"
                                                    class="btn-toggle-status relative inline-flex h-5 w-10 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $account['is_active'] ? 'bg-emerald-500' : 'bg-slate-300' }}"
                                                    title="Klik untuk {{ $account['is_active'] ? 'Nonaktifkan' : 'Aktifkan' }} akun"
                                                >
                                                    <span class="sr-only">Toggle Status Akun</span>
                                                    <span 
                                                        aria-hidden="true" 
                                                        class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $account['is_active'] ? 'translate-x-5' : 'translate-x-0' }}"
                                                    ></span>
                                                </button>

                                                <!-- Status Badge Text -->
                                                <div id="badge-status-{{ $account['id'] }}">
                                                    @if ($account['is_active'])
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/80 rounded-full">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                            Aktif
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-rose-700 bg-rose-50 border border-rose-200/80 rounded-full">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                                            Nonaktif
                                                        </span>
                                                    @endif
                                                </div>

                                            </div>
                                        </td>

                                        <!-- 5. LOG AKSES TERAKHIR -->
                                        <td class="py-4 px-5 align-middle whitespace-nowrap">
                                            <div class="space-y-0.5">
                                                <p class="font-medium text-slate-800 text-xs">
                                                    {{ $account['last_login'] ?? 'Belum login' }}
                                                </p>
                                                <p class="text-[10px] text-slate-400 font-mono">
                                                    IP: {{ $account['last_login_ip'] ?? '-' }}
                                                </p>
                                            </div>
                                        </td>

                                        <!-- 6. AKSI -->
                                        <td class="py-4 px-5 align-middle text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                
                                                <!-- Tombol Reset Password (Fitur 3) -->
                                                <button 
                                                    type="button" 
                                                    title="Reset Password Akun Customer" 
                                                    data-account-id="{{ $account['id'] }}"
                                                    data-account-username="{{ $account['username'] }}"
                                                    data-account-name="{{ $account['name'] }}"
                                                    data-account-customer="{{ $account['customer_name'] }}"
                                                    data-account-email="{{ $account['email'] }}"
                                                    class="btn-open-reset-modal inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-amber-300 bg-amber-50/80 text-amber-700 hover:bg-amber-100 hover:text-amber-800 text-xs font-semibold active:scale-95 transition-all cursor-pointer shadow-2xs"
                                                >
                                                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                                    </svg>
                                                    <span>Reset Password</span>
                                                </button>

                                                <!-- Tombol Detail / View Kredensial -->
                                                <button 
                                                    type="button" 
                                                    title="Lihat Detail Kredensial & Kontak" 
                                                    data-account-id="{{ $account['id'] }}"
                                                    data-account-username="{{ $account['username'] }}"
                                                    data-account-name="{{ $account['name'] }}"
                                                    data-account-customer="{{ $account['customer_name'] }}"
                                                    data-account-package="{{ $account['package'] ?? 'Dedicated Link' }}"
                                                    data-account-email="{{ $account['email'] }}"
                                                    data-account-phone="{{ $account['phone'] ?? '-' }}"
                                                    data-account-created="{{ $account['created_at'] ?? '10 Jan 2026' }}"
                                                    class="btn-open-detail-modal inline-flex items-center justify-center w-7 h-7 rounded-lg border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-100 active:scale-95 transition-all cursor-pointer"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>

                                                <!-- Tombol Hapus Akun -->
                                                <button 
                                                    type="button" 
                                                    title="Hapus Akun Customer" 
                                                    data-account-id="{{ $account['id'] }}"
                                                    data-account-username="{{ $account['username'] }}"
                                                    class="btn-open-delete-modal inline-flex items-center justify-center w-7 h-7 rounded-lg border border-rose-200 text-rose-500 hover:text-rose-600 hover:bg-rose-50 active:scale-95 transition-all cursor-pointer"
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
                                        <td colspan="6" class="py-12 text-center text-slate-400">
                                            <p class="font-medium text-xs">Belum ada akun perwakilan customer yang terdaftar.</p>
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>

                    <!-- Table Footer Info -->
                    <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500">
                        <p>
                            Menampilkan <span class="font-bold text-slate-800" id="shownCount">{{ count($accounts) }}</span> akun perwakilan customer B2B.
                        </p>
                        <div class="flex items-center gap-1.5 text-[11px] text-slate-400">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Akun diverifikasi menggunakan token Laravel Sanctum & hak akses terisolasi per-customer.</span>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 1: FORM BUAT AKUN USER BARU UNTUK PERWAKILAN CUSTOMER                -->
    <!-- ALUR: 1. Pilih Customer -> 2. Buat Username -> 3. Tetapkan Password Awal   -->
    <!-- ========================================================================= -->
    <div id="createAccountModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-2xl w-full overflow-hidden transform transition-all animate-in fade-in zoom-in duration-200 my-8">
            
            <!-- Modal Header with Steps Indicator -->
            <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                Buat Akun User Baru Perwakilan Customer
                            </h3>
                            <p class="text-[11px] text-slate-500">
                                Ikuti 3 alur formulir: Pilih Customer ➔ Buat Username ➔ Tetapkan Password Awal
                            </p>
                        </div>
                    </div>
                    <button type="button" class="close-create-modal text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- 3-Step Visual Progress Bar -->
                <div class="grid grid-cols-3 gap-2 pt-1 text-center">
                    <div class="p-2 rounded-lg bg-blue-50 border border-blue-200/80 text-blue-700">
                        <p class="text-[9px] font-extrabold uppercase tracking-wider">LANGKAH 1</p>
                        <p class="text-xs font-bold truncate">1. Pilih Customer</p>
                    </div>
                    <div class="p-2 rounded-lg bg-blue-50 border border-blue-200/80 text-blue-700">
                        <p class="text-[9px] font-extrabold uppercase tracking-wider">LANGKAH 2</p>
                        <p class="text-xs font-bold truncate">2. Buat Username</p>
                    </div>
                    <div class="p-2 rounded-lg bg-blue-50 border border-blue-200/80 text-blue-700">
                        <p class="text-[9px] font-extrabold uppercase tracking-wider">LANGKAH 3</p>
                        <p class="text-xs font-bold truncate">3. Password Awal</p>
                    </div>
                </div>
            </div>

            <!-- Form Body -->
            <form id="formCreateAccount" action="{{ route('accounts.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
                    
                    <!-- ============================================== -->
                    <!-- TAHAP 1: PILIH CUSTOMER & IDENTITAS PIC         -->
                    <!-- ============================================== -->
                    <div class="space-y-3.5 bg-slate-50/70 p-4 rounded-xl border border-slate-200/70">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white text-[10px] font-bold flex items-center justify-center">1</span>
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                                Pilih Mitra Customer & Detail Perwakilan (PIC)
                            </h4>
                        </div>

                        <!-- 1. Dropdown Pilih Customer -->
                        <div>
                            <label for="create_customer_id" class="block text-xs font-bold text-slate-700 mb-1">
                                Pilih Mitra Customer Korporat <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select 
                                    id="create_customer_id" 
                                    name="customer_id" 
                                    required
                                    class="w-full appearance-none pl-3 pr-8 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-pointer shadow-2xs"
                                >
                                    <option value="" disabled selected>-- Pilih Perusahaan Customer Terdaftar --</option>
                                    @foreach ($customers as $c)
                                        <option 
                                            value="{{ $c['id'] }}"
                                            data-name="{{ $c['name'] }}"
                                            data-code="{{ $c['code'] }}"
                                            data-package="{{ $c['package'] }}"
                                            data-pic="{{ $c['default_pic'] }}"
                                            data-email="{{ $c['default_email'] }}"
                                            data-phone="{{ $c['default_phone'] }}"
                                            data-prefix="{{ $c['username_prefix'] }}"
                                        >
                                            {{ $c['name'] }} ({{ $c['code'] }}) — {{ $c['category'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">
                                Memilih customer akan otomatis mengisi saran username dan kontak resmi.
                            </p>
                        </div>

                        <!-- Row: Nama PIC & Email -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="create_name" class="block text-xs font-bold text-slate-700 mb-1">
                                    Nama Lengkap PIC Perwakilan <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="create_name" 
                                    name="name" 
                                    required 
                                    placeholder="Contoh: Bambang Kurniawan, S.Kom"
                                    class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs"
                                >
                            </div>

                            <div>
                                <label for="create_email" class="block text-xs font-bold text-slate-700 mb-1">
                                    Email Resmi PIC Korporat <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    id="create_email" 
                                    name="email" 
                                    required 
                                    placeholder="Contoh: pic.noc@bankmandiri.co.id"
                                    class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs"
                                >
                            </div>
                        </div>

                        <!-- No. Telepon / WhatsApp PIC -->
                        <div>
                            <label for="create_phone" class="block text-xs font-bold text-slate-700 mb-1">
                                No. Telepon / WhatsApp PIC
                            </label>
                            <input 
                                type="text" 
                                id="create_phone" 
                                name="phone" 
                                placeholder="Contoh: 0812-8877-6655"
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs"
                            >
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- TAHAP 2: BUAT USERNAME                         -->
                    <!-- ============================================== -->
                    <div class="space-y-3.5 bg-slate-50/70 p-4 rounded-xl border border-slate-200/70">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-blue-600 text-white text-[10px] font-bold flex items-center justify-center">2</span>
                                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                                    Buat Username Kredensial Login
                                </h4>
                            </div>
                            <button 
                                type="button" 
                                id="btnSuggestUsername" 
                                class="text-[11px] font-semibold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1 cursor-pointer"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span>Generate Rekomendasi</span>
                            </button>
                        </div>

                        <div>
                            <label for="create_username" class="block text-xs font-bold text-slate-700 mb-1">
                                Username Unik <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 font-mono text-xs font-bold">
                                    @
                                </div>
                                <input 
                                    type="text" 
                                    id="create_username" 
                                    name="username" 
                                    required 
                                    pattern="[a-zA-Z0-9_.]+"
                                    placeholder="Contoh: pic_mandiri atau noc_mandiri"
                                    class="w-full pl-8 pr-24 py-2 text-xs font-mono font-semibold bg-white border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs"
                                >
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200 hidden" id="usernameValidBadge">
                                        Format Valid
                                    </span>
                                </div>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">
                                Gunakan huruf kecil, angka, atau garis bawah (_). Minimal 3 karakter dan unik.
                            </p>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- TAHAP 3: TETAPKAN PASSWORD AWAL                -->
                    <!-- ============================================== -->
                    <div class="space-y-3.5 bg-slate-50/70 p-4 rounded-xl border border-slate-200/70">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-blue-600 text-white text-[10px] font-bold flex items-center justify-center">3</span>
                                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                                    Tetapkan Password Awal Akun
                                </h4>
                            </div>
                            <button 
                                type="button" 
                                id="btnGeneratePassword" 
                                class="text-[11px] font-semibold text-emerald-600 hover:text-emerald-800 hover:underline flex items-center gap-1 cursor-pointer"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                <span>Generate Password Kuat</span>
                            </button>
                        </div>

                        <!-- Input Password Awal -->
                        <div>
                            <label for="create_password" class="block text-xs font-bold text-slate-700 mb-1">
                                Password Awal <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="password" 
                                    id="create_password" 
                                    name="password" 
                                    required 
                                    minlength="6"
                                    placeholder="Masukkan password awal (min. 6 karakter)..."
                                    class="w-full pl-3 pr-10 py-2 text-xs font-mono bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs"
                                >
                                <button 
                                    type="button" 
                                    id="toggleCreatePassword" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                                >
                                    <svg class="w-4 h-4 eye-icon-open" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg class="w-4 h-4 eye-icon-closed hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Checkbox Opsi Tambahan -->
                        <div class="space-y-2 pt-1">
                            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-700">
                                <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span>Langsung aktifkan status akun setelah dibuat</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-700">
                                <input type="checkbox" name="must_change_password" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span>Wajibkan ganti password saat perwakilan pertama kali login</span>
                            </label>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <button 
                        type="button" 
                        class="close-create-modal px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer"
                    >
                        Batalkan
                    </button>
                    <button 
                        type="submit" 
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm hover:shadow active:scale-95 transition-all cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan & Terbitkan Akun</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 2: FITUR RESET PASSWORD AKUN CUSTOMER                                -->
    <!-- ========================================================================= -->
    <div id="resetPasswordModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-lg w-full overflow-hidden transform transition-all animate-in fade-in zoom-in duration-200">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-100 bg-amber-50/60">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                Reset Password Akun Customer
                            </h3>
                            <p class="text-[11px] text-slate-500">
                                Atur ulang kata sandi login untuk perwakilan mitra customer
                            </p>
                        </div>
                    </div>
                    <button type="button" class="close-reset-modal text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Form Reset -->
            <form id="formResetPassword" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    
                    <!-- Account Context Card -->
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200/80 space-y-1.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 font-medium">Customer:</span>
                            <span class="font-bold text-slate-900" id="resetModalCustomer">PT. Bank Mandiri</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 font-medium">Username:</span>
                            <span class="font-mono font-bold text-blue-600" id="resetModalUsername">pic_mandiri</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 font-medium">Nama PIC:</span>
                            <span class="font-semibold text-slate-800" id="resetModalPicName">Bambang Kurniawan</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 font-medium">Email PIC:</span>
                            <span class="text-slate-600 truncate max-w-[200px]" id="resetModalEmail">pic@mandiri.co.id</span>
                        </div>
                    </div>

                    <!-- Password Baru Input -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="reset_password" class="block text-xs font-bold text-slate-700">
                                Password Baru <span class="text-rose-500">*</span>
                            </label>
                            <button 
                                type="button" 
                                id="btnGenerateResetPassword"
                                class="text-[11px] font-semibold text-emerald-600 hover:text-emerald-800 hover:underline flex items-center gap-1 cursor-pointer"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                <span>Generate Password Otomatis</span>
                            </button>
                        </div>
                        <div class="relative">
                            <input 
                                type="text" 
                                id="reset_password" 
                                name="password" 
                                required 
                                minlength="6"
                                placeholder="Ketik atau generate password baru..."
                                class="w-full pl-3 pr-20 py-2 text-xs font-mono font-semibold bg-white border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 shadow-2xs"
                            >
                            <button 
                                type="button" 
                                id="btnCopyResetPassword" 
                                title="Salin Password Baru"
                                class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-700 text-[11px] font-bold cursor-pointer"
                            >
                                Salin
                            </button>
                        </div>
                    </div>

                    <!-- One-Click Quick WhatsApp / Email Clipboard Card -->
                    <div class="p-3 bg-blue-50/70 border border-blue-200/80 rounded-xl space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-blue-900">Format Pemberitahuan Kredensial</span>
                            <button 
                                type="button" 
                                id="btnCopyFullCredentialMsg" 
                                class="text-[10px] font-bold text-blue-700 bg-white hover:bg-blue-100 px-2 py-0.5 rounded border border-blue-300 shadow-2xs transition-colors cursor-pointer"
                            >
                                Salin Pesan Lengkap
                            </button>
                        </div>
                        <p class="text-[10px] text-slate-500 leading-relaxed font-mono bg-white p-2 rounded border border-blue-100" id="previewCredentialText">
                            Halo [PIC], berikut kredensial baru akun Customer NetFly Anda:<br>
                            Username: [USERNAME]<br>
                            Password: [PASSWORD]<br>
                            Portal: https://portal.netfly.id
                        </p>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <button 
                        type="button" 
                        class="close-reset-modal px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg shadow-sm hover:shadow active:scale-95 transition-all cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan & Terapkan Password</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 3: DETAIL KREDENSIAL AKUN                                            -->
    <!-- ========================================================================= -->
    <div id="detailAccountModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-md w-full overflow-hidden transform transition-all animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">
                    Detail Kredensial Akun Customer
                </h3>
                <button type="button" class="close-detail-modal text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-4 text-xs">
                <div class="space-y-2">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Perusahaan Mitra</p>
                        <p class="text-sm font-bold text-slate-900" id="detailCustomerName">-</p>
                        <p class="text-xs text-slate-500 font-mono" id="detailPackage">-</p>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Username Login</p>
                            <p class="font-mono font-bold text-blue-600 text-xs mt-0.5" id="detailUsername">-</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Perwakilan PIC</p>
                            <p class="font-bold text-slate-800 text-xs mt-0.5" id="detailPicName">-</p>
                        </div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Kontak Resmi</p>
                        <p class="font-medium text-slate-700" id="detailEmail">-</p>
                        <p class="font-mono text-slate-500" id="detailPhone">-</p>
                    </div>
                </div>
                <button 
                    type="button" 
                    id="btnCopyDetailText"
                    class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg transition-colors cursor-pointer"
                >
                    Salin Informasi Akun ke Clipboard
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 4: KONFIRMASI HAPUS AKUN                                             -->
    <!-- ========================================================================= -->
    <div id="deleteAccountModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-sm w-full overflow-hidden transform transition-all animate-in fade-in zoom-in duration-200">
            <div class="p-6 text-center space-y-4">
                <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">
                        Hapus Akun Perwakilan Customer?
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Akun <span class="font-mono font-bold text-slate-800" id="deleteTargetUsername">user</span> akan dihapus dan token portal akan dicabut. Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="flex items-center gap-2 pt-2">
                    <button 
                        type="button" 
                        class="close-delete-modal flex-1 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer"
                    >
                        Batal
                    </button>
                    <button 
                        type="button" 
                        id="btnConfirmDeleteAccount"
                        class="flex-1 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors cursor-pointer"
                    >
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

</body>
</html>