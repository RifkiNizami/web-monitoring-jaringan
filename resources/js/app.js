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
       1. Mobile Navbar Toggle (Landing Page)
    ========================================== */
    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const iconOpen = document.getElementById('icon-open');
    const iconClose = document.getElementById('icon-close');
    const mobileLinks = mobileMenu ? mobileMenu.querySelectorAll('a') : [];

    if (menuBtn && mobileMenu) {
        const toggleMenu = () => {
            const isHidden = mobileMenu.classList.contains('hidden');
            mobileMenu.classList.toggle('hidden');
            if (iconOpen) iconOpen.classList.toggle('hidden');
            if (iconClose) iconClose.classList.toggle('hidden');
            menuBtn.setAttribute('aria-expanded', String(isHidden));
        };

        menuBtn.addEventListener('click', toggleMenu);

        // Close menu when a link is clicked
        mobileLinks.forEach(link => {
            link.addEventListener('click', toggleMenu);
        });

        // Accessibility: Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
                toggleMenu();
            }
        });
    }

    /* ==========================================
       2. Sticky Navbar Effect (Landing Page)
    ========================================== */
    const navbar = document.getElementById('navbar');
    if (navbar) {
        const handleScroll = () => {
            if (window.scrollY > 50) {
                navbar.classList.add('bg-slate-950/95', 'shadow-lg', 'shadow-blue-900/10', 'backdrop-blur-lg');
                navbar.classList.remove('bg-slate-950/80', 'backdrop-blur-md');
            } else {
                navbar.classList.add('bg-slate-950/80', 'backdrop-blur-md');
                navbar.classList.remove('bg-slate-950/95', 'shadow-lg', 'shadow-blue-900/10', 'backdrop-blur-lg');
            }
        };
        window.addEventListener('scroll', handleScroll, { passive: true });
    }

    /* ==========================================
       3. Scroll Reveal Animation (Intersection Observer)
    ========================================== */
    const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if ('IntersectionObserver' in window && !prefersReducedMotion) {
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            root: null,
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
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
    initAdminLogin();
    initAdminReports();
    initAdminTickets();
    initAdminDevices();
    initAdminCustomers();
    initAdminAccounts();
    initAdminMonitoring();
});
