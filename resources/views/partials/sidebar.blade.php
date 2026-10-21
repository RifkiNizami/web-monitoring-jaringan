<!-- Sidebar Partial -->
<aside id="sidebar" class="w-64 bg-white border-r border-slate-200/80 flex flex-col justify-between shrink-0 transition-transform duration-300 lg:translate-x-0 -translate-x-full fixed lg:static inset-y-0 left-0 z-50">
    
    <!-- Bagian Atas: Logo & Navigasi -->
    <div class="flex flex-col h-full overflow-y-auto">
        <!-- Logo Header -->
        <div class="px-6 py-5 flex items-center justify-between border-b border-slate-100">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <img src="{{ asset('assets/images/netfly-logo.png') }}" alt="NetFly" class="h-9 w-auto object-contain">
            </a>
            <!-- Mobile close button -->
            <button id="closeSidebar" class="lg:hidden text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Menu Navigation -->
        <div class="px-4 py-5 space-y-6 flex-1">
            
            <!-- Group: OVERVIEW -->
            <div>
                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                    OVERVIEW
                </p>
                <nav class="space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-xs {{ (request()->routeIs('dashboard') || request()->routeIs('admin')) && !request()->routeIs('tickets.*') && !request()->routeIs('devices.*') ? 'font-semibold text-slate-900 bg-slate-100/90' : 'font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} rounded-lg transition-colors group">
                        <svg class="w-4 h-4 {{ (request()->routeIs('dashboard') || request()->routeIs('admin')) && !request()->routeIs('tickets.*') && !request()->routeIs('devices.*') ? 'text-slate-700' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- Customer & Project -->
                    <a href="#customer-project" class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition-colors group">
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>Customer & Project</span>
                    </a>

                    <!-- Account Management -->
                    <a href="#account-management" class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition-colors group">
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Account Management</span>
                    </a>

                    <!-- Network Devices -->
                    <a href="{{ route('devices.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs {{ request()->routeIs('devices.*') ? 'font-semibold text-slate-900 bg-slate-100/90' : 'font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} rounded-lg transition-colors group">
                        <svg class="w-4 h-4 {{ request()->routeIs('devices.*') ? 'text-slate-700' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                        </svg>
                        <span>Network Devices</span>
                    </a>

                    <!-- Monitoring & Alert -->
                    <a href="#monitoring-alert" class="flex items-center justify-between px-3 py-2 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition-colors group">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>Monitoring & Alert</span>
                        </div>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-red-100 text-red-600 border border-red-200">
                            <span class="w-1.5 h-1.5 mr-1 rounded-full bg-red-500 animate-pulse"></span>
                            LIVE
                        </span>
                    </a>

                    <!-- Ticket & Insiden -->
                    <a href="{{ route('tickets.index') }}" class="flex items-center justify-between px-3 py-2 text-xs {{ request()->routeIs('tickets.*') ? 'font-semibold text-slate-900 bg-slate-100/90' : 'font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} rounded-lg transition-colors group">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 {{ request()->routeIs('tickets.*') ? 'text-slate-700' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                            <span>Ticket & Insiden</span>
                        </div>
                        <span class="inline-flex items-center justify-center w-4 h-4 text-[10px] font-semibold rounded-full {{ request()->routeIs('tickets.*') ? 'bg-slate-300 text-slate-800' : 'bg-slate-200 text-slate-700' }}">
                            3
                        </span>
                    </a>
                </nav>
            </div>

            <!-- Group: LAPORAN & AUDIT -->
            <div>
                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                    LAPORAN & AUDIT
                </p>
                <nav class="space-y-1">
                    <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3 py-2 text-xs {{ request()->routeIs('reports.*') ? 'font-semibold text-slate-900 bg-slate-100/90' : 'font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} rounded-lg transition-colors group">
                        <svg class="w-4 h-4 {{ request()->routeIs('reports.*') ? 'text-slate-700' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Report Generator</span>
                    </a>
                </nav>
            </div>

        </div>

        <!-- Bagian Bawah: Profile Admin & Logout -->
        <div class="p-3 border-t border-slate-100 bg-white">
            <div class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs font-bold shrink-0">
                        AD
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-800 truncate leading-tight">
                            Administrator
                        </p>
                        <p class="text-[10px] text-slate-400 truncate">
                            NOC Specialist
                        </p>
                    </div>
                </div>

                <!-- Tombol Logout -->
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" title="Keluar" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

    </div>
</aside>

<!-- Mobile Sidebar Backdrop -->
<div id="sidebarBackdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-40 hidden lg:hidden"></div>
