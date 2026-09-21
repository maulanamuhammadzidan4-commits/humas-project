    <?php

    require_once '../../backend/connection.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: index.php#kontak");
        exit;
    }

    $nama   = trim($_POST['nama'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $subjek = trim($_POST['subjek'] ?? '');
    $pesan  = trim($_POST['pesan'] ?? '');

    if ($nama === '' || $email === '' || $subjek === '' || $pesan === '') {
        echo "<script>
            alert('Semua data wajib diisi!');
            window.history.back();
        </script>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
            alert('Format email tidak valid!');
            window.history.back();
        </script>";
        exit;
    }

    try {

        $sql = "INSERT INTO kontak 
                (nama, email, subjek, pesan)
                VALUES (?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $nama,
            $email,
            $subjek,
            $pesan
        ]);

        echo "<script>
            alert('Terima kasih! Pesan Anda berhasil dikirim.');
            window.location.href = '../../index.php#kontak';
        </script>";

    } catch (PDOException $e) {

        echo "Gagal menyimpan pesan: " . $e->getMessage();

    }
    ?>