<!DOCTYPE html>
<html lang="id" class="h-full bg-white antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal autentikasi resmi Administrator untuk pengawasan dan pengelolaan infrastruktur jaringan terpadu PT Netmedia Fiberoptik Lancarmulya (NetFly).">
    <title>Login — Portal Manajemen Jaringan NetFly</title>
    <link rel="icon" href="{{ asset('assets/images/netfly-logo.png') }}" type="image/png">

    <!-- Preload LCP Logo Image -->
    <link rel="preload" as="image" href="{{ asset('assets/images/netfly-logo.webp') }}" type="image/webp" fetchpriority="high">

    <!-- Non-Blocking Google Fonts (Eliminates FCP Render-Blocking Penalty) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    <link id="googleFontCss" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" media="print">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    </noscript>

    @vite(['resources/css/admin/login-entry.css', 'resources/js/login-entry.js'])
</head>
<body class="h-full bg-white font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    <div id="login-page" class="min-h-screen w-full flex flex-col lg:flex-row overflow-hidden bg-white">
        
        <!-- SISI KIRI: Form Login -->
        <div class="w-full lg:w-[42%] xl:w-[38%] min-h-screen flex flex-col justify-between px-8 sm:px-14 lg:px-16 py-10 z-10 bg-white">
            <div></div> <!-- Spacer top -->

            <div class="w-full max-w-sm mx-auto">
                <!-- Logo NETFly (WebP 13KB + PNG Fallback) -->
                <div class="mb-10 text-center flex flex-col items-center login-logo-item">
                    <picture>
                        <source srcset="{{ asset('assets/images/netfly-logo.webp') }}" type="image/webp">
                        <img
                            src="{{ asset('assets/images/netfly-logo.png') }}"
                            alt="NetFly Logo"
                            width="189"
                            height="80"
                            fetchpriority="high"
                            decoding="sync"
                            class="h-16 sm:h-20 w-auto object-contain"
                        >
                    </picture>
                </div>

                @if ($errors->any())
                    <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200/80 text-xs text-red-700 flex items-start gap-2.5 login-item login-error-shake">
                        <svg class="w-4 h-4 text-red-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Form Login -->
                <form id="loginForm" action="{{ url('/login') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Email Field -->
                    <div class="login-item login-delay-1">
                        <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5 tracking-tight">Email</label>
                        <div class="relative">
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus
                                autocomplete="email"
                                placeholder="example@gmail.com"
                                class="w-full px-3.5 py-2.5 text-sm text-slate-800 bg-white border border-slate-300 rounded-lg shadow-xs placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-150"
                            >
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="login-item login-delay-2">
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5 tracking-tight">Kata Sandi</label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required 
                                autocomplete="current-password"
                                placeholder="••••••••••••"
                                class="w-full pl-3.5 pr-10 py-2.5 text-sm text-slate-800 bg-white border border-slate-300 rounded-lg shadow-xs placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-150 font-mono"
                            >
                            <button 
                                type="button" 
                                id="togglePassword" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-700 focus:outline-none transition-colors cursor-pointer"
                                aria-label="Tampilkan atau sembunyikan kata sandi"
                            >
                                <svg id="eyeIcon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eyeOffIcon" class="w-4 h-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center login-item login-delay-3">
                        <input 
                            type="checkbox" 
                            id="remember" 
                            name="remember" 
                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                        >
                        <label for="remember" class="ml-2 text-xs text-slate-700 cursor-pointer select-none">
                            Ingat saya
                        </label>
                    </div>

                    <!-- Tombol Masuk -->
                    <div class="pt-2 text-center login-item login-delay-4">
                        <button 
                            type="submit" 
                            id="loginSubmitBtn"
                            class="inline-flex items-center justify-center gap-2 w-auto min-w-[130px] px-8 py-2.5 bg-[#0066cc] hover:bg-[#0052a3] text-white text-xs font-bold rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 active:scale-95 transition-all duration-150 tracking-wider uppercase cursor-pointer"
                        >
                            <svg id="loginBtnSpinner" class="hidden w-3.5 h-3.5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span id="loginBtnText">MASUK</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer Kiri -->
            <div class="text-center text-[11px] text-slate-600 mt-10 login-item login-delay-5">
                &copy; {{ date('Y') }} PT Netmedia Fiberoptik Lancarmulya
            </div>
        </div>

        <!-- SISI KANAN: Visual Hero Curve & Portal Branding -->
        <div id="login-hero-pane" class="hidden lg:flex flex-1 relative overflow-hidden bg-[#0d276b] items-center justify-center text-center px-12">
            
            <!-- Organic Curve Divider on the left side of this pane -->
            <div class="absolute inset-y-0 left-0 w-24 xl:w-32 z-20 pointer-events-none fill-white login-curve-enter">
                <svg class="h-full w-full" preserveAspectRatio="none" viewBox="0 0 100 1000">
                    <path d="M0,0 L100,0 C30,300 0,650 100,1000 L0,1000 Z" />
                </svg>
            </div>

            <!-- Background Gradient Layer -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#123b88] via-[#0d276b] to-[#081842] z-0"></div>

            <!-- Decorative Rings (Static GPU Layer + rAF Parallax) -->
            <div id="login-rings-wrapper" class="absolute inset-0 pointer-events-none will-change-transform">
                <div class="absolute -top-20 -right-20 w-96 h-96 rounded-full border border-blue-400/15"></div>
                <div class="absolute -top-36 -right-36 w-[520px] h-[520px] rounded-full border border-blue-400/10"></div>
            </div>

            <!-- Subtle Accent Dots (Lightweight Static Opacity) -->
            <div class="absolute top-1/4 right-1/3 w-1.5 h-1.5 rounded-full bg-blue-300/90 pointer-events-none"></div>
            <div class="absolute top-1/2 left-1/4 w-1.5 h-1.5 rounded-full bg-white/80 pointer-events-none"></div>
            <div class="absolute bottom-1/4 right-1/4 w-2 h-2 rounded-full bg-cyan-300/90 pointer-events-none"></div>

            <!-- Glowing Blue Orb -->
            <div id="login-orb-wrapper" class="absolute bottom-24 left-12 pointer-events-none will-change-transform login-orb-enter">
                <div class="relative flex items-center justify-center w-36 h-36">
                    <div class="absolute inset-0 rounded-full bg-blue-500/35 blur-xl"></div>
                    <div class="relative w-24 h-24 rounded-full bg-gradient-to-tr from-blue-600 via-blue-500 to-cyan-400"></div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="relative z-10 max-w-lg mx-auto py-12 login-hero-content">
                <h1 class="text-4xl xl:text-5xl font-extrabold text-white tracking-tight leading-[1.2]">
                    Portal Manajemen<br>Jaringan
                </h1>
                
                <p class="mt-6 text-sm xl:text-base text-blue-100 font-normal leading-relaxed max-w-md mx-auto">
                    Sistem autentikasi khusus Administrator untuk pengawasan<br class="hidden sm:inline">
                    dan pengelolaan jaringan terpadu.
                </p>
            </div>
        </div>

    </div>

</body>
</html>
