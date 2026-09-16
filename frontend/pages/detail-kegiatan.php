<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kegiatan Humas | Humas SMK</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- FontAwesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <!-- NAVBAR -->
    <nav>
        <div class="logo">
            <i class="fa-solid fa-graduation-cap"></i>
            HUMAS <span>SMK</span>
        </div>

        <ul class="menu" id="navMenu">
            <li><a href="index.html#beranda">Beranda</a></li>
            <li><a href="index.html#tentang">Tentang</a></li>
            <li><a href="index.html#kegiatan">Kegiatan</a></li>
            <li><a href="index.html#berita">Berita</a></li>
            <li><a href="index.html#staff">Staff</a></li>
            <li><a href="index.html#kontak">Kontak</a></li>
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


    <!-- DETAIL HEADER -->
    <header class="detail-header">
        <div class="detail-header-content">
            <a href="index.html#kegiatan" class="back-btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Kegiatan
            </a>
            <div class="detail-header-title">
                <div class="detail-header-icon" id="detailIcon">
                    <i class="fa-solid fa-circle-info"></i>
                </div>
                <div class="detail-header-text">
                    <span class="section-tag" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24;"
                        id="detailBadge">PROGRAM UNGGULAN</span>
                    <h1 id="detailTitle">Detail Kegiatan Humas</h1>
                    <p id="detailTagline">Informasi lengkap kegiatan dan program kemitraan Humas SMK</p>
                </div>
            </div>
        </div>
    </header>


    <!-- DETAIL MAIN CONTENT -->
    <main class="detail-container">
        <div class="detail-grid">
            <div class="detail-main">
                <img src="" alt="Banner Kegiatan" class="detail-banner-img" id="detailBanner">

                <div class="detail-block">
                    <h3><i class="fa-solid fa-file-lines"></i> Deskripsi Lengkap</h3>
                    <p id="detailDeskripsi">Memuat deskripsi kegiatan...</p>
                </div>

                <div class="detail-block">
                    <h3><i class="fa-solid fa-bullseye"></i> Tujuan Kegiatan</h3>
                    <ul class="detail-check-list" id="detailTujuan">
                        <!-- Dynamic list -->
                    </ul>
                </div>

                <div class="detail-block">
                    <h3><i class="fa-solid fa-medal"></i> Manfaat Utama</h3>
                    <ul class="detail-check-list" id="detailManfaat">
                        <!-- Dynamic list -->
                    </ul>
                </div>

                <div class="detail-block">
                    <h3><i class="fa-solid fa-list-check"></i> Bentuk Kegiatan</h3>
                    <ul class="detail-check-list" id="detailBentuk">
                        <!-- Dynamic list -->
                    </ul>
                </div>
            </div>

            <!-- SIDEBAR -->
            <aside class="detail-sidebar">
                <div class="sidebar-card">
                    <h4><i class="fa-solid fa-circle-info"></i> Informasi Tambahan</h4>
                    <div class="sidebar-info-group">
                        <div class="sidebar-info-item">
                            <div class="sidebar-info-icon"><i class="fa-solid fa-users"></i></div>
                            <div class="sidebar-info-text">
                                <strong>Target Peserta</strong>
                                <span id="detailTarget">Siswa SMK</span>
                            </div>
                        </div>

                        <div class="sidebar-info-item">
                            <div class="sidebar-info-icon"><i class="fa-solid fa-calendar-days"></i></div>
                            <div class="sidebar-info-text">
                                <strong>Frekuensi / Pelaksanaan</strong>
                                <span id="detailFrekuensi">Berkala</span>
                            </div>
                        </div>

                        <div class="sidebar-info-item">
                            <div class="sidebar-info-icon"><i class="fa-solid fa-user-tie"></i></div>
                            <div class="sidebar-info-text">
                                <strong>Penanggung Jawab</strong>
                                <span id="detailPenanggungJawab">Tim Humas SMK</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sidebar-card"
                    style="background: linear-gradient(135deg, var(--primary-navy), var(--primary-slate)); color: white;">
                    <h4 style="color: white; border-color: rgba(255,255,255,0.1);"><i class="fa-solid fa-headset"
                            style="color: var(--brand-gold);"></i> Butuh Informasi?</h4>
                    <p style="font-size: 14px; color: #cbd5e1; margin-bottom: 20px; line-height: 1.6;">
                        Ada pertanyaan mengenai program ini? Hubungi tim Humas SMK untuk konsultasi atau kemitraan.
                    </p>
                    <a href="index.php" class="btn" style="width: 100%; font-size: 14px; padding: 12px;">
                        <i class="fa-solid fa-envelope"></i> Hubungi Humas
                    </a>
                </div>
            </aside>
        </div>
    </main>


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
                    <li><a href="index.html#beranda"><i class="fa-solid fa-chevron-right"></i> Beranda</a></li>
                    <li><a href="index.html#tentang"><i class="fa-solid fa-chevron-right"></i> Tentang Humas</a></li>
                    <li><a href="index.html#kegiatan"><i class="fa-solid fa-chevron-right"></i> Kegiatan</a></li>
                    <li><a href="index.html#berita"><i class="fa-solid fa-chevron-right"></i> Berita Terbaru</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Program Utama</h4>
                <ul class="footer-links">
                    <li><a href="detail-kegiatan.html?id=kunjungan-industri"><i class="fa-solid fa-chevron-right"></i>
                            Kunjungan Industri</a></li>
                    <li><a href="detail-kegiatan.html?id=kerja-sama-industri"><i class="fa-solid fa-chevron-right"></i>
                            Kemitraan Perusahaan</a></li>
                    <li><a href="detail-kegiatan.html?id=pkl"><i class="fa-solid fa-chevron-right"></i> Program Magang
                            PKL</a></li>
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


    <!-- DYNAMIC SCRIPT -->
    <script>
        const kegiatanData = {
            'kunjungan-industri': {
                judul: 'Kunjungan Industri (Industrial Visit)',
                tagline: 'Mengenal Lingkungan, Budaya Kerja, dan Teknologi Modern Secara Langsung',
                icon: 'fa-solid fa-building-user',
                badge: 'PROGRAM UNGGULAN',
                banner: 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=1200&q=80',
                deskripsi: 'Kunjungan Industri merupakan salah satu program tahunan unggulan Humas SMK yang memberikan kesempatan langsung kepada siswa-siswi untuk mendatangi dan mengamati lingkungan operasional dunia industri modern. Melalui kegiatan ini, para siswa dapat melihat secara langsung bagaimana alur kerja profesional, teknologi industri terkini, serta standar keselamatan kerja (K3) diterapkan dalam skala industri yang sesungguhnya.',
                tujuan: [
                    'Memberikan wawasan nyata mengenai alur kerja dan ekosistem industri modern.',
                    'Memotivasi siswa untuk mengembangkan kompetensi sesuai kebutuhan standar industri.',
                    'Membangun kesiapan mental dan wawasan profesionalitas siswa sebelum memasuki dunia kerja.',
                    'Mempererat hubungan sinergis antara pihak SMK dengan perusahaan mitra.'
                ],
                manfaat: [
                    'Bagi Siswa: Mendapatkan pengalaman autentik, wawasan karier, serta inspirasi proyek keahlian.',
                    'Bagi Sekolah: Memperbarui informasi standar kompetensi sesuai perkembangan teknologi industri terkini.',
                    'Bagi Industri: Mengenal calon potensi sumber daya manusia unggul dari SMK sejak dini.'
                ],
                bentuk: [
                    'Pembekalan & Orientasi K3 Industri sebelum keberangkatan.',
                    'Observasi Lapangan & Tur Fasilitas Produksi di lokasi mitra.',
                    'Sesi Diskusi & Tanya Jawab bersama Praktisi / Teknisi Perusahaan.',
                    'Evaluasi & Pembuatan Laporan Kunjungan Industri oleh siswa.'
                ],
                target: 'Siswa Kelas X & XI Seluruh Jurusan',
                frekuensi: 'Berkala Setiap Semester',
                penanggungJawab: 'Divisi Hubungan Industri Humas SMK'
            },
            'kerja-sama-industri': {
                judul: 'Kerja Sama Industri & Link and Match',
                tagline: 'Membangun Sinergi Strategis dengan Perusahaan Mitra Nasional & Multinasional',
                icon: 'fa-solid fa-briefcase',
                badge: 'KEMITRAAN STRATEGIS',
                banner: 'https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=1200&q=80',
                deskripsi: 'Program Kerja Sama Industri adalah pilar utama Humas SMK dalam mewujudkan prinsip Link and Match antara kurikulum pendidikan kejuruan dengan kebutuhan nyata dunia kerja dan dunia industri (DUDI). Humas SMK aktif menjalin Nota Kesepahaman (MoU) dengan berbagai perusahaan multinasional, BUMN, dan industri kreatif dalam pengembangan kelas industri, penyerapan lulusan, penyelarasan kurikulum, hingga sertifikasi kompetensi.',
                tujuan: [
                    'Menyelaraskan kurikulum sekolah dengan perkembangan standar dan teknologi industri terkini.',
                    'Membuka akses jaringan kerja sama dalam bentuk Kelas Industri dan program Guru Tamu.',
                    'Memfasilitasi proses penyerapan dan rekrutmen lulusan SMK secara berkesinambungan.',
                    'Meningkatkan kualitas sarana prasarana praktek sekolah sesuai standar industri.'
                ],
                manfaat: [
                    'Bagi Siswa: Mendapatkan materi pembelajaran yang relevan serta peluang sertifikasi dan kerja di perusahaan mitra.',
                    'Bagi Sekolah: Meningkatkan reputasi institusi dan persentase kebekerjaan lulusan.',
                    'Bagi Industri: Memperoleh tenaga kerja yang sudah terampil dan siap pakai sesuai kebutuhan spesifik perusahaan.'
                ],
                bentuk: [
                    'Penandatanganan MoU & MoA Kemitraan Strategis.',
                    'Penyusunan Kurikulum Bersama (Kelas Industri Mitra).',
                    'Program Guru Tamu (Expert Sharing) dari Praktisi Industri.',
                    'Rekrutmen Bersama & Job Fair Sekolah.'
                ],
                target: 'Institusi Sekolah & Mitra Perusahaan',
                frekuensi: 'Program Berkelanjutan',
                penanggungJawab: 'Koordinator Hubungan Industri & Kemitraan'
            },
            'pkl': {
                judul: 'Praktik Kerja Lapangan (PKL)',
                tagline: 'Pengalaman Kerja Nyata untuk Mencetak Lulusan Siap Kerja & Berdaya Saing',
                icon: 'fa-solid fa-laptop-code',
                badge: 'PENGALAMAN KERJA',
                banner: 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=1200&q=80',
                deskripsi: 'Praktik Kerja Lapangan (PKL) merupakan kegiatan wajib intrakurikuler di mana siswa terjun langsung bekerja di lingkungan perusahaan/industri mitra selama periode tertentu (3 hingga 6 bulan). Humas SMK berperan sebagai fasilitator utama mulai dari pemetaan tempat PKL, proses pengantaran, pembimbingan lapangan, hingga penarikan dan evaluasi nilai PKL siswa bersama pembimbing industri.',
                tujuan: [
                    'Mengaplikasikan teori dan ketrampilan praktek yang telah dipelajari di sekolah ke dalam lingkungan kerja nyata.',
                    'Membentuk etos kerja, disiplin, tanggung jawab, dan etika profesional siswa.',
                    'Memenuhi standar kompetensi kelulusan pendidikan kejuruan SMK.',
                    'Memberikan kesempatan bagi industri untuk menilai potensi siswa sebagai calon pegawai.'
                ],
                manfaat: [
                    'Bagi Siswa: Memiliki pengalaman kerja resmi (portofolio/sertifikat PKL) dan mengasah soft skills komunikasi.',
                    'Bagi Sekolah: Mendapatkan umpan balik mengenai relevansi materi ajaran dari penilaian industri.',
                    'Bagi Industri: Mendapat bantuan tenaga operasional yang terampil dan berpotensi menjadi karyawan tetap.'
                ],
                bentuk: [
                    'Pembekalan Fisik, Mental, dan Etika Kerja PKL.',
                    'Pengantaran & Serah Terima Siswa ke Tempat PKL Mitra.',
                    'Monitoring & Pembimbingan Berkala oleh Guru Pendamping.',
                    'Uji Sertifikasi & Penarikan Siswa PKL.'
                ],
                target: 'Siswa Kelas XI Seluruh Jurusan',
                frekuensi: '3 hingga 6 Bulan (Semester 4/5)',
                penanggungJawab: 'Pokja PKL Humas SMK'
            }
        };

        // Parse query parameter ?id=
        const urlParams = new URLSearchParams(window.location.search);
        const activityId = urlParams.get('id') || 'kunjungan-industri';
        const data = kegiatanData[activityId] || kegiatanData['kunjungan-industri'];

        // Render page elements
        document.title = `${data.judul} | Humas SMK`;
        document.getElementById('detailTitle').textContent = data.judul;
        document.getElementById('detailTagline').textContent = data.tagline;
        document.getElementById('detailBadge').textContent = data.badge;
        document.getElementById('detailIcon').innerHTML = `<i class="${data.icon}"></i>`;
        document.getElementById('detailBanner').src = data.banner;
        document.getElementById('detailBanner').alt = data.judul;
        document.getElementById('detailDeskripsi').textContent = data.deskripsi;
        document.getElementById('detailTarget').textContent = data.target;
        document.getElementById('detailFrekuensi').textContent = data.frekuensi;
        document.getElementById('detailPenanggungJawab').textContent = data.penanggungJawab;

        // Render list function
        function renderList(elementId, items) {
            const container = document.getElementById(elementId);
            container.innerHTML = items.map(item => `<li><i class="fa-solid fa-check"></i> <span>${item}</span></li>`).join('');
        }

        renderList('detailTujuan', data.tujuan);
        renderList('detailManfaat', data.manfaat);
        renderList('detailBentuk', data.bentuk);

        // Mobile Menu Toggle
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
    </script>
</body>

</html>