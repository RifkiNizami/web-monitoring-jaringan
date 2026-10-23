<section id="alur-kerja" class="relative bg-slate-50/70 border-b border-slate-200/80">
    
    <!-- Outer Tall Container for Desktop Sticky Scrollytelling -->
    <div id="workflowScrollytelling" class="relative min-h-[300vh] lg:min-h-[320vh]">
        
        <!-- Sticky Viewport Wrapper -->
        <div class="sticky top-20 sm:top-24 min-h-[calc(100vh-5rem)] sm:min-h-[calc(100vh-6rem)] flex flex-col justify-center py-8 lg:py-12 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-6 sm:space-y-8">
                
                <!-- Section Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200/80 pb-5">
                    <div class="space-y-2 max-w-2xl">
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">
                            Proses Berlangganan & Aktivasi Layanan NetFly
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Mulai dari diskusi kebutuhan hingga jaringan siap digunakan—seluruh proses pemasangan dan konfigurasi ditangani langsung oleh tim teknis kami.
                        </p>
                    </div>

                    <!-- Step Navigator & Progress Bar -->
                    <div class="flex flex-col items-start md:items-end gap-2 shrink-0">
                        <span id="workflowStepCounterText" class="text-xs font-mono font-bold text-netfly-blue bg-blue-50 border border-blue-200/80 px-3 py-1 rounded-full shadow-2xs">
                            Tahap 1 dari 5
                        </span>
                        <div class="w-48 sm:w-56 bg-slate-200/80 h-1.5 rounded-full overflow-hidden">
                            <div id="workflowProgressFill" class="bg-blue-600 h-full w-[20%] transition-all duration-300 ease-out rounded-full"></div>
                        </div>
                    </div>
                </div>

                <!-- 5 Step Tabs (Interactive Quick Jump) -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                    <button type="button" class="workflow-step-tab shrink-0 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer bg-blue-600 text-white shadow-md shadow-blue-500/20" data-step-index="0">
                        01. Konsultasi Kebutuhan
                    </button>
                    <button type="button" class="workflow-step-tab shrink-0 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-slate-600 border border-slate-200 hover:bg-slate-100" data-step-index="1">
                        02. Survei & Penawaran
                    </button>
                    <button type="button" class="workflow-step-tab shrink-0 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-slate-600 border border-slate-200 hover:bg-slate-100" data-step-index="2">
                        03. Instalasi Perangkat
                    </button>
                    <button type="button" class="workflow-step-tab shrink-0 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-slate-600 border border-slate-200 hover:bg-slate-100" data-step-index="3">
                        04. Uji Coba (Trial)
                    </button>
                    <button type="button" class="workflow-step-tab shrink-0 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-slate-600 border border-slate-200 hover:bg-slate-100" data-step-index="4">
                        05. Layanan Aktif 24/7
                    </button>
                </div>

                <!-- Main Content Grid (Left Narrative + Right Interactive Visualizer) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center pt-2">
                    
                    <!-- LEFT COLUMN: Step Narrative Content (CSS Grid Stacked Layering) -->
                    <div class="lg:col-span-5 grid grid-cols-1 grid-rows-1 items-center min-h-72.5 sm:min-h-77.5">
                        
                        <!-- STAGE 01 -->
                        <div class="workflow-stage-data col-start-1 row-start-1 transition-all duration-700 ease-out opacity-100 translate-y-0 scale-100 pointer-events-auto z-10" data-index="0">
                            <div class="space-y-4">
                                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-200 text-[11px] font-extrabold uppercase font-mono">
                                    Tahap 01 • Diskusi Awal
                                </div>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-snug">
                                    Konsultasi Kapasitas & Kebutuhan Jaringan
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                    Tim kami berdiskusi dengan tim IT atau manajemen Anda untuk memetakan jumlah pengguna aktif, beban aplikasi harian, serta titik lokasi kantor atau cabang yang ingin dihubungkan.
                                </p>
                                <div class="p-3.5 bg-white rounded-xl border border-slate-200 shadow-2xs space-y-1">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Output Tahap Ini</span>
                                    <p class="text-xs font-semibold text-slate-800">
                                        Rekomendasi kapasitas bandwidth yang pas dan rancangan topologi sesuai anggaran perusahaan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- STAGE 02 -->
                        <div class="workflow-stage-data col-start-1 row-start-1 transition-all duration-700 ease-out opacity-0 translate-y-8 scale-95 pointer-events-none z-0" data-index="1">
                            <div class="space-y-4">
                                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-200 text-[11px] font-extrabold uppercase font-mono">
                                    Tahap 02 • Pengecekan Lapangan
                                </div>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-snug">
                                    Survei Lokasi Gedung & Proposal Transparan
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                    Teknisi kami mengunjungi lokasi Anda untuk mengecek jarak titik distribusi terdekat, jalur masuk kabel ke ruang server, serta menyusun rincian biaya tanpa ada biaya tersembunyi.
                                </p>
                                <div class="p-3.5 bg-white rounded-xl border border-slate-200 shadow-2xs space-y-1">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Output Tahap Ini</span>
                                    <p class="text-xs font-semibold text-slate-800">
                                        Dokumen kelayakan lokasi, estimasi waktu pengerjaan, dan penawaran harga resmi.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- STAGE 03 -->
                        <div class="workflow-stage-data col-start-1 row-start-1 transition-all duration-700 ease-out opacity-0 translate-y-8 scale-95 pointer-events-none z-0" data-index="2">
                            <div class="space-y-4">
                                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-200 text-[11px] font-extrabold uppercase font-mono">
                                    Tahap 03 • Pengerjaan Teknis
                                </div>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-snug">
                                    Penarikan Jalur & Konfigurasi Perangkat
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                    Tim lapangan menarik jalur fiber optik atau memasang perangkat wireless di gedung Anda, dilanjutkan dengan instalasi router, pembagian IP publik, dan pengamanan jaringan di ruang server.
                                </p>
                                <div class="p-3.5 bg-white rounded-xl border border-slate-200 shadow-2xs space-y-1">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Output Tahap Ini</span>
                                    <p class="text-xs font-semibold text-slate-800">
                                        Perangkat terpasang rapi di rack server Anda disertai jalur cadangan (backup link) yang siap aktif.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- STAGE 04 -->
                        <div class="workflow-stage-data col-start-1 row-start-1 transition-all duration-700 ease-out opacity-0 translate-y-8 scale-95 pointer-events-none z-0" data-index="3">
                            <div class="space-y-4">
                                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-extrabold uppercase font-mono">
                                    Tahap 04 • Pengujian Kualitas
                                </div>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-snug">
                                    Uji Coba Koneksi & Beban Trafik (Trial)
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                    Sebelum layanan resmi ditagihkan, kami menguji kestabilan kecepatan upload/download simetris 1:1 dan ketahanan koneksi pada jam operasional sibuk bersama tim IT Anda.
                                </p>
                                <div class="p-3.5 bg-white rounded-xl border border-slate-200 shadow-2xs space-y-1">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Output Tahap Ini</span>
                                    <p class="text-xs font-semibold text-slate-800">
                                        Laporan hasil uji kecepatan penuh dan kepastian jaringan stabil tanpa putus-putus.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- STAGE 05 -->
                        <div class="workflow-stage-data col-start-1 row-start-1 transition-all duration-700 ease-out opacity-0 translate-y-8 scale-95 pointer-events-none z-0" data-index="4">
                            <div class="space-y-4">
                                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-rose-50 text-rose-700 border border-rose-200 text-[11px] font-extrabold uppercase font-mono">
                                    Tahap 05 • Layanan Berjalan
                                </div>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-snug">
                                    Serah Terima Resmi & Pantauan NOC 24/7
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                    Penandatanganan Berita Acara Aktivasi (BAA) dan penyerahan akun Portal Pelanggan NetFly. Sejak hari pertama aktif, jaringan Anda dipantau penuh 24 jam oleh tim engineer NOC kami.
                                </p>
                                <div class="p-3.5 bg-white rounded-xl border border-slate-200 shadow-2xs space-y-1">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Output Tahap Ini</span>
                                    <p class="text-xs font-semibold text-slate-800">
                                        Akses Portal Monitoring Pelanggan, garansi SLA 99,5%, dan dukungan teknis prioritas 24/7.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN: Dynamic Interactive Stage Visualizer Cards (CSS Grid Stacked) -->
                    <div class="lg:col-span-7">
                        <div class="p-2 sm:p-2.5 rounded-3xl bg-slate-100/90 border border-slate-200/90 shadow-sm relative overflow-hidden">
                            
                            <div class="grid grid-cols-1 grid-rows-1 w-full min-h-75 sm:min-h-85">
                                
                                <!-- VISUAL CARD 1: PEMETAAN KEBUTUHAN PELANGGAN -->
                                <div class="workflow-visual-card col-start-1 row-start-1 transition-all duration-700 ease-out opacity-100 translate-y-0 scale-100 pointer-events-auto z-10 blur-0" data-visual-index="0">
                                    <div class="rounded-2xl bg-slate-900 text-white p-5 sm:p-6 space-y-4 shadow-xl border border-slate-800 font-sans">
                                        
                                        <div class="flex items-center justify-between border-b border-slate-800 pb-3 text-xs">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                                                <span class="font-bold text-slate-200">RINGKASAN PEMETAAN KEBUTUHAN</span>
                                            </div>
                                            <span class="text-[10px] text-blue-400 font-semibold bg-blue-500/10 px-2.5 py-0.5 rounded-full border border-blue-500/30">TAHAP KONSULTASI</span>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                            <div class="p-3.5 bg-slate-800/70 rounded-xl border border-slate-700/70 space-y-1">
                                                <span class="text-[10px] text-slate-400 uppercase font-semibold">Skala Operasional</span>
                                                <p class="text-sm font-bold text-white">Kantor Pusat & 3 Cabang</p>
                                                <p class="text-[11px] text-slate-400">250+ perangkat karyawan & server aktif</p>
                                            </div>
                                            <div class="p-3.5 bg-slate-800/70 rounded-xl border border-slate-700/70 space-y-1">
                                                <span class="text-[10px] text-slate-400 uppercase font-semibold">Prioritas Penggunaan</span>
                                                <p class="text-sm font-bold text-sky-400">ERP, Cloud & Video Call</p>
                                                <p class="text-[11px] text-slate-400">Butuh trafik upload & download seimbang</p>
                                            </div>
                                        </div>

                                        <div class="p-3.5 bg-blue-950/50 rounded-xl border border-blue-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                            <div>
                                                <span class="text-[10px] text-blue-300 uppercase font-bold block">Rekomendasi Solusi NetFly</span>
                                                <span class="text-xs sm:text-sm font-extrabold text-white">Dedicated Fiber Optic 1:1 + Backup Wireless</span>
                                            </div>
                                            <span class="text-xs font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-3 py-1 rounded-lg self-start sm:self-center shrink-0">
                                                Bebas FUP / Kuota
                                            </span>
                                        </div>

                                    </div>
                                </div>

                                <!-- VISUAL CARD 2: SURVEI LOKASI & PROPOSAL -->
                                <div class="workflow-visual-card col-start-1 row-start-1 transition-all duration-700 ease-out opacity-0 translate-y-6 scale-95 pointer-events-none z-0 blur-[2px]" data-visual-index="1">
                                    <div class="rounded-2xl bg-white p-5 sm:p-6 space-y-4 shadow-xl border border-slate-200 font-sans">
                                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 text-xs font-semibold">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span class="text-slate-900 font-bold">Laporan Hasil Survei Lokasi (Site Survey)</span>
                                            </div>
                                            <span class="text-[11px] text-emerald-700 font-bold bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                                Lokasi Terjangkau
                                            </span>
                                        </div>

                                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/80 space-y-2.5 text-xs">
                                            <div class="flex items-center justify-between p-2.5 bg-white rounded-lg border border-slate-200">
                                                <span class="font-semibold text-slate-600">Jarak ke Titik Distribusi (POP):</span>
                                                <span class="text-slate-900 font-bold">1,2 KM (Jalur Fiber Tersedia)</span>
                                            </div>
                                            <div class="flex items-center justify-between p-2.5 bg-white rounded-lg border border-slate-200">
                                                <span class="font-semibold text-slate-600">Kesiapan Ruang Server:</span>
                                                <span class="text-emerald-600 font-bold">Jalur Tray Kabel & Rack Siap</span>
                                            </div>
                                            <div class="flex items-center justify-between p-2.5 bg-white rounded-lg border border-slate-200">
                                                <span class="font-semibold text-slate-600">Estimasi Pengerjaan:</span>
                                                <span class="text-blue-600 font-bold">3 – 5 Hari Kerja</span>
                                            </div>
                                        </div>

                                        <div class="text-[11px] text-slate-500 flex items-center justify-between pt-1">
                                            <span>Biaya Survei Area Tercover: Gratis</span>
                                            <span class="text-slate-800 font-bold">Rincian RAB Jelas & Transparan</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- VISUAL CARD 3: INSTALASI & KONFIGURASI PERANGKAT -->
                                <div class="workflow-visual-card col-start-1 row-start-1 transition-all duration-700 ease-out opacity-0 translate-y-6 scale-95 pointer-events-none z-0 blur-[2px]" data-visual-index="2">
                                    <div class="rounded-2xl bg-slate-900 text-white p-5 sm:p-6 space-y-4 shadow-xl border border-slate-800 font-sans">
                                        <div class="flex items-center justify-between border-b border-slate-800 pb-3 text-xs">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                <span class="font-bold text-slate-200">PROGRES INSTALASI & INTEGRASI</span>
                                            </div>
                                            <span class="text-[10px] text-emerald-400 font-semibold bg-emerald-500/10 px-2.5 py-0.5 rounded-full border border-emerald-500/30">ON-SITE SETUP</span>
                                        </div>

                                        <div class="space-y-2.5 text-xs">
                                            <div class="flex items-center justify-between p-3 bg-slate-800/70 rounded-xl border border-slate-700/70">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">✓</span>
                                                    <span class="text-slate-200 font-medium">Penarikan Kabel Fiber Optik ke Gedung</span>
                                                </div>
                                                <span class="text-[11px] text-emerald-400 font-bold">Selesai</span>
                                            </div>
                                            <div class="flex items-center justify-between p-3 bg-slate-800/70 rounded-xl border border-slate-700/70">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">✓</span>
                                                    <span class="text-slate-200 font-medium">Pemasangan Router & Alokasi IP Public</span>
                                                </div>
                                                <span class="text-[11px] text-emerald-400 font-bold">Selesai</span>
                                            </div>
                                            <div class="flex items-center justify-between p-3 bg-slate-800/70 rounded-xl border border-slate-700/70">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">✓</span>
                                                    <span class="text-slate-200 font-medium">Integrasi Jalur Cadangan (Auto-Failover)</span>
                                                </div>
                                                <span class="text-[11px] text-emerald-400 font-bold">Aktif</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- VISUAL CARD 4: UJI COBA / TRIAL KONEKSI -->
                                <div class="workflow-visual-card col-start-1 row-start-1 transition-all duration-700 ease-out opacity-0 translate-y-6 scale-95 pointer-events-none z-0 blur-[2px]" data-visual-index="3">
                                    <div class="rounded-2xl bg-white p-5 sm:p-6 space-y-4 shadow-xl border border-slate-200 font-sans">
                                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 text-xs font-semibold">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span class="text-slate-900 font-bold">Hasil Uji Kecepatan & Kestabilan (Trial)</span>
                                            </div>
                                            <span class="text-[11px] text-blue-700 font-bold bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-200">
                                                Rasio 1:1 Terbukti
                                            </span>
                                        </div>

                                        <div class="space-y-3 text-xs">
                                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-1.5 overflow-hidden relative">
                                                <div class="flex justify-between">
                                                    <span class="text-slate-600 font-medium">Kecepatan Download & Upload:</span>
                                                    <span class="font-bold text-slate-900">Simetris 1:1 Penuh</span>
                                                </div>
                                                <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden relative">
                                                    <div class="bg-blue-600 h-full rounded-full w-full relative overflow-hidden">
                                                        <div class="absolute inset-0 bg-white/30 animate-shimmer"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-3 gap-2">
                                                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200 text-center">
                                                    <span class="text-[10px] text-slate-500 uppercase font-semibold">Respon Ping</span>
                                                    <p class="font-bold text-emerald-600 mt-0.5 text-xs">&lt; 5 ms Stabil</p>
                                                </div>
                                                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200 text-center">
                                                    <span class="text-[10px] text-slate-500 uppercase font-semibold">Packet Loss</span>
                                                    <p class="font-bold text-emerald-600 mt-0.5 text-xs">0.00%</p>
                                                </div>
                                                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200 text-center">
                                                    <span class="text-[10px] text-slate-500 uppercase font-semibold">Jam Sibuk</span>
                                                    <p class="font-bold text-emerald-600 mt-0.5 text-xs">Tanpa Drop</p>
                                                </div>
                                            </div>
                                        </div>

                                        <p class="text-[11px] text-slate-500 text-center">
                                            Diuji langsung bersama tim IT pelanggan sebelum penandatanganan serah terima.
                                        </p>
                                    </div>
                                </div>

                                <!-- VISUAL CARD 5: LAYANAN AKTIF & DUKUNGAN NOC 24/7 -->
                                <div class="workflow-visual-card col-start-1 row-start-1 transition-all duration-700 ease-out opacity-0 translate-y-6 scale-95 pointer-events-none z-0 blur-[2px]" data-visual-index="4">
                                    <div class="rounded-2xl bg-linear-to-br from-slate-900 to-blue-950 text-white p-5 sm:p-6 space-y-4 shadow-xl border border-blue-900/40 font-sans">
                                        <div class="flex items-center justify-between border-b border-slate-800 pb-3 text-xs">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                                <span class="font-bold text-white">LAYANAN AKTIF & JAMINAN SLA RESMI</span>
                                            </div>
                                            <span class="text-[10px] text-emerald-400 font-bold bg-emerald-500/20 px-2.5 py-0.5 rounded-full border border-emerald-500/40">
                                                TERPANTAU 24/7
                                            </span>
                                        </div>

                                        <div class="p-4 bg-white/5 rounded-xl border border-white/10 space-y-2.5 text-xs">
                                            <div class="flex justify-between items-center">
                                                <span class="text-slate-300">Garansi Ketersediaan Jaringan:</span>
                                                <span class="font-bold text-emerald-400 text-sm">SLA 99,5% / Bulan</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-slate-300">Pemantauan Status Koneksi:</span>
                                                <span class="font-bold text-blue-400">Otomatis via Dashboard NMS</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-slate-300">Layanan Bantuan Teknis:</span>
                                                <span class="font-bold text-amber-300">Tim NOC & Teknisi Lapangan Siaga</span>
                                            </div>
                                        </div>

                                        <div class="pt-2 flex items-center justify-between text-xs text-slate-300">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                                <span>Dukungan Prioritas Pelanggan Korporat</span>
                                            </div>
                                            <span class="font-bold text-blue-300 font-mono">SLA Terjamin</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

</section>
