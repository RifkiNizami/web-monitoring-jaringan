<section id="solusi" class="py-24 sm:py-32 border-b border-slate-200/80 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-14">
        
        <div class="max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-blue-50/90 border border-blue-200/80 text-[10px] font-bold text-netfly-blue uppercase tracking-[0.2em] shadow-2xs">
                <span class="w-1.5 h-1.5 rounded-full bg-netfly-blue"></span>
                <span>Observabilitas & Telemetri</span>
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">
                Mesin Telemetri NMS NetFly & Verifikasi Kepatuhan SLA
            </h2>
            <p class="text-base text-slate-600 leading-relaxed">
                NetFly tidak menunggu laporan komplain dari pelanggan. Daemon monitoring otomatis memantau setiap perangkat router CPE setiap 30 detik melalui protokol SNMP dan ICMP, mendeteksi anomali sebelum konektivitas terganggu.
            </p>
        </div>

        <div class="p-2.5 sm:p-3 rounded-3xl bg-slate-100/90 border border-slate-200/90 shadow-sm space-y-6">
            <div class="rounded-[calc(1.5rem-0.375rem)] bg-white border border-slate-200/70 p-6 sm:p-8 space-y-6 shadow-xs">

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4 text-xs font-semibold">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 bg-netfly-blue text-white font-bold rounded-full">Daemon NMS Live</span>
                        <span class="text-slate-800">128 Node Korporat Terdaftar Aktif</span>
                    </div>
                    <div class="flex items-center gap-4 text-slate-500">
                        <span>Interval Polling: 30 Detik</span>
                        <span>&bull;</span>
                        <span class="flex items-center gap-1.5 text-emerald-700">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>WebSocket Terhubung</span>
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-xs">

                    <div class="p-1 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <div class="rounded-xl bg-white p-4 space-y-2 shadow-2xs">
                            <div class="flex justify-between text-slate-500 font-medium">
                                <span>Status Uptime</span>
                                <span class="text-emerald-700 font-bold">Normal</span>
                            </div>
                            <div class="text-2xl font-black text-slate-900">99.72%</div>
                            <div class="text-slate-500 text-[11px]">
                                121 Normal &bull; 3 Waspada &bull; 4 Down
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mt-2">
                                <div class="bg-emerald-600 h-full rounded-full" style="width: 94.5%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="p-1 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <div class="rounded-xl bg-white p-4 space-y-2 shadow-2xs">
                            <div class="flex justify-between text-slate-500 font-medium">
                                <span>Latensi Transit</span>
                                <span class="text-netfly-blue font-bold">Optimal</span>
                            </div>
                            <div class="text-2xl font-black text-slate-900">18.4 ms</div>
                            <div class="text-slate-500 text-[11px]">
                                Min 8.2ms &bull; Jitter 0.8ms
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mt-2">
                                <div class="bg-netfly-blue h-full rounded-full" style="width: 22%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="p-1 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <div class="rounded-xl bg-white p-4 space-y-2 shadow-2xs">
                            <div class="flex justify-between text-slate-500 font-medium">
                                <span>Packet Loss</span>
                                <span class="text-emerald-700 font-bold">&lt; 0.05%</span>
                            </div>
                            <div class="text-2xl font-black text-slate-900">0.04%</div>
                            <div class="text-slate-500 text-[11px]">
                                Batas alarm toleransi 0.50%
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mt-2">
                                <div class="bg-emerald-600 h-full rounded-full" style="width: 8%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="p-1 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <div class="rounded-xl bg-white p-4 space-y-2 shadow-2xs">
                            <div class="flex justify-between text-slate-500 font-medium">
                                <span>Margin Downtime</span>
                                <span class="text-amber-800 font-bold">Terkendali</span>
                            </div>
                            <div class="text-2xl font-black text-amber-700">2j 14m / 3j 36m</div>
                            <div class="text-slate-500 text-[11px]">
                                Sisa batas toleransi: 1 jam 22 menit
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mt-2">
                                <div class="bg-amber-500 h-full rounded-full" style="width: 62%"></div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="rounded-xl border border-slate-200/90 overflow-hidden text-xs bg-white shadow-2xs">
                    <div class="bg-slate-50 px-5 py-3.5 border-b border-slate-200/80 text-slate-800 flex justify-between font-bold">
                        <span>Data Telemetri Antarmuka Internet Aktif</span>
                        <span class="text-netfly-blue font-semibold">Sinkronisasi: Berjalan</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50/60 text-slate-600 border-b border-slate-200/80">
                                <tr>
                                    <th class="p-3.5">Kode Interface</th>
                                    <th class="p-3.5">Pelanggan Korporat</th>
                                    <th class="p-3.5">Media Koneksi</th>
                                    <th class="p-3.5">Utilisasi Bandwidth</th>
                                    <th class="p-3.5">Latensi</th>
                                    <th class="p-3.5">Kepatuhan SLA</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="p-3.5 text-netfly-blue font-bold">Gi0/0/1.100</td>
                                    <td class="p-3.5 text-slate-900 font-semibold">PT Bank Mandiri Tbk</td>
                                    <td class="p-3.5 text-slate-600">Fiber Dedicated 1G (Cyber - Thamrin)</td>
                                    <td class="p-3.5 text-slate-800 font-medium">740 Mbps / 680 Mbps</td>
                                    <td class="p-3.5 text-emerald-700 font-bold">12.1 ms</td>
                                    <td class="p-3.5"><span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold text-[11px]">99.98% Terpenuhi</span></td>
                                </tr>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="p-3.5 text-netfly-blue font-bold">Gi0/0/2.204</td>
                                    <td class="p-3.5 text-slate-900 font-semibold">PT Shopee Indonesia</td>
                                    <td class="p-3.5 text-slate-600">Fiber Dedicated 500M (Cyber - Kuningan)</td>
                                    <td class="p-3.5 text-slate-800 font-medium">410 Mbps / 395 Mbps</td>
                                    <td class="p-3.5 text-emerald-700 font-bold">14.8 ms</td>
                                    <td class="p-3.5"><span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold text-[11px]">99.95% Terpenuhi</span></td>
                                </tr>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="p-3.5 text-amber-700 font-bold">Wl0/1/0.301</td>
                                    <td class="p-3.5 text-slate-900 font-semibold">PT Trans Retail (Cikarang DC)</td>
                                    <td class="p-3.5 text-slate-600">Microwave 100M (BTS Aloha Hub)</td>
                                    <td class="p-3.5 text-slate-800 font-medium">82 Mbps / 78 Mbps</td>
                                    <td class="p-3.5 text-amber-700 font-bold">124.0 ms (Spike)</td>
                                    <td class="p-3.5"><span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 font-bold text-[11px]">Waspada Spike</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>
