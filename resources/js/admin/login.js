/**
 * NetFly Admin - Login Page Interactivity (Optimized 60fps, Zero Layout Thrashing)
 */
export function initAdminLogin() {
    const loginPage = document.getElementById('login-page');
    if (!loginPage) return;

    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    const eyeOffIcon = document.getElementById('eyeOffIcon');
    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.getElementById('loginSubmitBtn');
    const btnText = document.getElementById('loginBtnText');
    const btnSpinner = document.getElementById('loginBtnSpinner');
    const heroPane = document.getElementById('login-hero-pane');
    const orbWrapper = document.getElementById('login-orb-wrapper');
    const ringsWrapper = document.getElementById('login-rings-wrapper');

    // 1. Toggle Password Visibility
    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            if (eyeIcon) eyeIcon.classList.toggle('hidden', isPassword);
            if (eyeOffIcon) eyeOffIcon.classList.toggle('hidden', !isPassword);
        });
    }

    // 2. Form Submit Loading State
    if (loginForm && submitBtn) {
        loginForm.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-85', 'cursor-not-allowed');
            if (btnText) btnText.textContent = 'MEMPROSES...';
            if (btnSpinner) btnSpinner.classList.remove('hidden');
        });
    }

    // 3. Throttled rAF Parallax on Right Visual Panel (Cached Bounds to Avoid Reflow)
    if (heroPane && (orbWrapper || ringsWrapper)) {
        let cachedRect = null;
        let rafId = null;
        let targetX = 0;
        let targetY = 0;

        const updateCachedRect = () => {
            cachedRect = heroPane.getBoundingClientRect();
        };

        const renderParallax = () => {
            if (orbWrapper) {
                orbWrapper.style.transform = `translate3d(${targetX * -18}px, ${targetY * -18}px, 0)`;
            }
            if (ringsWrapper) {
                ringsWrapper.style.transform = `translate3d(${targetX * 12}px, ${targetY * 12}px, 0)`;
            }
            rafId = null;
        };

        heroPane.addEventListener('mouseenter', updateCachedRect, { passive: true });
        window.addEventListener('resize', () => { cachedRect = null; }, { passive: true });

        heroPane.addEventListener('mousemove', (e) => {
            if (!cachedRect) updateCachedRect();
            if (!cachedRect || cachedRect.width === 0) return;

            targetX = (e.clientX - cachedRect.left) / cachedRect.width - 0.5;
            targetY = (e.clientY - cachedRect.top) / cachedRect.height - 0.5;

            if (!rafId) {
                rafId = window.requestAnimationFrame(renderParallax);
            }
        }, { passive: true });

        heroPane.addEventListener('mouseleave', () => {
            targetX = 0;
            targetY = 0;
            if (!rafId) {
                rafId = window.requestAnimationFrame(renderParallax);
            }
        }, { passive: true });
    }
}
