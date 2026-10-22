/**
 * NetFly Admin - Network Devices Management Interactivity
 */
export function initAdminDevices() {
    const tbody = document.getElementById('devicesTableBody');
    if (!tbody) return;

    // Realtime Table Filter
    const searchInput = document.getElementById('deviceSearch');
    const typeFilter = document.getElementById('typeFilter');

    function filterDevices() {
        const term = (searchInput ? searchInput.value : '').toLowerCase().trim();
        const selectedType = (typeFilter ? typeFilter.value : 'all').toLowerCase();
        const rows = document.querySelectorAll('.device-row');

        rows.forEach(row => {
            const rowText = row.textContent?.toLowerCase() || '';
            const rowType = (row.getAttribute('data-type') || '').toLowerCase();

            const matchesSearch = !term || rowText.includes(term);
            const matchesType = selectedType === 'all' || rowType === selectedType;

            row.style.display = (matchesSearch && matchesType) ? '' : 'none';
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterDevices);
    if (typeFilter) typeFilter.addEventListener('change', filterDevices);

    // ==========================================
    // MODAL 1: TAMBAH INVENTARIS PERANGKAT BARU
    // ==========================================
    const createModal = document.getElementById('createDeviceModal');
    const btnOpenCreate = document.getElementById('btnOpenCreateDevice');
    const formCreate = document.getElementById('formCreateDevice');

    if (btnOpenCreate && createModal) {
        btnOpenCreate.addEventListener('click', () => {
            createModal.classList.remove('hidden');
        });
    }

    document.querySelectorAll('.close-create-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            if (createModal) createModal.classList.add('hidden');
        });
    });

    if (formCreate) {
        formCreate.addEventListener('submit', function (e) {
            e.preventDefault();
            
            const hostname = document.getElementById('create_hostname')?.value.trim() || '';
            const type = document.getElementById('create_type')?.value || '';
            const ip = document.getElementById('create_ip')?.value.trim() || '';
            const status = document.getElementById('create_status')?.value || '';
            const customer = document.getElementById('create_customer')?.value || '';
            const project = document.getElementById('create_project')?.value || '';
            const rack = document.getElementById('create_rack')?.value.trim() || '';
            const snmp = document.getElementById('create_snmp')?.value.trim() || '';

            const statusState = status.includes('Online') ? 'online' : (status.includes('Offline') ? 'offline' : 'maintenance');
            const statusPill = statusState === 'online'
                ? '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/80 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Online</span>'
                : (statusState === 'offline' 
                    ? '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-rose-700 bg-rose-50 border border-rose-200/80 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>Offline</span>'
                    : '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-amber-700 bg-amber-50 border border-amber-200/80 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Maintenance</span>');

            const newRow = document.createElement('tr');
            newRow.className = 'hover:bg-slate-50/80 transition-colors device-row';
            newRow.setAttribute('data-id', String(Date.now()));
            newRow.setAttribute('data-hostname', hostname);
            newRow.setAttribute('data-type', type);
            newRow.setAttribute('data-ip', ip);
            newRow.setAttribute('data-status', status);
            newRow.setAttribute('data-customer', customer);
            newRow.setAttribute('data-project', project);
            newRow.setAttribute('data-rack', rack);
            newRow.setAttribute('data-snmp', snmp);

            newRow.innerHTML = `
                <td class="py-4 px-5 align-middle">
                    <div class="space-y-1">
                        <span class="font-bold text-slate-900 font-mono tracking-tight text-xs block">${hostname}</span>
                        <span class="inline-block px-2 py-0.5 text-[9px] font-semibold rounded bg-slate-100 text-slate-700 border border-slate-200">${type}</span>
                    </div>
                </td>
                <td class="py-4 px-5 align-middle font-mono text-xs font-semibold text-blue-600 whitespace-nowrap">${ip}</td>
                <td class="py-4 px-5 align-middle whitespace-nowrap">${statusPill}</td>
                <td class="py-4 px-5 align-middle">
                    <div class="space-y-0.5 max-w-xs">
                        <p class="font-bold text-slate-800 text-xs">${customer}</p>
                        <p class="text-slate-500 text-[11px]">${project}</p>
                    </div>
                </td>
                <td class="py-4 px-5 align-middle font-medium text-slate-700 text-xs whitespace-nowrap">${rack}</td>
                <td class="py-4 px-5 align-middle font-mono text-[11px] text-slate-500 whitespace-nowrap">${snmp}</td>
                <td class="py-4 px-5 align-middle text-right whitespace-nowrap">
                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button" title="Edit Data Perangkat" class="btn-edit inline-flex items-center justify-center w-7 h-7 rounded-lg border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-100 active:scale-95 transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                        <button type="button" title="Hapus Perangkat Jaringan" class="btn-delete inline-flex items-center justify-center w-7 h-7 rounded-lg border border-rose-200/90 text-rose-500 hover:text-rose-600 hover:bg-rose-50 active:scale-95 transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </td>
            `;

            tbody.insertBefore(newRow, tbody.firstChild);
            attachRowActions(newRow);

            // Update total count
            const totalEl = document.getElementById('countTotal');
            if (totalEl) totalEl.textContent = String(parseInt(totalEl.textContent || '0') + 1);

            createModal.classList.add('hidden');
            formCreate.reset();
            const snmpInput = document.getElementById('create_snmp');
            if (snmpInput) snmpInput.value = 'netfly-ro-noc';
        });
    }

    // ==========================================
    // MODAL 2: EDIT DATA PERANGKAT
    // ==========================================
    const editModal = document.getElementById('editDeviceModal');
    const formEdit = document.getElementById('formEditDevice');
    let currentEditingRow = null;

    document.querySelectorAll('.close-edit-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            if (editModal) editModal.classList.add('hidden');
        });
    });

    function openEditModal(row) {
        currentEditingRow = row;
        const idInput = document.getElementById('edit_device_id');
        const hostInput = document.getElementById('edit_hostname');
        const typeInput = document.getElementById('edit_type');
        const ipInput = document.getElementById('edit_ip');
        const statusInput = document.getElementById('edit_status');
        const custInput = document.getElementById('edit_customer');
        const projInput = document.getElementById('edit_project');
        const rackInput = document.getElementById('edit_rack');
        const snmpInput = document.getElementById('edit_snmp');

        if (idInput) idInput.value = row.getAttribute('data-id') || '';
        if (hostInput) hostInput.value = row.getAttribute('data-hostname') || 'RTR-CORE-MANDIRI-01';
        if (typeInput) typeInput.value = row.getAttribute('data-type') || 'Router';
        if (ipInput) ipInput.value = row.getAttribute('data-ip') || '10.200.45.1';
        if (statusInput) statusInput.value = row.getAttribute('data-status') || 'Online (Beroperasi Normal)';
        if (custInput) custInput.value = row.getAttribute('data-customer') || 'PT. Bank Mandiri (Persero) Tbk';
        if (projInput) projInput.value = row.getAttribute('data-project') || 'MPLS Core Interconnect Plaza Mandiri';
        if (rackInput) rackInput.value = row.getAttribute('data-rack') || 'Rack Core-A, U-42';
        if (snmpInput) snmpInput.value = row.getAttribute('data-snmp') || 'mandiri-ro-noc';

        if (editModal) editModal.classList.remove('hidden');
    }

    if (formEdit) {
        formEdit.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!currentEditingRow) return;

            const hostname = document.getElementById('edit_hostname')?.value.trim() || '';
            const type = document.getElementById('edit_type')?.value || '';
            const ip = document.getElementById('edit_ip')?.value.trim() || '';
            const status = document.getElementById('edit_status')?.value || '';
            const customer = document.getElementById('edit_customer')?.value || '';
            const project = document.getElementById('edit_project')?.value || '';
            const rack = document.getElementById('edit_rack')?.value.trim() || '';
            const snmp = document.getElementById('edit_snmp')?.value.trim() || '';

            currentEditingRow.setAttribute('data-hostname', hostname);
            currentEditingRow.setAttribute('data-type', type);
            currentEditingRow.setAttribute('data-ip', ip);
            currentEditingRow.setAttribute('data-status', status);
            currentEditingRow.setAttribute('data-customer', customer);
            currentEditingRow.setAttribute('data-project', project);
            currentEditingRow.setAttribute('data-rack', rack);
            currentEditingRow.setAttribute('data-snmp', snmp);

            const statusState = status.includes('Online') ? 'online' : (status.includes('Offline') ? 'offline' : 'maintenance');
            const statusPill = statusState === 'online'
                ? '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/80 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Online</span>'
                : (statusState === 'offline' 
                    ? '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-rose-700 bg-rose-50 border border-rose-200/80 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>Offline</span>'
                    : '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-amber-700 bg-amber-50 border border-amber-200/80 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Maintenance</span>');

            currentEditingRow.innerHTML = `
                <td class="py-4 px-5 align-middle">
                    <div class="space-y-1">
                        <span class="font-bold text-slate-900 font-mono tracking-tight text-xs block">${hostname}</span>
                        <span class="inline-block px-2 py-0.5 text-[9px] font-semibold rounded bg-slate-100 text-slate-700 border border-slate-200">${type}</span>
                    </div>
                </td>
                <td class="py-4 px-5 align-middle font-mono text-xs font-semibold text-blue-600 whitespace-nowrap">${ip}</td>
                <td class="py-4 px-5 align-middle whitespace-nowrap">${statusPill}</td>
                <td class="py-4 px-5 align-middle">
                    <div class="space-y-0.5 max-w-xs">
                        <p class="font-bold text-slate-800 text-xs">${customer}</p>
                        <p class="text-slate-500 text-[11px]">${project}</p>
                    </div>
                </td>
                <td class="py-4 px-5 align-middle font-medium text-slate-700 text-xs whitespace-nowrap">${rack}</td>
                <td class="py-4 px-5 align-middle font-mono text-[11px] text-slate-500 whitespace-nowrap">${snmp}</td>
                <td class="py-4 px-5 align-middle text-right whitespace-nowrap">
                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button" title="Edit Data Perangkat" class="btn-edit inline-flex items-center justify-center w-7 h-7 rounded-lg border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-100 active:scale-95 transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                        <button type="button" title="Hapus Perangkat Jaringan" class="btn-delete inline-flex items-center justify-center w-7 h-7 rounded-lg border border-rose-200/90 text-rose-500 hover:text-rose-600 hover:bg-rose-50 active:scale-95 transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </td>
            `;
            attachRowActions(currentEditingRow);
            if (editModal) editModal.classList.add('hidden');
        });
    }

    // ==========================================
    // MODAL 3: HAPUS PERANGKAT JARINGAN
    // ==========================================
    const deleteModal = document.getElementById('deleteDeviceModal');
    const delTargetText = document.getElementById('delDeviceTarget');
    const btnConfirmDelete = document.getElementById('btnConfirmDelete');
    let currentDeletingRow = null;

    document.querySelectorAll('.close-delete-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            if (deleteModal) deleteModal.classList.add('hidden');
        });
    });

    function openDeleteModal(row) {
        currentDeletingRow = row;
        const hostname = row.getAttribute('data-hostname') || 'RTR-CORE-MANDIRI-01';
        const ip = row.getAttribute('data-ip') || '10.200.45.1';
        if (delTargetText) {
            delTargetText.textContent = `${hostname} (${ip})`;
        }
        if (deleteModal) deleteModal.classList.remove('hidden');
    }

    if (btnConfirmDelete) {
        btnConfirmDelete.addEventListener('click', function () {
            if (currentDeletingRow) {
                currentDeletingRow.remove();
                currentDeletingRow = null;
                const totalEl = document.getElementById('countTotal');
                if (totalEl && parseInt(totalEl.textContent || '0') > 0) {
                    totalEl.textContent = String(parseInt(totalEl.textContent || '0') - 1);
                }
            }
            if (deleteModal) deleteModal.classList.add('hidden');
        });
    }

    // Attach event listeners to all row action buttons
    function attachRowActions(row) {
        const editBtn = row.querySelector('.btn-edit');
        if (editBtn) {
            editBtn.addEventListener('click', () => openEditModal(row));
        }

        const deleteBtn = row.querySelector('.btn-delete');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', () => openDeleteModal(row));
        }
    }

    document.querySelectorAll('.device-row').forEach(row => {
        attachRowActions(row);
    });
}
