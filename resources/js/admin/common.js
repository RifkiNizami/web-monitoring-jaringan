/**
 * NetFly Admin Console - Common UI Utilities
 * (Realtime Clock WIB, Collapsible Sidebar & Smooth Navbar Menu, Staggered Content Animations)
 */
export function initAdminCommon() {
    // 1. Live Digital Clock (WIB)
    const clockEl = document.getElementById('liveClock');
    if (clockEl) {
        if (window._clockInterval) {
            clearInterval(window._clockInterval);
        }
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            clockEl.textContent = `${hours}.${minutes}.${seconds} WIB`;
        }
        window._clockInterval = setInterval(updateClock, 1000);
        updateClock();
    }

    // 2. Collapsible Sidebar & Smooth Navbar Menu
    const sidebar = document.getElementById('sidebar');
    const toggleButtons = document.querySelectorAll('#openSidebar, .sidebar-toggle-btn');
    const closeSidebarBtn = document.getElementById('closeSidebar');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    const STORAGE_KEY = 'netfly_sidebar_collapsed';

    function isDesktop() {
        return window.innerWidth >= 1024;
    }

    function applyDesktopCollapsedState(collapsed) {
        if (!sidebar) return;
        if (isDesktop()) {
            sidebar.classList.toggle('is-collapsed', collapsed);
            toggleButtons.forEach(btn => {
                btn.classList.toggle('is-collapsed', collapsed);
                btn.setAttribute('aria-expanded', String(!collapsed));
                btn.setAttribute('title', collapsed ? 'Perluas Menu Navigasi (Ctrl+B)' : 'Ciutkan Menu Navigasi (Ctrl+B)');
            });
        } else {
            sidebar.classList.remove('is-collapsed');
        }
    }

    function toggleDesktopSidebar() {
        if (!sidebar) return;
        const willCollapse = !sidebar.classList.contains('is-collapsed');
        applyDesktopCollapsedState(willCollapse);
        try {
            localStorage.setItem(STORAGE_KEY, String(willCollapse));
        } catch (e) {}
    }

    function toggleMobileSidebar(open) {
        if (!sidebar) return;
        if (open) {
            sidebar.classList.remove('-translate-x-full');
            if (sidebarBackdrop) sidebarBackdrop.classList.remove('hidden');
            document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
        } else {
            sidebar.classList.add('-translate-x-full');
            if (sidebarBackdrop) sidebarBackdrop.classList.add('hidden');
            document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
        }
    }

    function handleSidebarToggle(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        if (isDesktop()) {
            toggleDesktopSidebar();
        } else {
            const isOpen = !sidebar?.classList.contains('-translate-x-full');
            toggleMobileSidebar(!isOpen);
        }
    }

    // Initialize state on load
    const savedCollapsed = localStorage.getItem(STORAGE_KEY) === 'true';
    applyDesktopCollapsedState(savedCollapsed);
    toggleMobileSidebar(false);

    // Attach click listeners to all toggle buttons
    toggleButtons.forEach(btn => {
        btn.onclick = handleSidebarToggle;
    });

    if (closeSidebarBtn) {
        closeSidebarBtn.onclick = (e) => {
            e.stopPropagation();
            toggleMobileSidebar(false);
        };
    }

    if (sidebarBackdrop) {
        sidebarBackdrop.onclick = () => toggleMobileSidebar(false);
    }

    // Responsive resize listener
    if (!window._sidebarResizeAttached) {
        window.addEventListener('resize', () => {
            if (isDesktop()) {
                toggleMobileSidebar(false);
                const isSavedCollapsed = localStorage.getItem(STORAGE_KEY) === 'true';
                applyDesktopCollapsedState(isSavedCollapsed);
            } else {
                sidebar?.classList.remove('is-collapsed');
            }
        });
        window._sidebarResizeAttached = true;
    }

    // Keyboard Shortcuts (Ctrl+B / Cmd+B for toggle, Escape for mobile close)
    if (!window._sidebarKeydownAttached) {
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
                e.preventDefault();
                handleSidebarToggle();
                return;
            }
            if (e.key === 'Escape' && sidebar && !sidebar.classList.contains('-translate-x-full')) {
                toggleMobileSidebar(false);
            }
        });
        window._sidebarKeydownAttached = true;
    }

    // 3. Staggered Entrance Animation for Admin Page Content
    const adminMain = document.querySelector('main');
    if (adminMain && sidebar) {
        const blocks = Array.from(adminMain.children);
        blocks.forEach((block, index) => {
            block.classList.add('admin-reveal');
            block.style.animationDelay = `${index * 80}ms`;
        });
    }

    // 4. Dynamic Progress Bars (data-progress-width)
    document.querySelectorAll('[data-progress-width]').forEach((bar) => {
        const widthVal = bar.getAttribute('data-progress-width') || '0';
        bar.style.width = `${widthVal}%`;
    });
}
