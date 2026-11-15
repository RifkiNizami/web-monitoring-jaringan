import './bootstrap';
import * as Turbo from '@hotwired/turbo';

// Daftarkan Turbo secara global
if (typeof window !== 'undefined') {
    window.Turbo = Turbo;
}

import { initClientsCarousel } from './landing/clients';
import { initAdminCommon } from './admin/common';
import { initAdminLogin } from './admin/login';
import { initAdminReports } from './admin/reports';
import { initAdminTickets } from './admin/tickets';
import { initAdminDevices } from './admin/devices';
import { initAdminCustomers } from './admin/customers';
import { initAdminAccounts } from './admin/accounts';
import { initAdminMonitoring } from './admin/monitoring';
import { initAdminMonitoringDetail } from './admin/monitoring-detail';
import Chart from 'chart.js/auto';

if (typeof window !== 'undefined') {
    window.Chart = Chart;
}

function initAppModules() {
    /* ==========================================
       0. Fast-Path untuk Halaman Login
       (Tidak mengeksekusi observer/script halaman lain)
    ========================================== */
    if (document.getElementById('login-page')) {
        initAdminLogin();
        return;
    }

    /* ==========================================
       1. Mobile Navbar Toggle (Landing Page)
    ========================================== */
    const drawerBtn = document.getElementById('mobile-drawer-btn') || document.getElementById('menu-btn');
    const mobileDrawer = document.getElementById('mobile-drawer') || document.getElementById('mobile-menu');
    const mobileLinks = mobileDrawer ? mobileDrawer.querySelectorAll('a') : [];

    if (drawerBtn && mobileDrawer) {
        const toggleDrawer = () => {
            const isHidden = mobileDrawer.classList.contains('hidden');
            if (isHidden) {
                mobileDrawer.classList.remove('hidden');
                mobileDrawer.style.animation = 'revealFadeUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards';
            } else {
                mobileDrawer.classList.add('hidden');
                mobileDrawer.style.animation = '';
            }
            drawerBtn.setAttribute('aria-expanded', String(isHidden));
            const iconSvg = drawerBtn.querySelector('svg');
            if (iconSvg) {
                iconSvg.style.transform = isHidden ? 'rotate(90deg)' : 'none';
                iconSvg.style.transition = 'transform 0.25s ease';
            }
        };

        drawerBtn.addEventListener('click', toggleDrawer);

        // Close menu when a link is clicked
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (!mobileDrawer.classList.contains('hidden')) {
                    toggleDrawer();
                }
            });
        });

        // Accessibility: Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !mobileDrawer.classList.contains('hidden')) {
                toggleDrawer();
            }
        });
    }

    /* ==========================================
       2. Shrinking Sticky Header Effect (Landing Page)
    ========================================== */
    const headerWrapper = document.getElementById('header-wrapper');
    if (headerWrapper) {
        let isShrunk = false;
        let ticking = false;

        const updateHeaderState = () => {
            const scrollY = window.scrollY;

            // Gunakan hysteresis (ambang 50px untuk shrink, 20px untuk kembali normal)
            // agar bebas dari efek flickering/jitter di batas scroll
            if (!isShrunk && scrollY > 50) {
                isShrunk = true;
                headerWrapper.classList.add('is-shrunk');
            } else if (isShrunk && scrollY < 20) {
                isShrunk = false;
                headerWrapper.classList.remove('is-shrunk');
            }

            ticking = false;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(updateHeaderState);
                ticking = true;
            }
        }, { passive: true });

        // Evaluasi posisi awal saat halaman pertama kali dimuat/di-refresh
        updateHeaderState();
    }

    /* ==========================================
       3. Scroll Reveal Animation (Intersection Observer)
    ========================================== */
    const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');

    if ('IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            root: null,
            threshold: 0.08,
            rootMargin: '0px 0px -30px 0px'
        });

        revealElements.forEach(el => revealObserver.observe(el));
    } else {
        revealElements.forEach(el => el.classList.add('is-visible'));
    }

    /* ==========================================
       4. Smooth Scroll with Offset (Anchor Links)
    ========================================== */
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                e.preventDefault();
                const headerOffset = 80;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    /* ==========================================
       5. Module Initializations
    ========================================== */
    initClientsCarousel();
    initAdminCommon();
    initAdminReports();
    initAdminTickets();
    initAdminDevices();
    initAdminCustomers();
    initAdminAccounts();
    initAdminMonitoring();
    initAdminMonitoringDetail();
}

// Pre-apply sidebar collapsed class on Turbo visit to prevent layout flicker
document.addEventListener('turbo:before-render', (event) => {
    try {
        const isCollapsed = localStorage.getItem('netfly_sidebar_collapsed') === 'true';
        if (isCollapsed && window.innerWidth >= 1024) {
            const newSidebar = event.detail.newBody.querySelector('#sidebar');
            if (newSidebar) {
                newSidebar.classList.add('is-collapsed');
            }
            const toggleBtns = event.detail.newBody.querySelectorAll('#openSidebar, .sidebar-toggle-btn');
            toggleBtns.forEach(btn => btn.classList.add('is-collapsed'));
        }
    } catch (e) {}
});

// Jalankan modul pada load awal dan setiap perpindahan halaman via Turbo Drive
document.addEventListener('turbo:load', initAppModules);

// Fallback jika Turbo dinonaktifkan
document.addEventListener('DOMContentLoaded', () => {
    if (!window.Turbo) {
        initAppModules();
    }
});
