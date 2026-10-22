/**
 * NetFly Admin - Login Page Interactivity
 */
export function initAdminLogin() {
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    const eyeOffIcon = document.getElementById('eyeOffIcon');

    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            if (eyeIcon) eyeIcon.classList.toggle('hidden', isPassword);
            if (eyeOffIcon) eyeOffIcon.classList.toggle('hidden', !isPassword);
        });
    }
}
