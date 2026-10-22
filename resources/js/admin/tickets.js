/**
 * NetFly Admin - Tickets & Incident Queue Interactivity
 */
export function initAdminTickets() {
    const searchInput = document.getElementById('tableSearch');
    const prioritySelect = document.getElementById('prioritySelect');
    const rows = document.querySelectorAll('.ticket-row');

    if (!rows.length && !searchInput && !prioritySelect) return;

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

    // Delete action button
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const row = this.closest('tr');
            const ticketId = row ? row.getAttribute('data-id') : '';
            if (confirm(`Apakah Anda yakin ingin menghapus antrean tiket ${ticketId}?`)) {
                if (row) row.remove();
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
    const formTicket = document.getElementById('formNewTicket');

    if (btnCreate && createModal) {
        btnCreate.addEventListener('click', () => createModal.classList.remove('hidden'));
    }
    if (closeCreate && createModal) {
        closeCreate.addEventListener('click', () => createModal.classList.add('hidden'));
    }
    if (cancelCreate && createModal) {
        cancelCreate.addEventListener('click', () => createModal.classList.add('hidden'));
    }
    if (formTicket && createModal) {
        formTicket.addEventListener('submit', function (e) {
            e.preventDefault();
            alert('Tiket komplain baru berhasil ditambahkan ke antrean.');
            createModal.classList.add('hidden');
            formTicket.reset();
        });
    }
}
