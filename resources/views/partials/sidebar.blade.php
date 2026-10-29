<!-- Sidebar Partial -->
<aside id="sidebar"
    class="w-64 bg-white border-r border-slate-200/80 flex flex-col shrink-0 transition-transform duration-300 lg:translate-x-0 -translate-x-full fixed lg:static inset-y-0 left-0 h-full z-40 select-none">

    <!-- 1. Logo Header (Pinned Top - shrink-0) -->
    <div class="px-5 py-4 flex items-center justify-between border-b border-slate-100 shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
            <picture>
                <source srcset="{{ asset('assets/images/netfly-logo.webp') }}" type="image/webp">
                <img src="{{ asset('assets/images/netfly-logo.png') }}" alt="NetFly"
                    class="h-8 w-auto object-contain transition-transform duration-200 group-hover:scale-102">
            </picture>
        </a>
        <!-- Mobile close button -->
        <button id="closeSidebar"
            class="lg:hidden text-slate-400 hover:text-slate-600 p-1.5 rounded-md hover:bg-slate-100 cursor-pointer"
            aria-label="Tutup Menu">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- 2. Menu Navigation (Middle scrollable area ONLY - flex-1 min-h-0 overflow-y-auto) -->
    <div class="px-3 py-3 space-y-4 flex-1 min-h-0 overflow-y-auto">

        <!-- Group 1: MONITORING & KENDALI -->
        <div>
            <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                MONITORING & KENDALI
            </p>
            <nav class="space-y-1">
                <!-- Dashboard -->
                @php $isDashboardActive = request()->routeIs('dashboard') || request()->routeIs('admin'); @endphp
                <a href="{{ route('dashboard') }}"
                    class="flex items-center justify-between px-3.5 py-2 text-xs rounded-lg transition-all duration-150 group border-l-4 {{ $isDashboardActive ? 'bg-blue-50/90 text-netfly-blue font-semibold border-netfly-blue shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0 {{ $isDashboardActive ? 'text-netfly-blue' : 'text-slate-400 group-hover:text-slate-600' }}"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Dashboard</span>
                    </div>
                </a>

                <!-- Monitoring -->
                @php $isMonitoringActive = request()->routeIs('monitoring.*'); @endphp
                <a href="{{ route('monitoring.index') }}"
                    class="flex items-center justify-between px-3.5 py-2 text-xs rounded-lg transition-all duration-150 group border-l-4 {{ $isMonitoringActive ? 'bg-blue-50/90 text-netfly-blue font-semibold border-netfly-blue shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0 {{ $isMonitoringActive ? 'text-netfly-blue' : 'text-slate-400 group-hover:text-slate-600' }}"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>Monitoring</span>
                    </div>
                </a>

                <!-- Network Devices -->
                @php $isDevicesActive = request()->routeIs('devices.*'); @endphp
                <a href="{{ route('devices.index') }}"
                    class="flex items-center justify-between px-3.5 py-2 text-xs rounded-lg transition-all duration-150 group border-l-4 {{ $isDevicesActive ? 'bg-blue-50/90 text-netfly-blue font-semibold border-netfly-blue shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0 {{ $isDevicesActive ? 'text-netfly-blue' : 'text-slate-400 group-hover:text-slate-600' }}"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                        </svg>
                        <span>Network Devices</span>
                    </div>
                </a>

                <!-- Ticket & Insiden -->
                @php $isTicketsActive = request()->routeIs('tickets.*'); @endphp
                <a href="{{ route('tickets.index') }}"
                    class="flex items-center justify-between px-3.5 py-2 text-xs rounded-lg transition-all duration-150 group border-l-4 {{ $isTicketsActive ? 'bg-blue-50/90 text-netfly-blue font-semibold border-netfly-blue shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0 {{ $isTicketsActive ? 'text-netfly-blue' : 'text-slate-400 group-hover:text-slate-600' }}"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                        <span>Ticket & Insiden</span>
                    </div>
                    <span
                        class="inline-flex items-center justify-center min-w-4.5 h-4.5 px-1.5 text-[10px] font-bold rounded-full {{ $isTicketsActive ? 'bg-blue-100 text-netfly-blue' : 'bg-slate-100 text-slate-600' }}">
                        3
                    </span>
                </a>
            </nav>
        </div>

        <!-- Group 2: DATA & KEMITRAAN -->
        <div>
            <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                DATA & KEMITRAAN
            </p>
            <nav class="space-y-1">
                <!-- Customer & Project (Matches both customers.* and projects.*) -->
                @php $isCustomersActive = request()->routeIs('customers.*') || request()->routeIs('projects.*'); @endphp
                <a href="{{ route('customers.index') }}"
                    class="flex items-center justify-between px-3.5 py-2 text-xs rounded-lg transition-all duration-150 group border-l-4 {{ $isCustomersActive ? 'bg-blue-50/90 text-netfly-blue font-semibold border-netfly-blue shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0 {{ $isCustomersActive ? 'text-netfly-blue' : 'text-slate-400 group-hover:text-slate-600' }}"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>Customer & Project</span>
                    </div>
                </a>

                <!-- Account Management -->
                @php $isAccountsActive = request()->routeIs('accounts.*'); @endphp
                <a href="{{ route('accounts.index') }}"
                    class="flex items-center justify-between px-3.5 py-2 text-xs rounded-lg transition-all duration-150 group border-l-4 {{ $isAccountsActive ? 'bg-blue-50/90 text-netfly-blue font-semibold border-netfly-blue shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0 {{ $isAccountsActive ? 'text-netfly-blue' : 'text-slate-400 group-hover:text-slate-600' }}"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Account Management</span>
                    </div>
                </a>

                <!-- Teknisi -->
                @php $isTeknisiActive = request()->routeIs('teknisi.*'); @endphp
                <a href="{{ route('teknisi.index') }}"
                    class="flex items-center justify-between px-3.5 py-2 text-xs rounded-lg transition-all duration-150 group border-l-4 {{ $isTeknisiActive ? 'bg-blue-50/90 text-netfly-blue font-semibold border-netfly-blue shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0 {{ $isTeknisiActive ? 'text-netfly-blue' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
                        </svg>
                        <span>Teknisi</span>
                    </div>
                </a>
            </nav>
        </div>

        <!-- Group 3: LAPORAN & AUDIT -->
        <div>
            <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                LAPORAN & AUDIT
            </p>
            <nav class="space-y-1">
                @php $isReportsActive = request()->routeIs('reports.*'); @endphp
                <a href="{{ route('reports.index') }}"
                    class="flex items-center justify-between px-3.5 py-2 text-xs rounded-lg transition-all duration-150 group border-l-4 {{ $isReportsActive ? 'bg-blue-50/90 text-netfly-blue font-semibold border-netfly-blue shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0 {{ $isReportsActive ? 'text-netfly-blue' : 'text-slate-400 group-hover:text-slate-600' }}"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Report Generator</span>
                    </div>
                </a>
            </nav>
        </div>

    </div>

    <!-- 3. Profile Admin & Logout (Pinned Bottom - ALWAYS visible, shrink-0 mt-auto) -->
    <div class="p-3 border-t border-slate-100 bg-white shrink-0 mt-auto">
        <div class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 transition-colors">
            <div class="flex items-center gap-2.5 min-w-0">
                <div
                    class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs font-bold shrink-0">
                    {{ strtoupper(substr(Auth::user()->name ?? 'AD', 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-slate-800 truncate leading-tight">
                        {{ Auth::user()->name ?? 'Administrator' }}
                    </p>
                    <p class="text-[10px] text-slate-400 truncate">
                        {{ Auth::user()->role ? ucfirst(Auth::user()->role) : 'NOC Specialist' }}
                    </p>
                </div>
            </div>

            <!-- Tombol Logout -->
            <form action="{{ route('logout') }}" method="POST" class="inline shrink-0">
                @csrf
                <button type="submit" title="Keluar dari Portal"
                    class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-colors cursor-pointer"
                    aria-label="Logout">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

</aside>

<!-- Mobile Sidebar Backdrop -->
<div id="sidebarBackdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-30 hidden lg:hidden"></div>