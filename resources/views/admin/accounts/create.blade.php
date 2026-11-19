@php
    /** @var \Illuminate\Support\ViewErrorBag $errors */
    /** @var array $customers */
    $customers = $customers ?? [];
@endphp
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
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 px-3.5 py-1.5 rounded-lg transition shrink-0 shadow-2xs"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar Akun</span>
                </a>
            </header>

            <!-- MAIN PAGE CONTENT -->
            <main class="flex-1 p-6 lg:p-8 space-y-6 max-w-3xl mx-auto w-full">

                <!-- SIMPLE TITLE -->
                <div>
                    <h1 class="text-xl lg:text-2xl font-bold text-slate-900 tracking-tight">Buat Akun Customer Baru</h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Terbitkan akun login portal pelanggan untuk mitra perusahaan atau customer rumahan.
                    </p>
                </div>

                <!-- Alert Feedback Errors -->
                @if ($errors->any())
                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-xs shadow-2xs">
                        <p class="font-bold mb-1">Gagal menyimpan data akun customer:</p>
                        <ul class="list-disc list-inside space-y-0.5 ml-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(count($customers) === 0)
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-start gap-3 shadow-2xs">
                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div class="space-y-1">
                            <p class="font-bold text-amber-900">Belum Ada Data Customer Terdaftar</p>
                            <p class="text-amber-700">Untuk membuat akun login, sistem membutuhkan data profil customer terlebih dahulu.</p>
                            <a href="{{ route('customers.index') }}" class="inline-flex items-center gap-1.5 mt-1 font-bold text-blue-700 hover:text-blue-900 underline">
                                <span>Buka Customer Management →</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- FORM CARD CONTAINER (CLEAN & SIMPLE) -->
                <form action="{{ route('accounts.store') }}" method="POST" id="formAccountCreate" class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-2xs">
                    @csrf

                    <!-- Hidden Inputs & Data Manifest -->
                    <input type="hidden" name="customer_type" id="customer_type_input" value="{{ old('customer_type', 'perusahaan') }}">
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', '-6.208763') }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', '106.845599') }}">
                    <script type="application/json" id="customersDataJson">@json($customers)</script>

                    <div class="p-6 lg:p-8 space-y-5">

                        <!-- 1. Tipe Customer Selector -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Tipe Customer <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <button
                                    type="button"
                                    id="btnSelectTypePerusahaan"
                                    onclick="window.switchCustomerType && window.switchCustomerType('perusahaan')"
                                    class="type-toggle-btn flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg text-xs font-bold transition-all cursor-pointer {{ old('customer_type', 'perusahaan') === 'rumahan' ? 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' : 'bg-blue-600 text-white border border-blue-600 shadow-xs' }}"
                                >
                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span>Customer Perusahaan</span>
                                </button>
                                <button
                                    type="button"
                                    id="btnSelectTypeRumahan"
                                    onclick="window.switchCustomerType && window.switchCustomerType('rumahan')"
                                    class="type-toggle-btn flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg text-xs font-bold transition-all cursor-pointer {{ old('customer_type', 'perusahaan') === 'rumahan' ? 'bg-blue-600 text-white border border-blue-600 shadow-xs' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}"
                                >
                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    <span>Customer Rumahan</span>
                                </button>
                            </div>
                        </div>

                        <!-- 2. Pilih Customer Terdaftar -->
                        <div>
                            <label for="customer_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" id="labelCustomerSelect">
                                Mitra Customer Terdaftar <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select
                                    id="customer_id"
                                    name="customer_id"
                                    required
                                    class="w-full appearance-none pl-3.5 pr-10 py-2.5 text-xs font-medium bg-white border rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs cursor-pointer @error('customer_id') border-rose-500 @else border-slate-200 @enderror"
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
                                        >
                                            {{ $c['name'] }} ({{ $c['code'] }}) — {{ $c['category'] ?? 'Customer' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Nama Lengkap PIC / Pelanggan -->
                        <div>
                            <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" id="labelPicName">
                                Nama Lengkap PIC Perwakilan <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                placeholder="Contoh: Bambang Kurniawan, S.Kom"
                                class="w-full px-3.5 py-2.5 text-xs bg-white border rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('name') border-rose-500 @else border-slate-200 @enderror"
                            >
                            @error('name')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 4. Email & No. Telepon (2 Kolom) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                                    class="w-full px-3.5 py-2.5 text-xs bg-white border rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('email') border-rose-500 @else border-slate-200 @enderror"
                                >
                                @error('email')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

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
                                    class="w-full px-3.5 py-2.5 text-xs font-mono bg-white border rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('phone') border-rose-500 @else border-slate-200 @enderror"
                                >
                                @error('phone')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Garis Pemisah Kredensial -->
                        <div class="pt-3 border-t border-slate-100 space-y-4">
                            
                            <!-- Username -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Username Unik <span class="text-rose-500">*</span>
                                    </label>
                                    <button
                                        type="button"
                                        id="btnSuggestUsername"
                                        class="text-[11px] font-semibold text-blue-600 hover:text-blue-800 hover:underline cursor-pointer"
                                    >
                                        Generate Saran
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
                                        placeholder="username_akun"
                                        class="w-full pl-8 pr-20 py-2.5 text-xs font-mono font-semibold bg-white border rounded-lg text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('username') border-rose-500 @else border-slate-200 @enderror"
                                    >
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 hidden" id="usernameValidBadge">
                                            ✓ Valid
                                        </span>
                                    </div>
                                </div>
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
                                        class="text-[11px] font-semibold text-emerald-600 hover:text-emerald-800 hover:underline cursor-pointer"
                                    >
                                        Generate Sandi Kuat
                                    </button>
                                </div>
                                <div class="relative">
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        required
                                        minlength="6"
                                        placeholder="Minimal 6 karakter..."
                                        class="w-full pl-3.5 pr-11 py-2.5 text-xs font-mono bg-white border rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('password') border-rose-500 @else border-slate-200 @enderror"
                                    >
                                    <button
                                        type="button"
                                        id="togglePasswordVisibility"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                                        title="Lihat / Sembunyikan Password"
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

                        </div>

                        <!-- Garis Pemisah Alamat & Lokasi -->
                        <div class="pt-3 border-t border-slate-100 space-y-4">
                            
                            <!-- Alamat Lengkap -->
                            <div>
                                <label for="address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Alamat Instalasi / Lokasi Kantor
                                </label>
                                <textarea
                                    id="address"
                                    name="address"
                                    rows="2"
                                    placeholder="Alamat lengkap gedung, kantor, atau rumah pelanggan..."
                                    class="w-full px-3.5 py-2.5 text-xs bg-white border rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('address') border-rose-500 @else border-slate-200 @enderror"
                                >{{ old('address') }}</textarea>
                                @error('address')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Peta Interaktif Leaflet Ringkas -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Titik Koordinat Peta (Geser Pin / Cari Alamat)
                                    </label>
                                    <span class="text-[11px] font-mono text-slate-500" id="coordDisplay">-6.208763, 106.845599</span>
                                </div>

                                <div class="flex gap-2 mb-2">
                                    <input
                                        type="text"
                                        id="searchMapInput"
                                        placeholder="Cari jalan, gedung, atau kelurahan..."
                                        class="flex-1 px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 transition text-slate-700 placeholder-slate-400"
                                    >
                                    <button
                                        type="button"
                                        id="btnSearchMap"
                                        class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg transition cursor-pointer shrink-0"
                                    >
                                        Cari
                                    </button>
                                </div>

                                <div id="accountMapContainer" style="height: 260px; min-height: 260px; width: 100%;" class="w-full rounded-lg border border-slate-200 shadow-inner z-10 bg-slate-100 overflow-hidden relative">
                                    <div class="absolute inset-0 flex items-center justify-center text-xs text-slate-400 pointer-events-none" id="mapLoadingIndicator">
                                        <svg class="w-4 h-4 mr-2 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                        <span>Memuat peta interaktif OpenStreetMap...</span>
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
        (function () {
            let mapInstance = null;
            let markerInstance = null;

            function initCreateAccount() {
                const form = document.getElementById('formAccountCreate');
                if (!form) return;

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
                const coordDisplay = document.getElementById('coordDisplay');
                const btnSuggestUsername = document.getElementById('btnSuggestUsername');
                const btnGeneratePassword = document.getElementById('btnGeneratePassword');
                const togglePasswordBtn = document.getElementById('togglePasswordVisibility');
                const usernameValidBadge = document.getElementById('usernameValidBadge');

                // Parse customer data passed from server
                let customersList = [];
                try {
                    const jsonEl = document.getElementById('customersDataJson');
                    if (jsonEl && jsonEl.textContent) {
                        customersList = JSON.parse(jsonEl.textContent);
                    }
                } catch (e) {
                    console.error('Gagal membaca data customer:', e);
                }

                // Expose switchCustomerType globally
                window.switchCustomerType = function (type) {
                    if (!customerTypeInput) return;
                    customerTypeInput.value = type;

                    if (type === 'perusahaan') {
                        if (btnPerusahaan) {
                            btnPerusahaan.className = 'type-toggle-btn flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg text-xs font-bold transition-all cursor-pointer bg-blue-600 text-white border border-blue-600 shadow-xs';
                        }
                        if (btnRumahan) {
                            btnRumahan.className = 'type-toggle-btn flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-slate-700 border border-slate-200 hover:bg-slate-50';
                        }
                        if (labelCustomerSelect) labelCustomerSelect.innerHTML = 'Mitra Customer Perusahaan <span class="text-rose-500">*</span>';
                        if (placeholderCustomer) placeholderCustomer.textContent = '-- Pilih Customer Perusahaan Terdaftar --';
                        if (labelPicName) labelPicName.innerHTML = 'Nama Lengkap PIC Perwakilan <span class="text-rose-500">*</span>';
                        if (nameInput) {
                            nameInput.required = true;
                            nameInput.placeholder = 'Contoh: Bambang Kurniawan, S.Kom';
                        }
                    } else {
                        if (btnRumahan) {
                            btnRumahan.className = 'type-toggle-btn flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg text-xs font-bold transition-all cursor-pointer bg-blue-600 text-white border border-blue-600 shadow-xs';
                        }
                        if (btnPerusahaan) {
                            btnPerusahaan.className = 'type-toggle-btn flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-slate-700 border border-slate-200 hover:bg-slate-50';
                        }
                        if (labelCustomerSelect) labelCustomerSelect.innerHTML = 'Customer Rumahan Terdaftar <span class="text-rose-500">*</span>';
                        if (placeholderCustomer) placeholderCustomer.textContent = '-- Pilih Pelanggan Rumahan Terdaftar --';
                        if (labelPicName) labelPicName.innerHTML = 'Nama Lengkap Pelanggan <span class="text-slate-400 font-normal">(Opsional)</span>';
                        if (nameInput) {
                            nameInput.required = false;
                            nameInput.placeholder = 'Nama pelanggan rumahan...';
                        }
                    }

                    // Dynamically populate options in select
                    if (customerSelect) {
                        const previousVal = customerSelect.value;
                        customerSelect.innerHTML = '';

                        const defaultOpt = document.createElement('option');
                        defaultOpt.value = '';
                        defaultOpt.disabled = true;
                        defaultOpt.selected = true;
                        defaultOpt.id = 'placeholderCustomerOption';
                        defaultOpt.textContent = type === 'rumahan'
                            ? '-- Pilih Pelanggan Rumahan Terdaftar --'
                            : '-- Pilih Customer Perusahaan Terdaftar --';
                        customerSelect.appendChild(defaultOpt);

                        const filtered = customersList.filter(c => (c.customer_type || 'perusahaan') === type);

                        if (filtered.length === 0) {
                            const emptyOpt = document.createElement('option');
                            emptyOpt.value = '';
                            emptyOpt.disabled = true;
                            emptyOpt.textContent = type === 'rumahan'
                                ? '(Belum ada data customer rumahan terdaftar)'
                                : '(Belum ada data customer perusahaan terdaftar)';
                            customerSelect.appendChild(emptyOpt);
                        } else {
                            filtered.forEach(c => {
                                const opt = document.createElement('option');
                                opt.value = c.id;
                                opt.setAttribute('data-type', c.customer_type || 'perusahaan');
                                opt.setAttribute('data-name', c.name || '');
                                opt.setAttribute('data-code', c.code || '');
                                opt.setAttribute('data-package', c.package || '');
                                opt.setAttribute('data-address', c.address || '');
                                opt.setAttribute('data-latitude', c.latitude !== null && c.latitude !== undefined ? c.latitude : -6.208763);
                                opt.setAttribute('data-longitude', c.longitude !== null && c.longitude !== undefined ? c.longitude : 106.845599);
                                opt.setAttribute('data-pic', c.default_pic || c.pic_name || '');
                                opt.setAttribute('data-email', c.default_email || c.email || '');
                                opt.setAttribute('data-phone', c.default_phone || c.phone || '');
                                opt.setAttribute('data-prefix', c.username_prefix || '');

                                const codeLabel = c.code ? ` (${c.code})` : '';
                                const catLabel = c.category ? ` — ${c.category}` : '';
                                opt.textContent = `${c.name}${codeLabel}${catLabel}`;

                                if (String(previousVal) === String(c.id)) {
                                    opt.selected = true;
                                    defaultOpt.selected = false;
                                }
                                customerSelect.appendChild(opt);
                            });
                        }

                        if (customerSelect.value) {
                            customerSelect.dispatchEvent(new Event('change'));
                        }
                    }
                };

                btnPerusahaan?.addEventListener('click', () => window.switchCustomerType('perusahaan'));
                btnRumahan?.addEventListener('click', () => window.switchCustomerType('rumahan'));

                // Initial type setup
                const initialType = customerTypeInput ? (customerTypeInput.value || 'perusahaan') : 'perusahaan';
                window.switchCustomerType(initialType);

                // Auto-fill when customer is selected
                customerSelect?.addEventListener('change', function () {
                    const opt = this.options[this.selectedIndex];
                    if (!opt || !opt.value) return;

                    const pic = (opt.getAttribute('data-pic') || '').trim();
                    const email = opt.getAttribute('data-email') || '';
                    const phone = opt.getAttribute('data-phone') || '';
                    const addr = opt.getAttribute('data-address') || '';
                    const lat = parseFloat(opt.getAttribute('data-latitude')) || -6.208763;
                    const lng = parseFloat(opt.getAttribute('data-longitude')) || 106.845599;
                    const prefix = opt.getAttribute('data-prefix') || '';
                    const custName = (opt.getAttribute('data-name') || '').trim();

                    if (nameInput) {
                        if (pic && pic !== '-') {
                            nameInput.value = pic;
                        } else if (custName) {
                            nameInput.value = custName;
                        }
                    }

                    if (emailInput && email) emailInput.value = email;
                    if (phoneInput && phone) phoneInput.value = phone;
                    if (addressInput && addr) addressInput.value = addr;

                    // Auto-suggest username if blank
                    if (usernameInput && !usernameInput.value.trim()) {
                        let clean = (custName || prefix)
                            .toLowerCase()
                            .replace(/[^a-z0-9]/g, '_')
                            .replace(/_+/g, '_')
                            .replace(/^_|_$/g, '')
                            .slice(0, 15);
                        const suffix = (customerTypeInput && customerTypeInput.value === 'rumahan') ? 'home' : 'pic';
                        usernameInput.value = clean ? `${clean}_${suffix}` : `${suffix}_user`;
                        validateUsername();
                    }

                    updateMapPosition(lat, lng);
                });

                // Username Validation
                function validateUsername() {
                    if (!usernameInput) return;
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
                    const opt = customerSelect ? customerSelect.options[customerSelect.selectedIndex] : null;
                    const custName = opt && opt.value ? opt.getAttribute('data-name') : (nameInput ? nameInput.value : 'customer');
                    const base = (custName || 'user')
                        .toLowerCase()
                        .replace(/[^a-z0-9]/g, '_')
                        .replace(/_+/g, '_')
                        .replace(/^_|_$/g, '')
                        .slice(0, 12) || 'user';
                    const rand = Math.floor(100 + Math.random() * 900);
                    if (usernameInput) {
                        usernameInput.value = `${base}_${rand}`;
                        validateUsername();
                    }
                });

                // Password Generator
                btnGeneratePassword?.addEventListener('click', function () {
                    const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%';
                    let gen = '';
                    for (let i = 0; i < 10; i++) {
                        gen += chars.charAt(Math.floor(Math.random() * chars.length));
                    }
                    if (passwordInput) {
                        passwordInput.value = gen;
                        passwordInput.type = 'text';
                    }
                    document.querySelector('.eye-open')?.classList.add('hidden');
                    document.querySelector('.eye-closed')?.classList.remove('hidden');
                });

                togglePasswordBtn?.addEventListener('click', function () {
                    if (!passwordInput) return;
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

                // Leaflet Map Setup with Retry & Size Invalidation
                function initMap(retries = 20) {
                    const mapEl = document.getElementById('accountMapContainer');
                    if (!mapEl) return;

                    if (typeof L === 'undefined') {
                        if (retries > 0) {
                            setTimeout(() => initMap(retries - 1), 150);
                        }
                        return;
                    }

                    if (mapInstance) {
                        try {
                            mapInstance.remove();
                        } catch (e) {}
                        mapInstance = null;
                    }
                    if (mapEl._leaflet_id) {
                        mapEl._leaflet_id = null;
                        mapEl.innerHTML = '';
                    }

                    const defaultLat = parseFloat(latInput ? latInput.value : '') || -6.208763;
                    const defaultLng = parseFloat(lngInput ? lngInput.value : '') || 106.845599;

                    try {
                        mapInstance = L.map('accountMapContainer', {
                            zoomControl: true,
                            scrollWheelZoom: true,
                        }).setView([defaultLat, defaultLng], 13);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '© OpenStreetMap contributors'
                        }).addTo(mapInstance);

                        markerInstance = L.marker([defaultLat, defaultLng], {
                            draggable: true
                        }).addTo(mapInstance);

                        markerInstance.on('dragend', function (e) {
                            const coord = e.target.getLatLng();
                            if (latInput) latInput.value = coord.lat.toFixed(6);
                            if (lngInput) lngInput.value = coord.lng.toFixed(6);
                            if (coordDisplay) coordDisplay.textContent = `${coord.lat.toFixed(6)}, ${coord.lng.toFixed(6)}`;
                        });

                        mapInstance.on('click', function (e) {
                            if (markerInstance) markerInstance.setLatLng(e.latlng);
                            if (latInput) latInput.value = e.latlng.lat.toFixed(6);
                            if (lngInput) lngInput.value = e.latlng.lng.toFixed(6);
                            if (coordDisplay) coordDisplay.textContent = `${e.latlng.lat.toFixed(6)}, ${e.latlng.lng.toFixed(6)}`;
                        });

                        // Pastikan ukuran peta ter-render penuh saat load & Turbo drive
                        setTimeout(() => {
                            if (mapInstance) mapInstance.invalidateSize();
                        }, 200);
                        setTimeout(() => {
                            if (mapInstance) mapInstance.invalidateSize();
                        }, 600);

                        const loader = document.getElementById('mapLoadingIndicator');
                        if (loader) loader.style.display = 'none';
                    } catch (err) {
                        console.error('Leaflet initialization error:', err);
                    }
                }

                function updateMapPosition(lat, lng) {
                    const parsedLat = parseFloat(lat) || -6.208763;
                    const parsedLng = parseFloat(lng) || 106.845599;

                    if (latInput) latInput.value = parsedLat.toFixed(6);
                    if (lngInput) lngInput.value = parsedLng.toFixed(6);
                    if (coordDisplay) coordDisplay.textContent = `${parsedLat.toFixed(6)}, ${parsedLng.toFixed(6)}`;

                    if (!mapInstance || !markerInstance) return;
                    mapInstance.setView([parsedLat, parsedLng], 14);
                    markerInstance.setLatLng([parsedLat, parsedLng]);
                    setTimeout(() => {
                        if (mapInstance) mapInstance.invalidateSize();
                    }, 150);
                }

                initMap();

                // Geocoding Search
                const btnSearchMap = document.getElementById('btnSearchMap');
                const searchMapInput = document.getElementById('searchMapInput');

                async function performGeocoding() {
                    if (!searchMapInput || !btnSearchMap) return;
                    const query = searchMapInput.value.trim();
                    if (!query) return;

                    const originalBtnText = btnSearchMap.textContent;
                    btnSearchMap.textContent = '...';
                    btnSearchMap.disabled = true;

                    try {
                        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`);
                        const data = await response.json();
                        if (data && data.length > 0) {
                            const lat = parseFloat(data[0].lat);
                            const lng = parseFloat(data[0].lon);
                            updateMapPosition(lat, lng);
                            if (addressInput && !addressInput.value.trim()) {
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
            }

            document.addEventListener('turbo:load', initCreateAccount);
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initCreateAccount);
            } else {
                initCreateAccount();
            }
        })();
    </script>
</body>
</html>
