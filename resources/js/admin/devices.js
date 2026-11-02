/**
 * NetFly Admin - Hardware & Asset Inventory Management Interactivity
 * (Mencakup filter tabel di /devices, konfirmasi hapus di delete_modal.blade.php,
 * serta validasi & auto-compress foto di /devices/create & /devices/{id}/edit)
 */
export function initAdminDevices() {
    // 1. Live Photo Preview + Auto-Compress pada Halaman Create & Edit Barang
    const photoInput = document.getElementById('inputPhoto');
    const photoPreviewImg = document.getElementById('photoPreviewImg');
    const photoPlaceholderIcon = document.getElementById('photoPlaceholderIcon');
    const photoStatusInfo = document.getElementById('photoStatusInfo');

    function formatBytes(bytes) {
        if (bytes < 1024) return `${bytes} B`;
        const kb = bytes / 1024;
        if (kb < 1024) return `${kb.toFixed(1)} KB`;
        return `${(kb / 1024).toFixed(2)} MB`;
    }

    function setPhotoFeedback(text, type = 'muted') {
        if (!photoStatusInfo) return;
        photoStatusInfo.textContent = text;
        photoStatusInfo.className =
            type === 'error'
                ? 'text-[11px] font-semibold text-rose-600 mt-1.5'
                : type === 'success'
                ? 'text-[11px] font-semibold text-emerald-600 mt-1.5'
                : 'text-[11px] text-slate-400 mt-1.5';
    }

    if (photoInput && photoPreviewImg) {
        photoInput.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) return;

            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            const maxRawBytes = 10 * 1024 * 1024; // 10 MB

            if (!allowedTypes.includes(file.type.toLowerCase())) {
                this.value = '';
                setPhotoFeedback('Format tidak didukung. Gunakan file JPG, PNG, atau WEBP.', 'error');
                return;
            }

            if (file.size > maxRawBytes) {
                this.value = '';
                setPhotoFeedback(`Ukuran file terlalu besar (${formatBytes(file.size)}). Maksimal 10 MB.`, 'error');
                return;
            }

            const originalSize = file.size;
            const reader = new FileReader();

            reader.onload = function (ev) {
                const dataUrl = String(ev.target?.result || '');
                photoPreviewImg.src = dataUrl;
                photoPreviewImg.classList.remove('hidden');
                if (photoPlaceholderIcon) {
                    photoPlaceholderIcon.classList.add('hidden');
                }

                // Auto-compress menggunakan Canvas jika ukuran > 150 KB
                const img = new Image();
                img.onload = function () {
                    const maxDim = 800;
                    let width = img.width;
                    let height = img.height;

                    if (width > maxDim || height > maxDim) {
                        if (width > height) {
                            height = Math.round((height * maxDim) / width);
                            width = maxDim;
                        } else {
                            width = Math.round((width * maxDim) / height);
                            height = maxDim;
                        }
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    if (!ctx) return;

                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob(
                        blob => {
                            if (!blob) return;

                            // Gunakan hasil kompresi jika lebih kecil dari ukuran asli
                            if (blob.size < originalSize && typeof DataTransfer !== 'undefined') {
                                const baseName = file.name.replace(/\.[^/.]+$/, '');
                                const compressedFile = new File([blob], `${baseName}.jpg`, {
                                    type: 'image/jpeg',
                                    lastModified: Date.now(),
                                });

                                const dt = new DataTransfer();
                                dt.items.add(compressedFile);
                                photoInput.files = dt.files;

                                setPhotoFeedback(
                                    `✓ Foto siap diunggah (Dikompres otomatis: ${formatBytes(originalSize)} → ${formatBytes(compressedFile.size)})`,
                                    'success'
                                );
                            } else {
                                setPhotoFeedback(
                                    `✓ Foto siap diunggah (${formatBytes(originalSize)})`,
                                    'success'
                                );
                            }
                        },
                        'image/jpeg',
                        0.82
                    );
                };
                img.src = dataUrl;
            };

            reader.readAsDataURL(file);
        });
    }

    // 2. Modal Konfirmasi Hapus Barang Inventaris (delete_modal.blade.php)
    const deleteModal = document.getElementById('deleteInventoryDeviceModal');
    const deleteHostnameEl = document.getElementById('deleteDeviceHostname');
    const deleteForm = document.getElementById('formConfirmDeleteDevice');

    function closeDeleteModal() {
        if (deleteModal) deleteModal.classList.add('hidden');
    }

    document.querySelectorAll('.btn-delete-device').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            const hostname = btn.getAttribute('data-device-hostname') || '--';
            const deleteUrl = btn.getAttribute('data-delete-url') || '';

            if (deleteHostnameEl) deleteHostnameEl.textContent = hostname;
            if (deleteForm && deleteUrl) deleteForm.setAttribute('action', deleteUrl);
            if (deleteModal) deleteModal.classList.remove('hidden');
        });
    });

    document.querySelectorAll('.close-delete-device-modal').forEach(btn => {
        btn.addEventListener('click', closeDeleteModal);
    });

    if (deleteModal) {
        deleteModal.addEventListener('click', e => {
            if (e.target === deleteModal) closeDeleteModal();
        });
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && deleteModal && !deleteModal.classList.contains('hidden')) {
            closeDeleteModal();
        }
    });

    // 3. Realtime Table Filter pada Halaman Daftar Inventaris (/devices)
    const tbody = document.getElementById('devicesTableBody');
    if (!tbody) return;

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
}
