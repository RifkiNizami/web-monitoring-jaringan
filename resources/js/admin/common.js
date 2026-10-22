/**
 * NetFly Admin Console - Common UI Utilities
 * (Realtime Clock WIB & Mobile Sidebar Drawer)
 */
export function initAdminCommon() {
    // 1. Live Digital Clock (WIB)
    const clockEl = document.getElementById('liveClock');
    if (clockEl) {
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            clockEl.textContent = `${hours}.${minutes}.${seconds} WIB`;
        }
        setInterval(updateClock, 1000);
        updateClock();
    }

    // 2. Mobile Sidebar Controls
    const sidebar = document.getElementById('sidebar');
    const openSidebarBtn = document.getElementById('openSidebar');
    const closeSidebarBtn = document.getElementById('closeSidebar');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');

    function toggleMobileSidebar(open) {
        if (!sidebar) return;
        if (open) {
            sidebar.classList.remove('-translate-x-full');
            if (sidebarBackdrop) sidebarBackdrop.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            if (sidebarBackdrop) sidebarBackdrop.classList.add('hidden');
        }
    }

    if (openSidebarBtn) {
        openSidebarBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMobileSidebar(true);
        });
    }

    if (closeSidebarBtn) {
        closeSidebarBtn.addEventListener('click', () => toggleMobileSidebar(false));
    }

    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', () => toggleMobileSidebar(false));
    }

    // Close on Escape key press
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar && !sidebar.classList.contains('-translate-x-full')) {
            toggleMobileSidebar(false);
        }
    });
}
