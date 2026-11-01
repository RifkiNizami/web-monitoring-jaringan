<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit {{ $device['hostname'] }} — Monitoring NetFly</title>
    <link rel="icon" href="{{ asset('assets/images/netfly-logo.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    @php
        $statusMap = [
            'online' => ['label' => 'Online', 'pill' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20', 'dot' => 'bg-emerald-500'],
            'warning' => ['label' => 'Warning', 'pill' => 'bg-amber-50 text-amber-700 ring-amber-600/20', 'dot' => 'bg-amber-500'],
            'offline' => ['label' => 'Offline', 'pill' => 'bg-rose-50 text-rose-700 ring-rose-600/20', 'dot' => 'bg-rose-500'],
        ];
        $st = $statusMap[$device['status'] ?? 'online'] ?? $statusMap['online'];
    @endphp

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
                        <a href="{{ route('monitoring.show', $device['id']) }}" class="hover:text-slate-700 transition truncate max-w-[140px]">{{ $device['hostname'] }}</a>
                        <span>›</span>
                        <span class="text-slate-800 font-semibold truncate">Edit</span>
                    </nav>
                </div>

                <!-- Right Header Tools -->
                <div class="flex items-center gap-2.5 shrink-0">
                    <a
                        href="{{ route('monitoring.show', $device['id']) }}"
                        class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 px-3 py-1.5 rounded-lg transition"
                    >
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span>Pantau Detail</span>
                    </a>
                    <a
                        href="{{ route('monitoring.index') }}"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-50 px-3 py-1.5 rounded-lg transition"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Daftar perangkat</span>
                    </a>
                </div>
            </header>

            <!-- MAIN PAGE CONTENT -->
            <main class="flex-1 p-6 lg:p-8 space-y-6 max-w-4xl mx-auto w-full">

                <!-- PAGE HEADER -->
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl lg:text-2xl font-bold text-slate-900 tracking-tight">Edit Perangkat Monitoring</h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium ring-1 ring-inset {{ $st['pill'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $st['dot'] }}"></span>
                            {{ $st['label'] }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Perbarui identitas hostname, alamat IP target, jalur komunikasi, serta kredensial API untuk <strong class="text-slate-700 font-mono">{{ $device['hostname'] }}</strong>.
                    </p>
                </div>

                <!-- Alert Feedback Errors -->
                @if ($errors->any())
                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-xs">
                        <p class="font-bold mb-1">Gagal memperbarui perangkat:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- FORM CARD -->
                <form action="{{ route('monitoring.devices.update', $device['id']) }}" method="POST" class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    @csrf
                    @method('PUT')

                    <div class="p-6 lg:p-8 space-y-5">

                        <!-- Row 1: Hostname, Jenis Perangkat & Status Operasional -->
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <div class="sm:col-span-6">
                                <label for="editHostname" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Nama Hostname <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    id="editHostname"
                                    type="text" 
                                    name="hostname" 
                                    value="{{ old('hostname', $device['hostname'] ?? '') }}"
                                    required 
                                    placeholder="CONTOH: RTR-CORE-SBY-01" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 font-mono uppercase focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                            </div>

                            <div class="sm:col-span-3">
                                <label for="editUnitType" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Jenis Perangkat <span class="text-rose-500">*</span>
                                </label>
                                @php $currentType = old('unit_type', $device['unit_type'] ?? 'Router'); @endphp
                                <select id="editUnitType" name="unit_type" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition cursor-pointer">
                                    <option value="Router" {{ $currentType == 'Router' ? 'selected' : '' }}>Router</option>
                                    <option value="Switch" {{ $currentType == 'Switch' ? 'selected' : '' }}>Switch</option>
                                    <option value="Access Point" {{ $currentType == 'Access Point' ? 'selected' : '' }}>Access Point</option>
                                    <option value="Firewall" {{ $currentType == 'Firewall' ? 'selected' : '' }}>Firewall</option>
                                    <option value="Server" {{ $currentType == 'Server' ? 'selected' : '' }}>Server</option>
                                    <option value="OLT" {{ $currentType == 'OLT' ? 'selected' : '' }}>OLT GPON</option>
                                </select>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="editStatus" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Status Operasional
                                </label>
                                @php $currentStatus = old('status', $device['status'] ?? 'online'); @endphp
                                <select id="editStatus" name="status" class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition cursor-pointer">
                                    <option value="online" {{ $currentStatus == 'online' ? 'selected' : '' }}>Online (Siap Pantau)</option>
                                    <option value="warning" {{ $currentStatus == 'warning' ? 'selected' : '' }}>Warning (Peringatan)</option>
                                    <option value="offline" {{ $currentStatus == 'offline' ? 'selected' : '' }}>Offline</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 2: IP Address, Port API & Tipe Jalur Monitoring -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <div class="grid grid-cols-12 gap-3">
                                    <div class="col-span-8">
                                        <label for="editIpAddress" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                            IP Address / Host Target <span class="text-rose-500">*</span>
                                        </label>
                                        <input 
                                            id="editIpAddress"
                                            type="text" 
                                            name="ip_address" 
                                            value="{{ old('ip_address', $device['ip_address'] ?? '') }}"
                                            required 
                                            placeholder="10.200.x.x atau host domain" 
                                            class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 font-mono focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                        >
                                    </div>
                                    <div class="col-span-4">
                                        <label for="editApiPort" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                            Port API
                                        </label>
                                        <input 
                                            id="editApiPort"
                                            type="number" 
                                            name="api_port" 
                                            value="{{ old('api_port', $device['api_port'] ?? '8728') }}" 
                                            class="w-full px-3 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 font-mono text-center focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="editConnType" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Tipe Jalur Monitoring
                                </label>
                                @php $currentConn = old('connection_type', $device['connection_type'] ?? 'lan'); @endphp
                                <select id="editConnType" name="connection_type" class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition cursor-pointer">
                                    <option value="lan" {{ $currentConn == 'lan' ? 'selected' : '' }}>Direct IP / Local LAN</option>
                                    <option value="zerotier" {{ $currentConn == 'zerotier' ? 'selected' : '' }}>ZeroTier Virtual LAN</option>
                                    <option value="vpn" {{ $currentConn == 'vpn' ? 'selected' : '' }}>VPN Tunnel (WireGuard / SSTP)</option>
                                    <option value="ipcloud" {{ $currentConn == 'ipcloud' ? 'selected' : '' }}>MikroTik IP Cloud / DDNS</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 3: Mitra Pelanggan & Proyek -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="editCustomer" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Mitra Pelanggan / Entitas <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    id="editCustomer"
                                    type="text" 
                                    name="customer" 
                                    value="{{ old('customer', $device['customer'] ?? '') }}"
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
                                <label for="editProject" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Proyek / Lokasi Terhubung
                                </label>
                                <input 
                                    id="editProject"
                                    type="text" 
                                    name="project" 
                                    value="{{ old('project', $device['project'] ?? '') }}"
                                    placeholder="Contoh: Dedicated Internet SCBD" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                            </div>
                        </div>

                        <!-- Row 4: Model & Lokasi Rak -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="editModel" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Model / Seri Perangkat
                                </label>
                                <input 
                                    id="editModel"
                                    type="text" 
                                    name="model" 
                                    value="{{ old('model', $device['model'] ?? '') }}"
                                    placeholder="Contoh: MikroTik CCR2004-16G-2S+" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                            </div>

                            <div>
                                <label for="editRack" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Lokasi Rak / Titik POP
                                </label>
                                <input 
                                    id="editRack"
                                    type="text" 
                                    name="rack_location" 
                                    value="{{ old('rack_location', $device['rack_location'] ?? '') }}"
                                    placeholder="Contoh: Rack Core-A, Slot U-42" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                            </div>
                        </div>

                        <!-- Row 5: Kredensial API -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                            <div>
                                <label for="editApiUser" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Username API MikroTik (Opsional)
                                </label>
                                <input 
                                    id="editApiUser"
                                    type="text" 
                                    name="api_username" 
                                    value="{{ old('api_username', $device['api_username'] ?? 'webmonitor') }}"
                                    placeholder="webmonitor" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                            </div>
                            <div>
                                <label for="editApiPass" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Password API MikroTik (Opsional)
                                </label>
                                <input 
                                    id="editApiPass"
                                    type="password" 
                                    name="api_password" 
                                    placeholder="Kosongkan jika tidak diubah" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50/60 focus:bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 transition"
                                >
                            </div>
                        </div>

                    </div>

                    <!-- Card Action Footer -->
                    <div class="px-6 lg:px-8 py-4 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between gap-3">
                        <button
                            type="button"
                            data-device-id="{{ $device['id'] }}"
                            data-device-hostname="{{ $device['hostname'] }}"
                            class="btn-delete-node inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Hapus Perangkat</span>
                        </button>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('monitoring.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                                Batal
                            </a>
                            <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition shadow-xs active:scale-95 cursor-pointer">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>

            </main>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus Target Monitoring --}}
    @include('admin.monitoring.delete_modal')

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

</body>
</html>
