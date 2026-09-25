/**
 * Admin Panel — Master JavaScript (admin.js)
 * Mengelola sidebar, modal, header date/dropdown, flash alerts, serta aksi CRUD.
 */

// ── Sidebar Toggle ──
function toggleSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (!sidebar) return;
    sidebar.classList.toggle('open');
    if (overlay) overlay.classList.toggle('show');
    document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
}

// Close sidebar on resize to desktop
window.addEventListener('resize', () => {
    if (window.innerWidth > 1024) {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (sidebar) sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('show');
        document.body.style.overflow = '';
    }
});

// ── Modal Helpers ──
function openModal(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.remove('show');
        document.body.style.overflow = '';
    }
}

// Close modal when clicking overlay
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('show');
        document.body.style.overflow = '';
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.show').forEach(el => {
            el.classList.remove('show');
        });
        document.body.style.overflow = '';
    }
});

// ── Header Date & Dropdown Init ──
document.addEventListener('DOMContentLoaded', function () {
    // Tanggal dinamis di header
    const headerDate = document.getElementById('headerDate');
    if (headerDate) {
        const now = new Date();
        headerDate.textContent = now.toLocaleDateString('id-ID', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
    }

    // Toggle dropdown user
    const trigger = document.getElementById('userDropdownTrigger');
    const dropdown = document.getElementById('userDropdown');
    if (trigger && dropdown) {
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('show');
            trigger.classList.toggle('active');
        });
        document.addEventListener('click', () => {
            dropdown.classList.remove('show');
            trigger.classList.remove('active');
        });
    }

    // Auto hide flash alerts
    const alerts = document.querySelectorAll('.flash-alert');
    alerts.forEach(el => {
        setTimeout(() => {
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        }, 4000);
    });
});

// ── Client-side Table Search ──
function initTableSearch(inputId, tableId) {
    const input = document.getElementById(inputId);
    const tbody = document.querySelector('#' + tableId + ' tbody');
    if (!input || !tbody) return;
    input.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        tbody.querySelectorAll('tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
        updateEmptyState(tableId);
    });
}

function updateEmptyState(tableId) {
    const tbody = document.querySelector('#' + tableId + ' tbody');
    const existingEmpty = document.getElementById(tableId + '-empty');
    const visible = [...tbody.querySelectorAll('tr')].filter(tr => tr.style.display !== 'none');
    if (visible.length === 0) {
        if (!existingEmpty) {
            const tr = document.createElement('tr');
            tr.id = tableId + '-empty';
            tr.innerHTML = '<td colspan="99"><div class="table-empty"><i class="fa-solid fa-magnifying-glass"></i><p>Tidak ada data yang cocok.</p></div></td>';
            tbody.appendChild(tr);
        }
    } else {
        if (existingEmpty) existingEmpty.remove();
    }
}

// =========================================================
// CRUD MODAL & ALERT ACTIONS
// =========================================================

// ── 1. Perusahaan ──
function editPerusahaan(data) {
    if (!data) return;
    const fields = [
        ['edit_id', data.id],
        ['edit_nama_perusahaan', data.nama_perusahaan],
        ['edit_sektor_bidang', data.sektor_bidang],
        ['edit_jurusan', data.jurusan],
        ['edit_alamat', data.alamat],
        ['edit_penanggung_jawab', data.penanggung_jawab],
        ['edit_no_telepon', data.no_telepon],
        ['edit_status_mou', data.status_mou]
    ];
    fields.forEach(([id, val]) => {
        const el = document.getElementById(id);
        if (el) el.value = val || '';
    });
    openModal('modalEdit');
}

function hapusPerusahaan(id, nama_perusahaan) {
    if (typeof Swal === 'undefined') {
        if (confirm(`Hapus perusahaan ${nama_perusahaan}?`)) {
            document.getElementById('hapus_id').value = id;
            document.getElementById('formHapus').submit();
        }
        return;
    }
    Swal.fire({
        title: 'Hapus Perusahaan?',
        html: `Data <strong>${nama_perusahaan}</strong> akan dihapus beserta semua lowongan terkait!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('hapus_id').value = id;
            document.getElementById('formHapus').submit();
        }
    });
}

// ── 2. Lowongan Kerja ──
function editLoker(d) {
    if (!d) return;
    const fields = [
        ['e_id', d.id],
        ['e_perusahaan_id', d.perusahaan_id],
        ['e_judul_posisi', d.judul_posisi],
        ['e_deskripsi_pekerjaan', d.deskripsi_pekerjaan],
        ['e_kuota', d.kuota],
        ['e_batas_pendaftaran', d.batas_pendaftaran],
        ['e_status_loker', d.status_loker]
    ];
    fields.forEach(([id, val]) => {
        const el = document.getElementById(id);
        if (el) el.value = val || '';
    });
    openModal('modalEdit');
}

function hapusLoker(id, nama) {
    if (typeof Swal === 'undefined') {
        if (confirm(`Hapus lowongan ${nama}?`)) {
            document.getElementById('hapus_id').value = id;
            document.getElementById('formHapus').submit();
        }
        return;
    }
    Swal.fire({
        title: 'Hapus Lowongan?',
        html: `Lowongan <strong>${nama}</strong> akan dihapus permanen!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('hapus_id').value = id;
            document.getElementById('formHapus').submit();
        }
    });
}

// ── 3. Users / Pengguna ──
function editUser(d) {
    if (!d) return;
    const fields = [
        ['e_id_user', d.id_user],
        ['e_nama_lengkap', d.nama_lengkap],
        ['e_jabatan', d.jabatan || 'Staf Humas'],
        ['e_username', d.username]
    ];
    fields.forEach(([id, val]) => {
        const el = document.getElementById(id);
        if (el) el.value = val || '';
    });
    openModal('modalEdit');
}

function hapusUser(id, nama) {
    if (typeof Swal === 'undefined') {
        if (confirm(`Hapus akun ${nama}?`)) {
            document.getElementById('hapus_id').value = id;
            document.getElementById('formHapus').submit();
        }
        return;
    }
    Swal.fire({
        title: 'Hapus User?',
        html: `Akun <strong>${nama}</strong> akan dihapus permanen!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('hapus_id').value = id;
            document.getElementById('formHapus').submit();
        }
    });
}

// ── 4. Siswa ──
function editSiswa(d) {
    if (!d) return;
    const fields = [
        ['e_id', d.id],
        ['e_nisn', d.nisn],
        ['e_nama_siswa', d.nama_siswa],
        ['e_kelas', d.kelas],
        ['e_jurusan', d.jurusan]
    ];
    fields.forEach(([id, val]) => {
        const el = document.getElementById(id);
        if (el) el.value = val || '';
    });
    const chk = document.getElementById('e_status_alumni');
    if (chk) chk.checked = Number(d.status_alumni) === 1;
    openModal('modalEdit');
}

function hapusSiswa(id, nama) {
    if (typeof Swal === 'undefined') {
        if (confirm(`Hapus siswa ${nama}?`)) {
            document.getElementById('hapus_id').value = id;
            document.getElementById('formHapus').submit();
        }
        return;
    }
    Swal.fire({
        title: 'Hapus Siswa?',
        html: `Data <strong>${nama}</strong> akan dihapus!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('hapus_id').value = id;
            document.getElementById('formHapus').submit();
        }
    });
}

// ── 5. PKL Penempatan ──
function editPkl(data) {
    if (!data) return;
    const fields = [
        ['e_id', data.id],
        ['e_nama_siswa', data.nama_siswa || ''],
        ['e_nama_perusahaan', data.nama_perusahaan || ''],
        ['e_pembimbing', data.pembimbing || ''],
        ['e_tanggal_mulai', data.tanggal_mulai || ''],
        ['e_tanggal_selesai', data.tanggal_selesai || ''],
        ['e_status_penempatan', data.status_penempatan || 'Draft']
    ];
    fields.forEach(([id, val]) => {
        const el = document.getElementById(id);
        if (el) el.value = val || '';
    });
    openModal('modalEdit');
}

function hapusPkl(id, nama) {
    if (typeof Swal === 'undefined') {
        if (confirm(`Hapus data PKL ${nama}?`)) {
            document.getElementById('hapus_id').value = id;
            document.getElementById('formHapus').submit();
        }
        return;
    }
    Swal.fire({
        title: 'Hapus Data PKL?',
        html: `Data PKL untuk <strong>${nama}</strong> akan dihapus!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('hapus_id').value = id;
            document.getElementById('formHapus').submit();
        }
    });
}

// ── 6. Tracer Study ──
function editTracer(data) {
    if (!data) return;
    const fields = [
        ['e_id', data.id],
        ['e_nama_siswa', data.nama_siswa || ''],
        ['e_tahun_lulus', data.tahun_lulus || ''],
        ['e_status_alumni', data.status_alumni || ''],
        ['e_nama_instansi', data.nama_instansi || ''],
        ['e_pendapatan_bulanan', data.pendapatan_bulanan || '']
    ];
    fields.forEach(([id, val]) => {
        const el = document.getElementById(id);
        if (el) el.value = val || '';
    });
    openModal('modalEdit');
}

function hapusTracer(id, nama) {
    if (typeof Swal === 'undefined') {
        if (confirm(`Hapus data tracer ${nama}?`)) {
            document.getElementById('hapus_id').value = id;
            document.getElementById('formHapus').submit();
        }
        return;
    }
    Swal.fire({
        title: 'Hapus Data Tracer?',
        html: `Data tracer study <strong>${nama}</strong> akan dihapus!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('hapus_id').value = id;
            document.getElementById('formHapus').submit();
        }
    });
}
