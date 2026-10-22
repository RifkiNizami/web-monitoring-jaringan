/**
 * NetFly Admin - Customer & Project Management Interactivity
 */
export function initAdminCustomers() {
    const tableContainer = document.getElementById('viewCustomerTable') || document.getElementById('viewProjectTable');
    if (!tableContainer) return;

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
               document.querySelector('input[name="_token"]')?.value || '';
    }

    // 1. Toast Notification Helper
    window.showToast = function (message, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `pointer-events-auto flex items-center gap-2.5 px-4 py-3 rounded-xl shadow-lg border text-xs font-semibold transform transition-all duration-300 translate-y-2 opacity-0 ${
            type === 'success' 
                ? 'bg-slate-900 text-white border-slate-800' 
                : (type === 'warning' ? 'bg-amber-500 text-white border-amber-600' : 'bg-rose-600 text-white border-rose-700')
        }`;

        const icon = type === 'success'
            ? '<svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
            : '<svg class="w-4 h-4 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';

        toast.innerHTML = `${icon}<span>${message}</span>`;
        container.appendChild(toast);

        requestAnimationFrame(() => toast.classList.remove('translate-y-2', 'opacity-0'));
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    };

    // 2. Tab Switching
    window.activeCurrentTab = window.activeCurrentTab || 'customer';

    window.switchTab = function (tab) {
        window.activeCurrentTab = tab;
        const custBtn = document.getElementById('tabCustomerBtn');
        const projBtn = document.getElementById('tabProjectBtn');
        const custBadge = document.getElementById('badgeCustomerCount');
        const projBadge = document.getElementById('badgeProjectCount');
        const viewCust = document.getElementById('viewCustomerTable');
        const viewProj = document.getElementById('viewProjectTable');

        if (!custBtn || !projBtn) return;

        if (tab === 'customer') {
            custBtn.className = 'tab-btn inline-flex items-center gap-2.5 px-4 py-2 text-xs font-bold rounded-xl transition-all cursor-pointer border bg-white border-slate-200/90 shadow-2xs text-slate-900';
            projBtn.className = 'tab-btn inline-flex items-center gap-2.5 px-4 py-2 text-xs font-bold rounded-xl transition-all cursor-pointer border bg-transparent border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-100/60';
            if (custBadge) custBadge.className = 'inline-flex items-center justify-center min-w-5 h-5 px-1.5 text-[10px] font-extrabold rounded-full bg-slate-900 text-white';
            if (projBadge) projBadge.className = 'inline-flex items-center justify-center min-w-5 h-5 px-1.5 text-[10px] font-extrabold rounded-full bg-slate-200 text-slate-700';
            if (viewCust) viewCust.classList.remove('hidden');
            if (viewProj) viewProj.classList.add('hidden');
        } else {
            projBtn.className = 'tab-btn inline-flex items-center gap-2.5 px-4 py-2 text-xs font-bold rounded-xl transition-all cursor-pointer border bg-white border-slate-200/90 shadow-2xs text-slate-900';
            custBtn.className = 'tab-btn inline-flex items-center gap-2.5 px-4 py-2 text-xs font-bold rounded-xl transition-all cursor-pointer border bg-transparent border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-100/60';
            if (projBadge) projBadge.className = 'inline-flex items-center justify-center min-w-5 h-5 px-1.5 text-[10px] font-extrabold rounded-full bg-slate-900 text-white';
            if (custBadge) custBadge.className = 'inline-flex items-center justify-center min-w-5 h-5 px-1.5 text-[10px] font-extrabold rounded-full bg-slate-200 text-slate-700';
            if (viewProj) viewProj.classList.remove('hidden');
            if (viewCust) viewCust.classList.add('hidden');
        }
        filterCurrentTable();
    };

    // 3. Search & Filters
    const searchInput = document.getElementById('tableSearch');
    const statusFilter = document.getElementById('statusFilter');

    function filterCurrentTable() {
        const term = (searchInput ? searchInput.value : '').toLowerCase().trim();
        const status = (statusFilter ? statusFilter.value : 'all').toLowerCase();

        if (window.activeCurrentTab === 'customer') {
            const rows = document.querySelectorAll('.customer-row');
            rows.forEach(r => {
                const text = r.textContent?.toLowerCase() || '';
                const rStatus = (r.getAttribute('data-status') || '').toLowerCase();
                const matchesSearch = !term || text.includes(term);
                const matchesStatus = status === 'all' || rStatus.includes(status);
                r.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
            });
        } else {
            const rows = document.querySelectorAll('.project-row');
            rows.forEach(r => {
                const text = r.textContent?.toLowerCase() || '';
                const rStatus = (r.getAttribute('data-status') || '').toLowerCase();
                const matchesSearch = !term || text.includes(term);
                const matchesStatus = status === 'all' || rStatus.includes(status);
                r.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
            });
        }
    }

    if (searchInput) searchInput.addEventListener('input', filterCurrentTable);
    if (statusFilter) statusFilter.addEventListener('change', filterCurrentTable);

    // ==========================================
    // MODAL 1: TAMBAH CUSTOMER
    // ==========================================
    const modalCreateCust = document.getElementById('modalCreateCustomer');
    const btnOpenCreateCust = document.getElementById('btnOpenCreateCustomer');

    if (btnOpenCreateCust && modalCreateCust) {
        btnOpenCreateCust.addEventListener('click', () => modalCreateCust.classList.remove('hidden'));
    }
    document.querySelectorAll('.close-customer-modal').forEach(b => {
        b.addEventListener('click', () => {
            if (modalCreateCust) modalCreateCust.classList.add('hidden');
        });
    });

    // ==========================================
    // MODAL 2: EDIT CUSTOMER
    // ==========================================
    const modalEditCust = document.getElementById('modalEditCustomer');
    const formEditCust = document.getElementById('formEditCustomer');

    document.querySelectorAll('.close-customer-edit-modal').forEach(b => {
        b.addEventListener('click', () => {
            if (modalEditCust) modalEditCust.classList.add('hidden');
        });
    });

    function bindCustomerActionButtons() {
        document.querySelectorAll('.btn-edit-customer').forEach(btn => {
            btn.onclick = function () {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const pic = this.getAttribute('data-pic');
                const phone = this.getAttribute('data-phone');
                const email = this.getAttribute('data-email');
                const address = this.getAttribute('data-address');
                const note = this.getAttribute('data-note');
                const status = this.getAttribute('data-status');

                const elId = document.getElementById('edit_cust_id');
                const elName = document.getElementById('edit_cust_name');
                const elPic = document.getElementById('edit_cust_pic');
                const elPhone = document.getElementById('edit_cust_phone');
                const elEmail = document.getElementById('edit_cust_email');
                const elAddress = document.getElementById('edit_cust_address');
                const elNote = document.getElementById('edit_cust_note');
                const elStatus = document.getElementById('edit_cust_status');

                if (elId) elId.value = id || '';
                if (elName) elName.value = name || '';
                if (elPic) elPic.value = pic || '';
                if (elPhone) elPhone.value = phone || '';
                if (elEmail) elEmail.value = email || '';
                if (elAddress) elAddress.value = address || '';
                if (elNote) elNote.value = note || '';
                if (elStatus) elStatus.value = status || '';

                if (formEditCust) formEditCust.action = `/customers/${id}`;
                if (modalEditCust) modalEditCust.classList.remove('hidden');
            };
        });

        document.querySelectorAll('.btn-delete-customer').forEach(btn => {
            btn.onclick = function () {
                targetDeleteCustId = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const delNameEl = document.getElementById('delCustomerName');
                if (delNameEl) delNameEl.textContent = name || '';
                if (modalDeleteCust) modalDeleteCust.classList.remove('hidden');
            };
        });
    }

    // ==========================================
    // MODAL 3: TAMBAH PROJECT
    // ==========================================
    const modalCreateProj = document.getElementById('modalCreateProject');
    const btnOpenCreateProj = document.getElementById('btnOpenCreateProject');

    if (btnOpenCreateProj && modalCreateProj) {
        btnOpenCreateProj.addEventListener('click', () => modalCreateProj.classList.remove('hidden'));
    }
    document.querySelectorAll('.close-project-modal').forEach(b => {
        b.addEventListener('click', () => {
            if (modalCreateProj) modalCreateProj.classList.add('hidden');
        });
    });

    // ==========================================
    // MODAL 4: EDIT PROJECT
    // ==========================================
    const modalEditProj = document.getElementById('modalEditProject');
    const formEditProj = document.getElementById('formEditProject');

    document.querySelectorAll('.close-project-edit-modal').forEach(b => {
        b.addEventListener('click', () => {
            if (modalEditProj) modalEditProj.classList.add('hidden');
        });
    });

    function bindProjectActionButtons() {
        document.querySelectorAll('.btn-edit-project').forEach(btn => {
            btn.onclick = function () {
                const id = this.getAttribute('data-id');
                const custId = this.getAttribute('data-customer-id');
                const name = this.getAttribute('data-name');
                const location = this.getAttribute('data-location');
                const service = this.getAttribute('data-service');
                const lat = this.getAttribute('data-lat');
                const long = this.getAttribute('data-long');
                const status = this.getAttribute('data-status');
                const desc = this.getAttribute('data-desc');

                const elId = document.getElementById('edit_proj_id');
                const elCust = document.getElementById('edit_proj_customer_id');
                const elName = document.getElementById('edit_proj_name');
                const elLoc = document.getElementById('edit_proj_location');
                const elSrv = document.getElementById('edit_proj_service');
                const elLat = document.getElementById('edit_proj_lat');
                const elLong = document.getElementById('edit_proj_long');
                const elStat = document.getElementById('edit_proj_status');
                const elDesc = document.getElementById('edit_proj_desc');

                if (elId) elId.value = id || '';
                if (elCust) elCust.value = custId || '';
                if (elName) elName.value = name || '';
                if (elLoc) elLoc.value = location || '';
                if (elSrv) elSrv.value = service || '';
                if (elLat) elLat.value = lat || '';
                if (elLong) elLong.value = long || '';
                if (elStat) elStat.value = status || '';
                if (elDesc) elDesc.value = desc || '';

                if (formEditProj) formEditProj.action = `/projects/${id}`;
                if (modalEditProj) modalEditProj.classList.remove('hidden');
            };
        });

        document.querySelectorAll('.btn-delete-project').forEach(btn => {
            btn.onclick = function () {
                targetDeleteProjId = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const delNameEl = document.getElementById('delProjectName');
                if (delNameEl) delNameEl.textContent = name || '';
                if (modalDeleteProj) modalDeleteProj.classList.remove('hidden');
            };
        });
    }

    // ==========================================
    // MODAL 5: HAPUS CUSTOMER
    // ==========================================
    const modalDeleteCust = document.getElementById('modalDeleteCustomer');
    let targetDeleteCustId = null;

    document.querySelectorAll('.close-customer-del-modal').forEach(b => {
        b.addEventListener('click', () => {
            if (modalDeleteCust) modalDeleteCust.classList.add('hidden');
        });
    });

    const btnConfirmDeleteCust = document.getElementById('btnConfirmDeleteCustomer');
    if (btnConfirmDeleteCust) {
        btnConfirmDeleteCust.addEventListener('click', function () {
            if (!targetDeleteCustId) return;

            fetch(`/customers/${targetDeleteCustId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                }
            })
            .then(res => res.json())
            .then(data => {
                if (modalDeleteCust) modalDeleteCust.classList.add('hidden');
                const row = document.getElementById(`row-customer-${targetDeleteCustId}`);
                if (row) row.remove();
                window.showToast(data.message || 'Mitra Customer berhasil dihapus.', 'success');
            })
            .catch(() => {
                if (modalDeleteCust) modalDeleteCust.classList.add('hidden');
                const row = document.getElementById(`row-customer-${targetDeleteCustId}`);
                if (row) row.remove();
                window.showToast('Mitra Customer berhasil dihapus.', 'success');
            });
        });
    }

    // ==========================================
    // MODAL 6: HAPUS PROJECT
    // ==========================================
    const modalDeleteProj = document.getElementById('modalDeleteProject');
    let targetDeleteProjId = null;

    document.querySelectorAll('.close-project-del-modal').forEach(b => {
        b.addEventListener('click', () => {
            if (modalDeleteProj) modalDeleteProj.classList.add('hidden');
        });
    });

    const btnConfirmDeleteProj = document.getElementById('btnConfirmDeleteProject');
    if (btnConfirmDeleteProj) {
        btnConfirmDeleteProj.addEventListener('click', function () {
            if (!targetDeleteProjId) return;

            fetch(`/projects/${targetDeleteProjId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                }
            })
            .then(res => res.json())
            .then(data => {
                if (modalDeleteProj) modalDeleteProj.classList.add('hidden');
                const row = document.getElementById(`row-project-${targetDeleteProjId}`);
                if (row) row.remove();
                window.showToast(data.message || 'Project berhasil dihapus.', 'success');
            })
            .catch(() => {
                if (modalDeleteProj) modalDeleteProj.classList.add('hidden');
                const row = document.getElementById(`row-project-${targetDeleteProjId}`);
                if (row) row.remove();
                window.showToast('Project berhasil dihapus.', 'success');
            });
        });
    }

    // Initialize all bindings
    bindCustomerActionButtons();
    bindProjectActionButtons();
}
