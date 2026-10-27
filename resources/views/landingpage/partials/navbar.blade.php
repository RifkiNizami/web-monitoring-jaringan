<div id="header-wrapper" class="sticky top-0 z-50 pt-3 sm:pt-4 px-3 sm:px-6 transition-all duration-400 ease-spring">
    <header id="main-header"
        class="max-w-7xl mx-auto bg-white/95 backdrop-blur-xl border border-slate-200/90 rounded-2xl shadow-[0_4px_24px_-4px_rgba(0,0,0,0.06)] transition-all duration-400 ease-spring">
        <div id="header-container" class="px-4 sm:px-6 transition-all duration-400 ease-spring">
            <div id="header-inner"
                class="flex items-center justify-between h-18 sm:h-20 transition-all duration-400 ease-spring">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group focus:outline-none">
                    <img id="header-logo" src="{{ asset('assets/images/netfly-logo.png') }}"
                        alt="NetFly - PT Netmedia Fiberoptik Lancarmulya"
                        class="h-9 sm:h-10 w-auto object-contain transition-all duration-400 ease-spring group-hover:scale-102">
                </a>

                <nav id="header-nav"
                    class="hidden lg:flex items-center gap-2 text-xs sm:text-sm font-semibold transition-all duration-400 ease-spring">
                    <a href="{{ route('home') }}#layanan"
                        class="text-slate-600 hover:text-netfly-blue px-3.5 py-1.5 rounded-full hover:bg-slate-100/70 transition-all">
                        Layanan
                    </a>
                    <a href="{{ route('home') }}#klien"
                        class="text-slate-600 hover:text-netfly-blue px-3.5 py-1.5 rounded-full hover:bg-slate-100/70 transition-all">
                        Pelanggan
                    </a>
                    <a href="{{ route('home') }}#solusi"
                        class="text-slate-600 hover:text-netfly-blue px-3.5 py-1.5 rounded-full hover:bg-slate-100/70 transition-all">
                        Pemantauan
                    </a>
                    <a href="{{ route('home') }}#arsitektur"
                        class="text-slate-600 hover:text-netfly-blue px-3.5 py-1.5 rounded-full hover:bg-slate-100/70 transition-all">
                        Keunggulan
                    </a>
                    <a href="{{ route('home') }}#perusahaan"
                        class="text-slate-600 hover:text-netfly-blue px-3.5 py-1.5 rounded-full hover:bg-slate-100/70 transition-all">
                        Tentang Kami
                    </a>
                </nav>

                <div class="hidden sm:flex items-center gap-3">
                    <a id="header-cta" href="{{ route('home') }}#kontak"
                        class="group inline-flex items-center pl-4 pr-1.5 py-1.5 rounded-full bg-netfly-blue hover:bg-netfly-blue-dark text-white text-xs font-semibold tracking-wide transition-all duration-400 ease-spring shadow-xs hover:shadow-md active:scale-[0.98]">
                        <span>Hubungi Kami</span>
                        <span id="header-cta-icon"
                            class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center ml-2.5 group-hover:translate-x-0.5 group-hover:bg-white/30 transition-all duration-400 ease-spring">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </span>
                    </a>
                </div>

                <button id="mobile-drawer-btn" type="button"
                    class="lg:hidden p-2 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 hover:text-slate-900 transition-colors"
                    aria-label="Buka Menu Navigasi" aria-expanded="false">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <div id="mobile-drawer"
            class="hidden lg:hidden border-t border-slate-200/80 px-4 py-5 space-y-2 text-sm font-semibold bg-white/95 backdrop-blur-xl rounded-b-2xl">
            <a href="{{ route('home') }}#layanan"
                class="block text-slate-700 py-2 px-3 rounded-lg hover:bg-slate-50 hover:text-netfly-blue">Layanan</a>
            <a href="{{ route('home') }}#klien"
                class="block text-slate-700 py-2 px-3 rounded-lg hover:bg-slate-50 hover:text-netfly-blue">Pelanggan</a>
            <a href="{{ route('home') }}#solusi"
                class="block text-slate-700 py-2 px-3 rounded-lg hover:bg-slate-50 hover:text-netfly-blue">Pemantauan</a>
            <a href="{{ route('home') }}#arsitektur"
                class="block text-slate-700 py-2 px-3 rounded-lg hover:bg-slate-50 hover:text-netfly-blue">Keunggulan</a>
            <a href="{{ route('home') }}#perusahaan"
                class="block text-slate-700 py-2 px-3 rounded-lg hover:bg-slate-50 hover:text-netfly-blue">Tentang Kami</a>
            <div class="pt-3 border-t border-slate-200/80 flex flex-col gap-2">
                <a href="{{ route('home') }}#kontak"
                    class="w-full text-center py-2.5 bg-netfly-blue text-white font-semibold rounded-full shadow-xs">
                    Hubungi Kami &rarr;
                </a>
            </div>
        </div>
    </header>
</div>
