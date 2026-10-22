/**
 * NetFly Landing Page - Clients Carousel Component
 */
export function initClientsCarousel() {
    const carousel = document.getElementById('clients-carousel');
    const btnPrev = document.getElementById('slider-btn-prev');
    const btnNext = document.getElementById('slider-btn-next');
    const activeSlideNum = document.getElementById('active-slide-num');
    const dotsContainer = document.getElementById('slider-dots-container');
    const dots = dotsContainer ? dotsContainer.querySelectorAll('.slider-dot') : [];
    const slides = carousel ? carousel.querySelectorAll('.client-slide') : [];

    if (!carousel || slides.length === 0) return;

    let currentIndex = 0;
    let autoplayTimer = null;
    let isDragging = false;
    let startX = 0;
    let scrollStart = 0;
    let dragDistance = 0;

    function goToSlide(index, smooth = true) {
        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;
        
        currentIndex = index;
        const targetSlide = slides[index];
        const scrollPosition = targetSlide.offsetLeft - carousel.offsetLeft;
        
        carousel.scrollTo({
            left: scrollPosition,
            behavior: smooth ? 'smooth' : 'auto'
        });
        
        updateIndicators(currentIndex);
    }

    function updateIndicators(activeIndex) {
        if (activeSlideNum) {
            activeSlideNum.textContent = String(activeIndex + 1);
        }
        
        dots.forEach((dot, idx) => {
            if (idx === activeIndex) {
                dot.className = 'slider-dot w-6 h-2.5 rounded-full bg-netfly-blue transition-all duration-300';
            } else {
                dot.className = 'slider-dot w-2 h-2 rounded-full bg-slate-300 hover:bg-slate-400 transition-all duration-300';
            }
        });
    }

    if (btnNext) {
        btnNext.addEventListener('click', function () {
            stopAutoplay();
            const maxScroll = carousel.scrollWidth - carousel.clientWidth - 15;
            if (carousel.scrollLeft >= maxScroll || currentIndex >= slides.length - 1) {
                goToSlide(0);
            } else {
                goToSlide(currentIndex + 1);
            }
            startAutoplay();
        });
    }

    if (btnPrev) {
        btnPrev.addEventListener('click', function () {
            stopAutoplay();
            if (carousel.scrollLeft <= 15 || currentIndex <= 0) {
                goToSlide(slides.length - 1);
            } else {
                goToSlide(currentIndex - 1);
            }
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

    let scrollTimeout;
    carousel.addEventListener('scroll', function () {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(function () {
            const currentScroll = carousel.scrollLeft;
            let closestIdx = 0;
            let minDiff = Infinity;

            slides.forEach((slide, idx) => {
                const slidePos = slide.offsetLeft - carousel.offsetLeft;
                const diff = Math.abs(slidePos - currentScroll);
                if (diff < minDiff) {
                    minDiff = diff;
                    closestIdx = idx;
                }
            });

            currentIndex = closestIdx;
            updateIndicators(currentIndex);
        }, 60);
    }, { passive: true });

    carousel.addEventListener('mousedown', function (e) {
        isDragging = true;
        dragDistance = 0;
        startX = e.pageX - carousel.offsetLeft;
        scrollStart = carousel.scrollLeft;
        carousel.classList.add('cursor-grabbing');
        carousel.classList.remove('cursor-grab');
        stopAutoplay();
    });

    window.addEventListener('mousemove', function (e) {
        if (!isDragging) return;
        const x = e.pageX - carousel.offsetLeft;
        const walk = (x - startX) * 1.3;
        dragDistance = Math.abs(walk);
        carousel.scrollLeft = scrollStart - walk;
    });

    window.addEventListener('mouseup', function () {
        if (!isDragging) return;
        isDragging = false;
        carousel.classList.remove('cursor-grabbing');
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
            const maxScroll = carousel.scrollWidth - carousel.clientWidth - 15;
            if (carousel.scrollLeft >= maxScroll || currentIndex >= slides.length - 1) {
                goToSlide(0);
            } else {
                goToSlide(currentIndex + 1);
            }
        }, 4500);
    }

    function stopAutoplay() {
        if (autoplayTimer) {
            clearInterval(autoplayTimer);
            autoplayTimer = null;
        }
    }

    carousel.addEventListener('mouseenter', stopAutoplay);
    carousel.addEventListener('mouseleave', startAutoplay);
    carousel.addEventListener('touchstart', stopAutoplay, { passive: true });
    carousel.addEventListener('touchend', startAutoplay, { passive: true });

    startAutoplay();
}
