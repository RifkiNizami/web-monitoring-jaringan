/**
 * NetFly Admin - Tickets & Incident Queue Interactivity
 */
export function initAdminTickets() {
    const searchInput = document.getElementById('tableSearch');
    const prioritySelect = document.getElementById('prioritySelect');
    const rows = document.querySelectorAll('.ticket-row');

    function filterRows() {
        const term = (searchInput ? searchInput.value : '').toLowerCase().trim();
        const priority = (prioritySelect ? prioritySelect.value : 'all').toLowerCase();

        rows.forEach(row => {
            const rowText = row.textContent?.toLowerCase() || '';
            const rowPriority = (row.getAttribute('data-priority') || '').toLowerCase();

            const matchesSearch = !term || rowText.includes(term);
            const matchesPriority = priority === 'all' || rowPriority === priority;

            if (matchesSearch && matchesPriority) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterRows);
    }

    if (prioritySelect) {
        prioritySelect.addEventListener('change', filterRows);
    }

    // Sinkronisasi nilai dropdown ke hidden input form update tiket
    document.querySelectorAll('.ticket-row').forEach(row => {
        const techSelect = row.querySelector('.technician-select');
        const statusSelect = row.querySelector('.status-select');
        const hiddenTech = row.querySelector('.row-assigned-tech');
        const hiddenStatus = row.querySelector('.row-status');

        if (techSelect && hiddenTech) {
            techSelect.addEventListener('change', () => {
                hiddenTech.value = techSelect.value;
            });
        }

        if (statusSelect && hiddenStatus) {
            statusSelect.addEventListener('change', () => {
                hiddenStatus.value = statusSelect.value;
            });
        }
    });

    // Submit form update tiket ke database ketika tombol Update ditekan
    document.querySelectorAll('.form-update-ticket').forEach(form => {
        form.addEventListener('submit', function (e) {
            const row = this.closest('tr');
            if (row) {
                const techSelect = row.querySelector('.technician-select');
                const statusSelect = row.querySelector('.status-select');
                const hiddenTech = form.querySelector('.row-assigned-tech');
                const hiddenStatus = form.querySelector('.row-status');

                if (techSelect && hiddenTech) {
                    hiddenTech.value = techSelect.value;
                }
                if (statusSelect && hiddenStatus) {
                    hiddenStatus.value = statusSelect.value;
                }
            }

            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `
                    <svg class="animate-spin w-3.5 h-3.5 text-white" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span>Menyimpan...</span>
                `;
            }
        });
    });

    // Delete ticket form confirmation
    document.querySelectorAll('.delete-ticket-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            const row = this.closest('tr');
            const ticketId = row ? row.getAttribute('data-id') : '';
            if (!confirm(`Apakah Anda yakin ingin membatalkan/menghapus tiket ${ticketId}?`)) {
                e.preventDefault();
            }
        });
    });

    // Modals: SLA
    const btnSla = document.getElementById('btnSlaReport');
    const slaModal = document.getElementById('slaModal');
    const closeSla = document.getElementById('closeSlaModal');
    const dismissSla = document.getElementById('btnDismissSla');

    if (btnSla && slaModal) {
        btnSla.addEventListener('click', () => slaModal.classList.remove('hidden'));
    }
    if (closeSla && slaModal) {
        closeSla.addEventListener('click', () => slaModal.classList.add('hidden'));
    }
    if (dismissSla && slaModal) {
        dismissSla.addEventListener('click', () => slaModal.classList.add('hidden'));
    }

    // Modals: Create Ticket
    const btnCreate = document.getElementById('btnCreateTicket');
    const createModal = document.getElementById('createTicketModal');
    const closeCreate = document.getElementById('closeCreateModal');
    const cancelCreate = document.getElementById('cancelCreateModal');

    if (btnCreate && createModal) {
        btnCreate.addEventListener('click', () => createModal.classList.remove('hidden'));
    }
    if (closeCreate && createModal) {
        closeCreate.addEventListener('click', () => createModal.classList.add('hidden'));
    }
    if (cancelCreate && createModal) {
        cancelCreate.addEventListener('click', () => createModal.classList.add('hidden'));
    }
}

function showToastNotification(message) {
    let alertBox = document.getElementById('floatingToastAlert');
    if (!alertBox) {
        alertBox = document.createElement('div');
        alertBox.id = 'floatingToastAlert';
        alertBox.className = 'fixed bottom-6 right-6 z-50 p-4 bg-emerald-600 text-white rounded-xl shadow-xl flex items-center gap-3 text-xs font-semibold animate-in fade-in slide-in-from-bottom-5 duration-200';
        document.body.appendChild(alertBox);
    }
    alertBox.innerHTML = `
        <svg class="w-4 h-4 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
        </svg>
        <span>${message}</span>
        <button type="button" class="ml-2 text-white/80 hover:text-white font-bold cursor-pointer" onclick="this.parentElement.remove()">✕</button>
    `;
    setTimeout(() => {
        if (alertBox && alertBox.parentElement) {
            alertBox.remove();
        }
    }, 4500);
}

