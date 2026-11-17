<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun Customer Baru — Portal Manajemen Jaringan NetFly</title>
    <link rel="icon" href="{{ asset('assets/images/netfly-logo.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Leaflet.js 1.9.4 CSS & JS (OpenStreetMap Interactive Map) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

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
                        <a href="{{ route('accounts.index') }}" class="hover:text-slate-700 transition">DATA & KEMITRAAN</a>
                        <span>›</span>
                        <a href="{{ route('accounts.index') }}" class="hover:text-slate-700 transition">Account Management</a>
                        <span>›</span>
                        <span class="text-slate-800 font-semibold truncate">Buat Akun Customer</span>
                    </nav>
                </div>

                <!-- Right Header Tools -->
                <a
                    href="{{ route('accounts.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 px-3 py-1.5 rounded-lg transition shrink-0"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar Akun</span>
                </a>
            </header>

            <!-- MAIN PAGE CONTENT -->
            <main class="flex-1 p-6 lg:p-8 space-y-6 max-w-4xl mx-auto w-full">

                <!-- PAGE TITLE -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-200 text-[11px] font-bold uppercase tracking-wider mb-2">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            <span>Registrasi Kredensial Customer</span>
                        </div>
                        <h1 class="text-xl lg:text-2xl font-bold text-slate-900 tracking-tight">Buat Akun Customer Baru</h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Terbitkan akun login portal pelanggan untuk perwakilan mitra perusahaan atau customer rumahan.
                        </p>
                    </div>
                </div>

                <!-- Alert Feedback Errors -->
                @if ($errors->any())
                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-xs">
                        <p class="font-bold mb-1">Gagal menyimpan data akun customer:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- FORM CARD CONTAINER -->
                <form action="{{ route('accounts.store') }}" method="POST" id="formAccountCreate" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    @csrf

                    <div class="p-6 lg:p-8 space-y-8">

                        <!-- SECTION 1: PILIH MITRA & IDENTITAS AKUN -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-2.5 pb-2 border-b border-slate-100">
                                <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">
                                    1
                                </div>
                                <div>
                                    <h2 class="text-sm font-bold text-slate-900">Pilih Mitra & Data Perwakilan</h2>
                                    <p class="text-[11px] text-slate-500">Pilih customer terdaftar dan lengkapi kontak resmi pengguna akun.</p>
                                </div>
                            </div>

                            <!-- Pilihan Kategori Customer: Perusahaan vs Rumahan -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Tipe Customer <span class="text-rose-500">*</span>
                                </label>
                                <input type="hidden" name="customer_type" id="customer_type_input" value="{{ old('customer_type', 'perusahaan') }}">
                                <div class="grid grid-cols-2 gap-3 max-w-md">
                                    <button
                                        type="button"
                                        id="btnSelectTypePerusahaan"
                                        class="type-toggle-btn flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border text-xs font-bold transition-all cursor-pointer bg-blue-600 text-white border-blue-600 shadow-xs"
                                    >
                                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span>Customer Perusahaan</span>
                                    </button>
                                    <button
                                        type="button"
                                        id="btnSelectTypeRumahan"
                                        class="type-toggle-btn flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border text-xs font-bold transition-all cursor-pointer bg-white text-slate-700 border-slate-200 hover:bg-slate-50"
                                    >
                                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                        <span>Customer Rumahan</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Dropdown Pilih Customer Terdaftar -->
                            <div>
                                <label for="customer_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" id="labelCustomerSelect">
                                    Mitra Customer Terdaftar <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <select
                                        id="customer_id"
                                        name="customer_id"
                                        required
                                        class="w-full appearance-none pl-3.5 pr-10 py-2.5 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs cursor-pointer @error('customer_id') border-rose-500 @enderror"
                                    >
                                        <option value="" disabled selected id="placeholderCustomerOption">-- Pilih Customer Terdaftar --</option>
                                        @foreach ($customers as $c)
                                            <option
                                                value="{{ $c['id'] }}"
                                                data-type="{{ $c['customer_type'] ?? 'perusahaan' }}"
                                                data-name="{{ $c['name'] }}"
                                                data-code="{{ $c['code'] }}"
                                                data-package="{{ $c['package'] }}"
                                                data-address="{{ $c['address'] ?? '' }}"
                                                data-latitude="{{ $c['latitude'] ?? -6.208763 }}"
                                                data-longitude="{{ $c['longitude'] ?? 106.845599 }}"
                                                data-pic="{{ $c['default_pic'] }}"
                                                data-email="{{ $c['default_email'] }}"
                                                data-phone="{{ $c['default_phone'] }}"
                                                data-prefix="{{ $c['username_prefix'] }}"
                                                {{ (string) old('customer_id') === (string) $c['id'] ? 'selected' : '' }}
                                                class="{{ ($c['customer_type'] ?? 'perusahaan') === 'rumahan' ? 'hidden' : '' }}"
                                            >
                                                {{ $c['name'] }} ({{ $c['code'] }}) — {{ $c['category'] ?? 'Customer' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">
                                    Memilih customer akan mengisi otomatis saran username, email, no. telp, dan titik lokasi pada peta.
                                </p>
                            </div>

                            <!-- Baris 2: Nama PIC & Email -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div id="wrapperPicName">
                                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" id="labelPicName">
                                        Nama Lengkap PIC Perwakilan <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        value="{{ old('name') }}"
                                        required
                                        placeholder="Nama perwakilan penanggung jawab..."
                                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('name') border-rose-500 @enderror"
                                    >
                                    @error('name')
                                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Email Resmi Akun <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        required
                                        placeholder="email.resmi@domain.com"
                                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('email') border-rose-500 @enderror"
                                    >
                                    @error('email')
                                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Baris 3: No. Telepon / WhatsApp -->
                            <div>
                                <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    No. Telepon / WhatsApp
                                </label>
                                <input
                                    type="text"
                                    id="phone"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    placeholder="Contoh: 0812-3456-7890"
                                    class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 font-mono placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('phone') border-rose-500 @enderror"
                                >
                                @error('phone')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- SECTION 2: KEAMANAN & HAK AKSES -->
                        <div class="space-y-4 pt-4 border-t border-slate-100">
                            <div class="flex items-center gap-2.5 pb-2 border-b border-slate-100">
                                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">
                                    2
                                </div>
                                <div>
                                    <h2 class="text-sm font-bold text-slate-900">Kredensial Login & Keamanan</h2>
                                    <p class="text-[11px] text-slate-500">Tentukan username unik dan kata sandi awal untuk akses portal.</p>
                                </div>
                            </div>

                            <!-- Username -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Username Unik <span class="text-rose-500">*</span>
                                    </label>
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
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-mono text-xs font-bold">
                                        @
                                    </div>
                                    <input
                                        type="text"
                                        id="username"
                                        name="username"
                                        value="{{ old('username') }}"
                                        required
                                        pattern="[a-zA-Z0-9_.]+"
                                        placeholder="pic_perusahaan atau nama_customer"
                                        class="w-full pl-8 pr-28 py-2.5 text-xs font-mono font-semibold bg-white border border-slate-200 rounded-lg text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('username') border-rose-500 @enderror"
                                    >
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 hidden" id="usernameValidBadge">
                                            Valid
                                        </span>
                                    </div>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">
                                    Gunakan huruf, angka, atau underscore (_). Minimal 3 karakter dan belum dipakai akun lain.
                                </p>
                                @error('username')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password Awal -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Password Awal <span class="text-rose-500">*</span>
                                    </label>
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
                                <div class="relative">
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        required
                                        minlength="6"
                                        placeholder="Masukkan kata sandi awal (min. 6 karakter)..."
                                        class="w-full pl-3.5 pr-11 py-2.5 text-xs font-mono bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('password') border-rose-500 @enderror"
                                    >
                                    <button
                                        type="button"
                                        id="togglePasswordVisibility"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                                    >
                                        <svg class="w-4 h-4 eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg class="w-4 h-4 eye-closed hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Opsi Tambahan -->
                            <div class="space-y-2.5 pt-2">
                                <label class="flex items-center gap-2.5 cursor-pointer text-xs font-medium text-slate-700">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span>Langsung aktifkan status akun setelah diterbitkan</span>
                                </label>
                                <label class="flex items-center gap-2.5 cursor-pointer text-xs font-medium text-slate-700">
                                    <input type="checkbox" name="must_change_password" value="1" {{ old('must_change_password', '1') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span>Wajibkan pengguna mengubah kata sandi saat pertama kali masuk</span>
                                </label>
                            </div>
                        </div>

                        <!-- SECTION 3: LOKASI & TITIK KOORDINAT PETA -->
                        <div class="space-y-4 pt-4 border-t border-slate-100">
                            <div class="flex items-center gap-2.5 pb-2 border-b border-slate-100">
                                <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">
                                    3
                                </div>
                                <div>
                                    <h2 class="text-sm font-bold text-slate-900">Alamat & Titik Koordinat Peta</h2>
                                    <p class="text-[11px] text-slate-500">Tentukan alamat instalasi atau lokasi kantor untuk pemantauan titik jaringan.</p>
                                </div>
                            </div>

                            <!-- Alamat Lengkap -->
                            <div>
                                <label for="address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Alamat Instalasi / Lokasi Kantor
                                </label>
                                <textarea
                                    id="address"
                                    name="address"
                                    rows="2"
                                    placeholder="Alamat lengkap gedung / jalan..."
                                    class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('address') border-rose-500 @enderror"
                                >{{ old('address') }}</textarea>
                                @error('address')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Peta Interaktif Leaflet & Geocoding -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Titik Koordinat Peta (Geser Pin / Klik Peta)
                                    </label>
                                    <span class="text-[10px] font-semibold text-blue-600">OpenStreetMap Interactive</span>
                                </div>

                                <div class="flex gap-2 mb-2">
                                    <input
                                        type="text"
                                        id="searchMapInput"
                                        placeholder="Cari gedung, jalan, atau kota..."
                                        class="flex-1 px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 transition"
                                    >
                                    <button
                                        type="button"
                                        id="btnSearchMap"
                                        class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg transition cursor-pointer shrink-0"
                                    >
                                        Cari Lokasi
                                    </button>
                                </div>

                                <!-- Leaflet Map Container -->
                                <div id="accountMapContainer" class="w-full h-56 rounded-xl border border-slate-200 z-10 shadow-inner"></div>

                                <!-- Latitude & Longitude Inputs -->
                                <div class="grid grid-cols-2 gap-3 mt-2.5">
                                    <div>
                                        <label for="latitude" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Latitude</label>
                                        <input
                                            type="text"
                                            name="latitude"
                                            id="latitude"
                                            value="{{ old('latitude', '-6.208763') }}"
                                            readonly
                                            class="w-full px-3 py-2 text-xs font-mono bg-slate-50 border border-slate-200 rounded-lg text-slate-700 cursor-not-allowed"
                                        >
                                    </div>
                                    <div>
                                        <label for="longitude" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Longitude</label>
                                        <input
                                            type="text"
                                            name="longitude"
                                            id="longitude"
                                            value="{{ old('longitude', '106.845599') }}"
                                            readonly
                                            class="w-full px-3 py-2 text-xs font-mono bg-slate-50 border border-slate-200 rounded-lg text-slate-700 cursor-not-allowed"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- FOOTER ACTIONS -->
                    <div class="px-6 lg:px-8 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-3">
                        <a
                            href="{{ route('accounts.index') }}"
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
                            <span>Simpan & Terbitkan Akun</span>
                        </button>
                    </div>

                </form>

            </main>

        </div>

    </div>

    <!-- Interactive Logic Script for Dedicated Page -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const customerTypeInput = document.getElementById('customer_type_input');
            const btnPerusahaan = document.getElementById('btnSelectTypePerusahaan');
            const btnRumahan = document.getElementById('btnSelectTypeRumahan');
            const customerSelect = document.getElementById('customer_id');
            const labelCustomerSelect = document.getElementById('labelCustomerSelect');
            const placeholderCustomer = document.getElementById('placeholderCustomerOption');
            const labelPicName = document.getElementById('labelPicName');
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const phoneInput = document.getElementById('phone');
            const addressInput = document.getElementById('address');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const btnSuggestUsername = document.getElementById('btnSuggestUsername');
            const btnGeneratePassword = document.getElementById('btnGeneratePassword');
            const togglePasswordBtn = document.getElementById('togglePasswordVisibility');
            const usernameValidBadge = document.getElementById('usernameValidBadge');

            // 1. Tipe Customer Switch
            function switchCustomerType(type) {
                customerTypeInput.value = type;

                if (type === 'perusahaan') {
                    btnPerusahaan.className = 'type-toggle-btn flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border text-xs font-bold transition-all cursor-pointer bg-blue-600 text-white border-blue-600 shadow-xs';
                    btnRumahan.className = 'type-toggle-btn flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border text-xs font-bold transition-all cursor-pointer bg-white text-slate-700 border-slate-200 hover:bg-slate-50';
                    labelCustomerSelect.innerHTML = 'Mitra Customer Korporat <span class="text-rose-500">*</span>';
                    placeholderCustomer.textContent = '-- Pilih Perusahaan Customer Terdaftar --';
                    labelPicName.innerHTML = 'Nama Lengkap PIC Perwakilan <span class="text-rose-500">*</span>';
                    nameInput.required = true;
                    nameInput.placeholder = 'Contoh: Bambang Kurniawan, S.Kom';
                } else {
                    btnRumahan.className = 'type-toggle-btn flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border text-xs font-bold transition-all cursor-pointer bg-blue-600 text-white border-blue-600 shadow-xs';
                    btnPerusahaan.className = 'type-toggle-btn flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border text-xs font-bold transition-all cursor-pointer bg-white text-slate-700 border-slate-200 hover:bg-slate-50';
                    labelCustomerSelect.innerHTML = 'Customer Rumahan Terdaftar <span class="text-rose-500">*</span>';
                    placeholderCustomer.textContent = '-- Pilih Pelanggan Rumahan Terdaftar --';
                    labelPicName.innerHTML = 'Nama Lengkap Pelanggan <span class="text-slate-400 font-normal">(Opsional)</span>';
                    nameInput.required = false;
                    nameInput.placeholder = 'Nama pelanggan rumahan...';
                }

                // Filter options in select
                Array.from(customerSelect.options).forEach((opt, idx) => {
                    if (idx === 0) return;
                    const optType = opt.getAttribute('data-type');
                    if (optType === type) {
                        opt.classList.remove('hidden');
                    } else {
                        opt.classList.add('hidden');
                    }
                });

                // Reset select value if currently selected option doesn't match type
                const selectedOpt = customerSelect.options[customerSelect.selectedIndex];
                if (selectedOpt && selectedOpt.value && selectedOpt.getAttribute('data-type') !== type) {
                    customerSelect.value = '';
                }
            }

            btnPerusahaan?.addEventListener('click', () => switchCustomerType('perusahaan'));
            btnRumahan?.addEventListener('click', () => switchCustomerType('rumahan'));

            // Initial type setup
            switchCustomerType(customerTypeInput.value || 'perusahaan');

            // 2. Auto-fill when customer is selected
            customerSelect?.addEventListener('change', function () {
                const opt = this.options[this.selectedIndex];
                if (!opt || !opt.value) return;

                const pic = opt.getAttribute('data-pic') || '';
                const email = opt.getAttribute('data-email') || '';
                const phone = opt.getAttribute('data-phone') || '';
                const addr = opt.getAttribute('data-address') || '';
                const lat = parseFloat(opt.getAttribute('data-latitude')) || -6.208763;
                const lng = parseFloat(opt.getAttribute('data-longitude')) || 106.845599;
                const prefix = opt.getAttribute('data-prefix') || '';
                const custName = opt.getAttribute('data-name') || '';

                if (pic) nameInput.value = pic;
                else if (custName) nameInput.value = custName;

                if (email) emailInput.value = email;
                if (phone) phoneInput.value = phone;
                if (addr) addressInput.value = addr;

                // Auto-suggest username if blank
                if (!usernameInput.value.trim()) {
                    let clean = (prefix || custName)
                        .toLowerCase()
                        .replace(/[^a-z0-9]/g, '_')
                        .replace(/_+/g, '_')
                        .replace(/^_|_$/g, '')
                        .slice(0, 15);
                    usernameInput.value = clean ? `${clean}_pic` : 'pic_customer';
                    validateUsername();
                }

                // Move map
                updateMapPosition(lat, lng);
            });

            // 3. Username Validation & Suggestion
            function validateUsername() {
                const val = usernameInput.value.trim();
                const isValid = /^[a-zA-Z0-9_.]{3,50}$/.test(val);
                if (isValid && usernameValidBadge) {
                    usernameValidBadge.classList.remove('hidden');
                } else if (usernameValidBadge) {
                    usernameValidBadge.classList.add('hidden');
                }
            }
            usernameInput?.addEventListener('input', validateUsername);

            btnSuggestUsername?.addEventListener('click', function () {
                const opt = customerSelect.options[customerSelect.selectedIndex];
                const custName = opt && opt.value ? opt.getAttribute('data-name') : (nameInput.value || 'customer');
                const base = custName
                    .toLowerCase()
                    .replace(/[^a-z0-9]/g, '_')
                    .replace(/_+/g, '_')
                    .replace(/^_|_$/g, '')
                    .slice(0, 12) || 'user';
                const rand = Math.floor(100 + Math.random() * 900);
                usernameInput.value = `${base}_${rand}`;
                validateUsername();
            });

            // 4. Password Generator & Toggle Visibility
            btnGeneratePassword?.addEventListener('click', function () {
                const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%';
                let gen = '';
                for (let i = 0; i < 10; i++) {
                    gen += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                passwordInput.value = gen;
                passwordInput.type = 'text';
                document.querySelector('.eye-open')?.classList.add('hidden');
                document.querySelector('.eye-closed')?.classList.remove('hidden');
            });

            togglePasswordBtn?.addEventListener('click', function () {
                const eyeOpen = this.querySelector('.eye-open');
                const eyeClosed = this.querySelector('.eye-closed');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeOpen?.classList.add('hidden');
                    eyeClosed?.classList.remove('hidden');
                } else {
                    passwordInput.type = 'password';
                    eyeOpen?.classList.remove('hidden');
                    eyeClosed?.classList.add('hidden');
                }
            });

            // 5. Leaflet Map Setup
            let mapInstance = null;
            let markerInstance = null;

            function initMap() {
                const mapEl = document.getElementById('accountMapContainer');
                if (!mapEl || typeof L === 'undefined') return;

                const defaultLat = parseFloat(latInput.value) || -6.208763;
                const defaultLng = parseFloat(lngInput.value) || 106.845599;

                mapInstance = L.map('accountMapContainer').setView([defaultLat, defaultLng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap contributors'
                }).addTo(mapInstance);

                markerInstance = L.marker([defaultLat, defaultLng], {
                    draggable: true
                }).addTo(mapInstance);

                markerInstance.on('dragend', function (e) {
                    const coord = e.target.getLatLng();
                    latInput.value = coord.lat.toFixed(6);
                    lngInput.value = coord.lng.toFixed(6);
                });

                mapInstance.on('click', function (e) {
                    markerInstance.setLatLng(e.latlng);
                    latInput.value = e.latlng.lat.toFixed(6);
                    lngInput.value = e.latlng.lng.toFixed(6);
                });
            }

            function updateMapPosition(lat, lng) {
                if (!mapInstance || !markerInstance) return;
                mapInstance.setView([lat, lng], 14);
                markerInstance.setLatLng([lat, lng]);
                latInput.value = lat.toFixed(6);
                lngInput.value = lng.toFixed(6);
            }

            initMap();

            // Geocoding Search
            const btnSearchMap = document.getElementById('btnSearchMap');
            const searchMapInput = document.getElementById('searchMapInput');

            async function performGeocoding() {
                const query = searchMapInput.value.trim();
                if (!query) return;

                const originalBtnText = btnSearchMap.textContent;
                btnSearchMap.textContent = 'Mencari...';
                btnSearchMap.disabled = true;

                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`);
                    const data = await response.json();
                    if (data && data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lng = parseFloat(data[0].lon);
                        updateMapPosition(lat, lng);
                        if (!addressInput.value.trim()) {
                            addressInput.value = data[0].display_name;
                        }
                    } else {
                        alert('Lokasi tidak ditemukan. Silakan gunakan nama jalan atau kota yang lebih spesifik.');
                    }
                } catch (e) {
                    console.error('Geocoding error:', e);
                } finally {
                    btnSearchMap.textContent = originalBtnText;
                    btnSearchMap.disabled = false;
                }
            }

            btnSearchMap?.addEventListener('click', performGeocoding);
            searchMapInput?.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    performGeocoding();
                }
            });
        });
    </script>
</body>
</html>