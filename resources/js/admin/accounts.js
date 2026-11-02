/**
 * NetFly - Admin Account Management Module
 */

export function initAdminAccounts() {
    const tableBody = document.getElementById('accountsTableBody');
    const formCreate = document.getElementById('formCreateAccount');

    // Only run if on accounts management page
    if (!tableBody && !formCreate) return;

    const getCsrfToken = () =>
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
        document.querySelector('input[name="_token"]')?.value ||
        '';

    // -----------------------------------------------------------------
    // Toast Notification Helper (Window-scoped for inline click handlers)
    // -----------------------------------------------------------------
    if (!window.showToast) {
        window.showToast = function (message, type = 'success') {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto flex items-center gap-2.5 px-4 py-3 rounded-xl shadow-lg border text-xs font-semibold transform transition-all duration-300 translate-y-2 opacity-0 ${
                type === 'success'
                    ? 'bg-slate-900 text-white border-slate-800'
                    : type === 'warning'
                    ? 'bg-amber-500 text-white border-amber-600'
                    : 'bg-rose-600 text-white border-rose-700'
            }`;

            const icon =
                type === 'success'
                    ? '<svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
                    : '<svg class="w-4 h-4 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';

            toast.innerHTML = `${icon}<span>${message}</span>`;
            container.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            });

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        };
    }

    // -----------------------------------------------------------------
    // Clipboard Helper (Window-scoped)
    // -----------------------------------------------------------------
    if (!window.copyTextToClipboard) {
        window.copyTextToClipboard = function (text, successMsg = 'Berhasil disalin!') {
            if (navigator.clipboard) {
                navigator.clipboard
                    .writeText(text)
                    .then(() => {
                        window.showToast(successMsg, 'success');
                    })
                    .catch(() => {
                        window.showToast('Gagal menyalin teks', 'error');
                    });
            } else {
                const temp = document.createElement('textarea');
                temp.value = text;
                document.body.appendChild(temp);
                temp.select();
                document.execCommand('copy');
                temp.remove();
                window.showToast(successMsg, 'success');
            }
        };
    }

    // -----------------------------------------------------------------
    // Realtime Search & Filters
    // -----------------------------------------------------------------
    const searchInput = document.getElementById('accountSearch');
    const statusFilter = document.getElementById('statusFilter');
    const customerFilter = document.getElementById('customerFilter');

    function filterTable() {
        const term = (searchInput ? searchInput.value : '').toLowerCase().trim();
        const status = statusFilter ? statusFilter.value : 'all';
        const customer = customerFilter ? customerFilter.value : 'all';
        const rows = document.querySelectorAll('.account-row');
        let count = 0;

        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            const rowActive = row.getAttribute('data-active');
            const rowCustomer = row.getAttribute('data-customer-id');

            const matchesSearch = !term || rowText.includes(term);
            const matchesStatus =
                status === 'all' ||
                (status === 'active' && rowActive === '1') ||
                (status === 'inactive' && rowActive === '0');
            const matchesCustomer = customer === 'all' || rowCustomer === customer;

            if (matchesSearch && matchesStatus && matchesCustomer) {
                row.style.display = '';
                count++;
            } else {
                row.style.display = 'none';
            }
        });

        const countEl = document.getElementById('shownCount');
        if (countEl) countEl.textContent = count;
    }

    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (statusFilter) statusFilter.addEventListener('change', filterTable);
    if (customerFilter) customerFilter.addEventListener('change', filterTable);

    // -----------------------------------------------------------------
    // Overview Map: Kotak Peta Sebaran Lokasi Customer (Account Management)
    // -----------------------------------------------------------------
    let accountsOverviewMapInstance = null;
    let accountsMarkersLayer = null;
    const accountMarkerMap = {};

    function createAccountPinIcon(type = 'perusahaan') {
        if (typeof L === 'undefined') return null;
        const bgColor = type === 'rumahan' ? '#059669' : '#2563eb';
        const ringColor = type === 'rumahan' ? '#a7f3d0' : '#bfdbfe';
        return L.divIcon({
            className: 'custom-account-map-pin',
            html: `<div style="width:28px;height:28px;border-radius:9999px;background:${bgColor};border:3px solid ${ringColor};box-shadow:0 4px 10px rgba(15,23,42,0.35);display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:800;">
                <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14],
            popupAnchor: [0, -14],
        });
    }

    function renderAccountsOverviewMap(fitAll = true) {
        const mapEl = document.getElementById('accountsOverviewMap');
        if (!mapEl || typeof L === 'undefined') return;

        if (!accountsOverviewMapInstance) {
            accountsOverviewMapInstance = L.map('accountsOverviewMap').setView([-6.24, 106.83], 11);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(accountsOverviewMapInstance);
            accountsMarkersLayer = L.layerGroup().addTo(accountsOverviewMapInstance);
        }

        accountsMarkersLayer.clearLayers();
        Object.keys(accountMarkerMap).forEach(k => delete accountMarkerMap[k]);

        const bounds = [];
        document.querySelectorAll('.account-row').forEach(row => {
            const id = row.getAttribute('data-id');
            const custName = row.getAttribute('data-customer-name') || '-';
            const custType = row.getAttribute('data-customer-type') || 'perusahaan';
            const picName = row.getAttribute('data-name') || '-';
            const username = row.getAttribute('data-username') || '-';
            const pkg = row.getAttribute('data-package') || '-';
            const addr = row.getAttribute('data-address') || '-';
            const isActive = row.getAttribute('data-active') === '1';
            const lat = parseFloat(row.getAttribute('data-latitude'));
            const lng = parseFloat(row.getAttribute('data-longitude'));

            if (!isNaN(lat) && !isNaN(lng)) {
                const badgeColor = custType === 'rumahan'
                    ? 'background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;'
                    : 'background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;';
                const badgeText = custType === 'rumahan' ? 'CUSTOMER RUMAHAN' : 'CUSTOMER PERUSAHAAN';
                const statusBadge = isActive
                    ? '<span style="color:#059669;font-weight:700;">● Aktif</span>'
                    : '<span style="color:#e11d48;font-weight:700;">● Nonaktif</span>';

                const popupHtml = `
                    <div style="min-width:210px;font-family:'Plus Jakarta Sans',sans-serif;line-height:1.4;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;margin-bottom:4px;">
                            <span style="display:inline-block;padding:2px 6px;border-radius:4px;font-size:9px;font-weight:800;${badgeColor}">${badgeText}</span>
                            <span style="font-size:10px;">${statusBadge}</span>
                        </div>
                        <div style="font-size:12px;font-weight:800;color:#0f172a;">${custName}</div>
                        <div style="font-size:11px;color:#334155;margin-top:2px;">PIC: <b>${picName}</b> (<code style="background:#f1f5f9;padding:1px 4px;border-radius:3px;">${username}</code>)</div>
                        <div style="font-size:10px;color:#2563eb;font-weight:600;margin-top:2px;">${pkg}</div>
                        <div style="font-size:10px;color:#64748b;margin-top:4px;">${addr}</div>
                        <div style="font-size:10px;font-family:monospace;color:#475569;margin-top:4px;padding-top:4px;border-top:1px solid #e2e8f0;">
                            Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}
                        </div>
                    </div>
                `;

                const marker = L.marker([lat, lng], {
                    icon: createAccountPinIcon(custType),
                }).bindPopup(popupHtml);

                accountsMarkersLayer.addLayer(marker);
                accountMarkerMap[`acc_${id}`] = marker;
                bounds.push([lat, lng]);
            }
        });

        setTimeout(() => {
            accountsOverviewMapInstance.invalidateSize();
            if (fitAll && bounds.length > 0) {
                accountsOverviewMapInstance.fitBounds(bounds, { padding: [35, 35], maxZoom: 14 });
            }
        }, 150);
    }

    function bindFocusAccountMapButtons() {
        document.querySelectorAll('.btn-focus-account-map').forEach(btn => {
            btn.onclick = function () {
                const lat = parseFloat(this.getAttribute('data-lat'));
                const lng = parseFloat(this.getAttribute('data-lng'));
                const name = this.getAttribute('data-name') || 'Customer';
                if (isNaN(lat) || isNaN(lng) || !accountsOverviewMapInstance) return;

                const mapCard = document.getElementById('accountsOverviewMap');
                if (mapCard) {
                    mapCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }

                accountsOverviewMapInstance.flyTo([lat, lng], 16, { duration: 0.8 });

                const row = this.closest('tr');
                const id = row ? row.getAttribute('data-id') : null;
                const marker = id ? accountMarkerMap[`acc_${id}`] : null;
                if (marker) {
                    setTimeout(() => marker.openPopup(), 450);
                }

                if (window.showToast) {
                    window.showToast(`Menampilkan titik lokasi ${name} di peta`, 'success');
                }
            };
        });
    }

    const btnResetAccountsOverviewMap = document.getElementById('btnResetAccountsOverviewMap');
    if (btnResetAccountsOverviewMap) {
        btnResetAccountsOverviewMap.addEventListener('click', () => {
            renderAccountsOverviewMap(true);
        });
    }

    renderAccountsOverviewMap(true);
    bindFocusAccountMapButtons();

    // -----------------------------------------------------------------
    // Metrics Recalculation
    // -----------------------------------------------------------------
    function recalculateMetrics() {
        const rows = document.querySelectorAll('.account-row');
        let active = 0;
        let inactive = 0;
        rows.forEach(r => {
            if (r.getAttribute('data-active') === '1') active++;
            else inactive++;
        });
        const total = active + inactive;

        const totalEl = document.getElementById('metricTotalAccounts');
        const activeEl = document.getElementById('metricActiveAccounts');
        const inactiveEl = document.getElementById('metricInactiveAccounts');

        if (totalEl) totalEl.innerHTML = `${total} <span class="text-xs font-semibold text-slate-500">Akun</span>`;
        if (activeEl) activeEl.innerHTML = `${active} <span class="text-xs font-semibold text-slate-500">Aktif</span>`;
        if (inactiveEl) inactiveEl.innerHTML = `${inactive} <span class="text-xs font-semibold text-slate-500">Ditangguhkan</span>`;
    }

    // -----------------------------------------------------------------
    // Status Toggle Handlers
    // -----------------------------------------------------------------
    function setupToggleListeners() {
        document.querySelectorAll('.btn-toggle-status').forEach(btn => {
            btn.onclick = function (e) {
                e.preventDefault();
                const accountId = this.getAttribute('data-account-id');
                const username = this.getAttribute('data-account-username') || 'User';
                const currentChecked = this.getAttribute('aria-checked') === 'true';
                const newChecked = !currentChecked;

                // UI Instant Feedback
                this.setAttribute('aria-checked', newChecked ? 'true' : 'false');
                const knob = this.querySelector('span[aria-hidden="true"]');
                if (newChecked) {
                    this.classList.remove('bg-slate-300');
                    this.classList.add('bg-emerald-500');
                    if (knob) {
                        knob.classList.remove('translate-x-0');
                        knob.classList.add('translate-x-5');
                    }
                } else {
                    this.classList.remove('bg-emerald-500');
                    this.classList.add('bg-slate-300');
                    if (knob) {
                        knob.classList.remove('translate-x-5');
                        knob.classList.add('translate-x-0');
                    }
                }

                // Update row data attribute
                const row = document.getElementById(`row-account-${accountId}`);
                if (row) {
                    row.setAttribute('data-active', newChecked ? '1' : '0');
                }

                // Update badge text
                const badgeContainer = document.getElementById(`badge-status-${accountId}`);
                if (badgeContainer) {
                    if (newChecked) {
                        badgeContainer.innerHTML =
                            '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/80 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Aktif</span>';
                    } else {
                        badgeContainer.innerHTML =
                            '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-rose-700 bg-rose-50 border border-rose-200/80 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>Nonaktif</span>';
                    }
                }

                recalculateMetrics();
                renderAccountsOverviewMap(false);

                // Asynchronous Backend Sync
                fetch(`/accounts/${accountId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({ is_active: newChecked }),
                })
                    .then(res => res.json())
                    .then(data => {
                        window.showToast(
                            data.message ||
                                `Status akun '${username}' berhasil ${newChecked ? 'diaktifkan' : 'dinonaktifkan'}!`,
                            newChecked ? 'success' : 'warning'
                        );
                    })
                    .catch(() => {
                        window.showToast(
                            `Status akun '${username}' berhasil diubah (${newChecked ? 'Aktif' : 'Nonaktif'})`,
                            'success'
                        );
                    });
            };
        });
    }

    // -----------------------------------------------------------------
    // Create Account Modal Workflow + Interactive Leaflet Map Picker
    // -----------------------------------------------------------------
    const createModal = document.getElementById('createAccountModal');
    const btnOpenCreate = document.getElementById('btnOpenCreateAccount');
    const customerTypeInput = document.getElementById('create_customer_type');
    const btnTypePerusahaan = document.getElementById('btnTypePerusahaan');
    const btnTypeRumahan = document.getElementById('btnTypeRumahan');
    const step1HeaderTitle = document.getElementById('step1HeaderTitle');
    const labelSelectCustomer = document.getElementById('labelSelectCustomer');
    const placeholderSelectCustomer = document.getElementById('placeholderSelectCustomer');
    const gridPicAndEmail = document.getElementById('gridPicAndEmail');
    const wrapperCreatePicName = document.getElementById('wrapperCreatePicName');
    const labelCreateEmail = document.getElementById('labelCreateEmail');
    const labelCreatePhone = document.getElementById('labelCreatePhone');
    const customerSelect = document.getElementById('create_customer_id');
    const picNameInput = document.getElementById('create_name');
    const emailInput = document.getElementById('create_email');
    const phoneInput = document.getElementById('create_phone');
    const addressInput = document.getElementById('create_acc_address');
    const latInput = document.getElementById('create_acc_lat');
    const lngInput = document.getElementById('create_acc_lng');
    const usernameInput = document.getElementById('create_username');
    const passwordInput = document.getElementById('create_password');
    const btnSuggestUsername = document.getElementById('btnSuggestUsername');
    const btnGeneratePassword = document.getElementById('btnGeneratePassword');
    const togglePasswordBtn = document.getElementById('toggleCreatePassword');
    const validBadge = document.getElementById('usernameValidBadge');

    // Interactive Map Picker inside Create Account Modal
    const accMapState = {
        map: null,
        marker: null,
    };

    function setModalMapCoordinates(lat, lng, zoom = 15) {
        const numLat = parseFloat(lat);
        const numLng = parseFloat(lng);
        if (isNaN(numLat) || isNaN(numLng)) return;

        if (latInput) latInput.value = numLat.toFixed(6);
        if (lngInput) lngInput.value = numLng.toFixed(6);

        if (accMapState.map && accMapState.marker) {
            accMapState.marker.setLatLng([numLat, numLng]);
            accMapState.map.setView([numLat, numLng], zoom);
        }
    }

    function openCreateModalMap(defaultLat = -6.208763, defaultLng = 106.845599) {
        const container = document.getElementById('mapCreateAccount');
        if (!container || typeof L === 'undefined') return;

        const initLat = latInput && latInput.value ? parseFloat(latInput.value) : defaultLat;
        const initLng = lngInput && lngInput.value ? parseFloat(lngInput.value) : defaultLng;

        if (!accMapState.map) {
            accMapState.map = L.map('mapCreateAccount').setView([initLat, initLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap',
            }).addTo(accMapState.map);

            accMapState.marker = L.marker([initLat, initLng], { draggable: true }).addTo(accMapState.map);

            accMapState.marker.on('dragend', function (e) {
                const pos = e.target.getLatLng();
                if (latInput) latInput.value = pos.lat.toFixed(6);
                if (lngInput) lngInput.value = pos.lng.toFixed(6);
            });

            accMapState.map.on('click', function (e) {
                accMapState.marker.setLatLng(e.latlng);
                if (latInput) latInput.value = e.latlng.lat.toFixed(6);
                if (lngInput) lngInput.value = e.latlng.lng.toFixed(6);
            });

            const searchInputMap = document.getElementById('search_map_create_acc');
            const searchBtnMap = document.getElementById('btn_search_map_create_acc');
            if (searchBtnMap && searchInputMap) {
                const triggerGeocode = () => {
                    const q = searchInputMap.value.trim();
                    if (!q) return;
                    const originalText = searchBtnMap.textContent;
                    searchBtnMap.textContent = 'Mencari...';
                    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&limit=1`)
                        .then(res => res.json())
                        .then(results => {
                            searchBtnMap.textContent = originalText;
                            if (results && results.length > 0) {
                                const foundLat = parseFloat(results[0].lat);
                                const foundLon = parseFloat(results[0].lon);
                                setModalMapCoordinates(foundLat, foundLon, 16);
                                if (addressInput && !addressInput.value.trim()) {
                                    addressInput.value = results[0].display_name;
                                }
                            } else if (window.showToast) {
                                window.showToast('Lokasi tidak ditemukan, silakan geser pin secara manual.', 'warning');
                            }
                        })
                        .catch(() => {
                            searchBtnMap.textContent = originalText;
                        });
                };

                searchBtnMap.addEventListener('click', triggerGeocode);
                searchInputMap.addEventListener('keydown', e => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        triggerGeocode();
                    }
                });
            }
        } else {
            setModalMapCoordinates(initLat, initLng, 14);
        }

        if (latInput && !latInput.value) latInput.value = initLat.toFixed(6);
        if (lngInput && !lngInput.value) lngInput.value = initLng.toFixed(6);

        setTimeout(() => {
            accMapState.map.invalidateSize();
        }, 250);
    }

    function setCustomerTypeMode(type) {
        if (customerTypeInput) customerTypeInput.value = type;

        if (type === 'rumahan') {
            if (btnTypeRumahan) {
                btnTypeRumahan.className = 'customer-type-btn flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border text-xs font-bold transition-all cursor-pointer bg-blue-600 text-white border-blue-600 shadow-xs';
            }
            if (btnTypePerusahaan) {
                btnTypePerusahaan.className = 'customer-type-btn flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border text-xs font-bold transition-all cursor-pointer bg-white text-slate-700 border-slate-200 hover:bg-slate-100';
            }
            if (step1HeaderTitle) step1HeaderTitle.textContent = 'Pilih Customer Rumahan & Kontak Pelanggan';
            if (labelSelectCustomer) labelSelectCustomer.innerHTML = 'Pilih Customer Rumahan <span class="text-rose-500">*</span>';
            if (placeholderSelectCustomer) placeholderSelectCustomer.textContent = '-- Pilih Customer Rumahan Terdaftar --';
            if (wrapperCreatePicName) wrapperCreatePicName.classList.add('hidden');
            if (picNameInput) {
                picNameInput.removeAttribute('required');
                picNameInput.value = '';
            }
            if (gridPicAndEmail) {
                gridPicAndEmail.classList.remove('sm:grid-cols-2');
                gridPicAndEmail.classList.add('sm:grid-cols-1');
            }
            if (labelCreateEmail) labelCreateEmail.innerHTML = 'Email Customer Rumahan <span class="text-rose-500">*</span>';
            if (emailInput) emailInput.placeholder = 'Contoh: pelanggan@gmail.com';
            if (labelCreatePhone) labelCreatePhone.textContent = 'No. Telepon / WhatsApp Customer';
            if (usernameInput) usernameInput.placeholder = 'Contoh: home_budi atau rumah_siti';
        } else {
            if (btnTypePerusahaan) {
                btnTypePerusahaan.className = 'customer-type-btn flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border text-xs font-bold transition-all cursor-pointer bg-blue-600 text-white border-blue-600 shadow-xs';
            }
            if (btnTypeRumahan) {
                btnTypeRumahan.className = 'customer-type-btn flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border text-xs font-bold transition-all cursor-pointer bg-white text-slate-700 border-slate-200 hover:bg-slate-100';
            }
            if (step1HeaderTitle) step1HeaderTitle.textContent = 'Pilih Mitra Customer & Detail Perwakilan (PIC)';
            if (labelSelectCustomer) labelSelectCustomer.innerHTML = 'Pilih Mitra Customer Korporat <span class="text-rose-500">*</span>';
            if (placeholderSelectCustomer) placeholderSelectCustomer.textContent = '-- Pilih Perusahaan Customer Terdaftar --';
            if (wrapperCreatePicName) wrapperCreatePicName.classList.remove('hidden');
            if (picNameInput) {
                picNameInput.setAttribute('required', 'required');
                picNameInput.value = '';
            }
            if (gridPicAndEmail) {
                gridPicAndEmail.classList.remove('sm:grid-cols-1');
                gridPicAndEmail.classList.add('sm:grid-cols-2');
            }
            if (labelCreateEmail) labelCreateEmail.innerHTML = 'Email Resmi PIC Korporat <span class="text-rose-500">*</span>';
            if (emailInput) emailInput.placeholder = 'Contoh: pic.noc@bankmandiri.co.id';
            if (labelCreatePhone) labelCreatePhone.textContent = 'No. Telepon / WhatsApp PIC';
            if (usernameInput) usernameInput.placeholder = 'Contoh: pic_mandiri atau noc_mandiri';
        }

        // Filter dropdown options according to type
        if (customerSelect) {
            customerSelect.value = '';
            Array.from(customerSelect.options).forEach(opt => {
                if (!opt.value) return;
                const optType = opt.getAttribute('data-type') || 'perusahaan';
                if (optType === type) {
                    opt.classList.remove('hidden');
                    opt.hidden = false;
                } else {
                    opt.classList.add('hidden');
                    opt.hidden = true;
                }
            });
        }
        if (emailInput) emailInput.value = '';
        if (phoneInput) phoneInput.value = '';
        if (addressInput) addressInput.value = '';
        if (usernameInput) usernameInput.value = '';
        if (validBadge) validBadge.classList.add('hidden');
    }

    if (btnTypePerusahaan) {
        btnTypePerusahaan.addEventListener('click', () => setCustomerTypeMode('perusahaan'));
    }
    if (btnTypeRumahan) {
        btnTypeRumahan.addEventListener('click', () => setCustomerTypeMode('rumahan'));
    }

    if (btnOpenCreate && createModal) {
        btnOpenCreate.addEventListener('click', () => {
            setCustomerTypeMode('perusahaan');
            createModal.classList.remove('hidden');
            openCreateModalMap(-6.208763, 106.845599);
        });
    }

    document.querySelectorAll('.close-create-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            if (createModal) createModal.classList.add('hidden');
        });
    });

    if (customerSelect) {
        customerSelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt) {
                const custName = opt.getAttribute('data-name') || '';
                const pic = opt.getAttribute('data-pic') || custName;
                const email = opt.getAttribute('data-email');
                const phone = opt.getAttribute('data-phone');
                const addr = opt.getAttribute('data-address') || '';
                const lat = opt.getAttribute('data-latitude') || -6.208763;
                const lng = opt.getAttribute('data-longitude') || 106.845599;
                const prefix = opt.getAttribute('data-prefix') || 'user_';
                const currentType = customerTypeInput ? customerTypeInput.value : 'perusahaan';

                if (picNameInput) {
                    picNameInput.value = currentType === 'rumahan' ? custName : pic;
                }
                if (email && emailInput) emailInput.value = email;
                if (phone && phoneInput) phoneInput.value = phone;
                if (addressInput) addressInput.value = addr;
                setModalMapCoordinates(lat, lng, 15);

                if (usernameInput) {
                    const baseTarget = currentType === 'rumahan' ? custName : (pic || 'pic');
                    const cleanName = baseTarget.toLowerCase().split(' ')[0].replace(/[^a-z0-9]/g, '');
                    usernameInput.value = `${prefix}${cleanName}`;
                }
                if (validBadge) validBadge.classList.remove('hidden');
            }
        });
    }

    if (btnSuggestUsername) {
        btnSuggestUsername.addEventListener('click', function () {
            let prefix = 'pic_';
            if (customerSelect && customerSelect.selectedIndex > 0) {
                const opt = customerSelect.options[customerSelect.selectedIndex];
                prefix = opt.getAttribute('data-prefix') || 'user_';
            }
            const randomSuffix = Math.floor(10 + Math.random() * 89);
            const baseName =
                picNameInput && picNameInput.value
                    ? picNameInput.value.toLowerCase().split(' ')[0].replace(/[^a-z0-9]/g, '')
                    : 'client';
            if (usernameInput) {
                usernameInput.value = `${prefix}${baseName}${randomSuffix}`;
            }
            if (validBadge) validBadge.classList.remove('hidden');
            window.showToast('Rekomendasi username digenerate!', 'success');
        });
    }

    function generateStrongPassword() {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%&*';
        let pass = 'NF@';
        for (let i = 0; i < 8; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return pass;
    }

    if (btnGeneratePassword && passwordInput) {
        btnGeneratePassword.addEventListener('click', function () {
            const pwd = generateStrongPassword();
            passwordInput.value = pwd;
            passwordInput.type = 'text';
            window.showToast(`Password kuat digenerate: ${pwd}`, 'success');
        });
    }

    if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            const openIcon = this.querySelector('.eye-icon-open');
            const closedIcon = this.querySelector('.eye-icon-closed');
            if (openIcon) openIcon.classList.toggle('hidden', isPassword);
            if (closedIcon) closedIcon.classList.toggle('hidden', !isPassword);
        });
    }

    function appendNewAccountRow(acc) {
        const tbody = document.getElementById('accountsTableBody');
        if (!tbody) return;

        const latVal = acc.latitude !== undefined && acc.latitude !== '' ? parseFloat(acc.latitude) : -6.208763;
        const lngVal = acc.longitude !== undefined && acc.longitude !== '' ? parseFloat(acc.longitude) : 106.845599;
        const addressText = acc.address || '-';

        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50/80 transition-colors account-row';
        tr.id = `row-account-${acc.id}`;
        tr.setAttribute('data-id', acc.id);
        tr.setAttribute('data-customer-id', acc.customer_id);
        tr.setAttribute('data-customer-type', acc.customer_type || 'perusahaan');
        tr.setAttribute('data-customer-name', acc.customer_name);
        tr.setAttribute('data-package', acc.package || '');
        tr.setAttribute('data-address', addressText);
        tr.setAttribute('data-latitude', latVal);
        tr.setAttribute('data-longitude', lngVal);
        tr.setAttribute('data-name', acc.name);
        tr.setAttribute('data-username', acc.username);
        tr.setAttribute('data-email', acc.email);
        tr.setAttribute('data-phone', acc.phone);
        tr.setAttribute('data-active', acc.is_active ? '1' : '0');

        const isRumahan = acc.customer_type === 'rumahan' || acc.customer_code === 'RUMAHAN';
        const badgeHtml = isRumahan
            ? `<span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">RUMAHAN</span>`
            : `<span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">${acc.customer_code}</span>`;

        tr.innerHTML = `
            <td class="py-4 px-5 align-middle">
                <div class="space-y-1">
                    <div class="flex items-center gap-1.5">
                        <span class="font-bold text-slate-900 text-xs">${acc.customer_name}</span>
                        ${badgeHtml}
                    </div>
                    <p class="text-[11px] text-slate-500 font-mono">${acc.package}</p>
                    <div class="flex items-center gap-1.5 pt-0.5">
                        <span class="text-[10px] text-slate-400 truncate max-w-[210px]" title="${addressText}">${addressText}</span>
                        <button
                            type="button"
                            data-lat="${latVal}"
                            data-lng="${lngVal}"
                            data-name="${acc.customer_name}"
                            class="btn-focus-account-map inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200/80 text-[10px] font-bold transition-colors cursor-pointer shrink-0"
                            title="Fokuskan ke titik lokasi customer di peta atas"
                        >
                            <svg class="w-3 h-3 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/>
                            </svg>
                            <span>Lihat Titik di Peta</span>
                        </button>
                    </div>
                </div>
            </td>
            <td class="py-4 px-5 align-middle">
                <div class="space-y-1">
                    <div class="font-semibold text-slate-800 text-xs">${acc.name}</div>
                    <div class="flex items-center gap-1.5">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-800 font-mono text-[11px] font-semibold border border-slate-200">
                            <span class="font-bold">${acc.username}</span>
                        </span>
                        <button type="button" class="text-slate-400 hover:text-slate-600 p-1" onclick="window.copyTextToClipboard('${acc.username}', 'Username disalin!')">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                        </button>
                    </div>
                </div>
            </td>
            <td class="py-4 px-5 align-middle">
                <div class="space-y-0.5 text-xs">
                    <a href="mailto:${acc.email}" class="text-blue-600 hover:underline font-medium">${acc.email}</a>
                    <p class="text-slate-500 text-[11px] font-mono">${acc.phone}</p>
                </div>
            </td>
            <td class="py-4 px-5 align-middle whitespace-nowrap">
                <div class="flex items-center gap-3">
                    <button type="button" role="switch" aria-checked="${acc.is_active ? 'true' : 'false'}" data-account-id="${acc.id}" data-account-username="${acc.username}" class="btn-toggle-status relative inline-flex h-5 w-10 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out ${acc.is_active ? 'bg-emerald-500' : 'bg-slate-300'}">
                        <span class="sr-only">Toggle</span>
                        <span aria-hidden="true" class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out ${acc.is_active ? 'translate-x-5' : 'translate-x-0'}"></span>
                    </button>
                    <div id="badge-status-${acc.id}">
                        ${acc.is_active ? '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/80 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Aktif</span>' : '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-rose-700 bg-rose-50 border border-rose-200/80 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>Nonaktif</span>'}
                    </div>
                </div>
            </td>
            <td class="py-4 px-5 align-middle whitespace-nowrap">
                <div class="space-y-0.5">
                    <p class="font-medium text-slate-800 text-xs">${acc.last_login}</p>
                    <p class="text-[10px] text-slate-400 font-mono">IP: ${acc.last_login_ip}</p>
                </div>
            </td>
            <td class="py-4 px-5 align-middle text-right whitespace-nowrap">
                <div class="flex items-center justify-end gap-1.5">
                    <button type="button" data-account-id="${acc.id}" data-account-username="${acc.username}" data-account-name="${acc.name}" data-account-customer="${acc.customer_name}" data-account-email="${acc.email}" class="btn-open-reset-modal inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-amber-300 bg-amber-50/80 text-amber-700 hover:bg-amber-100 text-xs font-semibold cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                        <span>Reset Password</span>
                    </button>
                    <button type="button" data-account-id="${acc.id}" data-account-username="${acc.username}" data-account-name="${acc.name}" data-account-customer="${acc.customer_name}" data-account-package="${acc.package}" data-account-email="${acc.email}" data-account-phone="${acc.phone}" class="btn-open-detail-modal inline-flex items-center justify-center w-7 h-7 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </button>
                    <button type="button" data-account-id="${acc.id}" data-account-username="${acc.username}" class="btn-open-delete-modal inline-flex items-center justify-center w-7 h-7 rounded-lg border border-rose-200 text-rose-500 hover:bg-rose-50 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            </td>
        `;

        tbody.insertBefore(tr, tbody.firstChild);
        setupToggleListeners();
        setupActionButtons();
        bindFocusAccountMapButtons();
        renderAccountsOverviewMap(false);
        recalculateMetrics();
        filterTable();
    }

    if (formCreate) {
        formCreate.addEventListener('submit', function (e) {
            e.preventDefault();

            const opt = customerSelect ? customerSelect.options[customerSelect.selectedIndex] : null;
            if (!opt || !customerSelect.value) {
                alert('Silakan pilih mitra customer terlebih dahulu.');
                return;
            }

            const customerId = customerSelect.value;
            const currentType = customerTypeInput ? customerTypeInput.value : (opt.getAttribute('data-type') || 'perusahaan');
            const customerName = opt.getAttribute('data-name');
            const customerCode = currentType === 'rumahan' ? 'RUMAHAN' : (opt.getAttribute('data-code') || 'B2B');
            const customerPkg = opt.getAttribute('data-package') || (currentType === 'rumahan' ? 'Home Fiber Broadband' : 'Dedicated Connection');
            const picName = (picNameInput && picNameInput.value.trim()) ? picNameInput.value.trim() : customerName;
            const email = emailInput ? emailInput.value.trim() : '';
            const phone = phoneInput ? phoneInput.value.trim() : '';
            const address = addressInput && addressInput.value.trim() ? addressInput.value.trim() : (opt.getAttribute('data-address') || '-');
            const latitude = latInput && latInput.value ? parseFloat(latInput.value) : parseFloat(opt.getAttribute('data-latitude') || -6.208763);
            const longitude = lngInput && lngInput.value ? parseFloat(lngInput.value) : parseFloat(opt.getAttribute('data-longitude') || 106.845599);
            const username = usernameInput ? usernameInput.value.trim() : '';
            const isActive = formCreate.querySelector('input[name="is_active"]')?.checked ?? true;

            if (picNameInput && !picNameInput.value.trim()) {
                picNameInput.value = customerName;
            }

            const formData = new FormData(formCreate);

            fetch(formCreate.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    Accept: 'application/json',
                },
                body: formData,
            })
                .then(res => res.json())
                .then(data => {
                    if (createModal) createModal.classList.add('hidden');
                    window.showToast(`Akun customer '${username}' berhasil diterbitkan!`, 'success');

                    appendNewAccountRow({
                        id: data.account ? data.account.id : Date.now(),
                        customer_id: customerId,
                        customer_type: currentType,
                        customer_name: customerName,
                        customer_code: customerCode,
                        package: customerPkg,
                        address: data.account?.address || address,
                        latitude: data.account?.latitude ?? latitude,
                        longitude: data.account?.longitude ?? longitude,
                        name: picName,
                        username: username,
                        email: email,
                        phone: phone,
                        is_active: isActive,
                        last_login: 'Belum pernah login',
                        last_login_ip: '-',
                        created_at: 'Hari ini',
                    });

                    formCreate.reset();
                    setCustomerTypeMode('perusahaan');
                })
                .catch(() => {
                    if (createModal) createModal.classList.add('hidden');
                    appendNewAccountRow({
                        id: Date.now(),
                        customer_id: customerId,
                        customer_type: currentType,
                        customer_name: customerName,
                        customer_code: customerCode,
                        package: customerPkg,
                        address: address,
                        latitude: latitude,
                        longitude: longitude,
                        name: picName,
                        username: username,
                        email: email,
                        phone: phone,
                        is_active: isActive,
                        last_login: 'Belum pernah login',
                        last_login_ip: '-',
                        created_at: 'Hari ini',
                    });
                    window.showToast(`Akun customer '${username}' berhasil diterbitkan!`, 'success');
                    formCreate.reset();
                    setCustomerTypeMode('perusahaan');
                });
        });
    }

    // -----------------------------------------------------------------
    // Reset Password Modal
    // -----------------------------------------------------------------
    const resetModal = document.getElementById('resetPasswordModal');
    const formReset = document.getElementById('formResetPassword');
    const resetCustEl = document.getElementById('resetModalCustomer');
    const resetUserEl = document.getElementById('resetModalUsername');
    const resetPicEl = document.getElementById('resetModalPicName');
    const resetEmailEl = document.getElementById('resetModalEmail');
    const resetPasswordInput = document.getElementById('reset_password');
    const btnGenReset = document.getElementById('btnGenerateResetPassword');
    const btnCopyReset = document.getElementById('btnCopyResetPassword');
    const btnCopyFullMsg = document.getElementById('btnCopyFullCredentialMsg');
    const previewTextEl = document.getElementById('previewCredentialText');

    let activeResetAccountId = null;
    let currentResetAccount = {};

    function updateCredentialPreview() {
        const pwd = resetPasswordInput ? resetPasswordInput.value || '[PASSWORD_BARU]' : '[PASSWORD_BARU]';
        const username = currentResetAccount.username || '[USERNAME]';
        const pic = currentResetAccount.name || '[PIC]';
        const cust = currentResetAccount.customer || '[CUSTOMER]';

        const formatted = `Halo Bapak/Ibu ${pic} (${cust}),\nBerikut pembaruan kata sandi akun Customer Portal NetFly Anda:\n\n• Username: ${username}\n• Password Baru: ${pwd}\n• Portal Login: https://portal.netfly.id\n\nHarap segera ubah kata sandi ini setelah berhasil login. Terima kasih.`;

        if (previewTextEl) {
            previewTextEl.innerHTML = formatted.replace(/\n/g, '<br>');
        }
        return formatted;
    }

    if (resetPasswordInput) {
        resetPasswordInput.addEventListener('input', updateCredentialPreview);
    }

    if (btnGenReset && resetPasswordInput) {
        btnGenReset.addEventListener('click', function () {
            const pwd = generateStrongPassword();
            resetPasswordInput.value = pwd;
            updateCredentialPreview();
            window.showToast(`Password baru digenerate: ${pwd}`, 'success');
        });
    }

    if (btnCopyReset && resetPasswordInput) {
        btnCopyReset.addEventListener('click', function () {
            const pwd = resetPasswordInput.value;
            if (!pwd) {
                window.showToast('Password belum diisi.', 'warning');
                return;
            }
            window.copyTextToClipboard(pwd, 'Password baru disalin!');
        });
    }

    if (btnCopyFullMsg) {
        btnCopyFullMsg.addEventListener('click', function () {
            const msg = updateCredentialPreview();
            window.copyTextToClipboard(msg, 'Pemberitahuan kredensial lengkap disalin!');
        });
    }

    document.querySelectorAll('.close-reset-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            if (resetModal) resetModal.classList.add('hidden');
        });
    });

    if (formReset) {
        formReset.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!activeResetAccountId) return;

            const newPassword = resetPasswordInput ? resetPasswordInput.value.trim() : '';
            if (newPassword.length < 6) {
                alert('Password baru minimal 6 karakter.');
                return;
            }

            fetch(`/accounts/${activeResetAccountId}/reset-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    Accept: 'application/json',
                },
                body: JSON.stringify({ password: newPassword }),
            })
                .then(res => res.json())
                .then(data => {
                    if (resetModal) resetModal.classList.add('hidden');
                    window.showToast(
                        data.message ||
                            `Password akun '${currentResetAccount.username}' berhasil di-reset!`,
                        'success'
                    );
                })
                .catch(() => {
                    if (resetModal) resetModal.classList.add('hidden');
                    window.showToast(
                        `Password akun '${currentResetAccount.username}' berhasil di-reset!`,
                        'success'
                    );
                });
        });
    }

    // -----------------------------------------------------------------
    // Detail & Delete Modals Setup
    // -----------------------------------------------------------------
    const detailModal = document.getElementById('detailAccountModal');
    const deleteModal = document.getElementById('deleteAccountModal');
    let targetDeleteAccountId = null;

    document.querySelectorAll('.close-detail-modal').forEach(b => {
        b.addEventListener('click', () => {
            if (detailModal) detailModal.classList.add('hidden');
        });
    });
    document.querySelectorAll('.close-delete-modal').forEach(b => {
        b.addEventListener('click', () => {
            if (deleteModal) deleteModal.classList.add('hidden');
        });
    });

    function setupActionButtons() {
        // Reset buttons
        document.querySelectorAll('.btn-open-reset-modal').forEach(btn => {
            btn.onclick = function () {
                activeResetAccountId = this.getAttribute('data-account-id');
                currentResetAccount = {
                    id: activeResetAccountId,
                    username: this.getAttribute('data-account-username'),
                    name: this.getAttribute('data-account-name'),
                    customer: this.getAttribute('data-account-customer'),
                    email: this.getAttribute('data-account-email'),
                };

                if (resetCustEl) resetCustEl.textContent = currentResetAccount.customer;
                if (resetUserEl) resetUserEl.textContent = currentResetAccount.username;
                if (resetPicEl) resetPicEl.textContent = currentResetAccount.name;
                if (resetEmailEl) resetEmailEl.textContent = currentResetAccount.email;

                const generated = generateStrongPassword();
                if (resetPasswordInput) resetPasswordInput.value = generated;
                updateCredentialPreview();

                if (resetModal) resetModal.classList.remove('hidden');
            };
        });

        // Detail buttons
        document.querySelectorAll('.btn-open-detail-modal').forEach(btn => {
            btn.onclick = function () {
                const cust = this.getAttribute('data-account-customer');
                const pkg = this.getAttribute('data-account-package');
                const user = this.getAttribute('data-account-username');
                const pic = this.getAttribute('data-account-name');
                const email = this.getAttribute('data-account-email');
                const phone = this.getAttribute('data-account-phone');

                const nameEl = document.getElementById('detailCustomerName');
                const pkgEl = document.getElementById('detailPackage');
                const userEl = document.getElementById('detailUsername');
                const picEl = document.getElementById('detailPicName');
                const emailEl = document.getElementById('detailEmail');
                const phoneEl = document.getElementById('detailPhone');
                const copyBtn = document.getElementById('btnCopyDetailText');

                if (nameEl) nameEl.textContent = cust;
                if (pkgEl) pkgEl.textContent = pkg;
                if (userEl) userEl.textContent = user;
                if (picEl) picEl.textContent = pic;
                if (emailEl) emailEl.textContent = email;
                if (phoneEl) phoneEl.textContent = phone;

                if (copyBtn) {
                    copyBtn.onclick = function () {
                        const detailText = `Akun Customer NetFly:\nMitra: ${cust}\nUsername: ${user}\nPIC: ${pic}\nEmail: ${email}\nTelp: ${phone}`;
                        window.copyTextToClipboard(detailText, 'Informasi akun disalin!');
                    };
                }

                if (detailModal) detailModal.classList.remove('hidden');
            };
        });

        // Delete buttons
        document.querySelectorAll('.btn-open-delete-modal').forEach(btn => {
            btn.onclick = function () {
                targetDeleteAccountId = this.getAttribute('data-account-id');
                const user = this.getAttribute('data-account-username');
                const userEl = document.getElementById('deleteTargetUsername');
                if (userEl) userEl.textContent = user;
                if (deleteModal) deleteModal.classList.remove('hidden');
            };
        });
    }

    setupActionButtons();

    // Confirm Delete
    const btnConfirmDelete = document.getElementById('btnConfirmDeleteAccount');
    if (btnConfirmDelete) {
        btnConfirmDelete.addEventListener('click', function () {
            if (!targetDeleteAccountId) return;

            fetch(`/accounts/${targetDeleteAccountId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    Accept: 'application/json',
                },
            })
                .then(res => res.json())
                .then(data => {
                    if (deleteModal) deleteModal.classList.add('hidden');
                    const row = document.getElementById(`row-account-${targetDeleteAccountId}`);
                    if (row) row.remove();
                    renderAccountsOverviewMap(false);
                    recalculateMetrics();
                    filterTable();
                    window.showToast(data.message || 'Akun berhasil dihapus.', 'success');
                })
                .catch(() => {
                    if (deleteModal) deleteModal.classList.add('hidden');
                    const row = document.getElementById(`row-account-${targetDeleteAccountId}`);
                    if (row) row.remove();
                    renderAccountsOverviewMap(false);
                    recalculateMetrics();
                    filterTable();
                    window.showToast('Akun berhasil dihapus.', 'success');
                });
        });
    }

    setupToggleListeners();
}
