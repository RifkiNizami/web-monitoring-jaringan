import QRCode from 'qrcode';

/**
 * NetFly Admin - Mobile App Settings & Live QR Code Preview
 * Mengelola rendering QR Code real-time saat tautan unduhan diedit oleh Admin Konten.
 */
export function initAdminAppSetting() {
    const canvas = document.getElementById('adminPreviewQrCanvas');
    const input = document.getElementById('download_url');
    const testBtn = document.getElementById('testOpenLinkBtn');

    if (!canvas || !input) return;

    function renderQr() {
        const rawUrl = input.value.trim();
        const url = rawUrl || 'https://netfly.id';

        if (testBtn) {
            testBtn.href = url;
        }

        QRCode.toCanvas(
            canvas,
            url,
            {
                width: 200,
                margin: 1,
                color: {
                    dark: '#0f172a', // slate-900
                    light: '#ffffff',
                },
                errorCorrectionLevel: 'M',
            },
            function (err) {
                if (err) {
                    console.error('Admin Live QR Code Generation Error:', err);
                }
            }
        );
    }

    // Render pertama kali saat halaman dimuat
    renderQr();

    // Dengarkan event input agar barcode berubah seketika saat URL diubah
    if (!input.dataset.qrInitialized) {
        input.dataset.qrInitialized = 'true';
        input.addEventListener('input', renderQr);
    }
}
