</main>

<footer class="bg-white border-top py-3 mt-auto">
    <div class="container-fluid px-4 text-center text-muted small">
        &copy; <?= date('Y') ?> Humas SMK - Sistem Informasi Hubungan Masyarakat & Manajemen Praktik Industri
    </div>
</footer>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Konfirmasi hapus umum
    function confirmDelete(message = 'Apakah Anda yakin ingin menghapus data ini?') {
        return confirm(message);
    }
</script>
</body>
</html>
