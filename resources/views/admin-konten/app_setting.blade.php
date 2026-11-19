<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Link Aplikasi Mobile — Portal NetFly</title>
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
                        <a href="{{ route('admin-konten.dashboard') }}" class="tracking-wide uppercase hover:text-slate-700 transition-colors">MANAJEMEN KONTEN</a>
                        <span>›</span>
                        <span class="text-slate-800 font-bold">Pengaturan Aplikasi Mobile</span>
                    </div>
                </div>

                <!-- Right Header Tools -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-netfly-blue bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors border border-blue-200">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        <span>Lihat Landing Page</span>
                    </a>
                </div>
            </header>

            <!-- FLASH ALERTS -->
            @if(session('success'))
                <div class="mx-6 lg:mx-8 mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-emerald-800 text-sm">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mx-6 lg:mx-8 mt-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                    <div class="flex items-center gap-2.5 mb-1 font-bold">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Terjadi kesalahan pada input:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 ml-7">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- MAIN BODY -->
            <main class="p-6 lg:p-8 space-y-6">
                
                <!-- HEADER SECTION -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight">
                            Tautan & Barcode Unduh Aplikasi Mobile
                        </h1>
                        <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                            Atur tautan download aplikasi mobile untuk pengguna secara dinamis. Perubahan link di halaman ini akan otomatis mengubah barcode yang dapat di-scan pengunjung di landing page.
                        </p>
                    </div>
                </div>

                <!-- 2-COLUMN SETTINGS GRID -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    
                    <!-- Form Input Link (Kiri 7 cols) -->
                    <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden flex flex-col">
                        <div class="px-6 py-4.5 border-b border-slate-100 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-netfly-blue flex items-center justify-center font-bold">
                                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Formulir Tautan Unduh</h2>
                                <p class="text-xs text-slate-400">Masukkan tautan APK langsung, link Google Drive, atau URL Google Play Store</p>
                            </div>
                        </div>

                        <form action="{{ route('app-setting.update') }}" method="POST" class="p-6 space-y-5 flex-1 flex flex-col justify-between">
                            @csrf
                            @method('PUT')

                            <div class="space-y-4">
                                <div>
                                    <label for="download_url" class="block text-xs font-bold text-slate-700 mb-1.5">
                                        URL Tautan Unduh Aplikasi <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                            </svg>
                                        </div>
                                        <input type="url"
                                               name="download_url"
                                               id="download_url"
                                               required
                                               value="{{ old('download_url', $downloadUrl) }}"
                                               placeholder="https://netfly.id/download/netfly-mobile.apk"
                                               class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-50/70 border border-slate-200 rounded-xl text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all font-mono">
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">
                                        Tautan ini digunakan sebagai target barcode dan tombol download langsung di landing page publik.
                                    </p>
                                </div>

                                <div class="p-3.5 rounded-xl bg-blue-50/60 border border-blue-100 space-y-1 text-xs text-blue-900">
                                    <div class="font-bold flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-netfly-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Petunjuk Tautan Dinamis</span>
                                    </div>
                                    <p class="text-xs text-blue-800/80 leading-relaxed">
                                        Anda dapat memperbarui link ini kapan saja saat ada rilis versi aplikasi APK baru. Pengunjung yang men-scan barcode di landing page akan langsung diarahkan ke versi URL terbaru yang tersimpan di sini.
                                    </p>
                                </div>

                                @if($lastUpdated)
                                    <div class="text-xs text-slate-400 flex items-center gap-1.5 pt-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Terakhir diperbarui: <strong class="text-slate-600">{{ $lastUpdated->translatedFormat('d M Y, H:i') }} WIB</strong> ({{ $lastUpdated->locale('id')->diffForHumans() }})</span>
                                    </div>
                                @endif
                            </div>

                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                                <a href="{{ route('admin-konten.dashboard') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
                                    &larr; Kembali ke Dashboard
                                </a>
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-netfly-blue hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow transition-all cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Simpan Perubahan Tautan</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Live Barcode Preview Card (Kanan 5 cols) -->
                    <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden flex flex-col">
                        <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-sm font-bold text-slate-900">Pratinjau Barcode (Live)</h2>
                                    <p class="text-xs text-slate-400">Barcode real-time sesuai URL di atas</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Real-time</span>
                            </span>
                        </div>

                        <div class="p-6 flex-1 flex flex-col items-center justify-center text-center">
                            <!-- QR Code Preview Box -->
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 shadow-inner inline-block">
                                <div class="bg-white p-3 rounded-xl shadow-xs border border-slate-200/60 flex items-center justify-center">
                                    <canvas id="adminPreviewQrCanvas" class="w-48 h-48 mx-auto rounded-lg block"></canvas>
                                </div>
                                <div class="mt-3 text-xs text-slate-500 font-medium">
                                    Scan untuk menguji tautan saat ini
                                </div>
                            </div>

                            <div class="mt-5 w-full space-y-2">
                                <a id="testOpenLinkBtn"
                                   href="{{ $downloadUrl }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    <span>Uji Buka Tautan di Tab Baru</span>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

            </main>

        </div>

    </div>
</body>
</html>
