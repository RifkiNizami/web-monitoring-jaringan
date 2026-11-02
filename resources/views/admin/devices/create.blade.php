<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang Inventaris — Portal Manajemen Jaringan NetFly</title>
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
                        <a href="{{ route('devices.index') }}" class="hover:text-slate-700 transition">Inventaris Perangkat</a>
                        <span>›</span>
                        <span class="text-slate-800 font-semibold truncate">Tambah Barang Baru</span>
                    </nav>
                </div>

                <!-- Right Header Tools -->
                <a
                    href="{{ route('devices.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 px-3 py-1.5 rounded-lg transition shrink-0"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Daftar Inventaris</span>
                </a>
            </header>

            <!-- MAIN PAGE CONTENT -->
            <main class="flex-1 p-6 lg:p-8 space-y-6 max-w-4xl mx-auto w-full">

                <!-- PAGE HEADER -->
                <div>
                    <h1 class="text-xl lg:text-2xl font-bold text-slate-900 tracking-tight">Tambah Barang Inventaris Baru</h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Catat perangkat fisik baru beserta foto, kategori, merk/tipe seri, nomor seri (SN), jumlah stok unit, dan status ketersediaannya.
                    </p>
                </div>

                <!-- Alert Feedback Errors -->
                @if ($errors->any())
                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-xs">
                        <p class="font-bold mb-1">Gagal menyimpan data barang:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- FORM CARD -->
                <form action="{{ route('devices.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    @csrf

                    <div class="p-6 lg:p-8 space-y-5">

                        <!-- Row 0: Upload Foto Barang -->
                        <div class="pb-5 border-b border-slate-100">
                            <label for="inputPhoto" class="block text-xs font-semibold text-slate-700 mb-2">
                                Foto Barang / Perangkat <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                <div class="w-20 h-20 rounded-xl border border-dashed border-slate-300 bg-slate-50 flex items-center justify-center overflow-hidden shrink-0">
                                    <img id="photoPreviewImg" src="" alt="Preview Foto Barang" class="w-full h-full object-cover hidden">
                                    <svg id="photoPlaceholderIcon" class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <input
                                        id="inputPhoto"
                                        type="file"
                                        name="photo"
                                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                        class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer"
                                    >
                                    <p id="photoStatusInfo" class="text-[11px] text-slate-400 mt-1.5">
                                        Format: JPG, PNG, atau WEBP (Maks. 10 MB — otomatis dikompres agar hemat penyimpanan).
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Row 1: Nama / Kode Barang & Kategori Perangkat -->
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <div class="sm:col-span-7">
                                <label for="inputHostname" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Nama / Kode Barang <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    id="inputHostname"
                                    type="text" 
                                    name="hostname" 
                                    value="{{ old('hostname') }}"
                                    required 
                                    placeholder="CONTOH: RTR-CORE-01" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 font-mono uppercase focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                            </div>

                            <div class="sm:col-span-5">
                                <label for="inputType" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Kategori Perangkat <span class="text-rose-500">*</span>
                                </label>
                                <select id="inputType" name="type" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition cursor-pointer">
                                    @foreach ($deviceTypes as $type)
                                        <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Row 2: Merk / Tipe Seri & Nomor Seri (SN) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="inputBrandModel" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Merk / Tipe Seri <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    id="inputBrandModel"
                                    type="text" 
                                    name="brand_model" 
                                    value="{{ old('brand_model') }}"
                                    required 
                                    placeholder="Contoh: MikroTik CCR2004-16G-2S+" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                            </div>

                            <div>
                                <label for="inputSerialNumber" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Nomor Seri (SN) <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    id="inputSerialNumber"
                                    type="text" 
                                    name="serial_number" 
                                    value="{{ old('serial_number') }}"
                                    required 
                                    placeholder="Contoh: SN-9A82B1002" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 font-mono uppercase focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                            </div>
                        </div>

                        <!-- Row 3: Jumlah Barang (Unit) & Status Ketersediaan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="inputQuantity" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Jumlah Barang (Unit) <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    id="inputQuantity"
                                    type="number" 
                                    name="quantity" 
                                    min="0"
                                    value="{{ old('quantity', 1) }}"
                                    required 
                                    placeholder="1" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 tabular-nums focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                            </div>

                            <div>
                                <label for="inputStatus" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Status Ketersediaan <span class="text-rose-500">*</span>
                                </label>
                                <select id="inputStatus" name="status" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition cursor-pointer">
                                    @foreach ($statusOptions as $st)
                                        <option value="{{ $st }}" {{ old('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <!-- Card Action Footer -->
                    <div class="px-6 lg:px-8 py-4 bg-slate-50/70 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('devices.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-lg transition shadow-xs active:scale-95 cursor-pointer">
                            Simpan Barang
                        </button>
                    </div>
                </form>

            </main>
        </div>
    </div>

</body>
</html>
