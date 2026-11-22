<!-- MODAL: PREVIEW FOTO BUKTI KENDALA / INSIDEN -->
<div id="ticketPhotoModal" class="fixed inset-0 z-50 items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden [&:not(.hidden)]:flex">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200/90 w-full max-w-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200 flex flex-col max-h-[90vh]">
        
        <!-- Modal Header -->
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70 shrink-0">
            <div class="min-w-0 pr-4">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-mono" id="modalTicketPhotoId">
                        INC-TICKET
                    </span>
                    <span class="text-xs font-semibold text-slate-800 truncate" id="modalTicketPhotoClient">
                        Nama Klien
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5 truncate" id="modalTicketPhotoSubject">
                    Subjek Gangguan
                </p>
            </div>
            
            <div class="flex items-center gap-2 shrink-0">
                <a 
                    id="modalTicketPhotoDownload" 
                    href="#" 
                    target="_blank" 
                    download 
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition"
                    title="Buka / Unduh foto ukuran asli"
                >
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Unduh</span>
                </a>
                <button 
                    type="button" 
                    onclick="closeTicketPhotoModal()" 
                    class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 transition cursor-pointer"
                    aria-label="Tutup"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Image Body -->
        <div class="p-4 sm:p-6 overflow-y-auto flex-1 flex flex-col items-center justify-center bg-slate-950/5">
            <div class="relative max-h-[60vh] max-w-full rounded-xl overflow-hidden border border-slate-200/80 bg-white shadow-sm flex items-center justify-center">
                <img 
                    id="modalTicketPhotoImage" 
                    src="" 
                    alt="Bukti Foto Kendala" 
                    class="max-h-[60vh] w-auto max-w-full object-contain"
                >
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/60 flex items-center justify-between text-xs text-slate-500 shrink-0">
            <span class="text-[11px] text-slate-400">Bukti foto yang diunggah oleh pelapor / teknisi</span>
            <button 
                type="button" 
                onclick="closeTicketPhotoModal()" 
                class="px-4 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-2xs transition cursor-pointer"
            >
                Tutup
            </button>
        </div>

    </div>
</div>

<script>
    function openTicketPhotoModal(url, ticketId, subject, clientName) {
        const modal = document.getElementById('ticketPhotoModal');
        const img = document.getElementById('modalTicketPhotoImage');
        const downloadBtn = document.getElementById('modalTicketPhotoDownload');
        const idEl = document.getElementById('modalTicketPhotoId');
        const clientEl = document.getElementById('modalTicketPhotoClient');
        const subjectEl = document.getElementById('modalTicketPhotoSubject');

        if (img) img.src = url || '';
        if (downloadBtn) {
            downloadBtn.href = url || '#';
            downloadBtn.setAttribute('download', (ticketId || 'bukti-tiket') + '.jpg');
        }
        if (idEl) idEl.textContent = ticketId || 'TIKET';
        if (clientEl) clientEl.textContent = clientName || 'Klien NetFly';
        if (subjectEl) subjectEl.textContent = subject || 'Laporan Kendala';

        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeTicketPhotoModal() {
        const modal = document.getElementById('ticketPhotoModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    // Tutup modal jika klik di luar box modal atau tekan Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeTicketPhotoModal();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('ticketPhotoModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeTicketPhotoModal();
                }
            });
        }
    });
</script>
