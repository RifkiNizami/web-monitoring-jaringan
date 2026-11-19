import QRCode from 'qrcode';

/**
 * Inisialisasi Modal Unduh Aplikasi Mobile dengan Barcode / QR Code
 */
export function initDownloadModal() {
    const modal = document.getElementById('downloadAppModal');
    if (!modal) return;

    const modalBackdrop = document.getElementById('downloadAppBackdrop');
    const modalDialog = document.getElementById('downloadAppDialog');
    const closeBtns = document.querySelectorAll('[data-close-download-modal]');
    const triggerBtns = document.querySelectorAll('[data-open-download-modal]');
    const qrCanvas = document.getElementById('downloadAppQrCanvas');
    const copyLinkBtn = document.getElementById('copyAppLinkBtn');
    const copySuccessMsg = document.getElementById('copyAppSuccessMsg');

    const downloadUrl = modal.getAttribute('data-download-url') || 'https://netfly.id/download/netfly-mobile.apk';

    // Fungsi Render QR Code ke Canvas
    function renderQr() {
        if (!qrCanvas) return;
        QRCode.toCanvas(
            qrCanvas,
            downloadUrl,
            {
                width: 224,
                margin: 1,
                color: {
                    dark: '#0f172a', // slate-900
                    light: '#ffffff',
                },
                errorCorrectionLevel: 'M',
            },
            function (err) {
                if (err) {
                    console.error('NetFly QR Generation Error:', err);
                }
            }
        );
    }

    // Fungsi Buka Modal
    window.openDownloadAppModal = function () {
        if (!modal) return;
        modal.classList.remove('hidden');
        // Force reflow for CSS transition
        void modal.offsetWidth;
        modal.classList.add('is-open');
        document.body.classList.add('overflow-hidden');
        renderQr();
    };

    // Fungsi Tutup Modal
    window.closeDownloadAppModal = function () {
        if (!modal) return;
        modal.classList.remove('is-open');
        document.body.classList.remove('overflow-hidden');
        setTimeout(() => {
            if (!modal.classList.contains('is-open')) {
                modal.classList.add('hidden');
            }
        }, 200);
    };

    // Event listener pada tombol pemicu
    triggerBtns.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            window.openDownloadAppModal();
        });
    });

    // Event listener pada tombol tutup
    closeBtns.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            window.closeDownloadAppModal();
        });
    });

    // Tutup saat backdrop diklik
    if (modalBackdrop) {
        modalBackdrop.addEventListener('click', () => {
            window.closeDownloadAppModal();
        });
    }

    // Tutup dengan tombol Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            window.closeDownloadAppModal();
        }
    });

    // Fungsi Salin Link
    if (copyLinkBtn) {
        copyLinkBtn.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(downloadUrl);
                if (copySuccessMsg) {
                    copySuccessMsg.classList.remove('hidden');
                    setTimeout(() => {
                        copySuccessMsg.classList.add('hidden');
                    }, 2500);
                }
            } catch (err) {
                console.warn('Clipboard write failed, fallback selection', err);
                const input = document.getElementById('downloadAppUrlInput');
                if (input) {
                    input.select();
                    document.execCommand('copy');
                    if (copySuccessMsg) {
                        copySuccessMsg.classList.remove('hidden');
                        setTimeout(() => {
                            copySuccessMsg.classList.add('hidden');
                        }, 2500);
                    }
                }
            }
        });
    }

    // Render awal jika modal dibuka langsung
    renderQr();
}
