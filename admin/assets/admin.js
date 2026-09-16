/**
 * Admin Panel — Sidebar Toggle JS
 */
function toggleSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (!sidebar) return;
    sidebar.classList.toggle('open');
    overlay.classList.toggle('show');
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
    if (el) { el.classList.add('show'); document.body.style.overflow = 'hidden'; }
}
function closeModal(id) {
    const el = document.getElementById(id);
    if (el) { el.classList.remove('show'); document.body.style.overflow = ''; }
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

// ── Auto hide flash alerts ──
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.flash-alert');
    alerts.forEach(el => {
        setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 400); }, 4000);
    });
});
