<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Klien Baru — Admin Konten NetFly</title>
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
                        <span class="tracking-wide uppercase">KONTEN LANDING PAGE</span>
                        <span>›</span>
                        <a href="{{ route('klien.index') }}" class="hover:text-slate-700 transition">Klien Landing Page</a>
                        <span>›</span>
                        <span class="text-slate-800 font-bold truncate">Tambah Klien Baru</span>
                    </nav>
                </div>

                <!-- Right Header Tools -->
                <a
                    href="{{ route('klien.index') }}"
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
                    <div class="text-[11px] font-semibold text-slate-400 mb-1 flex items-center gap-1.5">
                        <span class="text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded border border-blue-200">ADMIN KONTEN</span>
                        <span>›</span>
                        <span>Formulir Kemitraan</span>
                    </div>
                    <h1 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight">
                        Tambah Klien Landing Page
                    </h1>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Lengkapi profil instansi atau perusahaan klien serta unggah logo untuk dipublikasikan pada slider landing page NetFly.
                    </p>
                </div>

                <!-- Alert Feedback Errors -->
                @if ($errors->any())
                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-xs">
                        <div class="flex items-center gap-2 font-bold mb-1.5">
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Gagal menyimpan data klien:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 ml-6">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- FORM CARD -->
                <form action="{{ route('klien.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
                    @csrf

                    <div class="p-6 lg:p-8 space-y-6">

                        <!-- Field 1: Nama Instansi -->
                        <div>
                            <label for="inputName" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Nama Instansi / Perusahaan <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                id="inputName"
                                type="text" 
                                name="name" 
                                value="{{ old('name') }}"
                                required 
                                placeholder="Contoh: PT. Bank Mandiri (Persero) Tbk"
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                            >
                            <p class="text-[11px] text-slate-400 mt-1">Nama resmi perusahaan atau institusi mitra yang akan tampil di landing page.</p>
                        </div>

                        <!-- Field 2: Sektor Industri & Lokasi / Kota -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="inputCategory" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Sektor Industri <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    id="inputCategory"
                                    type="text" 
                                    name="category" 
                                    list="categoryList"
                                    value="{{ old('category') }}"
                                    required 
                                    placeholder="Contoh: Perhotelan / Perbankan / BUMN"
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                                <datalist id="categoryList">
                                    @foreach($existingCategories as $cat)
                                        <option value="{{ $cat }}"></option>
                                    @endforeach
                                    <option value="Perhotelan & Hospitality"></option>
                                    <option value="Perbankan & Fintech"></option>
                                    <option value="Manufaktur & Industri"></option>
                                    <option value="Pemerintahan & BUMN"></option>
                                    <option value="Kesehatan & Rumah Sakit"></option>
                                    <option value="Pendidikan & Kampus"></option>
                                </datalist>
                                <p class="text-[11px] text-slate-400 mt-1">Pilih dari rekomendasi yang ada atau ketik sektor baru.</p>
                            </div>

                            <div>
                                <label for="inputLocation" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Lokasi / Wilayah Operasional <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    id="inputLocation"
                                    type="text" 
                                    name="location" 
                                    value="{{ old('location') }}"
                                    required 
                                    placeholder="Contoh: Jakarta Pusat, Surabaya, atau Nasional"
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                                <p class="text-[11px] text-slate-400 mt-1">Kota atau cakupan regional lokasi klien.</p>
                            </div>
                        </div>

                        <!-- Field 3: Upload Logo & Preview Interaktif -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                File Logo Klien <span class="text-rose-500">*</span>
                            </label>

                            <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-4 rounded-xl bg-slate-50/60 border border-slate-200">
                                <div class="w-20 h-16 rounded-lg bg-white border border-slate-200/80 p-1.5 flex items-center justify-center shrink-0 shadow-2xs">
                                    <img id="logoPreview" src="{{ asset('assets/images/netfly-logo.png') }}" alt="Preview Logo" class="max-h-12 max-w-16 object-contain opacity-50">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <input 
                                        id="inputLogo"
                                        type="file" 
                                        name="logo" 
                                        accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                        required
                                        onchange="previewImage(this)"
                                        class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-netfly-blue hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-lg p-1 bg-white focus:outline-none focus:border-blue-500"
                                    >
                                    <p class="text-[11px] text-slate-400 mt-1.5">
                                        Format didukung: <strong>PNG, SVG, WebP, JPG</strong> transparan. Ukuran maksimal <strong>3 MB</strong>.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Field 4: Urutan Tampilan & URL Website -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="inputOrder" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Urutan Tampilan (Order)
                                </label>
                                <input 
                                    id="inputOrder"
                                    type="number" 
                                    name="order" 
                                    value="{{ old('order', $nextOrder) }}" 
                                    min="0"
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 font-mono focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                                <p class="text-[11px] text-slate-400 mt-1">Angka urutan posisi klien saat ditampilkan di deretan carousel landing page.</p>
                            </div>

                            <div>
                                <label for="inputWebsite" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    URL Website Klien <span class="text-slate-400 font-normal">(Opsional)</span>
                                </label>
                                <input 
                                    id="inputWebsite"
                                    type="url" 
                                    name="website_url" 
                                    value="{{ old('website_url') }}"
                                    placeholder="https://www.mandiri.co.id"
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                                <p class="text-[11px] text-slate-400 mt-1">Tautan opsional ke situs resmi instansi atau profil klien.</p>
                            </div>
                        </div>

                        <!-- Field 5: Visibilitas / Aktif -->
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                            <input 
                                type="checkbox" 
                                id="inputIsActive" 
                                name="is_active" 
                                value="1" 
                                {{ old('is_active', '1') ? 'checked' : '' }}
                                class="mt-0.5 rounded border-slate-300 text-netfly-blue focus:ring-blue-500 cursor-pointer"
                            >
                            <div>
                                <label for="inputIsActive" class="text-xs font-bold text-slate-800 cursor-pointer block">
                                    Publikasikan Langsung ke Landing Page
                                </label>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Jika dicentang, logo dan nama klien akan langsung muncul secara publik pada carousel beranda.
                                </p>
                            </div>
                        </div>

                    </div>

                    <!-- FOOTER ACTIONS -->
                    <div class="px-6 lg:px-8 py-4 bg-slate-50/80 border-t border-slate-200/80 flex items-center justify-between gap-3">
                        <a 
                            href="{{ route('klien.index') }}" 
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
                            <span>Simpan & Terbitkan Klien</span>
                        </button>
                    </div>

                </form>

            </main>

        </div>

    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('logoPreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('opacity-50');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
