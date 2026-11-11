<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Customer Perusahaan — Portal Manajemen Jaringan NetFly</title>
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
                        <a href="{{ route('customers.index', ['tab' => 'project']) }}" class="hover:text-slate-700 transition">Customer Management</a>
                        <span>›</span>
                        <a href="{{ route('customers.index', ['tab' => 'project']) }}" class="hover:text-slate-700 transition">Customer Perusahaan</a>
                        <span>›</span>
                        <span class="text-slate-800 font-semibold truncate">Tambah Baru</span>
                    </nav>
                </div>

                <!-- Right Header Tools -->
                <a
                    href="{{ route('customers.index', ['tab' => 'project']) }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 px-3 py-1.5 rounded-lg transition shrink-0"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Daftar Perusahaan</span>
                </a>
            </header>

            <!-- MAIN PAGE CONTENT -->
            <main class="flex-1 p-6 lg:p-8 space-y-6 max-w-4xl mx-auto w-full">

                <!-- PAGE HEADER -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-200 text-[11px] font-bold uppercase tracking-wider mb-2">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Kemitraan Korporat & B2B</span>
                        </div>
                        <h1 class="text-xl lg:text-2xl font-bold text-slate-900 tracking-tight">Tambah Customer Perusahaan Baru</h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Daftarkan mitra bisnis perusahaan, nama PIC penanggung jawab, status SLA kontrak, dan alamat kantor operasional.
                        </p>
                    </div>
                </div>

                <!-- Alert Feedback Errors -->
                @if ($errors->any())
                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-xs">
                        <p class="font-bold mb-1">Gagal menyimpan data customer perusahaan:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- FORM CARD -->
                <form action="{{ route('projects.store') }}" method="POST" class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-2xs">
                    @csrf

                    <div class="p-6 lg:p-8 space-y-6">

                        <!-- Row 1: Nama Perusahaan & Status Kontrak -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <div class="sm:col-span-2">
                                <label for="name" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    NAMA PERUSAHAAN / PT <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    required
                                    placeholder="Contoh: PT. Bank Mandiri (Persero) Tbk"
                                    class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('name') border-rose-500 @enderror"
                                >
                                @error('name')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    STATUS KONTRAK <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <select
                                        id="status"
                                        name="status"
                                        required
                                        class="w-full appearance-none pl-3.5 pr-8 py-2.5 text-xs font-semibold bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs cursor-pointer @error('status') border-rose-500 @enderror"
                                    >
                                        @foreach ($statusOptions as $option)
                                            <option value="{{ $option }}" {{ old('status', 'Aktif') === $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                @error('status')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Row 2: Nama PIC Penanggung Jawab -->
                        <div>
                            <label for="pic_name" class="block text-xs font-bold text-slate-700 mb-1.5">
                                NAMA PIC (PENANGGUNG JAWAB TEKNIS / IT) <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="pic_name"
                                name="pic_name"
                                value="{{ old('pic_name') }}"
                                required
                                placeholder="Contoh: Bambang Pratama (IT Infrastructure Lead)"
                                class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('pic_name') border-rose-500 @enderror"
                            >
                            @error('pic_name')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Row 3: Email Resmi & Kontak Telepon/WA -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    EMAIL RESMI PIC / KORPORAT <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    placeholder="contoh: pic.it@perusahaan.co.id"
                                    class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('email') border-rose-500 @enderror"
                                >
                                @error('email')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    KONTAK TELEPON / WA PIC <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="phone"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    required
                                    placeholder="021-xxxx-xxxx atau 0812-xxxx-xxxx"
                                    class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 font-mono placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('phone') border-rose-500 @enderror"
                                >
                                @error('phone')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Row 4: Alamat Kantor Pusat / Cabang -->
                        <div>
                            <label for="address" class="block text-xs font-bold text-slate-700 mb-1.5">
                                ALAMAT KANTOR PUSAT / LOKASI POP CABANG <span class="text-rose-500">*</span>
                            </label>
                            <textarea
                                id="address"
                                name="address"
                                rows="3"
                                required
                                placeholder="Gedung / Menara, Lantai, Jalan, Kawasan Bisnis, Kota..."
                                class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('address') border-rose-500 @enderror"
                            >{{ old('address') }}</textarea>
                            @error('address')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Row 5: Catatan SLA & Layanan Dedicated -->
                        <div>
                            <label for="note" class="block text-xs font-bold text-slate-700 mb-1.5">
                                CATATAN TAMBAHAN SLA / LAYANAN DEDICATED <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input
                                type="text"
                                id="note"
                                name="note"
                                value="{{ old('note') }}"
                                placeholder="Contoh: Dedicated Leased Line 1 Gbps 1:1, SLA Garansi 99.8% 24/7"
                                class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('note') border-rose-500 @enderror"
                            >
                            <p class="text-[11px] text-slate-400 mt-1">
                                Ketentuan Service Level Agreement (SLA), kapasitas bandwidth dedicated, atau perjanjian kontrak khusus.
                            </p>
                            @error('note')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <!-- FOOTER ACTIONS -->
                    <div class="px-6 lg:px-8 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-3">
                        <a
                            href="{{ route('customers.index', ['tab' => 'project']) }}"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-100 rounded-lg transition"
                        >
                            Batal
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg shadow-sm active:scale-95 transition-all cursor-pointer"
                        >
                            <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Customer Perusahaan</span>
                        </button>
                    </div>

                </form>

            </main>

        </div>

    </div>

</body>
</html>
