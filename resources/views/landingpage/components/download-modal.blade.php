{{-- [PANDUAN UBAH MODAL QR LANDING PAGE] URL barcode ditarik dari app_setting('mobile_app_download_url') lalu dirender ke #downloadAppQrCanvas oleh resources/js/landing/download-modal.js --}}
<!-- Modal Barcode / QR Code Unduh Aplikasi Mobile -->
<div id="downloadAppModal"
     data-download-url="{{ $mobileAppDownloadUrl ?? app_setting('mobile_app_download_url', 'https://netmediafiberoptik.com/download/netfly-mobile.apk') }}"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden transition-opacity duration-200"
     role="dialog"
     aria-modal="true"
     aria-labelledby="downloadModalTitle">

    <!-- Backdrop -->
    <div id="downloadAppBackdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

    <!-- Dialog Card -->
    <div id="downloadAppDialog"
         class="relative w-full max-w-sm sm:max-w-md bg-white rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden z-10 transform transition-all p-6 sm:p-7 text-center">

        <!-- Close Button (X) -->
        <button type="button"
                data-close-download-modal
                class="absolute top-4 right-4 p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-full transition-colors cursor-pointer"
                aria-label="Tutup Dialog">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Header / Logo Emblem -->
        <div class="mx-auto w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100/90 flex items-center justify-center p-2 mb-3 shadow-2xs">
            <picture class="w-full h-full flex items-center justify-center">
                <source srcset="{{ asset('assets/images/netfly-emblem.webp') }}" type="image/webp">
                <img src="{{ asset('assets/images/netfly-emblem.png') }}" alt="NetFly" class="w-full h-full object-contain">
            </picture>
        </div>

        <h3 id="downloadModalTitle" class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight leading-tight">
            Unduh Aplikasi NetFly Mobile
        </h3>
        <p class="text-xs text-slate-500 mt-1 leading-normal max-w-xs mx-auto">
            Scan barcode di bawah ini menggunakan smartphone Anda untuk mengunduh aplikasi NetFly.
        </p>

        <!-- QR Code Frame -->
        <div class="mt-5 p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80 inline-block shadow-inner relative group">
            <div class="bg-white p-2.5 rounded-xl shadow-xs border border-slate-200/60">
                <canvas id="downloadAppQrCanvas" class="w-48 h-48 sm:w-52 sm:h-52 mx-auto rounded-lg block"></canvas>
            </div>
            <div class="mt-2.5 flex items-center justify-center gap-1.5 text-[11px] font-semibold text-slate-500">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Arahkan kamera smartphone ke barcode</span>
            </div>
        </div>

        <!-- Download URL Field & Copy Button -->
        <div class="mt-5 text-left">
            <label for="downloadAppUrlInput" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                Tautan Unduh Langsung
            </label>
            <div class="flex items-center gap-2">
                <input id="downloadAppUrlInput"
                       type="text"
                       readonly
                       value="{{ $mobileAppDownloadUrl ?? app_setting('mobile_app_download_url', 'https://netmediafiberoptik.com/download/netfly-mobile.apk') }}"
                       class="flex-1 min-w-0 px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-mono focus:outline-none select-all">
                <button type="button"
                        id="copyAppLinkBtn"
                        class="shrink-0 px-3 py-2 text-xs font-semibold text-netfly-blue bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-xl transition-colors cursor-pointer flex items-center gap-1.5"
                        title="Salin Tautan">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span>Salin</span>
                </button>
            </div>
            <!-- Alert copy success -->
            <p id="copyAppSuccessMsg" class="hidden text-[11px] font-semibold text-emerald-600 mt-1 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Tautan berhasil disalin ke clipboard!</span>
            </p>
        </div>

        <!-- Direct Action Buttons -->
        <div class="mt-5 pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center gap-2.5">
            <a href="{{ $mobileAppDownloadUrl ?? app_setting('mobile_app_download_url', 'https://netmediafiberoptik.com/download/netfly-mobile.apk') }}"
               target="_blank"
               rel="noopener noreferrer"
               class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-netfly-blue hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Buka / Unduh APK Langsung</span>
            </a>
            <button type="button"
                    data-close-download-modal
                    class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors cursor-pointer">
                Tutup
            </button>
        </div>

    </div>
</div>
