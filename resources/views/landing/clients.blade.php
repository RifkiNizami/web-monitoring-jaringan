<section id="klien" class="py-20 sm:py-24 border-b border-slate-200/80 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    Pelanggan NetFly
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-2xl leading-relaxed">
                    Dipercaya oleh berbagai sektor strategis: grup perhotelan, fasilitas kesehatan, universitas, hingga
                    manufaktur multinasional. Geser untuk melihat logo pelanggan kami:
                </p>
            </div>

            <div class="flex items-center gap-2.5 self-start sm:self-end shrink-0">
                <span class="text-xs font-semibold text-slate-500 mr-1" id="client-slide-indicator">
                    <span id="active-slide-num">1</span> / 8 Pelanggan
                </span>
                <button id="slider-btn-prev" type="button"
                    class="w-9 h-9 rounded-full border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 hover:text-netfly-blue hover:border-netfly-blue flex items-center justify-center transition-all shadow-xs active:scale-95"
                    title="Slide Sebelumnya" aria-label="Slide Pelanggan Sebelumnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button id="slider-btn-next" type="button"
                    class="w-9 h-9 rounded-full border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 hover:text-netfly-blue hover:border-netfly-blue flex items-center justify-center transition-all shadow-xs active:scale-95"
                    title="Slide Berikutnya" aria-label="Slide Pelanggan Berikutnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="relative">
            <div id="clients-carousel"
                class="flex gap-4 overflow-x-auto scroll-smooth snap-x snap-mandatory pb-4 pt-1 px-0.5 select-none cursor-grab active:cursor-grabbing [&::-webkit-scrollbar]:hidden"
                style="scrollbar-width: none; -ms-overflow-style: none;">

                <div
                    class="client-slide snap-start shrink-0 w-47.5 sm:w-55 md:w-57.5 p-1.5 rounded-2xl bg-slate-100/80 border border-slate-200/80 hover:border-blue-300 transition-all duration-300 group shadow-xs">
                    <div
                        class="rounded-xl bg-white p-4 h-41.25 sm:h-43.75 flex flex-col items-center justify-between shadow-2xs group-hover:shadow-md transition-shadow">
                        <div class="w-full flex-1 flex items-center justify-center p-2">
                            <img src="{{ asset('assets/images/clients/logo-prime-royal.png') }}" alt="Logo Prime Royal Hotel"
                                class=" max-h-14 max-w-40 object-contain transition-transform duration-300
                                group-hover:scale-105">
                        </div>
                        <div class="w-full pt-2.5 border-t border-slate-100 text-center">
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-netfly-blue transition-colors truncate"
                                title="Prime Royal Hotel">
                                Prime Royal Hotel
                            </h3>
                            <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                                Perhotelan &bull; Surabaya
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="client-slide snap-start shrink-0 w-47.5 sm:w-55 md:w-57.5 p-1.5 rounded-2xl bg-slate-100/80 border border-slate-200/80 hover:border-blue-300 transition-all duration-300 group shadow-xs">
                    <div
                        class="rounded-xl bg-white p-4 h-41.25 sm:h-43.75 flex flex-col items-center justify-between shadow-2xs group-hover:shadow-md transition-shadow">
                        <div class="w-full flex-1 flex items-center justify-center p-2">
                            <img src="{{ asset('assets/images/clients/logo-bebek-tepi-sawah.png') }}" alt="Logo Bebek Tepi Sawah"
                                class=" max-h-14 max-w-37.5 object-contain transition-transform duration-300
                                group-hover:scale-105">
                        </div>
                        <div class="w-full pt-2.5 border-t border-slate-100 text-center">
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-netfly-blue transition-colors truncate"
                                title="Bebek Tepi Sawah">
                                Bebek Tepi Sawah
                            </h3>
                            <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                                Resto &bull; Jawa Timur
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="client-slide snap-start shrink-0 w-47.5 sm:w-55 md:w-57.5 p-1.5 rounded-2xl bg-slate-100/80 border border-slate-200/80 hover:border-blue-300 transition-all duration-300 group shadow-xs">
                    <div
                        class="rounded-xl bg-white p-4 h-41.25 sm:h-43.75 flex flex-col items-center justify-between shadow-2xs group-hover:shadow-md transition-shadow">
                        <div class="w-full flex-1 flex items-center justify-center p-2">
                            <img src="{{ asset('assets/images/clients/logo-gkm.png') }}" alt="Logo PT. Grahasindo Karya Mandiri"
                                class=" max-h-14 max-w-37.5 object-contain transition-transform duration-300
                                group-hover:scale-105">
                        </div>
                        <div class="w-full pt-2.5 border-t border-slate-100 text-center">
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-netfly-blue transition-colors truncate"
                                title="PT. Grahasindo Karya Mandiri">
                                PT. GKM Mandiri
                            </h3>
                            <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                                Konstruksi &bull; Sidoarjo
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="client-slide snap-start shrink-0 w-47.5 sm:w-55 md:w-57.5 p-1.5 rounded-2xl bg-slate-100/80 border border-slate-200/80 hover:border-blue-300 transition-all duration-300 group shadow-xs">
                    <div
                        class="rounded-xl bg-white p-4 h-41.25 sm:h-43.75 flex flex-col items-center justify-between shadow-2xs group-hover:shadow-md transition-shadow">
                        <div class="w-full flex-1 flex items-center justify-center p-2">
                            <img src="{{ asset('assets/images/clients/logo-al-azhar.png') }}" alt="Logo Klinik Pratama Ranap Bersalin Al-Azhar"
                                class=" max-h-12 max-w-40 object-contain transition-transform duration-300
                                group-hover:scale-105">
                        </div>
                        <div class="w-full pt-2.5 border-t border-slate-100 text-center">
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-netfly-blue transition-colors truncate"
                                title="Klinik Pratama Al-Azhar">
                                Klinik Al-Azhar
                            </h3>
                            <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                                Kesehatan &bull; Surabaya
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="client-slide snap-start shrink-0 w-47.5 sm:w-55 md:w-57.5 p-1.5 rounded-2xl bg-slate-100/80 border border-slate-200/80 hover:border-blue-300 transition-all duration-300 group shadow-xs">
                    <div
                        class="rounded-xl bg-white p-4 h-41.25 sm:h-43.75 flex flex-col items-center justify-between shadow-2xs group-hover:shadow-md transition-shadow">
                        <div class="w-full flex-1 flex items-center justify-center p-2">
                            <img src="{{ asset('assets/images/clients/logo-um-surabaya.png') }}" alt="Logo UM Surabaya"
                                class="max-h-14 max-w-40 object-contain transition-transform duration-300
                                group-hover:scale-105">
                        </div>
                        <div class="w-full pt-2.5 border-t border-slate-100 text-center">
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-netfly-blue transition-colors truncate"
                                title="Universitas Muhammadiyah Surabaya">
                                UM Surabaya
                            </h3>
                            <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                                Pendidikan &bull; Surabaya
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="client-slide snap-start shrink-0 w-47.5 sm:w-55 md:w-57.5 p-1.5 rounded-2xl bg-slate-100/80 border border-slate-200/80 hover:border-blue-300 transition-all duration-300 group shadow-xs">
                    <div
                        class="rounded-xl bg-white p-4 h-41.25 sm:h-43.75 flex flex-col items-center justify-between shadow-2xs group-hover:shadow-md transition-shadow">
                        <div class="w-full flex-1 flex items-center justify-center p-2">
                            <img src="{{ asset('assets/images/clients/logo-unej.png') }}" alt="Logo Universitas Jember UNEJ"
                                class=" max-h-14 max-w-32.5 object-contain transition-transform duration-300
                                group-hover:scale-105">
                        </div>
                        <div class="w-full pt-2.5 border-t border-slate-100 text-center">
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-netfly-blue transition-colors truncate"
                                title="Universitas Jember UNEJ">
                                Univ. Jember (UNEJ)
                            </h3>
                            <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                                PTN &bull; Jember
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="client-slide snap-start shrink-0 w-47.5 sm:w-55 md:w-57.5 p-1.5 rounded-2xl bg-slate-100/80 border border-slate-200/80 hover:border-blue-300 transition-all duration-300 group shadow-xs">
                    <div
                        class="rounded-xl bg-white p-4 h-41.25 sm:h-43.75 flex flex-col items-center justify-between shadow-2xs group-hover:shadow-md transition-shadow">
                        <div class="w-full flex-1 flex items-center justify-center p-2">
                            <img src="{{ asset('assets/images/clients/logo-inez.png') }}" alt="Logo Inez Cosmetics"
                                class="max-h-14 max-w-37.5 object-contain transition-transform duration-300
                                group-hover:scale-105">
                        </div>
                        <div class="w-full pt-2.5 border-t border-slate-100 text-center">
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-netfly-blue transition-colors truncate"
                                title="Inez Cosmetics">
                                Inez Cosmetics
                            </h3>
                            <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                                Manufaktur &bull; Jawa Timur
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="client-slide snap-start shrink-0 w-47.5 sm:w-55 md:w-57.5 p-1.5 rounded-2xl bg-slate-100/80 border border-slate-200/80 hover:border-blue-300 transition-all duration-300 group shadow-xs">
                    <div
                        class="rounded-xl bg-white p-4 h-41.25 sm:h-43.75 flex flex-col items-center justify-between shadow-2xs group-hover:shadow-md transition-shadow">
                        <div class="w-full flex-1 flex items-center justify-center p-2">
                            <img src="{{ asset('assets/images/clients/logo-fuji-seal.png') }}" alt="Logo Fuji Seal Indonesia"
                                class=" max-h-14 max-w-37.5 object-contain transition-transform duration-300
                                group-hover:scale-105">
                        </div>
                        <div class="w-full pt-2.5 border-t border-slate-100 text-center">
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-netfly-blue transition-colors truncate"
                                title="Fuji Seal Indonesia">
                                Fuji Seal Indonesia
                            </h3>
                            <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                                Manufaktur &bull; Global
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="flex items-center justify-center gap-2 pt-2" id="slider-dots-container">
            <button class="slider-dot w-6 h-2.5 rounded-full bg-netfly-blue transition-all" data-index="0"
                aria-label="Lihat Slide 1"></button>
            <button class="slider-dot w-2 h-2 rounded-full bg-slate-300 hover:bg-slate-400 transition-all"
                data-index="1" aria-label="Lihat Slide 2"></button>
            <button class="slider-dot w-2 h-2 rounded-full bg-slate-300 hover:bg-slate-400 transition-all"
                data-index="2" aria-label="Lihat Slide 3"></button>
            <button class="slider-dot w-2 h-2 rounded-full bg-slate-300 hover:bg-slate-400 transition-all"
                data-index="3" aria-label="Lihat Slide 4"></button>
            <button class="slider-dot w-2 h-2 rounded-full bg-slate-300 hover:bg-slate-400 transition-all"
                data-index="4" aria-label="Lihat Slide 5"></button>
            <button class="slider-dot w-2 h-2 rounded-full bg-slate-300 hover:bg-slate-400 transition-all"
                data-index="5" aria-label="Lihat Slide 6"></button>
            <button class="slider-dot w-2 h-2 rounded-full bg-slate-300 hover:bg-slate-400 transition-all"
                data-index="6" aria-label="Lihat Slide 7"></button>
            <button class="slider-dot w-2 h-2 rounded-full bg-slate-300 hover:bg-slate-400 transition-all"
                data-index="7" aria-label="Lihat Slide 8"></button>
        </div>

    </div>
</section>