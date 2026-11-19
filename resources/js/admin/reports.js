/**
 * NetFly Admin - Executive Report Generator Interactivity
 */
export function initAdminReports() {
    const exportPdfBtn = document.getElementById('btnExportPdf');
    const exportExcelBtn = document.getElementById('btnExportExcel');
    const previewPdfBtn = document.getElementById('btnPreviewPdf');

    const customerSelect = document.getElementById('filterCustomer');
    const monthSelect = document.getElementById('filterMonth');
    const yearSelect = document.getElementById('filterYear');
    const filterForm = document.getElementById('reportFilterForm');

    // Sinkronisasi dinamis URL tombol ekspor & pratinjau dengan memelihara path absolut subfolder
    function syncExportUrls() {
        if (!customerSelect || !monthSelect || !yearSelect) return;
        const cust = customerSelect.value;
        const month = monthSelect.value;
        const year = yearSelect.value;

        function updateBtn(btn, isPreview = false) {
            if (!btn) return;
            const baseUrl = btn.dataset.baseUrl || btn.getAttribute('href') || '';
            if (!baseUrl) return;
            try {
                const parsed = new URL(baseUrl, window.location.href);
                parsed.searchParams.set('customer', cust);
                parsed.searchParams.set('month', month);
                parsed.searchParams.set('year', year);
                if (isPreview) {
                    parsed.searchParams.set('preview', '1');
                } else {
                    parsed.searchParams.delete('preview');
                }
                btn.href = parsed.toString();
            } catch (e) {
                // Fallback manual jika URL constructor gagal
                const cleanBase = baseUrl.split('?')[0];
                const params = new URLSearchParams();
                params.set('customer', cust);
                params.set('month', month);
                params.set('year', year);
                if (isPreview) params.set('preview', '1');
                btn.href = `${cleanBase}?${params.toString()}`;
            }
        }

        updateBtn(exportPdfBtn, false);
        updateBtn(exportExcelBtn, false);
        updateBtn(previewPdfBtn, true);
    }

    // Jalankan sinkronisasi awal saat halaman dimuat
    syncExportUrls();

    // Dengarkan perubahan pada select dropdown: sinkronkan link dan otomatis submit form filter
    [customerSelect, monthSelect, yearSelect].forEach(el => {
        if (el && !el.dataset.listenerBound) {
            el.dataset.listenerBound = 'true';
            el.addEventListener('change', () => {
                syncExportUrls();
                if (filterForm) {
                    filterForm.submit();
                }
            });
        }
    });

    // Indikator loading untuk Unduh PDF Resmi
    if (exportPdfBtn && !exportPdfBtn.dataset.listenerBound) {
        exportPdfBtn.dataset.listenerBound = 'true';
        exportPdfBtn.addEventListener('click', function () {
            const originalHtml = this.innerHTML;
            this.classList.add('opacity-75');
            this.innerHTML = `
                <svg class="animate-spin -ml-0.5 mr-2 h-3.5 w-3.5 text-white inline-block" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Menyiapkan PDF...</span>
            `;
            setTimeout(() => {
                this.classList.remove('opacity-75');
                this.innerHTML = originalHtml;
            }, 3000);
        });
    }

    // Indikator loading untuk Unduh Excel
    if (exportExcelBtn && !exportExcelBtn.dataset.listenerBound) {
        exportExcelBtn.dataset.listenerBound = 'true';
        exportExcelBtn.addEventListener('click', function () {
            const originalHtml = this.innerHTML;
            this.classList.add('opacity-75');
            this.innerHTML = `
                <svg class="animate-spin -ml-0.5 mr-2 h-3.5 w-3.5 text-white inline-block" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Menyiapkan Excel...</span>
            `;
            setTimeout(() => {
                this.classList.remove('opacity-75');
                this.innerHTML = originalHtml;
            }, 3000);
        });
    }
}
