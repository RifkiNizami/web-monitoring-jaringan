import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    
    /* ==========================================
       1. Mobile Navbar Toggle
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
            iconOpen.classList.toggle('hidden');
            iconClose.classList.toggle('hidden');
            menuBtn.setAttribute('aria-expanded', String(isHidden));
        };

        menuBtn.addEventListener('click', toggleMenu);
        
        // Tutup menu saat salah satu link diklik
        mobileLinks.forEach(link => {
            link.addEventListener('click', toggleMenu);
        });

        // Aksesibilitas: Tutup menu saat menekan tombol Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
                toggleMenu();
            }
        });
    }

    /* ==========================================
       2. Sticky Navbar Effect
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
        // Optimasi scroll listener dengan passive true
        window.addEventListener('scroll', handleScroll, { passive: true });
    }

    /* ==========================================
       3. Scroll Reveal Animation (Intersection Observer)
    ========================================== */
    const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Jika browser mendukung observer dan user tidak mematikan animasi OS
    if ('IntersectionObserver' in window && !prefersReducedMotion) {
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target); // Hanya animasi satu kali (tidak berulang saat scroll atas/bawah)
                }
            });
        }, {
            root: null,
            threshold: 0.15, // Muncul ketika 15% elemen terlihat
            rootMargin: "0px 0px -50px 0px"
        });

        revealElements.forEach(el => revealObserver.observe(el));
    } else {
        // Fallback jika browser lawas atau user meminta reduced motion
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
                const headerOffset = 80; // Sesuaikan dengan tinggi navbar (h-20 Tailwind = 80px)
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: "smooth"
                });
            }
        });
    });
});