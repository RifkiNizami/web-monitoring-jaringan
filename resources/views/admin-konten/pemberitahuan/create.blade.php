<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Pemberitahuan Baru — Admin Konten NetFly</title>
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
                    <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 min-w-0" aria-label="Breadcrumb">
                        <span class="tracking-wide uppercase">BROADCAST & PENGUMUMAN</span>
                        <span>›</span>
                        <a href="{{ route('pemberitahuan.index') }}" class="hover:text-slate-700 transition">Kelola Pemberitahuan</a>
                        <span>›</span>
                        <span class="text-slate-800 font-bold truncate">Buat Pemberitahuan Baru</span>
                    </nav>
                </div>

                <!-- Right Header Tools -->
                <a
                    href="{{ route('pemberitahuan.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 px-3 py-1.5 rounded-lg transition shrink-0"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar</span>
                </a>
            </header>

            <!-- MAIN PAGE CONTENT -->
            <main class="flex-1 p-6 lg:p-8 space-y-6 max-w-4xl mx-auto w-full">

                <!-- PAGE HEADER -->
                <div>
                    <h1 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight">
                        Buat Pemberitahuan Baru
                    </h1>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Siarkan informasi pemeliharaan berkala, update firmware, insiden jaringan darurat, atau pengumuman internal untuk tim operasional NOC.
                    </p>
                </div>

                <!-- Alert Feedback Errors -->
                @if ($errors->any())
                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-xs">
                        <div class="flex items-center gap-2 font-bold mb-1.5">
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Gagal mempublikasikan pemberitahuan:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 ml-6">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- FORM CARD -->
                <form action="{{ route('pemberitahuan.store') }}" method="POST" class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
                    @csrf

                    <div class="p-6 lg:p-8 space-y-6">

                        <!-- Field 1: Judul Pengumuman -->
                        <div>
                            <label for="inputTitle" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Judul Pemberitahuan <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                id="inputTitle"
                                type="text" 
                                name="title" 
                                value="{{ old('title') }}"
                                required 
                                placeholder="Contoh: Pemeliharaan Terjadwal (Maintenance) Core Router Wilayah Surabaya"
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                            >
                            <p class="text-[11px] text-slate-400 mt-1">Gunakan judul yang ringkas, deskriptif, dan mudah dipahami oleh tim teknisi/NOC.</p>
                        </div>

                        <!-- Field 2: Kategori & Prioritas & Status -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="selectCategory" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Kategori <span class="text-rose-500">*</span>
                                </label>
                                <select 
                                    id="selectCategory"
                                    name="category" 
                                    required 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition cursor-pointer"
                                >
                                    <option value="maintenance" {{ old('category') === 'maintenance' ? 'selected' : '' }}>Maintenance (Pemeliharaan)</option>
                                    <option value="pembaruan" {{ old('category') === 'pembaruan' ? 'selected' : '' }}>Pembaruan Sistem</option>
                                    <option value="gangguan" {{ old('category') === 'gangguan' ? 'selected' : '' }}>Gangguan Jaringan</option>
                                    <option value="info" {{ old('category', 'info') === 'info' ? 'selected' : '' }}>Pengumuman Info</option>
                                </select>
                            </div>

                            <div>
                                <label for="selectPriority" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Tingkat Urgensi <span class="text-rose-500">*</span>
                                </label>
                                <select 
                                    id="selectPriority"
                                    name="priority" 
                                    required 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition cursor-pointer"
                                >
                                    <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="tinggi" {{ old('priority') === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                                    <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Mendesak (Urgent)</option>
                                    <option value="rendah" {{ old('priority') === 'rendah' ? 'selected' : '' }}>Rendah</option>
                                </select>
                            </div>

                            <div>
                                <label for="selectStatus" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Status Awal Siaran <span class="text-rose-500">*</span>
                                </label>
                                <select 
                                    id="selectStatus"
                                    name="status" 
                                    required 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition cursor-pointer"
                                >
                                    <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif (Disiarkan)</option>
                                    <option value="selesai" {{ old('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="diarsipkan" {{ old('status') === 'diarsipkan' ? 'selected' : '' }}>Diarsipkan</option>
                                </select>
                            </div>
                        </div>

                        <!-- Field 3: Jadwal Mulai & Estimasi Selesai -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="inputScheduledAt" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Jadwal Mulai Pelaksanaan <span class="text-slate-400 font-normal">(Opsional)</span>
                                </label>
                                <input 
                                    id="inputScheduledAt"
                                    type="datetime-local" 
                                    name="scheduled_at" 
                                    value="{{ old('scheduled_at') }}"
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 font-mono focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                                <p class="text-[11px] text-slate-400 mt-1">Kosongkan jika pemberitahuan bersifat pengumuman langsung/umum.</p>
                            </div>

                            <div>
                                <label for="inputExpiresAt" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Estimasi Selesai / Kedaluwarsa <span class="text-slate-400 font-normal">(Opsional)</span>
                                </label>
                                <input 
                                    id="inputExpiresAt"
                                    type="datetime-local" 
                                    name="expires_at" 
                                    value="{{ old('expires_at') }}"
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 font-mono focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                                <p class="text-[11px] text-slate-400 mt-1">Batas waktu pemeliharaan berakhir atau masa tayang informasi.</p>
                            </div>
                        </div>

                        <!-- Field 4: Detail Pesan & Instruksi Siaran -->
                        <div>
                            <label for="inputMessage" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Detail Pesan & Instruksi Siaran <span class="text-rose-500">*</span>
                            </label>
                            <textarea 
                                id="inputMessage"
                                name="message" 
                                rows="6" 
                                required 
                                placeholder="Jelaskan secara terperinci rincian pekerjaan pemeliharaan, perangkat yang terdampak, langkah mitigasi jalur redundansi, atau nomor kontak petugas penanggung jawab..."
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition leading-relaxed"
                            >{{ old('message') }}</textarea>
                            <p class="text-[11px] text-slate-400 mt-1">Mendukung format baris baru (*line break*) agar tampilan pesan tertata rapi.</p>
                        </div>

                    </div>

                    <!-- FOOTER ACTIONS -->
                    <div class="px-6 lg:px-8 py-4 bg-slate-50/80 border-t border-slate-200/80 flex items-center justify-between gap-3">
                        <a 
                            href="{{ route('pemberitahuan.index') }}" 
                            class="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-white border border-slate-200 hover:bg-slate-100 rounded-lg transition-colors inline-flex items-center gap-1.5"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Batal</span>
                        </a>

                        <button 
                            type="submit" 
                            class="px-5 py-2.5 text-xs font-bold text-white bg-netfly-blue hover:bg-blue-700 rounded-lg shadow-xs hover:shadow transition-all inline-flex items-center gap-2 cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Publikasikan Siaran</span>
                        </button>
                    </div>

                </form>

            </main>

        </div>

    </div>

</body>
</html>
