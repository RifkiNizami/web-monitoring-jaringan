import { initAdminLogin } from './admin/login';

// Aktifkan stylesheet Google Fonts non-blocking segera setelah script dieksekusi
const fontLink = document.getElementById('googleFontCss');
if (fontLink) {
    fontLink.media = 'all';
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAdminLogin);
} else {
    initAdminLogin();
}
