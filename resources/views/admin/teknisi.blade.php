<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Teknisi — Portal Manajemen Jaringan NetFly</title>
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
                        <span class="tracking-wide uppercase">DATA & KEMITRAAN</span>
                        <span>›</span>
                        <span class="text-slate-800 font-bold">Teknisi</span>
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
                
                <!-- HEADER SECTION: TITLE & ACTION BUTTON -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="text-[11px] font-semibold text-slate-400 mb-1 flex items-center gap-1.5">
                            <span class="text-blue-600 font-bold uppercase tracking-wider">TIM LAPANGAN</span>
                            <span>•</span>
                            <span class="text-slate-500 font-medium">NetFly Network Engineers</span>
                        </div>
                        <h1 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight">
                            Database Teknisi
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Daftar personel teknisi, alamat domisili, nomor HP, dan akses langsung ke chat WhatsApp.
                        </p>
                    </div>

                    <!-- Top Action Button: Tambah Teknisi -->
                    <div class="flex items-center gap-2.5 shrink-0">
                        <button 
                            type="button" 
                            id="btnOpenCreateTeknisi"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm hover:shadow active:scale-95 transition-all cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Tambah Teknisi</span>
                        </button>
                    </div>
                </div>

                <!-- NOTIFICATION ALERT (IF FLASH SESSION MESSAGE) -->
                @if (session('success'))
                    <div id="flashAlert" class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-xs text-emerald-800 animate-in fade-in duration-200 shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="p-1 rounded-full bg-emerald-200 text-emerald-800">
                                <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                        <button type="button" onclick="document.getElementById('flashAlert').remove()" class="text-emerald-600 hover:text-emerald-900 font-bold p-1 cursor-pointer">✕</button>
                    </div>
                @endif

                @if ($errors->any())
                    <div id="errorAlert" class="p-4 bg-rose-50 border border-rose-200 rounded-xl flex items-center justify-between text-xs text-rose-800 animate-in fade-in duration-200 shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="p-1 rounded-full bg-rose-200 text-rose-800">
                                <svg class="w-4 h-4 text-rose-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </span>
                            <div>
                                <p class="font-bold">Gagal menyimpan data teknisi:</p>
                                <ul class="list-disc list-inside mt-0.5 text-[11px] text-rose-700">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button type="button" onclick="document.getElementById('errorAlert').remove()" class="text-rose-600 hover:text-rose-900 font-bold p-1 cursor-pointer">✕</button>
                    </div>
                @endif

                <!-- ========================================================================= -->
                <!-- FILTER BAR (SEARCH INPUT & RECORD COUNT)                                  -->
                <!-- ========================================================================= -->
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
                            id="teknisiSearch" 
                            value="{{ $searchQuery ?? '' }}"
                            placeholder="Cari nama teknisi, nomor HP, atau alamat..." 
                            class="w-full pl-10 pr-4 py-2 text-xs bg-white border border-slate-200 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs transition-all"
                        >
                    </div>

                    <!-- Right Info Badge -->
                    <span class="text-xs font-semibold text-slate-500 bg-white border border-slate-200 px-3 py-2 rounded-lg shadow-2xs w-fit">
                        Total: <strong class="text-slate-800" id="tableRecordCount">{{ count($teknisis) }}</strong> Teknisi
                    </span>

                </div>

                <!-- ========================================================================= -->
                <!-- DAFTAR DATABASE TEKNISI (NAMA, NOMOR HP, ALAMAT, WHATSAPP REDIRECT)       -->
                <!-- ========================================================================= -->
                <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs" id="teknisiTable">
                            <thead class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <tr>
                                    <th scope="col" class="py-3.5 px-4 w-12 text-center">#</th>
                                    <th scope="col" class="py-3.5 px-4">Nama</th>
                                    <th scope="col" class="py-3.5 px-4">Nomor HP</th>
                                    <th scope="col" class="py-3.5 px-4">Alamat</th>
                                    <th scope="col" class="py-3.5 px-4 text-center">Aksi & WhatsApp</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                                @forelse ($teknisis as $index => $item)
                                    <tr class="hover:bg-slate-50/70 transition-colors group teknisi-row"
                                        data-id="{{ $item->id }}"
                                        data-nama="{{ $item->nama }}"
                                        data-phone="{{ $item->nomor_hp }}"
                                        data-alamat="{{ $item->alamat }}"
                                        data-search="{{ strtolower($item->nama . ' ' . $item->nomor_hp . ' ' . $item->alamat) }}"
                                    >
                                        <!-- 1. Nomor urut -->
                                        <td class="py-3.5 px-4 text-center text-slate-400 font-mono text-[11px]">
                                            {{ $index + 1 }}
                                        </td>

                                        <!-- 2. Nama -->
                                        <td class="py-3.5 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                                                    {{ strtoupper(substr($item->nama, 0, 2)) }}
                                                </div>
                                                <span class="font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
                                                    {{ $item->nama }}
                                                </span>
                                            </div>
                                        </td>

                                        <!-- 3. Nomor HP & Tautan WhatsApp Cepat -->
                                        <td class="py-3.5 px-4">
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono text-xs font-bold text-slate-800">
                                                    {{ $item->nomor_hp }}
                                                </span>
                                            </div>
                                        </td>

                                        <!-- 4. Alamat -->
                                        <td class="py-3.5 px-4 max-w-md">
                                            <div class="flex items-start gap-1.5 text-slate-600">
                                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span class="text-xs leading-relaxed" title="{{ $item->alamat }}">
                                                    {{ $item->alamat }}
                                                </span>
                                            </div>
                                        </td>

                                        <!-- 5. Aksi: Redirect WhatsApp, Edit, Hapus -->
                                        <td class="py-3.5 px-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                
                                                <!-- TOMBOL WHATSAPP (Redirect ke WhatsApp berbasis nomor HP) -->
                                                <a 
                                                    href="{{ route('teknisi.whatsapp', $item->id) }}" 
                                                    target="_blank" 
                                                    rel="noopener noreferrer"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#25D366] hover:bg-[#20ba59] text-white text-[11px] font-bold rounded-lg shadow-2xs hover:shadow active:scale-95 transition-all cursor-pointer"
                                                    title="Hubungi via WhatsApp"
                                                >
                                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.747.953 3.71 1.456 5.711 1.457h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                                    </svg>
                                                    <span>WhatsApp</span>
                                                </a>

                                                <!-- Edit Button -->
                                                <button 
                                                    type="button" 
                                                    class="btn-edit-teknisi p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer"
                                                    title="Edit Data Teknisi"
                                                    data-id="{{ $item->id }}"
                                                    data-nama="{{ $item->nama }}"
                                                    data-phone="{{ $item->nomor_hp }}"
                                                    data-alamat="{{ $item->alamat }}"
                                                >
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>

                                                <!-- Delete Button -->
                                                <button 
                                                    type="button" 
                                                    class="btn-delete-teknisi p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer"
                                                    title="Hapus Teknisi"
                                                    data-id="{{ $item->id }}"
                                                    data-nama="{{ $item->nama }}"
                                                    data-phone="{{ $item->nomor_hp }}"
                                                >
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="emptyTableRow">
                                        <td colspan="5" class="py-12 text-center text-slate-400">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                </div>
                                                <p class="text-sm font-semibold text-slate-700">Belum Ada Data Teknisi</p>
                                                <p class="text-xs text-slate-400">Klik tombol "Tambah Teknisi" untuk menambahkan teknisi baru.</p>
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

    <!-- ========================================================================= -->
    <!-- MODAL 1: TAMBAH TEKNISI BARU                                              -->
    <!-- Hanya: Nama, Nomor HP, Alamat                                             -->
    <!-- ========================================================================= -->
    <div id="createTeknisiModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs items-center justify-center p-4 hidden [&:not(.hidden)]:flex">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            
            <div class="p-5 border-b border-slate-100 flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 leading-tight">
                            Tambah Teknisi Baru
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            Masukkan data nama, nomor HP, dan alamat
                        </p>
                    </div>
                </div>

                <button type="button" class="close-create-modal text-slate-400 hover:text-slate-600 p-1 rounded-md cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('teknisi.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                
                <!-- 1. Nama -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                        Nama <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama" 
                        required 
                        placeholder="Contoh: Bambang Sutrisno" 
                        class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-medium"
                    >
                </div>

                <!-- 2. Nomor HP -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                        Nomor HP <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#25D366]">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.747.953 3.71 1.456 5.711 1.457h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </div>
                        <input 
                            type="tel" 
                            name="nomor_hp" 
                            required 
                            placeholder="0812-3456-7890" 
                            class="w-full pl-9 pr-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-mono"
                        >
                    </div>
                </div>

                <!-- 3. Alamat -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                        Alamat <span class="text-rose-500">*</span>
                    </label>
                    <textarea 
                        name="alamat" 
                        rows="3" 
                        required 
                        placeholder="Alamat domisili atau wilayah tugas teknisi..." 
                        class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all leading-relaxed"
                    ></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button 
                        type="button" 
                        class="close-create-modal px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-900 transition-colors cursor-pointer"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer"
                    >
                        Simpan Teknisi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 2: EDIT DATA TEKNISI                                                -->
    <!-- Hanya: Nama, Nomor HP, Alamat                                             -->
    <!-- ========================================================================= -->
    <div id="editTeknisiModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs items-center justify-center p-4 hidden [&:not(.hidden)]:flex">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            
            <div class="p-5 border-b border-slate-100 flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 leading-tight">
                            Edit Data Teknisi
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            Perbarui nama, nomor HP, atau alamat
                        </p>
                    </div>
                </div>

                <button type="button" class="close-edit-modal text-slate-400 hover:text-slate-600 p-1 rounded-md cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="editTeknisiForm" method="POST" action="" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <!-- 1. Nama -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                        Nama <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama" 
                        id="edit_nama"
                        required 
                        class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-medium"
                    >
                </div>

                <!-- 2. Nomor HP -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                        Nomor HP <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#25D366]">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.747.953 3.71 1.456 5.711 1.457h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </div>
                        <input 
                            type="tel" 
                            name="nomor_hp" 
                            id="edit_nomor_hp"
                            required 
                            class="w-full pl-9 pr-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-mono"
                        >
                    </div>
                </div>

                <!-- 3. Alamat -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-tight mb-1.5">
                        Alamat <span class="text-rose-500">*</span>
                    </label>
                    <textarea 
                        name="alamat" 
                        id="edit_alamat"
                        rows="3" 
                        required 
                        class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all leading-relaxed"
                    ></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button 
                        type="button" 
                        class="close-edit-modal px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-900 transition-colors cursor-pointer"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 3: HAPUS TEKNISI                                                    -->
    <!-- ========================================================================= -->
    <div id="deleteTeknisiModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs items-center justify-center p-4 hidden [&:not(.hidden)]:flex">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            
            <div class="p-5 border-b border-slate-100 flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-500 border border-rose-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 leading-tight">
                            Hapus Data Teknisi
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            Tindakan ini memerlukan konfirmasi admin
                        </p>
                    </div>
                </div>

                <button type="button" class="close-delete-modal text-slate-400 hover:text-slate-600 p-1 rounded-md cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 text-xs text-slate-600 leading-relaxed">
                    Apakah Anda yakin ingin menghapus teknisi <strong id="deleteTeknisiName" class="text-slate-900"></strong> (<span id="deleteTeknisiPhone" class="font-mono"></span>) dari database sistem?
                </div>
            </div>

            <form id="deleteTeknisiForm" method="POST" action="" class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end gap-3">
                @csrf
                @method('DELETE')
                <button 
                    type="button" 
                    class="close-delete-modal px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-900 transition-colors cursor-pointer"
                >
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer"
                >
                    Ya, Hapus Data
                </button>
            </form>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- INTERACTIVE JAVASCRIPT LOGIC                                              -->
    <!-- ========================================================================= -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // 1. Digital Clock (WIB)
            function updateClock() {
                const clockEl = document.getElementById('liveClock');
                if (!clockEl) return;
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                clockEl.textContent = `${hours}:${minutes}:${seconds} WIB`;
            }
            updateClock();
            setInterval(updateClock, 1000);

            // 2. Mobile Sidebar Toggle
            const openSidebarBtn = document.getElementById('openSidebar');
            const closeSidebarBtn = document.getElementById('closeSidebar');
            const sidebar = document.getElementById('sidebar');

            if (openSidebarBtn && sidebar) {
                openSidebarBtn.addEventListener('click', () => {
                    sidebar.classList.remove('-translate-x-full');
                });
            }
            if (closeSidebarBtn && sidebar) {
                closeSidebarBtn.addEventListener('click', () => {
                    sidebar.classList.add('-translate-x-full');
                });
            }

            // 3. Modal 1: Tambah Teknisi
            const createModal = document.getElementById('createTeknisiModal');
            const btnOpenCreate = document.getElementById('btnOpenCreateTeknisi');
            const closeCreateBtns = document.querySelectorAll('.close-create-modal');

            if (btnOpenCreate && createModal) {
                btnOpenCreate.addEventListener('click', () => {
                    createModal.classList.remove('hidden');
                });
            }
            closeCreateBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    createModal.classList.add('hidden');
                });
            });

            // 4. Modal 2: Edit Data Teknisi
            const editModal = document.getElementById('editTeknisiModal');
            const editForm = document.getElementById('editTeknisiForm');
            const closeEditBtns = document.querySelectorAll('.close-edit-modal');
            const editBtns = document.querySelectorAll('.btn-edit-teknisi');

            editBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const nama = this.getAttribute('data-nama');
                    const phone = this.getAttribute('data-phone');
                    const alamat = this.getAttribute('data-alamat');

                    editForm.action = `/teknisi/${id}`;
                    document.getElementById('edit_nama').value = nama;
                    document.getElementById('edit_nomor_hp').value = phone;
                    document.getElementById('edit_alamat').value = alamat;

                    editModal.classList.remove('hidden');
                });
            });

            closeEditBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    editModal.classList.add('hidden');
                });
            });

            // 5. Modal 3: Hapus Data Teknisi
            const deleteModal = document.getElementById('deleteTeknisiModal');
            const deleteForm = document.getElementById('deleteTeknisiForm');
            const closeDeleteBtns = document.querySelectorAll('.close-delete-modal');
            const deleteBtns = document.querySelectorAll('.btn-delete-teknisi');

            deleteBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const nama = this.getAttribute('data-nama');
                    const phone = this.getAttribute('data-phone');

                    deleteForm.action = `/teknisi/${id}`;
                    document.getElementById('deleteTeknisiName').textContent = nama;
                    document.getElementById('deleteTeknisiPhone').textContent = phone;

                    deleteModal.classList.remove('hidden');
                });
            });

            closeDeleteBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    deleteModal.classList.add('hidden');
                });
            });

            // Close modal on background click
            [createModal, editModal, deleteModal].forEach(modal => {
                if (modal) {
                    modal.addEventListener('click', function (e) {
                        if (e.target === this) {
                            this.classList.add('hidden');
                        }
                    });
                }
            });

            // 6. Realtime Live Search Filter
            const searchInput = document.getElementById('teknisiSearch');
            const rows = document.querySelectorAll('.teknisi-row');
            const countEl = document.getElementById('tableRecordCount');

            function performFilter() {
                const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
                let visibleCount = 0;

                rows.forEach(row => {
                    const searchData = row.getAttribute('data-search') || '';
                    const matchesQuery = !query || searchData.includes(query);

                    if (matchesQuery) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (countEl) {
                    countEl.textContent = visibleCount;
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', performFilter);
            }
        });
    </script>
</body>
</html>
