<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer Rumahan — {{ $customer->name }} ({{ $customer->code }})</title>
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
                        <a href="{{ route('customers.index', ['tab' => 'customer']) }}" class="hover:text-slate-700 transition">Customer Management</a>
                        <span>›</span>
                        <a href="{{ route('customers.index', ['tab' => 'customer']) }}" class="hover:text-slate-700 transition">Customer Rumahan</a>
                        <span>›</span>
                        <span class="text-slate-800 font-semibold truncate">Edit #{{ $customer->code }}</span>
                    </nav>
                </div>

                <!-- Right Header Tools -->
                <a
                    href="{{ route('customers.index', ['tab' => 'customer']) }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 px-3 py-1.5 rounded-lg transition shrink-0"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Daftar Customer</span>
                </a>
            </header>

            <!-- MAIN PAGE CONTENT -->
            <main class="flex-1 p-6 lg:p-8 space-y-6 max-w-4xl mx-auto w-full">

                <!-- PAGE HEADER -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <span>Customer Rumahan</span>
                            </span>
                            <span class="font-mono text-xs px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 font-bold border border-slate-200">
                                #{{ $customer->code }}
                            </span>
                        </div>
                        <h1 class="text-xl lg:text-2xl font-bold text-slate-900 tracking-tight">
                            Edit Data Customer: <span class="text-blue-600">{{ $customer->name }}</span>
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Perbarui rincian kontak, alamat, status langganan, dan catatan operasional pelanggan.
                        </p>
                    </div>
                </div>

                <!-- Alert Feedback Errors -->
                @if ($errors->any())
                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-xs">
                        <p class="font-bold mb-1">Gagal memperbarui data customer:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- FORM CARD -->
                <form action="{{ route('customers.update', $customer->id) }}" method="POST" class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-2xs">
                    @csrf
                    @method('PUT')

                    <div class="p-6 lg:p-8 space-y-6">

                        <!-- Row 1: Nama Customer & Status Kontrak -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <div class="sm:col-span-2">
                                <label for="name" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    NAMA LENGKAP CUSTOMER <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name', $customer->name) }}"
                                    required
                                    placeholder="Contoh: Budi Santoso"
                                    class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('name') border-rose-500 @enderror"
                                >
                                @error('name')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    STATUS LAYANAN <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <select
                                        id="status"
                                        name="status"
                                        required
                                        class="w-full appearance-none pl-3.5 pr-8 py-2.5 text-xs font-semibold bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs cursor-pointer @error('status') border-rose-500 @enderror"
                                    >
                                        @foreach ($statusOptions as $option)
                                            <option value="{{ $option }}" {{ old('status', $customer->status) === $option ? 'selected' : '' }}>
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

                        <!-- Row 2: Email Resmi & Kontak Telepon/WA -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    EMAIL PELANGGAN <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email', $customer->email) }}"
                                    required
                                    placeholder="contoh: budi.santoso@email.com"
                                    class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('email') border-rose-500 @enderror"
                                >
                                @error('email')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    KONTAK TELEPON / WHATSAPP <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="phone"
                                    name="phone"
                                    value="{{ old('phone', $customer->phone) }}"
                                    required
                                    placeholder="0812-xxxx-xxxx atau 021-xxxx"
                                    class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 font-mono placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('phone') border-rose-500 @enderror"
                                >
                                @error('phone')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Row 3: Alamat Rumah -->
                        <div>
                            <label for="address" class="block text-xs font-bold text-slate-700 mb-1.5">
                                ALAMAT LENGKAP RUMAH / TITIK INSTALASI <span class="text-rose-500">*</span>
                            </label>
                            <textarea
                                id="address"
                                name="address"
                                rows="3"
                                required
                                placeholder="Tuliskan nama jalan, blok/nomor rumah, RT/RW, kelurahan, dan kota..."
                                class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('address') border-rose-500 @enderror"
                            >{{ old('address', $customer->address) }}</textarea>
                            @error('address')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Row 4: Catatan Layanan / Paket -->
                        <div>
                            <label for="note" class="block text-xs font-bold text-slate-700 mb-1.5">
                                CATATAN LAYANAN / PAKET INTERNET <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input
                                type="text"
                                id="note"
                                name="note"
                                value="{{ old('note', $customer->note) }}"
                                placeholder="Contoh: Paket Home Ultra Fiber 50 Mbps, ONT ZTE F670L"
                                class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-2xs @error('note') border-rose-500 @enderror"
                            >
                            <p class="text-[11px] text-slate-400 mt-1">
                                Informasi paket berlangganan atau instruksi penanganan teknisi saat instalasi.
                            </p>
                            @error('note')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <!-- FOOTER ACTIONS -->
                    <div class="px-6 lg:px-8 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-3">
                        <a
                            href="{{ route('customers.index', ['tab' => 'customer']) }}"
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
                            <span>Perbarui Customer Rumahan</span>
                        </button>
                    </div>

                </form>

            </main>

        </div>

    </div>

</body>
</html>
