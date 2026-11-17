<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Teknisi Baru — Portal Manajemen Jaringan NetFly</title>
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
                    <!-- Collapsible Sidebar Navbar Toggle Button -->
                    <button id="openSidebar" type="button" class="sidebar-toggle-btn p-1.5 -ml-1 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 cursor-pointer transition-colors" title="Buka / Tutup Menu Navigasi (Ctrl+B)" aria-label="Toggle menu navigasi" aria-expanded="true">
                        <svg class="w-5 h-5 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path class="bar-top" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16" />
                            <path class="bar-mid" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16" />
                            <path class="bar-bot" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 18h16" />
                        </svg>
                    </button>

                    <!-- Breadcrumb -->
                    <nav class="flex items-center gap-2 text-xs font-medium text-slate-400 min-w-0" aria-label="Breadcrumb">
                        <a href="{{ route('teknisi.index') }}" class="hover:text-slate-700 transition">DATA & KEMITRAAN</a>
                        <span>›</span>
                        <a href="{{ route('teknisi.index') }}" class="hover:text-slate-700 transition">Teknisi</a>
                        <span>›</span>
                        <span class="text-slate-800 font-semibold truncate">Tambah Teknisi Baru</span>
                    </nav>
                </div>

                <!-- Right Header Tools -->
                <a
                    href="{{ route('teknisi.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 px-3 py-1.5 rounded-lg transition shrink-0"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Database Teknisi</span>
                </a>
            </header>

            <!-- MAIN PAGE CONTENT -->
            <main class="flex-1 p-6 lg:p-8 space-y-6 max-w-3xl mx-auto w-full">

                <!-- PAGE TITLE -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-200 text-[11px] font-bold uppercase tracking-wider mb-2">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Tim Lapangan NetFly</span>
                        </div>
                        <h1 class="text-xl lg:text-2xl font-bold text-slate-900 tracking-tight">Tambah Teknisi Baru</h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Daftarkan personel teknisi lapangan baru, nomor kontak WhatsApp, dan wilayah penugasan atau alamat domisili.
                        </p>
                    </div>
                </div>

                <!-- Alert Feedback Errors -->
                @if ($errors->any())
                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-xs">
                        <p class="font-bold mb-1">Gagal menyimpan data teknisi:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- FORM CARD CONTAINER -->
                <form action="{{ route('teknisi.store') }}" method="POST" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    @csrf

                    <div class="p-6 lg:p-8 space-y-6">

                        <!-- Field 1: Nama Lengkap -->
                        <div>
                            <label for="nama" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nama Lengkap Teknisi <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="nama"
                                name="nama"
                                value="{{ old('nama') }}"
                                required
                                placeholder="Contoh: Bambang Sutrisno"
                                class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs font-medium @error('nama') border-rose-500 @enderror"
                            >
                            @error('nama')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Field 2: Nomor HP / WhatsApp -->
                        <div>
                            <label for="nomor_hp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nomor HP / WhatsApp <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#25D366]">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.747.953 3.71 1.456 5.711 1.457h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                </div>
                                <input
                                    type="tel"
                                    id="nomor_hp"
                                    name="nomor_hp"
                                    value="{{ old('nomor_hp') }}"
                                    required
                                    placeholder="Contoh: 0812-3456-7890"
                                    class="w-full pl-10 pr-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 font-mono placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('nomor_hp') border-rose-500 @enderror"
                                >
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">
                                Pastikan nomor aktif untuk keperluan koordinasi cepat via tautan langsung WhatsApp.
                            </p>
                            @error('nomor_hp')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Field 3: Alamat Domisili / Wilayah Tugas -->
                        <div>
                            <label for="alamat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Alamat Domisili / Pos Penugasan <span class="text-rose-500">*</span>
                            </label>
                            <textarea
                                id="alamat"
                                name="alamat"
                                rows="3"
                                required
                                placeholder="Alamat domisili lengkap atau cakupan area kerja lapangan..."
                                class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs leading-relaxed @error('alamat') border-rose-500 @enderror"
                            >{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <!-- FOOTER ACTIONS -->
                    <div class="px-6 lg:px-8 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-3">
                        <a
                            href="{{ route('teknisi.index') }}"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-100 rounded-lg transition"
                        >
                            Batal
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm hover:shadow active:scale-95 transition-all cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Teknisi</span>
                        </button>
                    </div>

                </form>

            </main>

        </div>

    </div>
</body>
</html>