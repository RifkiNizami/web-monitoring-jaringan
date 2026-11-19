<!DOCTYPE html>
<html lang="id"
    class="scroll-smooth bg-slate-50 text-slate-800 antialiased selection:bg-netfly-blue selection:text-white">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>NetFly - Konsultan Infrastruktur Jaringan & Sistem Monitoring</title>
    <meta name="description"
        content="PT. Netmedia Fiberoptik Lancarmulya (NetFly) — Penyedia infrastruktur jaringan korporat, dedicated fiber optic 1:1, microwave wireless broadband, dan autonomous network monitoring (NMS).">
    <meta name="keywords"
        content="NetFly, PT Netmedia Fiberoptik Lancarmulya, Konsultan IT, Infrastruktur Jaringan, Fiber Optic Ring, Microwave Wireless, NMS, SLA 99.5%, Jakarta, Sidoarjo, Surabaya">

    <link rel="icon" href="{{ asset('assets/images/netfly-logo.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased overflow-x-hidden min-h-screen">

    @yield('content')

    <!-- Floating Back to Top Button with Circular Scroll Progress Ring -->
    <button id="backToTopBtn"
            type="button"
            aria-label="Kembali ke atas"
            class="fixed bottom-6 right-6 z-40 w-11 h-11 rounded-full bg-white/95 backdrop-blur-md border border-slate-200/90 shadow-lg text-slate-700 hover:text-netfly-blue hover:shadow-xl hover:scale-105 active:scale-95 transition-all duration-300 flex items-center justify-center opacity-0 pointer-events-none translate-y-3 cursor-pointer group">
        <!-- Circular Progress Ring (SVG) -->
        <svg class="absolute inset-0 w-full h-full -rotate-90 pointer-events-none p-0.5" viewBox="0 0 44 44">
            <circle cx="22" cy="22" r="18" class="stroke-slate-200/70" stroke-width="2.5" fill="none" />
            <circle id="scrollProgressCircle" cx="22" cy="22" r="18" class="stroke-netfly-blue transition-all duration-100" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="113.1" stroke-dashoffset="113.1" fill="none" />
        </svg>
        <!-- Arrow Icon -->
        <svg class="w-4 h-4 relative z-10 transition-transform duration-300 group-hover:-translate-y-0.5 text-slate-700 group-hover:text-netfly-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>

</body>

</html>