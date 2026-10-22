<!DOCTYPE html>
<html lang="id" class="h-full bg-white antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Portal Manajemen Jaringan NetFly</title>
    <link rel="icon" href="{{ asset('assets/images/netfly-logo.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-white font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    <div class="min-h-screen w-full flex flex-col lg:flex-row overflow-hidden bg-white">
        
        <!-- SISI KIRI: Form Login -->
        <div class="w-full lg:w-[42%] xl:w-[38%] min-h-screen flex flex-col justify-between px-8 sm:px-14 lg:px-16 py-10 z-10 bg-white">
            <div></div> <!-- Spacer top -->

            <div class="w-full max-w-sm mx-auto">
                <!-- Logo NETFly -->
                <div class="mb-10 text-center flex flex-col items-center">
                    <img src="{{ asset('assets/images/netfly-logo.png') }}" alt="NetFly Logo" class="h-16 sm:h-20 w-auto object-contain">
                </div>

                @if ($errors->any())
                    <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200/80 text-xs text-red-600 flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                <form action="{{ url('/login') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5 tracking-tight">Email</label>
                        <div class="relative">
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email', 'admin@gmail.com') }}" 
                                required 
                                autofocus
                                placeholder="admin@gmail.com"
                                class="w-full px-3.5 py-2.5 text-sm text-slate-800 bg-white border border-slate-300 rounded-lg shadow-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            >
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5 tracking-tight">Kata Sandi</label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                value="password123"
                                required 
                                placeholder="••••••••••••"
                                class="w-full pl-3.5 pr-10 py-2.5 text-sm text-slate-800 bg-white border border-slate-300 rounded-lg shadow-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-mono"
                            >
                            <button 
                                type="button" 
                                id="togglePassword" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                                aria-label="Toggle password visibility"
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
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            id="remember" 
                            name="remember" 
                            checked
                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                        >
                        <label for="remember" class="ml-2 text-xs text-slate-600 cursor-pointer select-none">
                            Ingat saya
                        </label>
                    </div>

                    <!-- Tombol Masuk -->
                    <div class="pt-2 text-center">
                        <button 
                            type="submit" 
                            class="w-auto min-w-[130px] px-8 py-2.5 bg-[#0066cc] hover:bg-[#0052a3] text-white text-xs font-bold rounded-lg shadow-lg shadow-blue-500/40 hover:shadow-blue-500/60 active:scale-95 transition-all duration-200 tracking-wider uppercase cursor-pointer"
                        >
                            MASUK
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer Kiri -->
            <div class="text-center text-[11px] text-slate-400 mt-10">
                &copy; {{ date('Y') }} PT Netmedia Fiberoptik Lancarmulya
            </div>
        </div>

        <!-- SISI KANAN: Visual Hero Curve & Portal Branding -->
        <div class="hidden lg:flex flex-1 relative overflow-hidden bg-[#0d276b] items-center justify-center text-center px-12">
            
            <!-- Organic Curve Divider on the left side of this pane -->
            <div class="absolute inset-y-0 left-0 w-24 xl:w-32 z-20 pointer-events-none fill-white">
                <svg class="h-full w-full" preserveAspectRatio="none" viewBox="0 0 100 1000">
                    <path d="M0,0 L100,0 C30,300 0,650 100,1000 L0,1000 Z" />
                </svg>
            </div>

            <!-- Background Gradient Layer -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#123b88] via-[#0d276b] to-[#081842] z-0"></div>

            <!-- Decorative Glow Rings (Top Right) -->
            <div class="absolute -top-20 -right-20 w-96 h-96 rounded-full border border-blue-400/15 pointer-events-none"></div>
            <div class="absolute -top-36 -right-36 w-[520px] h-[520px] rounded-full border border-blue-400/10 pointer-events-none"></div>

            <!-- Floating Subtle Star/Dot Glows -->
            <div class="absolute top-1/4 right-1/3 w-1.5 h-1.5 rounded-full bg-blue-300 shadow-[0_0_8px_2px_rgba(147,197,253,0.8)] animate-pulse"></div>
            <div class="absolute top-1/2 left-1/4 w-1.5 h-1.5 rounded-full bg-white shadow-[0_0_10px_2px_rgba(255,255,255,0.9)] animate-pulse" style="animation-delay: 1.5s;"></div>
            <div class="absolute bottom-1/4 right-1/4 w-2 h-2 rounded-full bg-cyan-300 shadow-[0_0_12px_3px_rgba(103,232,249,0.9)] animate-pulse" style="animation-delay: 0.8s;"></div>

            <!-- Glowing Blue Orb (Bottom Left of Right Side) -->
            <div class="absolute bottom-28 left-16 w-24 h-24 rounded-full bg-gradient-to-tr from-blue-600 via-blue-500 to-cyan-400 shadow-[0_0_50px_20px_rgba(37,99,235,0.55)] pointer-events-none animate-float"></div>

            <!-- Content Area -->
            <div class="relative z-10 max-w-lg mx-auto py-12">
                <h1 class="text-4xl xl:text-5xl font-extrabold text-white tracking-tight leading-[1.2]">
                    Portal Manajemen<br>Jaringan
                </h1>
                
                <p class="mt-6 text-sm xl:text-base text-blue-100/85 font-normal leading-relaxed max-w-md mx-auto">
                    Sistem autentikasi khusus Administrator untuk pengawasan<br class="hidden sm:inline">
                    dan pengelolaan jaringan terpadu.
                </p>
            </div>
        </div>

    </div>

</body>
</html>
