<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Perangkat Monitoring — Portal Manajemen Jaringan NetFly</title>
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
                        <a href="{{ route('monitoring.index') }}" class="hover:text-slate-700 transition">Monitoring</a>
                        <span>›</span>
                        <span class="text-slate-800 font-semibold truncate">Tambah Perangkat</span>
                    </nav>
                </div>

                <!-- Right Header Tools -->
                <a
                    href="{{ route('monitoring.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 px-3 py-1.5 rounded-lg transition shrink-0"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Daftar perangkat</span>
                </a>
            </header>

            <!-- MAIN PAGE CONTENT -->
            <main class="flex-1 p-6 lg:p-8 space-y-6 max-w-4xl mx-auto w-full">

                <!-- PAGE HEADER -->
                <div>
                    <h1 class="text-xl lg:text-2xl font-bold text-slate-900 tracking-tight">Tambah Perangkat Monitoring</h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Daftarkan target perangkat jaringan baru untuk pemantauan telemetri CPU, RAM, bandwidth, dan latensi secara mandiri.
                    </p>
                </div>

                <!-- Alert Feedback Errors -->
                @if ($errors->any())
                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-xs">
                        <p class="font-bold mb-1">Gagal menyimpan data perangkat:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- FORM CARD -->
                <form action="{{ route('monitoring.devices.store') }}" method="POST" class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    @csrf

                    <div class="p-6 lg:p-8 space-y-5">
                        
                        <!-- Row 1: Hostname, Jenis Perangkat & Status Awal -->
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <div class="sm:col-span-6">
                                <label for="inputHostname" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Nama Hostname <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    id="inputHostname"
                                    type="text" 
                                    name="hostname" 
                                    value="{{ old('hostname') }}"
                                    required 
                                    placeholder="CONTOH: RTR-CORE-SBY-01" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 font-mono uppercase focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                            </div>

                            <div class="sm:col-span-3">
                                <label for="inputUnitType" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Jenis Perangkat <span class="text-rose-500">*</span>
                                </label>
                                <select id="inputUnitType" name="unit_type" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition cursor-pointer">
                                    <option value="Router" {{ old('unit_type') == 'Router' ? 'selected' : '' }}>Router</option>
                                    <option value="Switch" {{ old('unit_type') == 'Switch' ? 'selected' : '' }}>Switch</option>
                                    <option value="Access Point" {{ old('unit_type') == 'Access Point' ? 'selected' : '' }}>Access Point</option>
                                    <option value="Firewall" {{ old('unit_type') == 'Firewall' ? 'selected' : '' }}>Firewall</option>
                                    <option value="Server" {{ old('unit_type') == 'Server' ? 'selected' : '' }}>Server</option>
                                    <option value="OLT" {{ old('unit_type') == 'OLT' ? 'selected' : '' }}>OLT GPON</option>
                                </select>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="inputStatus" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Status Awal
                                </label>
                                <select id="inputStatus" name="status" class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition cursor-pointer">
                                    <option value="online" {{ old('status') == 'online' ? 'selected' : '' }}>Online (Siap Pantau)</option>
                                    <option value="warning" {{ old('status') == 'warning' ? 'selected' : '' }}>Warning (Peringatan)</option>
                                    <option value="offline" {{ old('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 2: IP Address, Port API & Tipe Jalur Monitoring -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <div class="grid grid-cols-12 gap-3">
                                    <div class="col-span-8">
                                        <label for="inputIpAddress" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                            IP Address / Host Target <span class="text-rose-500">*</span>
                                        </label>
                                        <input 
                                            id="inputIpAddress"
                                            type="text" 
                                            name="ip_address" 
                                            value="{{ old('ip_address') }}"
                                            required 
                                            placeholder="10.200.x.x atau host domain" 
                                            class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 font-mono focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                        >
                                    </div>
                                    <div class="col-span-4">
                                        <label for="inputApiPort" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                            Port API
                                        </label>
                                        <input 
                                            id="inputApiPort"
                                            type="number" 
                                            name="api_port" 
                                            value="{{ old('api_port', '8728') }}" 
                                            class="w-full px-3 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 font-mono text-center focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="inputConnType" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Tipe Jalur Monitoring
                                </label>
                                <select id="inputConnType" name="connection_type" class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition cursor-pointer">
                                    <option value="lan" {{ old('connection_type') == 'lan' ? 'selected' : '' }}>Direct IP / Local LAN</option>
                                    <option value="zerotier" {{ old('connection_type') == 'zerotier' ? 'selected' : '' }}>ZeroTier Virtual LAN</option>
                                    <option value="vpn" {{ old('connection_type') == 'vpn' ? 'selected' : '' }}>VPN Tunnel (WireGuard / SSTP)</option>
                                    <option value="ipcloud" {{ old('connection_type') == 'ipcloud' ? 'selected' : '' }}>MikroTik IP Cloud / DDNS</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 3: Mitra Pelanggan & Proyek -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="inputCustomer" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Mitra Pelanggan / Entitas <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    id="inputCustomer"
                                    type="text" 
                                    name="customer" 
                                    value="{{ old('customer') }}"
                                    required 
                                    placeholder="Nama klien / instansi" 
                                    list="monitoringCustomerSuggestions"
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                                <datalist id="monitoringCustomerSuggestions">
                                    <option value="PT. Bank Mandiri (Persero) Tbk">
                                    <option value="PT. Shopee International Indonesia">
                                    <option value="Dinas Pendidikan Provinsi DKI Jakarta">
                                    <option value="PT. XYZ Teknologi">
                                    <option value="RS Medika Center">
                                    <option value="Internal NOC NetFly Backbone">
                                </datalist>
                            </div>

                            <div>
                                <label for="inputProject" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Proyek / Lokasi Terhubung
                                </label>
                                <input 
                                    id="inputProject"
                                    type="text" 
                                    name="project" 
                                    value="{{ old('project') }}"
                                    placeholder="Contoh: Dedicated Internet SCBD" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                            </div>
                        </div>

                        <!-- Row 4: Model & Lokasi Rak -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="inputModel" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Model / Seri Perangkat
                                </label>
                                <input 
                                    id="inputModel"
                                    type="text" 
                                    name="model" 
                                    value="{{ old('model') }}"
                                    placeholder="Contoh: MikroTik CCR2004-16G-2S+" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                            </div>

                            <div>
                                <label for="inputRack" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Lokasi Rak / Titik POP
                                </label>
                                <input 
                                    id="inputRack"
                                    type="text" 
                                    name="rack_location" 
                                    value="{{ old('rack_location') }}"
                                    placeholder="Contoh: Rack Core-A, Slot U-42" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                            </div>
                        </div>

                        <!-- Row 5: Kredensial API -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                            <div>
                                <label for="inputApiUser" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Username API MikroTik (Opsional)
                                </label>
                                <input 
                                    id="inputApiUser"
                                    type="text" 
                                    name="api_username" 
                                    value="{{ old('api_username', 'webmonitor') }}"
                                    placeholder="webmonitor" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                            </div>
                            <div>
                                <label for="inputApiPass" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Password API MikroTik (Opsional)
                                </label>
                                <input 
                                    id="inputApiPass"
                                    type="password" 
                                    name="api_password" 
                                    placeholder="••••••••" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                            </div>
                        </div>

                    </div>

                    <!-- Card Action Footer -->
                    <div class="px-6 lg:px-8 py-4 bg-slate-50/70 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('monitoring.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition shadow-xs active:scale-95 cursor-pointer">
                            Simpan & Mulai Pantau
                        </button>
                    </div>
                </form>

            </main>
        </div>
    </div>

</body>
</html>
