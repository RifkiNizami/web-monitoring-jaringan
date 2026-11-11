<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Tiket Baru — Antrean Tiket Gangguan NetFly</title>
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
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Mobile Hamburger -->
                    <button id="openSidebar" class="lg:hidden p-1.5 -ml-1 text-slate-600 hover:text-slate-900 rounded-md hover:bg-slate-100 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Breadcrumb -->
                    <nav class="flex items-center gap-2 text-xs font-medium text-slate-400 min-w-0" aria-label="Breadcrumb">
                        <a href="{{ route('tickets.index') }}" class="hover:text-slate-700 transition">Antrean Tiket</a>
                        <span>›</span>
                        <span class="text-slate-800 font-semibold truncate">Buat Tiket Baru</span>
                    </nav>
                </div>

                <!-- Right Header Tools -->
                <a
                    href="{{ route('tickets.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 px-3 py-1.5 rounded-lg transition shrink-0"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Daftar Antrean Tiket</span>
                </a>
            </header>

            <!-- MAIN PAGE CONTENT -->
            <main class="flex-1 p-6 lg:p-8 space-y-6 max-w-4xl mx-auto w-full">

                <!-- PAGE HEADER -->
                <div>
                    <h1 class="text-xl lg:text-2xl font-bold text-slate-900 tracking-tight">Buat Tiket Gangguan Baru</h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Catat komplain gangguan pelanggan, tentukan tingkat urgensi SLA, tunjuk teknisi lapangan, dan masukkan ke antrean penanganan NOC.
                    </p>
                </div>

                <!-- Alert Feedback Errors -->
                @if ($errors->any())
                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-xs">
                        <p class="font-bold mb-1">Gagal membuat tiket insiden:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- FORM CARD -->
                <form action="{{ route('tickets.store') }}" method="POST" class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
                    @csrf

                    <div class="p-6 lg:p-8 space-y-5">

                        <!-- Row 1: Nama Klien & Nama Layanan / Project -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="inputClientName" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Nama Klien / Perusahaan <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    id="inputClientName"
                                    type="text" 
                                    name="client_name" 
                                    list="customerList"
                                    value="{{ old('client_name') }}"
                                    required 
                                    placeholder="Contoh: PT. Bank Mandiri (Persero) Tbk" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                                <datalist id="customerList">
                                    @foreach ($customers as $c)
                                        <option value="{{ $c->name }}">{{ $c->name }} ({{ $c->package ?? $c->category }})</option>
                                    @endforeach
                                </datalist>
                                <p class="text-[11px] text-slate-400 mt-1">Pilih dari rekomendasi pelanggan terdaftar atau ketik manual.</p>
                            </div>

                            <div>
                                <label for="inputProjectName" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Nama Project / Layanan <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    id="inputProjectName"
                                    type="text" 
                                    name="project_name" 
                                    value="{{ old('project_name') }}"
                                    required 
                                    placeholder="Contoh: Dedicated Internet Broadband 500M" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                            </div>
                        </div>

                        <!-- Row 2: Node / Host Perangkat & Tingkat Prioritas -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="inputNodeCode" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Node / Host Perangkat <span class="text-slate-400 font-normal">(Opsional)</span>
                                </label>
                                <input 
                                    id="inputNodeCode"
                                    type="text" 
                                    name="node_code" 
                                    value="{{ old('node_code') }}"
                                    placeholder="Contoh: RTR-CORE-MANDIRI-01" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 font-mono uppercase focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                            </div>

                            <div>
                                <label for="inputPriority" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Tingkat Prioritas (SLA) <span class="text-rose-500">*</span>
                                </label>
                                <select 
                                    id="inputPriority" 
                                    name="priority" 
                                    required 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition cursor-pointer"
                                >
                                    <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>P1 - CRITICAL (Outage / Putus Total)</option>
                                    <option value="high" {{ old('priority', 'high') == 'high' ? 'selected' : '' }}>P2 - HIGH (Degradasi / Packet Loss Tinggi)</option>
                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>P3 - MEDIUM (Permintaan / Non-Kritis)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 3: Tunjuk Teknisi Lapangan -->
                        <div>
                            <label for="inputAssignedTech" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Tunjuk Teknisi Lapangan <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <div class="relative">
                                <select 
                                    id="inputAssignedTech" 
                                    name="assigned_technician" 
                                    class="w-full appearance-none px-3.5 py-2.5 pr-8 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition cursor-pointer"
                                >
                                    <option value="">-- Pilih Teknisi (Opsional) --</option>
                                    @foreach ($technicians as $tech)
                                        @php
                                            $techName = is_object($tech) ? $tech->nama : (is_array($tech) ? $tech['nama'] : $tech);
                                            $techSpec = is_object($tech) ? ($tech->spesialisasi ?? null) : (is_array($tech) ? ($tech['spesialisasi'] ?? null) : null);
                                        @endphp
                                        <option value="{{ $techName }}" {{ old('assigned_technician') == $techName ? 'selected' : '' }}>
                                            {{ $techName }}{{ !empty($techSpec) ? ' (' . $techSpec . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Jika teknisi ditunjuk, tiket otomatis berstatus <strong>Assigned (Ditugaskan)</strong>.</p>
                        </div>

                        <!-- Row 4: Subjek / Ringkasan Masalah -->
                        <div>
                            <label for="inputSubject" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Subjek / Ringkasan Masalah <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                id="inputSubject"
                                type="text" 
                                name="subject" 
                                value="{{ old('subject') }}"
                                required 
                                placeholder="Contoh: Packet Loss Tinggi (>14%) Backbone Menuju Hub Pusat" 
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                            >
                        </div>

                        <!-- Row 5: Keterangan Tambahan / Gejala Masalah -->
                        <div>
                            <label for="inputDescription" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Keterangan Tambahan / Gejala Masalah <span class="text-rose-500">*</span>
                            </label>
                            <textarea 
                                id="inputDescription"
                                name="description" 
                                required 
                                rows="4" 
                                placeholder="Deskripsikan indikasi kendala, kronologi waktu awal terjadinya, hasil traceroute/ping, atau permintaan khusus dari pelanggan..." 
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                            >{{ old('description') }}</textarea>
                        </div>

                    </div>

                    <!-- Card Action Footer -->
                    <div class="px-6 lg:px-8 py-4 bg-slate-50/70 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('tickets.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-lg transition shadow-xs active:scale-95 cursor-pointer">
                            Simpan ke Antrean
                        </button>
                    </div>
                </form>

            </main>
        </div>
    </div>

</body>
</html>
