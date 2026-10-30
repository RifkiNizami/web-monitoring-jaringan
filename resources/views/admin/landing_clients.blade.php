<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Klien Landing Page — Portal NetFly</title>
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
                        <span class="tracking-wide uppercase">KONTEN LANDING PAGE</span>
                        <span>›</span>
                        <span class="text-slate-800 font-bold">Klien Landing Page</span>
                    </div>
                </div>

                <!-- Right Header Tools -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}#klien" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-netfly-blue bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors border border-blue-200">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        <span>Pratinjau di Landing Page</span>
                    </a>

                    <!-- Realtime Digital Clock -->
                    <div class="hidden sm:flex items-center gap-1.5 text-xs font-mono text-slate-600 bg-slate-100/80 px-2.5 py-1 rounded-md border border-slate-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="liveClock">--:--:-- WIB</span>
                    </div>
                </div>
            </header>

            <!-- FLASH ALERTS -->
            @if(session('success'))
                <div class="mx-6 lg:mx-8 mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-emerald-800 text-sm">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="mx-6 lg:mx-8 mt-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Periksa kembali isian formulir:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 ml-7">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- MAIN BODY PADDING -->
            <main class="p-6 lg:p-8 space-y-6">
                
                <!-- HEADER & ACTION -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="text-[11px] font-semibold text-slate-400 mb-1 flex items-center gap-1.5">
                            <span class="text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded border border-blue-200">CRUD KLIEN LANDING</span>
                            <span>›</span>
                            <span>Kemitraan Publik</span>
                        </div>
                        <h1 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight">
                            Kelola Klien Landing Page
                        </h1>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Unggah logo klien baru, perbarui data nama & sektor industri, serta kelola visibilitas pada slider landing page.
                        </p>
                    </div>

                    <!-- Tombol Tambah Klien Baru -->
                    <button onclick="openModal('addClientModal')" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-netfly-blue hover:bg-blue-700 rounded-xl shadow-sm transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Klien Baru</span>
                    </button>
                </div>

                <!-- 3 STATS PILLS -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Klien Terdaftar</span>
                            <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalClients }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-netfly-blue flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Aktif di Landing Page</span>
                            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $activeClients }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Nonaktif / Draft</span>
                            <p class="text-2xl font-black text-slate-500 mt-1">{{ $inactiveClients }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- FILTER & SEARCH BAR -->
                <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs">
                    <form method="GET" action="{{ route('landing-clients.index') }}" class="flex flex-col sm:flex-row gap-3">
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
                                placeholder="Cari nama klien, sektor, atau lokasi..." 
                                class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 transition-all"
                            >
                        </div>

                        <div class="flex gap-2">
                            <select name="category" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:bg-white focus:border-blue-500">
                                <option value="all">Semua Sektor Industri</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>

                            <select name="status" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:bg-white focus:border-blue-500">
                                <option value="all">Semua Status</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif di Landing</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif (Draft)</option>
                            </select>

                            @if(request('search') || request('category') || request('status'))
                                <a href="{{ route('landing-clients.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span>Reset</span>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- TABLE DAFTAR KLIEN -->
                <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900">
                            Daftar Klien & Logo Landing Page ({{ count($clients) }})
                        </h2>
                        <span class="text-xs text-slate-400">Urutan menentukan posisi pada slider landing page</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/75 border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                    <th class="px-5 py-3">Urutan</th>
                                    <th class="px-5 py-3">Logo</th>
                                    <th class="px-5 py-3">Nama Instansi / Klien</th>
                                    <th class="px-5 py-3">Sektor Industri</th>
                                    <th class="px-5 py-3">Lokasi</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                                @forelse($clients as $client)
                                    <tr class="hover:bg-slate-50/80 transition-colors group">
                                        <!-- Urutan -->
                                        <td class="px-5 py-3.5 font-mono font-bold text-slate-400">
                                            #{{ $client->order ?: $loop->iteration }}
                                        </td>

                                        <!-- Logo -->
                                        <td class="px-5 py-3.5">
                                            <div class="w-16 h-12 rounded-lg bg-slate-100 border border-slate-200 p-1 flex items-center justify-center shadow-2xs">
                                                <img src="{{ $client->logo_url }}" alt="{{ $client->name }}" class="max-h-9 max-w-14 object-contain" onerror="this.onerror=null; this.src='{{ asset('assets/images/netfly-logo.png') }}';">
                                            </div>
                                        </td>

                                        <!-- Nama -->
                                        <td class="px-5 py-3.5">
                                            <div class="font-bold text-slate-900">{{ $client->name }}</div>
                                            <div class="text-[10px] text-slate-400">Diperbarui: {{ $client->updated_at ? $client->updated_at->diffForHumans() : '-' }}</div>
                                        </td>

                                        <!-- Sektor -->
                                        <td class="px-5 py-3.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                                {{ $client->category }}
                                            </span>
                                        </td>

                                        <!-- Lokasi -->
                                        <td class="px-5 py-3.5 text-slate-600">
                                            {{ $client->location }}
                                        </td>

                                        <!-- Status & Quick Toggle -->
                                        <td class="px-5 py-3.5">
                                            <form action="{{ route('landing-clients.toggle-status', $client->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" title="Klik untuk mengubah status" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold cursor-pointer transition-colors {{ $client->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border border-slate-200 hover:bg-slate-200' }}">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ $client->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                                    <span>{{ $client->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                                </button>
                                            </form>
                                        </td>

                                        <!-- Aksi -->
                                        <td class="px-5 py-3.5 text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <!-- Edit Button -->
                                                <button 
                                                    onclick="openEditModal({{ json_encode($client) }})"
                                                    class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors cursor-pointer" 
                                                    title="Edit Klien"
                                                >
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>

                                                <!-- Delete Button -->
                                                <button 
                                                    onclick="openDeleteModal({{ $client->id }}, '{{ addslashes($client->name) }}')"
                                                    class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-colors cursor-pointer" 
                                                    title="Hapus Klien"
                                                >
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-8 h-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                <p class="font-semibold text-slate-600">Tidak ada klien yang cocok dengan pencarian.</p>
                                                <p class="text-xs text-slate-400 mt-0.5">Silakan reset filter atau tambahkan klien baru.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>

        </div>

    </div>

    <!-- ==========================================
         MODAL 1: TAMBAH KLIEN BARU
         ========================================== -->
    <div id="addClientModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl w-full max-w-lg overflow-hidden animate-scale">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Tambah Klien Baru ke Landing Page</h3>
                    <p class="text-xs text-slate-400">Unggah logo dan lengkapi rincian instansi mitra</p>
                </div>
                <button onclick="closeModal('addClientModal')" class="text-slate-400 hover:text-slate-600 p-1 rounded-md">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('landing-clients.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf

                <!-- Nama Instansi -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Nama Instansi / Perusahaan <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        required 
                        placeholder="Contoh: PT. Bank Central Asia, Tbk"
                        class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500"
                    >
                </div>

                <!-- Sektor & Lokasi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Sektor Industri <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="category" 
                            required 
                            placeholder="Contoh: Perhotelan / Manufaktur"
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Lokasi / Wilayah <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="location" 
                            required 
                            placeholder="Contoh: Surabaya / Global"
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500"
                        >
                    </div>
                </div>

                <!-- File Upload Logo -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Upload File Logo <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="file" 
                        name="logo" 
                        accept="image/png,image/jpeg,image/webp,image/svg+xml"
                        required
                        class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-netfly-blue hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-lg p-1 bg-slate-50"
                    >
                    <p class="text-[10px] text-slate-400 mt-1">Format didukung: PNG, WebP, JPG, SVG transparan. Maks 3 MB.</p>
                </div>

                <!-- Urutan & URL Website -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Urutan Tampilan (Order)
                        </label>
                        <input 
                            type="number" 
                            name="order" 
                            value="{{ $totalClients + 1 }}" 
                            min="0"
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 font-mono"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            URL Website (Opsional)
                        </label>
                        <input 
                            type="url" 
                            name="website_url" 
                            placeholder="https://example.com"
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500"
                        >
                    </div>
                </div>

                <!-- Checkbox Aktif -->
                <div class="pt-2 flex items-center gap-2">
                    <input type="checkbox" id="add_is_active" name="is_active" value="1" checked class="rounded border-slate-300 text-netfly-blue focus:ring-blue-500">
                    <label for="add_is_active" class="text-xs font-semibold text-slate-700 cursor-pointer">
                        Langsung publikasikan dan tampilkan di carousel landing page
                    </label>
                </div>

                <!-- Modal Actions -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeModal('addClientModal')" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-netfly-blue hover:bg-blue-700 rounded-lg shadow-sm transition-all cursor-pointer">
                        Simpan & Terbitkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==========================================
         MODAL 2: EDIT KLIEN
         ========================================== -->
    <div id="editClientModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl w-full max-w-lg overflow-hidden animate-scale">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Perbarui Data Klien</h3>
                    <p class="text-xs text-slate-400">Edit informasi atau ganti file logo instansi</p>
                </div>
                <button onclick="closeModal('editClientModal')" class="text-slate-400 hover:text-slate-600 p-1 rounded-md">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="editClientForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <!-- Nama Instansi -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Nama Instansi / Perusahaan <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="edit_name"
                        name="name" 
                        required 
                        class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500"
                    >
                </div>

                <!-- Sektor & Lokasi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Sektor Industri <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="edit_category"
                            name="category" 
                            required 
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Lokasi / Wilayah <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="edit_location"
                            name="location" 
                            required 
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500"
                        >
                    </div>
                </div>

                <!-- Preview Logo Saat Ini & Upload Baru -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Logo Saat Ini & Ganti Logo
                    </label>
                    <div class="flex items-center gap-3 mb-2 p-2 rounded-lg bg-slate-50 border border-slate-200">
                        <div class="w-16 h-12 rounded bg-white border border-slate-200 p-1 flex items-center justify-center">
                            <img id="edit_logo_preview" src="" alt="Preview" class="max-h-9 max-w-14 object-contain">
                        </div>
                        <span class="text-[11px] text-slate-500">Pilih file baru di bawah ini hanya jika ingin mengganti logo.</span>
                    </div>
                    <input 
                        type="file" 
                        name="logo" 
                        accept="image/png,image/jpeg,image/webp,image/svg+xml"
                        class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-netfly-blue hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-lg p-1 bg-slate-50"
                    >
                </div>

                <!-- Urutan & URL Website -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Urutan Tampilan (Order)
                        </label>
                        <input 
                            type="number" 
                            id="edit_order"
                            name="order" 
                            min="0"
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 font-mono"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            URL Website (Opsional)
                        </label>
                        <input 
                            type="url" 
                            id="edit_website_url"
                            name="website_url" 
                            placeholder="https://example.com"
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500"
                        >
                    </div>
                </div>

                <!-- Checkbox Aktif -->
                <div class="pt-2 flex items-center gap-2">
                    <input type="checkbox" id="edit_is_active" name="is_active" value="1" class="rounded border-slate-300 text-netfly-blue focus:ring-blue-500">
                    <label for="edit_is_active" class="text-xs font-semibold text-slate-700 cursor-pointer">
                        Aktif dan tampilkan pada carousel landing page publik
                    </label>
                </div>

                <!-- Modal Actions -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeModal('editClientModal')" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-netfly-blue hover:bg-blue-700 rounded-lg shadow-sm transition-all cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==========================================
         MODAL 3: KONFIRMASI HAPUS
         ========================================== -->
    <div id="deleteClientModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl w-full max-w-sm overflow-hidden animate-scale">
            <div class="p-6 text-center space-y-3">
                <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">Hapus Klien Ini?</h3>
                <p class="text-xs text-slate-500">
                    Apakah Anda yakin ingin menghapus <span id="deleteClientName" class="font-bold text-slate-800"></span> dari landing page? Tindakan ini tidak dapat dibatalkan.
                </p>

                <form id="deleteClientForm" method="POST" class="pt-4 flex items-center justify-center gap-2.5">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeModal('deleteClientModal')" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-sm transition-all cursor-pointer">
                        Ya, Hapus Klien
                    </button>
                </form>
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

        function openEditModal(client) {
            const form = document.getElementById('editClientForm');
            form.action = `/landing-clients/${client.id}`;
            document.getElementById('edit_name').value = client.name || '';
            document.getElementById('edit_category').value = client.category || '';
            document.getElementById('edit_location').value = client.location || '';
            document.getElementById('edit_order').value = client.order || 0;
            document.getElementById('edit_website_url').value = client.website_url || '';
            document.getElementById('edit_is_active').checked = !!client.is_active;

            // Logo preview
            const preview = document.getElementById('edit_logo_preview');
            if (client.logo) {
                if (client.logo.startsWith('http') || client.logo.startsWith('assets/') || client.logo.startsWith('uploads/')) {
                    preview.src = `/${client.logo}`;
                } else {
                    preview.src = `/uploads/clients/${client.logo}`;
                }
            } else {
                preview.src = '/assets/images/netfly-logo.png';
            }

            openModal('editClientModal');
        }

        function openDeleteModal(id, name) {
            const form = document.getElementById('deleteClientForm');
            form.action = `/landing-clients/${id}`;
            document.getElementById('deleteClientName').textContent = name;
            openModal('deleteClientModal');
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
