/**
 * NetFly Landing Page - Clients Carousel Component
 * (Accurate 1/8 - 8/8 Indicator, Active Card Highlight & Smooth Drag Scrolling)
 */
export function initClientsCarousel() {
    const carousel = document.getElementById('clients-carousel');
    const btnPrev = document.getElementById('slider-btn-prev');
    const btnNext = document.getElementById('slider-btn-next');
    const activeSlideNum = document.getElementById('active-slide-num');
    const totalSlideNum = document.getElementById('total-slide-num');
    const dotsContainer = document.getElementById('slider-dots-container');
    const dots = dotsContainer ? dotsContainer.querySelectorAll('.slider-dot') : [];
    const slides = carousel ? carousel.querySelectorAll('.client-slide') : [];

    if (!carousel || slides.length === 0) return;

    const totalSlides = slides.length;
    if (totalSlideNum) {
        totalSlideNum.textContent = String(totalSlides);
    }

    let currentIndex = 0;
    let autoplayTimer = null;
    let isDragging = false;
    let isProgrammaticScroll = false;
    let programmaticTimeout = null;
    let startX = 0;
    let scrollStart = 0;
    let dragDistance = 0;

    function getMaxScroll() {
        return Math.max(0, carousel.scrollWidth - carousel.clientWidth);
    }

    function goToSlide(index, smooth = true) {
        if (index < 0) index = totalSlides - 1;
        if (index >= totalSlides) index = 0;

        currentIndex = index;
        updateIndicators(currentIndex);

        const targetSlide = slides[currentIndex];
        const maxScroll = getMaxScroll();

        // Pusatkan kartu aktif di dalam area pandang carousel (dibatasi 0 s/d maxScroll)
        const slideLeft = targetSlide.offsetLeft - carousel.offsetLeft;
        const centeredPos = slideLeft - (carousel.clientWidth - targetSlide.offsetWidth) / 2;
        const targetScroll = Math.max(0, Math.min(maxScroll, centeredPos));

        isProgrammaticScroll = true;
        clearTimeout(programmaticTimeout);

        carousel.scrollTo({
            left: targetScroll,
            behavior: smooth ? 'smooth' : 'auto'
        });

        programmaticTimeout = setTimeout(() => {
            isProgrammaticScroll = false;
        }, 550);
    }

    function updateIndicators(activeIndex) {
        if (activeSlideNum) {
            activeSlideNum.textContent = String(activeIndex + 1);
        }

        slides.forEach((slide, idx) => {
            slide.classList.toggle('is-active-slide', idx === activeIndex);
        });

        dots.forEach((dot, idx) => {
            if (idx === activeIndex) {
                dot.className = 'slider-dot w-6 h-2.5 rounded-full bg-netfly-blue transition-all duration-300 cursor-pointer';
            } else {
                dot.className = 'slider-dot w-2 h-2 rounded-full bg-slate-300 hover:bg-slate-400 transition-all duration-300 cursor-pointer';
            }
        });
    }

    if (btnNext) {
        btnNext.addEventListener('click', function () {
            stopAutoplay();
            goToSlide(currentIndex + 1);
            startAutoplay();
        });
    }

    if (btnPrev) {
        btnPrev.addEventListener('click', function () {
            stopAutoplay();
            goToSlide(currentIndex - 1);
            startAutoplay();
        });
    }

    dots.forEach((dot) => {
        dot.addEventListener('click', function () {
            stopAutoplay();
            const idx = parseInt(this.getAttribute('data-index') || '0', 10);
            goToSlide(idx);
            startAutoplay();
        });
    });

    slides.forEach((slide, idx) => {
        slide.addEventListener('click', function () {
            if (dragDistance > 6) return;
            stopAutoplay();
            goToSlide(idx);
            startAutoplay();
        });
    });

    // Update indikator secara proporsional (1/8 s/d 8/8) saat user melakukan scroll/drag manual
    let scrollTimeout;
    carousel.addEventListener('scroll', function () {
        if (isProgrammaticScroll) return;

        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(function () {
            if (isProgrammaticScroll) return;
            const maxScroll = getMaxScroll();
            if (maxScroll <= 0) return;

            const ratio = Math.max(0, Math.min(1, carousel.scrollLeft / maxScroll));
            const mappedIdx = Math.round(ratio * (totalSlides - 1));

            if (mappedIdx !== currentIndex) {
                currentIndex = mappedIdx;
                updateIndicators(currentIndex);
            }
        }, 40);
    }, { passive: true });

    // Smooth Mouse Dragging (tanpa terganggu CSS scroll-snap)
    carousel.addEventListener('mousedown', function (e) {
        isDragging = true;
        isProgrammaticScroll = false;
        dragDistance = 0;
        startX = e.pageX - carousel.offsetLeft;
        scrollStart = carousel.scrollLeft;
        carousel.classList.add('is-dragging', 'cursor-grabbing');
        carousel.classList.remove('cursor-grab');
        stopAutoplay();
    });

    window.addEventListener('mousemove', function (e) {
        if (!isDragging) return;
        e.preventDefault();
        const x = e.pageX - carousel.offsetLeft;
        const walk = (x - startX) * 1.25;
        dragDistance = Math.abs(walk);
        carousel.scrollLeft = scrollStart - walk;
    });

    window.addEventListener('mouseup', function () {
        if (!isDragging) return;
        isDragging = false;
        carousel.classList.remove('is-dragging', 'cursor-grabbing');
        carousel.classList.add('cursor-grab');
        startAutoplay();
    });

    carousel.addEventListener('click', function (e) {
        if (dragDistance > 6) {
            e.preventDefault();
            e.stopPropagation();
        }
    }, true);

    function startAutoplay() {
        stopAutoplay();
        autoplayTimer = setInterval(function () {
            goToSlide(currentIndex + 1);
        }, 4000);
    }

    function stopAutoplay() {
        if (autoplayTimer) {
            clearInterval(autoplayTimer);
            autoplayTimer = null;
        }
    }

    carousel.addEventListener('mouseenter', stopAutoplay);
    carousel.addEventListener('mouseleave', startAutoplay);
    carousel.addEventListener('touchstart', () => {
        isProgrammaticScroll = false;
        stopAutoplay();
    }, { passive: true });
    carousel.addEventListener('touchend', startAutoplay, { passive: true });

    // Set initial active state (1 / 8)
    updateIndicators(0);
    startAutoplay();
}
