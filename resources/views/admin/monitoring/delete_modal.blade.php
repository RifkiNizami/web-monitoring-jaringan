<!-- MODAL: KONFIRMASI HAPUS PERANGKAT MONITORING -->
<div id="deleteMonitoringNodeModal" class="fixed inset-0 z-50 items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs hidden [&:not(.hidden)]:flex">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200/80 w-full max-w-sm overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="p-6 text-center space-y-4">
            <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 mx-auto flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-sm text-slate-900">Hapus dari Monitoring?</h4>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Perangkat <strong id="deleteNodeHostname" class="text-slate-800">--</strong> akan dihentikan dari pemantauan telemetri.
                </p>
            </div>
            <div class="flex items-center justify-center gap-2 pt-2">
                <button type="button" class="close-delete-monitoring-modal px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                    Batal
                </button>
                <button type="button" id="btnConfirmDeleteNode" class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-lg transition cursor-pointer shadow-xs">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
