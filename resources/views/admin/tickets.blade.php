<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrean Tiket Komplain Klien — Portal Manajemen Jaringan NetFly</title>
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
                        <span class="text-slate-800 font-bold">Ticket & Insiden</span>
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
                            <span>Layanan Pelanggan & Penanganan Insiden</span>
                        </div>
                        <h1 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight">
                            Antrean Tiket Komplain Klien
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Kelola antrean laporan gangguan, delegasikan teknisi lapangan, dan perbarui tahapan eskalasi penanganan SLA.
                        </p>
                    </div>

                    <!-- Top Action Buttons -->
                    <div class="flex items-center gap-2.5 shrink-0">
                        <!-- Button 1: Lihat Laporan SLA -->
                        <button 
                            id="btnSlaReport" 
                            class="inline-flex items-center gap-2 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-lg border border-slate-200 shadow-2xs active:scale-95 transition-all cursor-pointer"
                        >
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Lihat Laporan SLA</span>
                        </button>

                        <!-- Button 2: Buat Tiket Baru -->
                        <button 
                            id="btnCreateTicket" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Buat Tiket Baru</span>
                        </button>
                    </div>
                </div>

                <!-- 6 SUMMARY STATS CARDS -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
                    
                    <!-- Card 1: SEMUA ANTREAN -->
                    <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-2xs">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tight mb-2">
                            SEMUA ANTREAN
                        </p>
                        <p class="text-2xl font-black text-slate-900 tracking-tight" id="countAll">
                            {{ $summary['all'] ?? 4 }}
                        </p>
                    </div>

                    <!-- Card 2: OPEN -->
                    <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-2xs">
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">
                                OPEN
                            </p>
                        </div>
                        <p class="text-2xl font-black text-slate-900 tracking-tight" id="countOpen">
                            {{ $summary['open'] ?? 1 }}
                        </p>
                    </div>

                    <!-- Card 3: ASSIGNED -->
                    <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-2xs">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tight mb-2">
                            ASSIGNED
                        </p>
                        <p class="text-2xl font-black text-slate-900 tracking-tight" id="countAssigned">
                            {{ $summary['assigned'] ?? 1 }}
                        </p>
                    </div>

                    <!-- Card 4: IN PROGRESS -->
                    <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-2xs">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tight mb-2">
                            IN PROGRESS
                        </p>
                        <p class="text-2xl font-black text-slate-900 tracking-tight" id="countInProgress">
                            {{ $summary['in_progress'] ?? 1 }}
                        </p>
                    </div>

                    <!-- Card 5: RESOLVED -->
                    <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-2xs">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tight mb-2">
                            RESOLVED
                        </p>
                        <p class="text-2xl font-black text-slate-900 tracking-tight" id="countResolved">
                            {{ $summary['resolved'] ?? 1 }}
                        </p>
                    </div>

                    <!-- Card 6: CLOSED -->
                    <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-2xs">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tight mb-2">
                            CLOSED
                        </p>
                        <p class="text-2xl font-black text-slate-900 tracking-tight" id="countClosed">
                            {{ $summary['closed'] ?? 0 }}
                        </p>
                    </div>

                </div>

                <!-- FILTER BAR (SEARCH & PRIORITY SELECT) -->
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
                            id="tableSearch" 
                            placeholder="Cari nomor tiket, judul, customer, teknisi..." 
                            class="w-full pl-10 pr-4 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs transition-all"
                        >
                    </div>

                    <!-- Priority Dropdown Filter -->
                    <div class="relative shrink-0">
                        <select 
                            id="prioritySelect" 
                            class="appearance-none pl-3.5 pr-8 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs cursor-pointer"
                        >
                            <option value="all">Semua Prioritas</option>
                            <option value="critical">P1 - Critical</option>
                            <option value="high">P2 - High</option>
                            <option value="medium">P3 - Medium</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                </div>

                <!-- TABLE: ANTREAN TIKET KOMPLAIN KLIEN -->
                <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse" id="ticketsTable">
                            
                            <!-- Table Header -->
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/60 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    <th scope="col" class="py-3 px-5 whitespace-nowrap">ID & PRIORITAS</th>
                                    <th scope="col" class="py-3 px-5 whitespace-nowrap">KLIEN / PROJECT</th>
                                    <th scope="col" class="py-3 px-5 whitespace-nowrap">SUBJEK / MASALAH</th>
                                    <th scope="col" class="py-3 px-5 whitespace-nowrap min-w-[190px]">TEKNISI DITUGASKAN (ASSIGN)</th>
                                    <th scope="col" class="py-3 px-5 whitespace-nowrap min-w-[210px]">STATUS PENANGANAN (WORKFLOW)</th>
                                    <th scope="col" class="py-3 px-5 whitespace-nowrap">WAKTU LAPOR</th>
                                    <th scope="col" class="py-3 px-5 text-center whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>

                            <!-- Table Body -->
                            <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                                
                                @forelse ($tickets as $ticket)
                                    <tr class="hover:bg-slate-50/80 transition-colors ticket-row" data-priority="{{ $ticket['priority_level'] }}" data-id="{{ $ticket['id'] }}">
                                        
                                        <!-- 1. ID & PRIORITAS -->
                                        <td class="py-4 px-5 align-top">
                                            <div class="space-y-1.5">
                                                <span class="inline-block text-xs font-bold text-blue-600 font-mono tracking-tight hover:underline cursor-pointer">
                                                    {{ $ticket['id'] }}
                                                </span>
                                                <div>
                                                    @if ($ticket['priority_level'] === 'critical')
                                                        <span class="inline-block px-2 py-0.5 text-[9px] font-extrabold uppercase rounded bg-rose-600 text-white tracking-wide">
                                                            {{ $ticket['priority'] }}
                                                        </span>
                                                    @elseif ($ticket['priority_level'] === 'high')
                                                        <span class="inline-block px-2 py-0.5 text-[9px] font-extrabold uppercase rounded bg-amber-500 text-white tracking-wide">
                                                            {{ $ticket['priority'] }}
                                                        </span>
                                                    @elseif ($ticket['priority_level'] === 'medium')
                                                        <span class="inline-block px-2 py-0.5 text-[9px] font-extrabold uppercase rounded bg-blue-500 text-white tracking-wide">
                                                            {{ $ticket['priority'] }}
                                                        </span>
                                                    @else
                                                        <span class="inline-block px-2 py-0.5 text-[9px] font-extrabold uppercase rounded bg-slate-500 text-white tracking-wide">
                                                            {{ $ticket['priority'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <!-- 2. KLIEN / PROJECT -->
                                        <td class="py-4 px-5 align-top">
                                            <div class="space-y-0.5 max-w-xs">
                                                <p class="font-bold text-slate-900 text-xs leading-snug">
                                                    {{ $ticket['client_name'] }}
                                                </p>
                                                <p class="text-slate-500 text-[11px] leading-tight">
                                                    {{ $ticket['project_name'] }}
                                                </p>
                                                @if (!empty($ticket['node_code']))
                                                    <p class="text-slate-400 text-[10px] font-mono pt-0.5">
                                                        Node: {{ $ticket['node_code'] }}
                                                    </p>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- 3. SUBJEK / MASALAH -->
                                        <td class="py-4 px-5 align-top">
                                            <div class="space-y-1 max-w-sm">
                                                <p class="font-bold text-slate-900 text-xs leading-snug">
                                                    {{ $ticket['subject'] }}
                                                </p>
                                                <p class="text-slate-500 text-[11px] leading-relaxed line-clamp-2">
                                                    {{ $ticket['description'] }}
                                                </p>
                                            </div>
                                        </td>

                                        <!-- 4. TEKNISI DITUGASKAN (ASSIGN) -->
                                        <td class="py-4 px-5 align-top">
                                            <div>
                                                <div class="relative">
                                                    <select 
                                                        class="w-full appearance-none pl-3 pr-8 py-1.5 text-xs rounded-lg border {{ $ticket['is_tech_warning'] ? 'border-amber-300 bg-amber-50/20' : 'border-slate-200 bg-white' }} text-slate-700 focus:outline-none focus:border-blue-500 shadow-2xs cursor-pointer transition-colors"
                                                    >
                                                        <option value="">-- Belum Ditunjuk --</option>
                                                        @foreach ($technicians as $tech)
                                                            <option value="{{ $tech }}" {{ $ticket['assigned_technician'] === $tech ? 'selected' : '' }}>
                                                                {{ $tech }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </div>
                                                </div>

                                                @if ($ticket['is_tech_warning'])
                                                    <p class="text-[10px] text-amber-600 font-semibold italic mt-1">
                                                        * Wajib tunjuk teknisi
                                                    </p>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- 5. STATUS PENANGANAN (WORKFLOW) -->
                                        <td class="py-4 px-5 align-top">
                                            <div>
                                                <div class="relative">
                                                    @php
                                                        $statusStyle = match($ticket['status_type']) {
                                                            'open' => 'bg-rose-50/70 border-rose-300 text-rose-700',
                                                            'assigned' => 'bg-blue-50/70 border-blue-300 text-blue-700',
                                                            'in_progress' => 'bg-amber-50/70 border-amber-300 text-amber-800',
                                                            'resolved' => 'bg-emerald-50/70 border-emerald-300 text-emerald-700',
                                                            default => 'bg-slate-50 border-slate-300 text-slate-700',
                                                        };
                                                    @endphp
                                                    <select 
                                                        class="w-full appearance-none pl-3 pr-8 py-1.5 text-xs font-semibold rounded-lg border {{ $statusStyle }} shadow-2xs focus:outline-none cursor-pointer transition-all"
                                                    >
                                                        @foreach ($statusOptions as $opt)
                                                            <option value="{{ $opt }}" {{ $ticket['status'] === $opt ? 'selected' : '' }}>
                                                                {{ $opt }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none {{ match($ticket['status_type']) {
                                                        'open' => 'text-rose-400',
                                                        'assigned' => 'text-blue-400',
                                                        'in_progress' => 'text-amber-500',
                                                        'resolved' => 'text-emerald-500',
                                                        default => 'text-slate-400'
                                                    } }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </div>
                                                </div>

                                                @if (!empty($ticket['status_note']))
                                                    <p class="text-[10px] text-emerald-600 font-semibold mt-1">
                                                        {{ $ticket['status_note'] }}
                                                    </p>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- 6. WAKTU LAPOR -->
                                        <td class="py-4 px-5 align-top whitespace-nowrap">
                                            <p class="font-medium text-slate-800 text-xs">
                                                {{ $ticket['report_date'] }}
                                            </p>
                                            <p class="text-[11px] text-slate-400 font-mono">
                                                {{ $ticket['report_time'] }}
                                            </p>
                                        </td>

                                        <!-- 7. AKSI -->
                                        <td class="py-4 px-5 align-top text-center">
                                            <button 
                                                type="button" 
                                                title="Hapus / Batalkan Tiket"
                                                class="delete-btn inline-flex items-center justify-center w-7 h-7 rounded-lg border border-rose-200/90 text-rose-500 hover:text-rose-600 hover:bg-rose-50 active:scale-95 transition-all cursor-pointer"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-xs text-slate-400">
                                            Tidak ada antrean tiket yang tersedia.
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

    <!-- MODAL: BUAT TIKET BARU -->
    <div id="createTicketModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Buat Tiket Gangguan Baru</h3>
                    <p class="text-xs text-slate-400">Input komplain gangguan jaringan klien untuk antrean NOC.</p>
                </div>
                <button id="closeCreateModal" class="text-slate-400 hover:text-slate-600 p-1 rounded-md cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="formNewTicket" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Klien / Perusahaan</label>
                    <input type="text" required placeholder="Contoh: PT. Mitra Sejahtera Mandiri" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Project / Layanan</label>
                    <input type="text" required placeholder="Contoh: Dedicated Internet Broadband 100M" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Node / Host Perangkat</label>
                        <input type="text" placeholder="AP-OUTDOOR-01" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tingkat Prioritas</label>
                        <select class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-blue-500">
                            <option value="critical">P1 - CRITICAL</option>
                            <option value="high" selected>P2 - HIGH</option>
                            <option value="medium">P3 - MEDIUM</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Subjek / Masalah Singkat</label>
                    <input type="text" required placeholder="Contoh: Fluktuasi RTT & Packet Loss Tinggi" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan Tambahan Masalah</label>
                    <textarea rows="3" placeholder="Deskripsikan gejala awal kendala yang dialami pengguna..." class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-blue-500"></textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2.5">
                    <button type="button" id="cancelCreateModal" class="px-3.5 py-2 text-xs font-medium text-slate-600 hover:text-slate-800 bg-slate-100 rounded-lg cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-lg shadow-sm cursor-pointer">
                        Simpan ke Antrean
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: LIHAT LAPORAN SLA -->
    <div id="slaModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Ringkasan SLA & Penanganan</h3>
                    <p class="text-xs text-slate-400">Periode berjalan: Oktober {{ date('Y') }}</p>
                </div>
                <button id="closeSlaModal" class="text-slate-400 hover:text-slate-600 p-1 rounded-md cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-4 text-xs">
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-medium">SLA Ketersediaan Bulanan:</span>
                        <span class="font-bold text-emerald-600">99.82% (Target: 99.5%)</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-medium">MTTR (Mean Time to Resolve):</span>
                        <span class="font-bold text-slate-800">1 Jam 42 Menit</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-medium">MTTD (Mean Time to Detect):</span>
                        <span class="font-bold text-slate-800">&lt; 35 Detik</span>
                    </div>
                </div>

                <div class="text-[11px] text-slate-500 leading-relaxed">
                    Seluruh penanganan insiden SLA P1 wajib diselesaikan maksimal 4 jam sejak tiket dibuat. Insiden P2 memiliki batas eskalasi 8 jam.
                </div>

                <div class="pt-2 flex justify-end">
                    <button id="btnDismissSla" class="px-4 py-2 text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-lg cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Client-side Interactive Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Live Clock WIB
            function updateClock() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                const clockEl = document.getElementById('liveClock');
                if (clockEl) {
                    clockEl.textContent = `${hours}.${minutes}.${seconds} WIB`;
                }
            }
            setInterval(updateClock, 1000);
            updateClock();

            // Mobile Sidebar Toggle
            const openSidebarBtn = document.getElementById('openSidebar');
            const closeSidebarBtn = document.getElementById('closeSidebar');
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (openSidebarBtn && sidebar && backdrop) {
                openSidebarBtn.addEventListener('click', function () {
                    sidebar.classList.remove('-translate-x-full');
                    backdrop.classList.remove('hidden');
                });
            }

            if (closeSidebarBtn && sidebar && backdrop) {
                closeSidebarBtn.addEventListener('click', function () {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                });
            }

            if (backdrop && sidebar) {
                backdrop.addEventListener('click', function () {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                });
            }

            // Real-time Table Filter (Search input & Priority select)
            const searchInput = document.getElementById('tableSearch');
            const prioritySelect = document.getElementById('prioritySelect');
            const rows = document.querySelectorAll('.ticket-row');

            function filterRows() {
                const term = (searchInput ? searchInput.value : '').toLowerCase().trim();
                const priority = (prioritySelect ? prioritySelect.value : 'all').toLowerCase();

                rows.forEach(row => {
                    const rowText = row.textContent.toLowerCase();
                    const rowPriority = (row.getAttribute('data-priority') || '').toLowerCase();

                    const matchesSearch = !term || rowText.includes(term);
                    const matchesPriority = priority === 'all' || rowPriority === priority;

                    if (matchesSearch && matchesPriority) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterRows);
            }

            if (prioritySelect) {
                prioritySelect.addEventListener('change', filterRows);
            }

            // Delete action button
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const row = this.closest('tr');
                    const ticketId = row.getAttribute('data-id');
                    if (confirm(`Apakah Anda yakin ingin menghapus antrean tiket ${ticketId}?`)) {
                        row.remove();
                    }
                });
            });

            // Modals: SLA
            const btnSla = document.getElementById('btnSlaReport');
            const slaModal = document.getElementById('slaModal');
            const closeSla = document.getElementById('closeSlaModal');
            const dismissSla = document.getElementById('btnDismissSla');

            if (btnSla && slaModal) {
                btnSla.addEventListener('click', () => slaModal.classList.remove('hidden'));
            }
            if (closeSla && slaModal) {
                closeSla.addEventListener('click', () => slaModal.classList.add('hidden'));
            }
            if (dismissSla && slaModal) {
                dismissSla.addEventListener('click', () => slaModal.classList.add('hidden'));
            }

            // Modals: Create Ticket
            const btnCreate = document.getElementById('btnCreateTicket');
            const createModal = document.getElementById('createTicketModal');
            const closeCreate = document.getElementById('closeCreateModal');
            const cancelCreate = document.getElementById('cancelCreateModal');
            const formTicket = document.getElementById('formNewTicket');

            if (btnCreate && createModal) {
                btnCreate.addEventListener('click', () => createModal.classList.remove('hidden'));
            }
            if (closeCreate && createModal) {
                closeCreate.addEventListener('click', () => createModal.classList.add('hidden'));
            }
            if (cancelCreate && createModal) {
                cancelCreate.addEventListener('click', () => createModal.classList.add('hidden'));
            }
            if (formTicket && createModal) {
                formTicket.addEventListener('submit', function (e) {
                    e.preventDefault();
                    alert('Tiket komplain baru berhasil ditambahkan ke antrean.');
                    createModal.classList.add('hidden');
                    formTicket.reset();
                });
            }
        });
    </script>
</body>
</html>
