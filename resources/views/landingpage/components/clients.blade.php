<section id="klien" class="py-20 sm:py-24 border-b border-slate-200/80 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <div class="reveal-left">
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Pelanggan NetFly
            </h2>
            <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-2xl leading-relaxed">
                Dipercaya oleh berbagai sektor strategis: grup perhotelan, fasilitas kesehatan, universitas, hingga
                manufaktur multinasional. Geser untuk melihat logo pelanggan kami:
            </p>
        </div>

        <div class="relative reveal delay-150">
            <div id="clients-carousel"
                class="flex gap-4 overflow-x-auto scroll-smooth snap-x snap-mandatory pb-4 pt-2 px-1 select-none cursor-grab active:cursor-grabbing [&::-webkit-scrollbar]:hidden [scrollbar-width:none] [-ms-overflow-style:none]">

                @forelse($clients as $index => $client)
                    <div
                        class="client-slide hover-lift snap-start shrink-0 w-47.5 sm:w-55 md:w-57.5 p-1.5 rounded-2xl bg-slate-100/80 border border-slate-200/80 hover:border-blue-300 transition-all duration-300 group shadow-xs">
                        <div
                            class="rounded-xl bg-white p-4 h-41.25 sm:h-43.75 flex flex-col items-center justify-between shadow-2xs group-hover:shadow-md transition-all">
                            <div class="w-full flex-1 flex items-center justify-center p-2">
                                <img src="{{ $client->logo_url }}" alt="Logo {{ $client->name }}"
                                    draggable="false"
                                    class="pointer-events-none select-none max-h-14 max-w-40 object-contain transition-transform duration-300 group-hover:scale-105"
                                    onerror="this.onerror=null; this.src='{{ asset('assets/images/netfly-logo.png') }}';">
                            </div>
                            <div class="w-full pt-2.5 border-t border-slate-100 text-center">
                                <h3 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-netfly-blue transition-colors truncate"
                                    title="{{ $client->name }}">
                                    {{ $client->name }}
                                </h3>
                                <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                                    {{ $client->category }} &bull; {{ $client->location }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="w-full py-8 text-center text-sm text-slate-400">
                        Belum ada pelanggan yang ditampilkan.
                    </div>
                @endforelse

            </div>
        </div>

        @if(count($clients) > 1)
            <div class="reveal delay-250 flex items-center justify-center gap-3.5 pt-2">
                <!-- Tombol Panah Kiri (Prev) -->
                <button id="slider-btn-prev" type="button"
                    class="w-8 h-8 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 hover:text-netfly-blue hover:border-blue-300 flex items-center justify-center transition-all shadow-xs active:scale-95 cursor-pointer"
                    title="Slide Sebelumnya" aria-label="Slide Pelanggan Sebelumnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <!-- Dots Container -->
                <div class="flex items-center gap-2" id="slider-dots-container">
                    @foreach($clients as $index => $client)
                        <button class="slider-dot {{ $index === 0 ? 'w-6 h-2.5 bg-netfly-blue' : 'w-2 h-2 bg-slate-300 hover:bg-slate-400' }} rounded-full transition-all cursor-pointer" 
                            data-index="{{ $index }}"
                            aria-label="Lihat Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>

                <!-- Tombol Panah Kanan (Next) -->
                <button id="slider-btn-next" type="button"
                    class="w-8 h-8 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 hover:text-netfly-blue hover:border-blue-300 flex items-center justify-center transition-all shadow-xs active:scale-95 cursor-pointer"
                    title="Slide Berikutnya" aria-label="Slide Pelanggan Berikutnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        @endif

    </div>
</section>
