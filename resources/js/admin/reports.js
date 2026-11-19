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

    // Sinkronisasi dinamis URL tombol ekspor & pratinjau ketika dropdown filter berubah
    function syncExportUrls() {
        if (!customerSelect || !monthSelect || !yearSelect) return;
        const cust = customerSelect.value;
        const month = monthSelect.value;
        const year = yearSelect.value;

        if (exportPdfBtn) {
            exportPdfBtn.href = `/reports/export-pdf?customer=${encodeURIComponent(cust)}&month=${encodeURIComponent(month)}&year=${encodeURIComponent(year)}`;
        }
        if (exportExcelBtn) {
            exportExcelBtn.href = `/reports/export-excel?customer=${encodeURIComponent(cust)}&month=${encodeURIComponent(month)}&year=${encodeURIComponent(year)}`;
        }
        if (previewPdfBtn) {
            previewPdfBtn.href = `/reports/export-pdf?customer=${encodeURIComponent(cust)}&month=${encodeURIComponent(month)}&year=${encodeURIComponent(year)}&preview=1`;
        }
    }

    [customerSelect, monthSelect, yearSelect].forEach(el => {
        if (el && !el.dataset.listenerBound) {
            el.dataset.listenerBound = 'true';
            el.addEventListener('change', syncExportUrls);
        }
    });

    // Indikator loading untuk Unduh PDF
    if (exportPdfBtn && !exportPdfBtn.dataset.listenerBound) {
        exportPdfBtn.dataset.listenerBound = 'true';
        exportPdfBtn.addEventListener('click', function () {
            const originalHtml = this.innerHTML;
            this.classList.add('opacity-75', 'pointer-events-none');
            this.innerHTML = `
                <svg class="animate-spin -ml-0.5 mr-2 h-3.5 w-3.5 text-white inline-block" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Menyiapkan PDF...</span>
            `;
            setTimeout(() => {
                this.classList.remove('opacity-75', 'pointer-events-none');
                this.innerHTML = originalHtml;
            }, 3500);
        });
    }

    // Indikator loading untuk Unduh Excel
    if (exportExcelBtn && !exportExcelBtn.dataset.listenerBound) {
        exportExcelBtn.dataset.listenerBound = 'true';
        exportExcelBtn.addEventListener('click', function () {
            const originalHtml = this.innerHTML;
            this.classList.add('opacity-75', 'pointer-events-none');
            this.innerHTML = `
                <svg class="animate-spin -ml-0.5 mr-2 h-3.5 w-3.5 text-white inline-block" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Menyiapkan Excel...</span>
            `;
            setTimeout(() => {
                this.classList.remove('opacity-75', 'pointer-events-none');
                this.innerHTML = originalHtml;
            }, 3000);
        });
    }
}
