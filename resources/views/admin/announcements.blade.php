<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pemberitahuan — Portal NetFly</title>
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
                    <!-- Mobile Hamburger -->
                    <button id="openSidebar" class="lg:hidden p-1.5 -ml-1 text-slate-600 hover:text-slate-900 rounded-md hover:bg-slate-100 cursor-pointer" aria-label="Buka Menu">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Breadcrumb -->
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                        <span class="tracking-wide uppercase">BROADCAST & PENGUMUMAN</span>
                        <span>›</span>
                        <span class="text-slate-800 font-bold">Kelola Pemberitahuan</span>
                    </div>
                </div>

                <!-- Right Header Tools -->
                <div class="flex items-center gap-3">
                    <!-- Realtime Digital Clock -->
                    <div class="hidden sm:flex items-center gap-1.5 text-xs font-mono text-slate-600 bg-slate-100/80 px-2.5 py-1 rounded-md border border-slate-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="liveClock">--:--:-- WIB</span>
                    </div>
                </div>
            </header>

            <!-- FLASH ALERTS -->
            @if(session('success'))
                <div class="mx-6 lg:mx-8 mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-emerald-800 text-sm">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="mx-6 lg:mx-8 mt-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Periksa kembali formulir:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 ml-7">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- MAIN BODY PADDING -->
            <main class="p-6 lg:p-8 space-y-6">
                
                <!-- HEADER & ACTION -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="text-[11px] font-semibold text-slate-400 mb-1 flex items-center gap-1.5">
                            <span class="text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded border border-blue-200">ADMIN KONTEN</span>
                            <span>›</span>
                            <span>Sistem Pengumuman Terpusat</span>
                        </div>
                        <h1 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight">
                            Manajemen Pemberitahuan & Broadcast
                        </h1>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Buat dan terbitkan informasi jadwal maintenance rutin, update firmware, insiden jaringan, dan pemberitahuan internal untuk admin operasional.
                        </p>
                    </div>

                    <!-- Tombol Tambah Pemberitahuan -->
                    <button onclick="openModal('addAnnouncementModal')" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-netfly-blue hover:bg-blue-700 rounded-xl shadow-sm transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Buat Pemberitahuan Baru</span>
                    </button>
                </div>

                <!-- 4 STATS PILLS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Pengumuman</span>
                            <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalCount }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-netfly-blue flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Sedang Siar (Aktif)</span>
                            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $activeCount }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Jadwal Maintenance</span>
                            <p class="text-2xl font-black text-amber-600 mt-1">{{ $maintenanceCount }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pembaruan Sistem</span>
                            <p class="text-2xl font-black text-blue-600 mt-1">{{ $systemUpdateCount }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- FILTER & SEARCH BAR -->
                <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs">
                    <form method="GET" action="{{ route('announcements.index') }}" class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Cari judul atau isi pemberitahuan..." 
                                class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 transition-all"
                            >
                        </div>

                        <div class="flex gap-2">
                            <select name="category" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:bg-white focus:border-blue-500">
                                <option value="all">Semua Kategori</option>
                                <option value="maintenance" {{ request('category') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="pembaruan" {{ request('category') === 'pembaruan' ? 'selected' : '' }}>Pembaruan Sistem</option>
                                <option value="gangguan" {{ request('category') === 'gangguan' ? 'selected' : '' }}>Gangguan Jaringan</option>
                                <option value="info" {{ request('category') === 'info' ? 'selected' : '' }}>Pengumuman Info</option>
                            </select>

                            <select name="status" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:bg-white focus:border-blue-500">
                                <option value="all">Semua Status</option>
                                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif (Siaran)</option>
                                <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="diarsipkan" {{ request('status') === 'diarsipkan' ? 'selected' : '' }}>Diarsipkan</option>
                            </select>

                            @if(request('search') || request('category') || request('status'))
                                <a href="{{ route('announcements.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span>Reset</span>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- LIST PEMBERITAHUAN -->
                <div class="space-y-4">
                    @forelse($announcements as $item)
                        @php
                            $badge = $item->category_badge;
                            $prioBadge = $item->priority_badge;
                        @endphp
                        <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs p-5 hover:border-blue-300 transition-all flex flex-col md:flex-row md:items-start justify-between gap-4">
                            <div class="space-y-2 flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <!-- Badge Kategori -->
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $badge['bg'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                        {{ $badge['label'] }}
                                    </span>

                                    <!-- Priority Badge -->
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border {{ $prioBadge['class'] }}">
                                        {{ $prioBadge['label'] }}
                                    </span>

                                    <!-- Status Badge -->
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $item->status === 'aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                                        {{ ucfirst($item->status) }}
                                    </span>

                                    <!-- Tanggal Publikasi -->
                                    <span class="text-[11px] text-slate-400 font-mono ml-auto">
                                        {{ $item->created_at->format('d M Y, H:i') }} WIB
                                    </span>
                                </div>

                                <h2 class="text-base font-bold text-slate-900">
                                    {{ $item->title }}
                                </h2>

                                <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line">
                                    {{ $item->message }}
                                </p>

                                @if($item->scheduled_at || $item->expires_at)
                                    <div class="pt-2 flex flex-wrap items-center gap-4 text-[11px] text-slate-500">
                                        @if($item->scheduled_at)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Jadwal Mulai: <strong class="text-slate-700">{{ $item->scheduled_at->format('d M Y, H:i') }} WIB</strong>
                                            </span>
                                        @endif
                                        @if($item->expires_at)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Estimasi Selesai: <strong class="text-slate-700">{{ $item->expires_at->format('d M Y, H:i') }} WIB</strong>
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center md:flex-col justify-end gap-2 shrink-0 border-t md:border-t-0 md:border-l border-slate-100 pt-3 md:pt-0 md:pl-4">
                                <form action="{{ route('announcements.toggle-status', $item->id) }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full px-3 py-1.5 text-xs font-semibold rounded-lg border transition-colors cursor-pointer {{ $item->status === 'aktif' ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }}">
                                        {{ $item->status === 'aktif' ? 'Tandai Selesai' : 'Aktifkan Kembali' }}
                                    </button>
                                </form>

                                <button onclick="openEditModal({{ json_encode($item) }})" class="w-full px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors cursor-pointer flex items-center justify-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span>Edit</span>
                                </button>

                                <button onclick="openDeleteModal({{ $item->id }}, '{{ addslashes($item->title) }}')" class="w-full px-3 py-1.5 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors cursor-pointer flex items-center justify-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span>Hapus</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-xl border border-slate-200/80 p-12 text-center text-slate-400">
                            <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                            <p class="font-bold text-slate-700">Belum ada pemberitahuan</p>
                            <p class="text-xs text-slate-400 mt-0.5">Klik tombol "Buat Pemberitahuan Baru" untuk menyiarkan informasi maintenance atau pengumuman.</p>
                        </div>
                    @endforelse

                    <div class="mt-4">
                        {{ $announcements->links() }}
                    </div>
                </div>

            </main>

        </div>

    </div>

    <!-- ==========================================
         MODAL 1: TAMBAH PEMBERITAHUAN
         ========================================== -->
    <div id="addAnnouncementModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl w-full max-w-lg overflow-hidden animate-scale">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Buat Pemberitahuan Baru</h3>
                    <p class="text-xs text-slate-400">Siarkan informasi ke dashboard administrator</p>
                </div>
                <button onclick="closeModal('addAnnouncementModal')" class="text-slate-400 hover:text-slate-600 p-1 rounded-md">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('announcements.store') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <!-- Judul -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Judul Pemberitahuan <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="title" 
                        required 
                        placeholder="Contoh: Jadwal Pemeliharaan (Maintenance) Router Surabaya"
                        class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500"
                    >
                </div>

                <!-- Kategori & Prioritas -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Kategori <span class="text-rose-500">*</span>
                        </label>
                        <select name="category" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                            <option value="maintenance">Maintenance (Pemeliharaan)</option>
                            <option value="pembaruan">Pembaruan Sistem</option>
                            <option value="gangguan">Gangguan Jaringan</option>
                            <option value="info">Pengumuman Info</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Tingkat Urgensi / Prioritas <span class="text-rose-500">*</span>
                        </label>
                        <select name="priority" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                            <option value="normal">Normal</option>
                            <option value="tinggi">Tinggi</option>
                            <option value="urgent">Mendesak (Urgent)</option>
                            <option value="rendah">Rendah</option>
                        </select>
                    </div>
                </div>

                <!-- Jadwal Pelaksanaan (Opsional) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Jadwal Mulai (Opsional)
                        </label>
                        <input 
                            type="datetime-local" 
                            name="scheduled_at" 
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 font-mono"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Estimasi Selesai (Opsional)
                        </label>
                        <input 
                            type="datetime-local" 
                            name="expires_at" 
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 font-mono"
                        >
                    </div>
                </div>

                <!-- Pesan / Detail Pengumuman -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Detail Pesan & Instruksi <span class="text-rose-500">*</span>
                    </label>
                    <textarea 
                        name="message" 
                        rows="4" 
                        required 
                        placeholder="Tuliskan deskripsi lengkap, rute perangkat yang terimbas, atau penanganan redundansi..."
                        class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500"
                    ></textarea>
                </div>

                <!-- Status Langsung Aktif -->
                <input type="hidden" name="status" value="aktif">

                <!-- Modal Actions -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeModal('addAnnouncementModal')" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-netfly-blue hover:bg-blue-700 rounded-lg shadow-sm transition-all cursor-pointer">
                        Publikasikan Siaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==========================================
         MODAL 2: EDIT PEMBERITAHUAN
         ========================================== -->
    <div id="editAnnouncementModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl w-full max-w-lg overflow-hidden animate-scale">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Perbarui Pemberitahuan</h3>
                    <p class="text-xs text-slate-400">Edit isi pesan atau status jadwal pemeliharaan</p>
                </div>
                <button onclick="closeModal('editAnnouncementModal')" class="text-slate-400 hover:text-slate-600 p-1 rounded-md">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="editAnnouncementForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Judul Pemberitahuan <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="edit_title"
                        name="title" 
                        required 
                        class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500"
                    >
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Kategori <span class="text-rose-500">*</span>
                        </label>
                        <select id="edit_category" name="category" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                            <option value="maintenance">Maintenance</option>
                            <option value="pembaruan">Pembaruan Sistem</option>
                            <option value="gangguan">Gangguan</option>
                            <option value="info">Info</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Prioritas <span class="text-rose-500">*</span>
                        </label>
                        <select id="edit_priority" name="priority" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                            <option value="normal">Normal</option>
                            <option value="tinggi">Tinggi</option>
                            <option value="urgent">Mendesak</option>
                            <option value="rendah">Rendah</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Status <span class="text-rose-500">*</span>
                        </label>
                        <select id="edit_status" name="status" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                            <option value="aktif">Aktif</option>
                            <option value="selesai">Selesai</option>
                            <option value="diarsipkan">Diarsipkan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Jadwal Mulai
                        </label>
                        <input 
                            type="datetime-local" 
                            id="edit_scheduled_at"
                            name="scheduled_at" 
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 font-mono"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Estimasi Selesai
                        </label>
                        <input 
                            type="datetime-local" 
                            id="edit_expires_at"
                            name="expires_at" 
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 font-mono"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Detail Pesan & Instruksi <span class="text-rose-500">*</span>
                    </label>
                    <textarea 
                        id="edit_message"
                        name="message" 
                        rows="4" 
                        required 
                        class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500"
                    ></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeModal('editAnnouncementModal')" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-netfly-blue hover:bg-blue-700 rounded-lg shadow-sm transition-all cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==========================================
         MODAL 3: KONFIRMASI HAPUS
         ========================================== -->
    <div id="deleteAnnouncementModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl w-full max-w-sm overflow-hidden animate-scale">
            <div class="p-6 text-center space-y-3">
                <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">Hapus Pemberitahuan Ini?</h3>
                <p class="text-xs text-slate-500">
                    Apakah Anda yakin ingin menghapus <span id="deleteAnnouncementTitle" class="font-bold text-slate-800"></span>?
                </p>

                <form id="deleteAnnouncementForm" method="POST" class="pt-4 flex items-center justify-center gap-2.5">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeModal('deleteAnnouncementModal')" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-sm transition-all cursor-pointer">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.remove('hidden');
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.add('hidden');
        }

        function openEditModal(item) {
            const form = document.getElementById('editAnnouncementForm');
            form.action = `/announcements/${item.id}`;
            document.getElementById('edit_title').value = item.title || '';
            document.getElementById('edit_category').value = item.category || 'maintenance';
            document.getElementById('edit_priority').value = item.priority || 'normal';
            document.getElementById('edit_status').value = item.status || 'aktif';
            document.getElementById('edit_message').value = item.message || '';

            // Format datetime for datetime-local input (YYYY-MM-DDTHH:mm)
            if (item.scheduled_at) {
                const date = new Date(item.scheduled_at);
                document.getElementById('edit_scheduled_at').value = date.toISOString().slice(0, 16);
            } else {
                document.getElementById('edit_scheduled_at').value = '';
            }

            if (item.expires_at) {
                const date = new Date(item.expires_at);
                document.getElementById('edit_expires_at').value = date.toISOString().slice(0, 16);
            } else {
                document.getElementById('edit_expires_at').value = '';
            }

            openModal('editAnnouncementModal');
        }

        function openDeleteModal(id, title) {
            const form = document.getElementById('deleteAnnouncementForm');
            form.action = `/announcements/${id}`;
            document.getElementById('deleteAnnouncementTitle').textContent = title;
            openModal('deleteAnnouncementModal');
        }

        // Live Clock
        function updateLiveClock() {
            const clockEl = document.getElementById('liveClock');
            if (!clockEl) return;
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            clockEl.textContent = `${hours}:${minutes}:${seconds} WIB`;
        }
        setInterval(updateLiveClock, 1000);
        updateLiveClock();

        // Mobile Sidebar Toggle
        const openSidebarBtn = document.getElementById('openSidebar');
        const closeSidebarBtn = document.getElementById('closeSidebar');
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        if (openSidebarBtn && sidebar && sidebarBackdrop) {
            openSidebarBtn.addEventListener('click', () => {
                sidebar.classList.remove('-translate-x-full');
                sidebarBackdrop.classList.remove('hidden');
            });
            if (closeSidebarBtn) {
                closeSidebarBtn.addEventListener('click', () => {
                    sidebar.classList.add('-translate-x-full');
                    sidebarBackdrop.classList.add('hidden');
                });
            }
            sidebarBackdrop.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                sidebarBackdrop.classList.add('hidden');
            });
        }
    </script>
</body>
</html>
