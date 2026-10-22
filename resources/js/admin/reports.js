/**
 * NetFly Admin - Executive Report Generator Interactivity
 */
export function initAdminReports() {
    const exportBtn = document.getElementById('btnExportPdf');
    if (!exportBtn) return;

    exportBtn.addEventListener('click', function () {
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
