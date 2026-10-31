import './bootstrap';

import { initClientsCarousel } from './landing/clients';
import { initAdminCommon } from './admin/common';
import { initAdminLogin } from './admin/login';
import { initAdminReports } from './admin/reports';
import { initAdminTickets } from './admin/tickets';
import { initAdminDevices } from './admin/devices';
import { initAdminCustomers } from './admin/customers';
import { initAdminAccounts } from './admin/accounts';
import { initAdminMonitoring } from './admin/monitoring';

document.addEventListener('DOMContentLoaded', () => {
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
            mobileDrawer.classList.toggle('hidden');
            drawerBtn.setAttribute('aria-expanded', String(isHidden));
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
});
