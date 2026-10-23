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

</body>

</html>