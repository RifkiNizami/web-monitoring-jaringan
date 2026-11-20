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
    const resetBtn = document.getElementById('btnResetFilter');

    // Dapatkan path dasar halaman reports (misal /project-monitoring-web/public/reports atau /reports)
    function getReportsBasePath() {
        const currentPath = window.location.pathname.replace(/\/+$/, '');
        return currentPath.endsWith('/reports') ? currentPath : (currentPath + '/reports');
    }

    // Pastikan action form filter dan tombol reset selalu mengarah ke URL path saat ini
    const basePath = getReportsBasePath();
    if (filterForm) {
        filterForm.action = basePath;
    }
    if (resetBtn) {
        resetBtn.href = basePath;
    }

    // Sinkronisasi dinamis URL tombol ekspor & pratinjau dengan memelihara path absolut subfolder
    function syncExportUrls() {
        if (!customerSelect || !monthSelect || !yearSelect) return;
        const cust = customerSelect.value;
        const month = monthSelect.value;
        const year = yearSelect.value;
        const base = getReportsBasePath();

        // 1. URL Export PDF (Download)
        const pdfUrl = new URL(base + '/export-pdf', window.location.origin);
        pdfUrl.searchParams.set('customer', cust);
        pdfUrl.searchParams.set('month', month);
        pdfUrl.searchParams.set('year', year);

        // 2. URL Preview PDF (Buka di tab baru dengan stream/preview)
        const previewUrl = new URL(pdfUrl.toString());
        previewUrl.searchParams.set('preview', '1');

        // 3. URL Export Excel
        const excelUrl = new URL(base + '/export-excel', window.location.origin);
        excelUrl.searchParams.set('customer', cust);
        excelUrl.searchParams.set('month', month);
        excelUrl.searchParams.set('year', year);

        if (exportPdfBtn) exportPdfBtn.href = pdfUrl.toString();
        if (previewPdfBtn) previewPdfBtn.href = previewUrl.toString();
        if (exportExcelBtn) exportExcelBtn.href = excelUrl.toString();
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
                    filterForm.action = getReportsBasePath();
                    filterForm.submit();
                }
            });
        }
    });

    // Preview PDF click handler
    if (previewPdfBtn && !previewPdfBtn.dataset.listenerBound) {
        previewPdfBtn.dataset.listenerBound = 'true';
        previewPdfBtn.addEventListener('click', function () {
            syncExportUrls();
        });
    }

    // Indikator loading untuk Unduh PDF Resmi
    if (exportPdfBtn && !exportPdfBtn.dataset.listenerBound) {
        exportPdfBtn.dataset.listenerBound = 'true';
        exportPdfBtn.addEventListener('click', function () {
            syncExportUrls();
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
            syncExportUrls();
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
