<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Tentukan base URL relatif
$base_path = (strpos($_SERVER['PHP_SELF'], '/form/') !== false) ? '../' : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' - ' : '' ?>Panel Admin Humas SMK</title>
    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --primary-dark: #0a58ca;
            --sidebar-bg: #1e293b;
            --body-bg: #f8fafc;
            --card-border: #e2e8f0;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--body-bg);
            color: #334155;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .nav-link {
            font-weight: 500;
            padding: 0.5rem 0.85rem !important;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .nav-link:hover, .nav-link.active {
            background-color: rgba(255, 255, 255, 0.12);
            color: #38bdf8 !important;
        }
        .card {
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            background-color: #ffffff;
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid var(--card-border);
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
            padding: 1rem 1.25rem;
        }
        .table > :not(caption) > * > * {
            padding: 0.85rem 0.75rem;
            vertical-align: middle;
        }
        .btn {
            border-radius: 8px;
            font-weight: 500;
        }
        .badge {
            font-weight: 500;
            padding: 0.4em 0.75em;
            border-radius: 6px;
        }
        .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .form-label {
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.4rem;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 0.6rem 0.85rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top py-2">
    <div class="container-fluid px-4">
        <a class="navbar-brand text-white d-flex align-items-center gap-2" href="<?= $base_path ?>index.php">
            <span class="badge bg-primary p-2"><i class="fa-solid fa-school"></i></span>
            <span>HUMAS <strong>SMK</strong> <small class="fw-light text-info" style="font-size: 0.75rem;">Admin CRUD</small></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1 ms-lg-3">
                <li class="nav-item">
                    <a class="nav-link text-white-50 <?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active text-info' : '' ?>" href="<?= $base_path ?>index.php">
                        <i class="fa-solid fa-gauge me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50 <?= (strpos($_SERVER['PHP_SELF'], 'perusahaan') !== false) ? 'active text-info' : '' ?>" href="<?= $base_path ?>perusahaan.php">
                        <i class="fa-solid fa-building me-1"></i> Perusahaan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50 <?= (strpos($_SERVER['PHP_SELF'], 'lowongan_kerja') !== false || strpos($_SERVER['PHP_SELF'], 'lowongan-kerja') !== false) ? 'active text-info' : '' ?>" href="<?= $base_path ?>lowongan_kerja.php">
                        <i class="fa-solid fa-briefcase me-1"></i> Lowongan Kerja
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50 <?= (strpos($_SERVER['PHP_SELF'], 'siswa') !== false) ? 'active text-info' : '' ?>" href="<?= $base_path ?>siswa.php">
                        <i class="fa-solid fa-user-graduate me-1"></i> Siswa
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50 <?= (strpos($_SERVER['PHP_SELF'], 'penempatan_pkl') !== false || strpos($_SERVER['PHP_SELF'], 'penempatan-pkl') !== false) ? 'active text-info' : '' ?>" href="<?= $base_path ?>penempatan_pkl.php">
                        <i class="fa-solid fa-id-card-clip me-1"></i> Penempatan PKL
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50 <?= (strpos($_SERVER['PHP_SELF'], 'tracer_study') !== false || strpos($_SERVER['PHP_SELF'], 'tracer-study') !== false) ? 'active text-info' : '' ?>" href="<?= $base_path ?>tracer_study.php">
                        <i class="fa-solid fa-chart-line me-1"></i> Tracer Study
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50 <?= (strpos($_SERVER['PHP_SELF'], 'users') !== false) ? 'active text-info' : '' ?>" href="<?= $base_path ?>users.php">
                        <i class="fa-solid fa-users-gear me-1"></i> Users
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= $base_path ?>../frontend/pages/index.html" target="_blank" class="btn btn-outline-info btn-sm">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Lihat Frontend
                </a>
            </div>
        </div>
    </div>
</nav>

<main class="container-fluid px-4 py-4 flex-grow-1">
