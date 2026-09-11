<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Humas SMK | Hubungan Masyarakat & Kemitraan Industri</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- FontAwesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <!-- NAVBAR -->
    <nav>
        <div class="logo">
            <i class="fa-solid fa-graduation-cap"></i>
            HUMAS <span>SMK</span>
        </div>

        <ul class="menu" id="navMenu">
            <li><a href="#beranda">Beranda</a></li>
            <li><a href="#tentang">Tentang</a></li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Kegiatan
                </a>

                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="#berita">
                            Berita
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="#staff">
                            Staff
                        </a>
                    </li>
                </ul>
            </li>
            <li><a href="#kontak">Kontak</a></li>
            <li>
                <a href="login.php" class="btn-login">
                    <i class="fa-solid fa-right-to-bracket"></i> Login Admin
                </a>
            </li>
        </ul>

        <button class="mobile-toggle" id="menuToggle" aria-label="Toggle Mobile Navigation">
            <i class="fa-solid fa-bars" id="toggleIcon"></i>
        </button>
    </nav>


    <!-- HERO SECTION -->
    <section class="hero" id="beranda">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fa-solid fa-sparkles"></i> Portal Resmi Hubungan Masyarakat SMK
            </div>
            <h1>
                Hubungan Masyarakat <span>SMK</span>
            </h1>
            <p>
                Selamat datang di Website Humas SMK. Sebagai jembatan antara sekolah, siswa,
                orang tua, dunia industri, dan masyarakat, kami berkomitmen membangun komunikasi,
                sinergi, dan kerja sama yang unggul demi mencetak lulusan berdaya saing global.
            </p>
            <div class="hero-actions">
                <a href="#tentang" class="btn">
                    <i class="fa-solid fa-circle-info"></i> Tentang Humas
                </a>
                <a href="#kontak" class="btn btn-outline">
                    <i class="fa-solid fa-paper-plane"></i> Hubungi Kami
                </a>
            </div>
        </div>

        <!-- Floating Stats Banner -->
        <div class="hero-stats">
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <div class="stat-info">
                    <h4>76+</h4>
                    <p>Mitra Industri Aktif</p>
                </div>
            </div>

            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
                <div class="stat-info">
                    <h4>1.200+</h4>
                    <p>Siswa Terfasilitasi PKL</p>
                </div>
            </div>

            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <h4>98%</h4>
                    <p>Penyerapan Kerja Lulusan</p>
                </div>
            </div>
        </div>
    </section>


    <!-- TENTANG HUMAS -->
    <section id="tentang">
        <div class="section-title">
            <span class="section-tag">PERAN & FUNGSI</span>
            <h2>Tentang Humas</h2>
            <p>
                Mengenal lebih dekat peran strategis dan fungsi Humas SMK dalam membangun kemitraan berkelanjutan.
            </p>
        </div>

        <div class="cards">
            <div class="card">
                <div class="icon">
                    <i class="fa-solid fa-handshake-simple"></i>
                </div>
                <h3>Hubungan Industri</h3>
                <p>
                    Membangun dan menjaga hubungan sinergis yang erat antara sekolah dengan berbagai perusahaan,
                    instansi, dan dunia industri terkemuka.
                </p>
            </div>

            <div class="card">
                <div class="icon">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <h3>Informasi Sekolah</h3>
                <p>
                    Menyampaikan publikasi resmi mengenai kegiatan, pencapaian prestasi, dan program-program unggulan
                    sekolah secara transparan kepada publik.
                </p>
            </div>

            <div class="card">
                <div class="icon">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <h3>Program PKL</h3>
                <p>
                    Mengelola koordinasi sekolah dengan dunia usaha/industri dalam penempatan dan pelaksanaan
                    Praktik Kerja Lapangan siswa.
                </p>
            </div>
        </div>
    </section>


    <!-- KEGIATAN HUMAS -->
    <section id="kegiatan" class="kegiatan-section">
        <div class="section-title">
            <span class="section-tag">PROGRAM UNGGULAN</span>
            <h2>Kegiatan Humas</h2>
            <p>
                Berbagai aktivitas dan agenda strategis yang dilaksanakan oleh tim Humas SMK.
            </p>
        </div>

        <div class="cards">
            <div class="card">
                <div class="icon">
                    <i class="fa-solid fa-building-user"></i>
                </div>
                <h3>Kunjungan Industri</h3>
                <p>
                    Kegiatan kunjungan edukatif siswa ke perusahaan mitra untuk mengenali budaya kerja, teknologi,
                    serta standar industri modern secara langsung.
                </p>
            </div>

            <div class="card">
                <div class="icon">
                    <i class="fa-solid fa-briefcase"></i>
                </div>
                <h3>Kerja Sama Industri</h3>
                <p>
                    Menjalin MoU dan kolaborasi strategis bersama mitra industri nasional dan multinasional
                    guna mendukung kurikulum berbasis industri.
                </p>
            </div>

            <div class="card">
                <div class="icon">
                    <i class="fa-solid fa-laptop-code"></i>
                </div>
                <h3>Praktik Kerja Lapangan</h3>
                <p>
                    Mendukung serta mendampingi pelaksanaan PKL siswa agar selaras dengan kompetensi keahlian
                    yang dibutuhkan di dunia kerja modern.
                </p>
            </div>
        </div>
    </section>


    <!-- BERITA TERBARU -->
    <section id="berita">
        <div class="section-title">
            <span class="section-tag">KABAR TERBARU</span>
            <h2>Berita & Informasi</h2>
            <p>
                Informasi terbaru, pengumuman, dan liputan kegiatan dari Humas SMK.
            </p>
        </div>

        <div class="cards">
            <div class="berita-card">
                <div class="berita-img-wrapper">
                    <span class="berita-badge">KEGIATAN</span>
                    <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=800&q=80"
                        alt="Kegiatan Kunjungan Industri">
                </div>
                <div class="berita-content">
                    <small><i class="fa-regular fa-calendar-check"></i> 05 SEPTEMBER 2026</small>
                    <h3>Kegiatan Kunjungan Industri Siswa</h3>
                    <p>
                        Siswa antusias mengikuti kegiatan kunjungan industri ke pabrik manufaktur modern
                        untuk mengenal standar operasional kerja secara langsung.
                    </p>
                    <a href="detail-berita.php?id=1" class="berita-link">Baca Selengkapnya <i
                            class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="berita-card">
                <div class="berita-img-wrapper">
                    <span class="berita-badge">KERJA SAMA</span>
                    <img src="https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=800&q=80"
                        alt="Kerja Sama Industri">
                </div>
                <div class="berita-content">
                    <small><i class="fa-regular fa-calendar-check"></i> 01 SEPTEMBER 2026</small>
                    <h3>Penandatanganan MoU Kerja Sama Industri</h3>
                    <p>
                        Sekolah resmi menjalin penandatanganan kemitraan strategis baru dengan perusahaan teknologi
                        untuk penyaluran tenaga kerja lulusan.
                    </p>
                    <a href="detail_dudi.php?id=2" class="berita-link">Baca Selengkapnya <i
                            class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="berita-card">
                <div class="berita-img-wrapper">
                    <span class="berita-badge">PKL</span>
                    <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=800&q=80"
                        alt="Pelaksanaan PKL">
                </div>
                <div class="berita-content">
                    <small><i class="fa-regular fa-calendar-check"></i> 28 AGUSTUS 2026</small>
                    <h3>Pelepasan Siswa Praktik Kerja Lapangan</h3>
                    <p>
                        Ratusan siswa dilepas secara resmi oleh kepala sekolah untuk memulai program PKL
                        selama 6 bulan di berbagai mitra perusahaan terkemuka.
                    </p>
                    <a href="detail-berita.php?id=3" class="berita-link">Baca Selengkapnya <i
                            class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>


    <!-- STAFF HUMAS -->
    <section id="staff" class="kegiatan-section">
        <div class="section-title">
            <span class="section-tag">TIM STRATEGIS</span>
            <h2>Staff Humas SMK</h2>
            <p>
                Tim profesional yang mengelola komitmen dan hubungan komunikasi publik sekolah.
            </p>
        </div>

        <div class="cards">
            <div class="card staff-card">
                <div class="staff-img-wrapper">
                    <img src="assets/img/pa_rohim.jpg" alt="Kepala Humas">
                </div>
                <h3>Rohim Hermawan, S.Kom</h3>
                <span class="staff-role">Wakasek Humas</span>
                <p>Mengendalikan kebijakan strategis kemitraan dan reputasi publik sekolah.</p>
                <div class="staff-socials">
                    <a href="#" aria-label="Email"><i class="fa-solid fa-envelope"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>

            <div class="card staff-card">
                <div class="staff-img-wrapper">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=400&q=80"
                        alt="Staff Humas 1">
                </div>
                <h3>Nano Setiono, S.T.</h3>
                <span class="staff-role"> Kepala Bursa Kerja Khusus (BKK)</span>
                <p>Mengelola operasional hubungan industri dan penempatan magang siswa.</p>
                <div class="staff-socials">
                    <a href="#" aria-label="Email"><i class="fa-solid fa-envelope"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>

            <div class="card staff-card">
                <div class="staff-img-wrapper">
                    <img src="assets/img/bu_dede.jpg" alt="Staff Humas 2">
                </div>
                <h3>Dede Rasih, S.Pd</h3>
                <span class="staff-role">Bursa Kerja Khusus (BKK)</span>
                <p>Mengelola dokumentasi, portal berita, dan saluran komunikasi resmi sekolah.</p>
                <div class="staff-socials">
                    <a href="#" aria-label="Email"><i class="fa-solid fa-envelope"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>

            <div class="card staff-card">
                <div class="staff-img-wrapper">
                    <img src="assets/img/pa_firman.jpg" alt="Staff Humas 2">
                </div>
                <h3> Firman Herdiana, S.Pd.</h3>
                <span class="staff-role">Bursa Kerja Khusus (BKK)</span>
                <p>Mengelola dokumentasi, portal berita, dan saluran komunikasi resmi sekolah.</p>
                <div class="staff-socials">
                    <a href="#" aria-label="Email"><i class="fa-solid fa-envelope"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>

            <div class="card staff-card">
                <div class="staff-img-wrapper">
                    <img src="assets/img/pa_guntur.jpg" alt="Staff Humas 2">
                </div>
                <h3>Guntur Irfan Haerudin, S.Kom</h3>
                <span class="staff-role">Bursa Kerja Khusus (BKK)</span>
                <p>Mengelola dokumentasi, portal berita, dan saluran komunikasi resmi sekolah.</p>
                <div class="staff-socials">
                    <a href="#" aria-label="Email"><i class="fa-solid fa-envelope"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>

            <div class="card staff-card">
                <div class="staff-img-wrapper">
                    <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=400&q=80"
                        alt="Staff Humas 2">
                </div>
                <h3>Sutanto Wibowo, S.Pd.</h3>
                <span class="staff-role">Bursa Kerja Khusus (BKK)</span>
                <p>Mengelola dokumentasi, portal berita, dan saluran komunikasi resmi sekolah.</p>
                <div class="staff-socials">
                    <a href="#" aria-label="Email"><i class="fa-solid fa-envelope"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </section>


    <!-- KONTAK -->
    <section id="kontak" class="contact-section">
        <div class="section-title">
            <span class="section-tag">KONTAK & LOKASI</span>
            <h2>Hubungi Kami</h2>
            <p>
                Silakan hubungi Humas SMK untuk informasi kemitraan, PKL, atau pertanyaan seputar sekolah.
            </p>
        </div>

        <div class="contact-container">
            <div class="contact-info-card">
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div class="info-text">
                        <h3>Alamat Kantor</h3>
                        <p>
                            Jl. Contoh No. 123, Kabupaten/Kota,<br>
                            Jawa Barat, Indonesia
                        </p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fa-solid fa-phone"></i>
                    </div>
                    <div class="info-text">
                        <h3>Telepon & WhatsApp</h3>
                        <p>
                            0812-3456-7890 / (021) 555-0123
                        </p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div class="info-text">
                        <h3>Email Resmi</h3>
                        <p>
                            humas@smk.sch.id
                        </p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div class="info-text">
                        <h3>Jam Pelayanan</h3>
                        <p>
                            Senin - Jumat: 07.00 - 15.00 WIB<br>
                            (Sabtu & Minggu Libur)
                        </p>
                    </div>
                </div>
            </div>

            <div class="contact-form-card">
                <h3>Kirim Pesan Langsung</h3>
                <form class="contact-form"
                    onsubmit="event.preventDefault(); alert('Terima kasih! Pesan Anda berhasil dikirim.');">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <div class="input-with-icon">
                            <input type="text" id="nama" placeholder="Masukkan nama Anda" required>
                            <i class="fa-solid fa-user"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <div class="input-with-icon">
                            <input type="email" id="email" placeholder="nama@email.com" required>
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="subjek">Subjek Pesan</label>
                        <div class="input-with-icon">
                            <input type="text" id="subjek" placeholder="Contoh: Kemitraan PKL" required>
                            <i class="fa-solid fa-tag"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pesan">Pesan Anda</label>
                        <textarea id="pesan" placeholder="Tuliskan pesan atau pertanyaan Anda di sini..."
                            required></textarea>
                    </div>

                    <button type="submit" class="btn">
                        <i class="fa-solid fa-paper-plane"></i> Kirim Pesan
                    </button>
                </form>
            </div>
        </div>
    </section>


    <!-- FOOTER -->
    <footer>
        <div class="footer-container">
            <div class="footer-col">
                <div class="logo" style="color: white; margin-bottom: 16px;">
                    <i class="fa-solid fa-graduation-cap"></i> HUMAS <span style="color: var(--brand-gold);">SMK</span>
                </div>
                <p>
                    Portal Resmi Hubungan Masyarakat SMK. Berkomitmen membangun sinergi antara dunia pendidikan
                    kejuruan dan dunia kerja secara berkelanjutan.
                </p>
                <div class="footer-socials">
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <h4>Navigasi Cepat</h4>
                <ul class="footer-links">
                    <li><a href="#beranda"><i class="fa-solid fa-chevron-right"></i> Beranda</a></li>
                    <li><a href="#tentang"><i class="fa-solid fa-chevron-right"></i> Tentang Humas</a></li>
                    <li><a href="#kegiatan"><i class="fa-solid fa-chevron-right"></i> Kegiatan</a></li>
                    <li><a href="#berita"><i class="fa-solid fa-chevron-right"></i> Berita Terbaru</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Program Utama</h4>
                <ul class="footer-links">
                    <li><a href="#kegiatan"><i class="fa-solid fa-chevron-right"></i> Kunjungan Industri</a></li>
                    <li><a href="#kegiatan"><i class="fa-solid fa-chevron-right"></i> Kemitraan Perusahaan</a></li>
                    <li><a href="#kegiatan"><i class="fa-solid fa-chevron-right"></i> Program Magang PKL</a></li>
                    <li><a href="login.php"><i class="fa-solid fa-chevron-right"></i> Portal Admin</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Hubungi Kami</h4>
                <p><i class="fa-solid fa-location-dot" style="color: var(--brand-gold);"></i> Jl. Contoh No. 123, Jawa
                    Barat</p>
                <p><i class="fa-solid fa-phone" style="color: var(--brand-gold);"></i> (021) 555-0123</p>
                <p><i class="fa-solid fa-envelope" style="color: var(--brand-gold);"></i> humas@smk.sch.id</p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 Humas SMK. All Rights Reserved.</p>
            <p>Website Hubungan Masyarakat & Kemitraan Industri Sekolah</p>
        </div>
    </footer>

    <!-- Mobile Drawer JavaScript -->
    <script>
        const menuToggle = document.getElementById('menuToggle');
        const navMenu = document.getElementById('navMenu');
        const toggleIcon = document.getElementById('toggleIcon');

        menuToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            if (navMenu.classList.contains('active')) {
                toggleIcon.classList.remove('fa-bars');
                toggleIcon.classList.add('fa-xmark');
            } else {
                toggleIcon.classList.remove('fa-xmark');
                toggleIcon.classList.add('fa-bars');
            }
        });

        // Close mobile drawer when clicking a link
        document.querySelectorAll('.menu a').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                toggleIcon.classList.remove('fa-xmark');
                toggleIcon.classList.add('fa-bars');
            });
        });
    </script>
</body>

</html>