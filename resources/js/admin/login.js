/**
 * NetFly Admin - Login Page Entrance Animation & Interactivity
 */
export function initAdminLogin() {
    const loginPage = document.getElementById('login-page');
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

    // 1. Trigger Staggered Entrance Animation on Page Load
    if (loginPage) {
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (prefersReducedMotion) {
            loginPage.classList.add('is-loaded');
        } else {
            window.requestAnimationFrame(() => {
                window.setTimeout(() => {
                    loginPage.classList.add('is-loaded');
                }, 40);
            });
        }
    }

    // 2. Toggle Password Visibility
    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            if (eyeIcon) eyeIcon.classList.toggle('hidden', isPassword);
            if (eyeOffIcon) eyeOffIcon.classList.toggle('hidden', !isPassword);
        });
    }

    // 3. Form Submit Loading State
    if (loginForm && submitBtn) {
        loginForm.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-85', 'cursor-not-allowed');
            if (btnText) btnText.textContent = 'MEMPROSES...';
            if (btnSpinner) btnSpinner.classList.remove('hidden');
        });
    }

    // 4. Interactive Subtle Parallax on Right Visual Panel
    if (heroPane && (orbWrapper || ringsWrapper)) {
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (!prefersReducedMotion) {
            heroPane.addEventListener('mousemove', (e) => {
                const rect = heroPane.getBoundingClientRect();
                const relX = (e.clientX - rect.left) / rect.width - 0.5;
                const relY = (e.clientY - rect.top) / rect.height - 0.5;

                if (orbWrapper) {
                    orbWrapper.style.transform = `translate3d(${relX * -24}px, ${relY * -24}px, 0)`;
                }
                if (ringsWrapper) {
                    ringsWrapper.style.transform = `translate3d(${relX * 16}px, ${relY * 16}px, 0)`;
                }
            });

            heroPane.addEventListener('mouseleave', () => {
                if (orbWrapper) {
                    orbWrapper.style.transform = 'translate3d(0, 0, 0)';
                }
                if (ringsWrapper) {
                    ringsWrapper.style.transform = 'translate3d(0, 0, 0)';
                }
            });
        }
    }
}
