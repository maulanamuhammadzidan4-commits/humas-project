-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 30, 2026 at 03:17 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_humas_smk`
--
CREATE DATABASE IF NOT EXISTS `db_humas_smk` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `db_humas_smk`;

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE IF NOT EXISTS `berita` (
  `id_berita` int NOT NULL AUTO_INCREMENT,
  `kategori` varchar(50) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `isi` longtext NOT NULL,
  `tujuan` text,
  `manfaat` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_berita`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `berita`
--

INSERT INTO `berita` (`id_berita`, `kategori`, `judul`, `tanggal`, `gambar`, `deskripsi`, `isi`, `tujuan`, `manfaat`, `created_at`) VALUES
(1, 'KEGIATAN', 'Kegiatan Kunjungan Industri Siswa', '2026-09-05', 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=1200&q=80', 'Siswa antusias mengikuti kegiatan kunjungan industri ke pabrik manufaktur modern untuk mengenal standar operasional kerja secara langsung.', 'Dalam rangka memperluas wawasan kejuruan dan kesiapan kerja siswa, Humas SMK menyelenggarakan Kegiatan Kunjungan Industri bagi para siswa. Kegiatan ini bertujuan memberikan gambaran secara langsung mengenai lingkungan industri, penerapan keselamatan dan kesehatan kerja (K3), penggunaan mesin otomatisasi modern, serta etika profesionalisme di dunia kerja. Para siswa berkesempatan melihat alur produksi dari tahap perancangan hingga pengemasan produk akhir.', 'Mengenalkan budaya kerja profesional dan standar industri modern secara langsung.|Memberikan wawasan aplikatif mengenai perkembangan teknologi produksi.|Memotivasi siswa dalam mengasah keterampilan keahlian sesuai standar pasar kerja.', 'Bagi Siswa: Memperoleh gambaran langsung lingkungan kerja nyata dan inspirasi karier.|Bagi Sekolah: Memperbarui acuan standar kompetensi dengan kebutuhan industri terkini.|Bagi Perusahaan: Mengenal potensi dan bakat calon tenaga kerja masa depan.', '2026-09-07 12:06:29'),
(2, 'KERJA SAMA', 'Penandatanganan MoU Kerja Sama Industri', '2026-09-01', 'https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=1200&q=80', 'Sekolah resmi menjalin penandatanganan kemitraan strategis baru dengan perusahaan teknologi untuk penyaluran tenaga kerja lulusan.', 'Humas SMK kembali mengukuhkan kemitraan strategis dengan menandatangani Nota Kesepahaman (MoU) bersama lima perusahaan papan atas bidang teknologi dan manufaktur. Kerjasama ini mencakup penyelarasan kurikulum berbasis industri, pelaksanaan program Guru Tamu, penyediaan kuota Praktik Kerja Lapangan (PKL), serta fasilitasi rekrutmen langsung bagi lulusan SMK yang memenuhi kualifikasi.', 'Menyelaraskan kurikulum pendidikan kejuruan agar sesuai dengan kebutuhan nyata dunia industri (Link and Match).|Meningkatkan persentase penyerapan lulusan SMK di perusahaan mitra.|Memfasilitasi program magang guru dan sertifikasi kompetensi industri.', 'Bagi Siswa: Jaminan penyaluran kerja dan sertifikasi standar industri.|Bagi Sekolah: Peningkatan kualitas lulusan dan pemutakhiran sarana praktek.|Bagi Perusahaan: Efisiensi rekrutmen dengan calon karyawan terampil yang telah tersaring.', '2026-09-07 12:06:29');

-- --------------------------------------------------------

--
-- Table structure for table `kontak`
--

CREATE TABLE IF NOT EXISTS `kontak` (
  `id_kontak` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subjek` varchar(200) NOT NULL,
  `pesan` text NOT NULL,
  `tanggal_kirim` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Belum Dibaca','Sudah Dibaca') DEFAULT 'Belum Dibaca',
  PRIMARY KEY (`id_kontak`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kontak`
--

INSERT INTO `kontak` (`id_kontak`, `nama`, `email`, `subjek`, `pesan`, `tanggal_kirim`, `status`) VALUES
(1, 'haha', 'kaylanurlela7@gmail.com', 'jkjhuioi', 'nbh', '2026-09-16 14:28:38', 'Sudah Dibaca'),
(2, 'haha', 'kaylanurlela7@gmail.com', 'jkjhuioi', 'nb', '2026-09-16 14:28:50', 'Sudah Dibaca'),
(3, 'haha', 'kaylanurlela7@gmail.com', 'jkjhuioi', 'hg', '2026-09-16 14:35:25', 'Sudah Dibaca'),
(4, 'haha', 'kaylanurlela7@gmail.com', 'jkjhuioi', 'haloww', '2026-09-17 11:21:51', 'Sudah Dibaca'),
(5, 'haha', 'kaylanurlela7@gmail.com', 'jkjhuioi', 'jhgfwhryu4wt55y', '2026-09-21 03:58:54', 'Sudah Dibaca'),
(6, 'jihan', 'jihan@gmail.com', 'pkl', 'mau pkl 7 bulan', '2026-09-22 08:51:24', 'Sudah Dibaca');

-- --------------------------------------------------------

--
-- Table structure for table `lowongan_kerja`
--

CREATE TABLE IF NOT EXISTS `lowongan_kerja` (
  `id` int NOT NULL AUTO_INCREMENT,
  `perusahaan_id` int NOT NULL,
  `judul_posisi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi_pekerjaan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kuota` int NOT NULL,
  `batas_pendaftaran` date NOT NULL,
  `status_loker` enum('Buka','Tutup') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Buka',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_loker_perusahaan` (`perusahaan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `perusahaan`
--

CREATE TABLE IF NOT EXISTS `perusahaan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_perusahaan` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sektor_bidang` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jurusan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `penanggung_jawab` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telepon` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_mou` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Proses',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `perusahaan`
--

INSERT INTO `perusahaan` (`id`, `nama_perusahaan`, `sektor_bidang`, `jurusan`, `alamat`, `penanggung_jawab`, `no_telepon`, `status_mou`, `created_at`, `updated_at`) VALUES
(10, 'MY KOMPUTER', 'Instansi Pemerintah / Layanan Publik', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. Raya KH Abdul Halim Tonjong', 'Maya Ulfa Pujianti', '085314812704', 'Aktif', '2026-09-19 11:57:03', '2026-09-19 11:57:03'),
(11, 'KANTOR POS MAJA', 'Instansi Pemerintah / Layanan Publik', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. Raya Talaga - Cikijing, Maja Selatan, Kec. Maja, Kab. Majalengka', 'belum diketahui', '000000', 'Aktif', '2026-09-19 11:59:36', '2026-09-19 11:59:36'),
(12, 'KANTOR DESA MAJA UTARA', 'Instansi Pemerintah / Layanan Publik', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. Pasukan Sindangkasih No.1, Maja Utara, Kec.Maja, Kab.Majalengka', 'Didi Juhari', '089664222353', 'Aktif', '2026-09-19 12:02:33', '2026-09-19 12:02:33'),
(13, 'KANTOR KECAMATAN ARGAPURA', 'Instansi Pemerintah / Layanan Publik', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Sukasari Kidul, Kec.Argapura, Kab.Majalengka', 'Bani Fadilah Rarnandar, S.STP.,M.A.P', '089631435920', 'Aktif', '2026-09-19 12:05:33', '2026-09-19 12:05:33'),
(14, 'DINAS PEKERJAAN UMUM DAN TATA RUANG (PUTR)', 'Instansi Pemerintah / Layanan Publik', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. K.H. Abdul Halim no.99, Munjul, Majalengka Kulon, Kec. Majalengka, Kab. Majalengka', 'belum diketahui', '00000', 'Aktif', '2026-09-19 12:09:59', '2026-09-19 12:09:59'),
(15, 'DP3AKB', 'Instansi Pemerintah / Layanan Publik', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. Ahmad Yani No.40, Majalengka Wetan, Majalengka', 'belum diketahui', '0000000', 'Aktif', '2026-09-19 12:12:12', '2026-09-19 12:12:12'),
(16, 'DPMD MAJALENGKA', 'Instansi Pemerintah / Layanan Publik', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. Ahmad Kusuma No.58, Cicurug, Kec. Majalengka, Kab. Majalengka', 'belum diketahui', '0000000', 'Aktif', '2026-09-19 12:15:06', '2026-09-19 12:15:06'),
(17, 'POWER KOMPUTER', 'Perdagangan / Jasa Service Komputer', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Squre, BI. B No.6, Jatiwangi, Kec. Jatiwangi, Kab. Majalengka', 'Donal', '082318648188', 'Aktif', '2026-09-19 12:18:11', '2026-09-19 12:18:11'),
(18, 'RUMAH CCTV', 'Penyedia Layanan CCTV', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. Raya K.H. Abdul Halim No.379, Majalengka, Kec. Majalengka, Kab. Majalengka', 'Fajar Sunarli', '00000', 'Aktif', '2026-09-19 12:21:24', '2026-09-19 12:21:24'),
(19, 'BKAD MAJALENGKA', 'Instansi Pemerintah / Layanan Publik', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. Jenderal Ahmad Yani No.9, Majalengka ', 'Dr. H. Lalan Soeherlan S., M.Si', '081322882220', 'Aktif', '2026-09-19 12:24:27', '2026-09-19 12:24:27'),
(20, 'LP3I MAJALENGKA', 'Jasa Pendidikan', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. Raya Timur Ciborelang No.06, Kec. Jatiwangi, Kab. Majalengka', 'Ade Deni Nurjaman, S.Pd.', '085722287102', 'Aktif', '2026-09-19 12:28:08', '2026-09-19 12:28:08'),
(21, 'PT. MEGA DATA ARTHA LINTAS DATA', 'Penyedia Layanan Internet', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Blok Jum\'at, Maja Utara, Kec. Maja, kab. Majalengka', 'Indra Indriyanto', '085221484214', 'Aktif', '2026-09-19 12:31:40', '2026-09-19 12:31:40'),
(22, 'DINAS KETENAGAKERJAAN KOPERASI DAN UKM', 'Instansi Pemerintah / Layanan Publik', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. Siti Armilah, Kab. Majalengka', 'H. Arif Daryana, AP.,M.Si', '082117524490', 'Aktif', '2026-09-19 12:36:32', '2026-09-19 12:36:32'),
(23, 'DISDUKCAPIL MAJALENGKA', 'Instansi Pemerintah / Layanan Publik', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. K.H Abdul Halim No.483, Tonjong, Kec. Majalengka', 'H. Ade Saepudin, S.Sos.', '081278134966', 'Aktif', '2026-09-19 12:40:29', '2026-09-19 12:40:29'),
(24, 'ALFANET', 'Perdagangan', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. Raya K.H. Abdul Halim No.176, Majalengka Kulon, Kab. Majalengka', 'Henri Dwi Purnama', '085320212005', 'Aktif', '2026-09-19 12:44:41', '2026-09-19 12:44:41'),
(25, 'CV.MITRA INDEXINDO PRATAMA', 'Perdagangan / Jasa Service Komputer', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. Panjunan, Kec. Lemahwungkuk, Kota Cirebon', 'Beny Izuddin Bakri', '089639522004', 'Aktif', '2026-09-19 12:48:20', '2026-09-19 12:48:20'),
(26, 'CENTRAL COMPUTER CIREBON', 'Perdagangan / Jasa Service Komputer', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. Panjunan No.66A, Panjunan, Kec. Lemahwungkuk, Kota Cirebon', 'Aang Ihsanudin', '081313061641', 'Aktif', '2026-09-19 12:51:48', '2026-09-19 12:51:48'),
(27, 'PARAHYANGAN COMPUTER CIREBON', 'Perdagangan / Jasa Service Komputer', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Cirebon Mall Lantai 1, Jl. Sarief Abdurachman No.159, panjunan, Kec. Lemahwungkuk, Kota Cirebon', 'Eka Agustian', '081229222636', 'Aktif', '2026-09-19 12:55:49', '2026-09-19 12:55:49'),
(28, 'DINAS PERHUBUNGAN KABUPATEN MAJALENGKA', 'Instansi Pemerintah / Layanan Publik', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. Pangeran Muhammad, Simpereum Majalengka, kab. Majalengka', 'belum diketahui', '00000', 'Aktif', '2026-09-19 12:58:32', '2026-09-19 12:58:32'),
(29, 'YOGYA GRAND MAJALENGKA', 'Retail', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. K.H. Abdul Halim, Majalengka Kulon, Kec. Majalengka, Kab. Majalengka', 'Sasmoko Adisantoso, SE,ME,CHRM', '089660415636', 'Aktif', '2026-09-19 13:02:50', '2026-09-19 13:02:50'),
(30, 'SATPOL PP DAN DAMKAR', 'Instansi Pemerintah / Layanan Publik', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. Cigasong - Jatiwangi, Cicenang, Majalengka', 'belum diketahui', '000000', 'Aktif', '2026-09-19 13:05:34', '2026-09-19 13:05:34'),
(31, 'UNIVERSITAS MAJALENGKA', 'Pelayanan Pendidikan', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. K.H. Abdul Halim No.103, Majalengka', 'Dr. Indra A. Budiman, M.Pd.', '085314107394', 'Aktif', '2026-09-19 13:08:46', '2026-09-19 13:08:46'),
(32, 'ARTA FLASH', 'Jasa Layanan Internet', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. Cempaka No.9, Maja Selatan, Kec. Maja, Kab. Majalengka', 'Alek Nandana Wiguna', '081223346665', 'Aktif', '2026-09-19 13:11:53', '2026-09-19 13:11:53'),
(33, 'POLITEKNIK MARDIRA INDONESIA', 'Pelayanan Pendidikan', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. Majalengka Ring Road - Panyingkiran No.080, Kab. Majalengka', 'Enang Rusnandi, S.Pd., M.Kom.', '000000', 'Aktif', '2026-09-19 13:15:33', '2026-09-19 13:15:33'),
(34, 'SKNET', 'Jasa Layanan Internet', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. K.H. Abdul Halim, Pasar Balong', 'Budi Gunawan', '082240998474', 'Aktif', '2026-09-19 13:17:31', '2026-09-19 13:17:31'),
(35, 'FNOTEBOOK_2', 'Perdagangan / Jasa Service Komputer', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. Raya Abdul Halim, Dekat POM Bensin', 'Odi Fahrian. S.T.', '081395399399', 'Aktif', '2026-09-19 13:20:19', '2026-09-19 13:20:19'),
(36, 'FNOTEBOOK_1', 'Perdagangan / Jasa Service Komputer', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. K.H. Abdul Halim, GGM Majalengka', 'Odi Fahrian, S.T.', '081395399399', 'Aktif', '2026-09-19 13:22:09', '2026-09-19 13:22:09'),
(37, 'GALAN KOMPUTER', 'Perdagangan / Jasa Service Komputer', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. Pangeran Muhamad, Rajagaluh, Kec. Rajagaluh, Kab. Majalengka', 'Anto Budianto', '089664949855', 'Aktif', '2026-09-19 13:24:24', '2026-09-19 13:24:24'),
(38, 'MAX-PRO', 'Perdagangan', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. K.H. Abdul Halim, Pasar Balong', 'Henri Dwi Purnama', '085320212005', 'Aktif', '2026-09-19 13:26:23', '2026-09-19 13:26:23'),
(39, 'GISAKA NET', 'Jasa Layanan Internet', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. Ahmad Kusumah, Majalengka Wetan, Kec. Majalengka, Kab. Majalengka', 'Gilang Bhirawa Noraga', '085295644177', 'Aktif', '2026-09-19 13:28:58', '2026-09-19 13:28:58'),
(40, 'MANDIRI NET', 'Jasa Layanan Internet', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Perum Grand Rahayu Resindence Blok G No.11, Simpeureum Cigasong', 'Rudi, M.Pd', '081947331555', 'Aktif', '2026-09-19 13:32:29', '2026-09-19 13:32:29'),
(41, 'BAPENDA MAJALENGKA (SAMSAT MAJALENGKA)', 'Instansi Pemerintah / Layanan Publik', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. K.H. Abdul Halim No.88, Majalengka', 'H. Dwi Yudhi Ginanto Rahman, S.P., M.A.P.', '00000', 'Aktif', '2026-09-19 13:35:50', '2026-09-19 13:35:50'),
(42, 'KUA SUKAHAJI', 'Instansi Pemerintah / Layanan Publik', 'PENGEMBANGAN PERANGKAT LUNAK DAN GIM', 'Jl. Remaja Utara, Desa No.65, Cikoneng, Kec. Sukahaji, Kab. Majalengka', 'Oo Koimudin, S.Ag.', '00000', 'Aktif', '2026-09-21 04:30:30', '2026-09-21 04:30:30'),
(44, 'ABUBA STEAK', 'Perdagangan', 'rpl', 'Jl. Cipete Raya No. 14A, Cilandak, Jakarta Selatan.', 'Rizal Baydillah, S.Hi, MH.', '000', 'Aktif', '2026-09-21 04:33:55', '2026-09-21 04:33:55'),
(45, 'PT. FORIT ASTA SOLUSINDO', 'IT', 'rpl', 'LT. 3 Gedung BITC, Jl. HMS Mintareja Sarjana Hukum, Baros, Kec. Cimahi Tengah, Kota Cimahi, Jawa Barat 40512', 'Hendry Cahya Irawan, S.T., M.T', '082240442749', 'Aktif', '2026-09-21 04:38:27', '2026-09-21 04:38:27'),
(46, 'DINAS KETAHANAN PANGAN PERTANIAN DAN PERIKANAN', 'Instansi Pemerintah / Layanan Publik', 'rpl', 'Jalan Kh. Abdul Halim No.31, Jatipamor, Panyingkiran, Cijati, Kec. Majalengka, Kabupaten Majalengka', 'H. Nana Rohmana S. Sos. M. Si', '085314451100', 'Aktif', '2026-09-21 04:40:31', '2026-09-21 04:40:31'),
(47, 'DPRD MAJALENGKA', 'Instansi Pemerintah / Layanan Publik', 'rpl', 'Jl. Raya K H Abdul Halim No.247, Majalengka Kulon, Kec. Majalengka, Kabupaten Majalengka,', 'Drs. Agus Permana, MP', '08122071969', 'Aktif', '2026-09-21 04:45:53', '2026-09-21 04:45:53'),
(48, 'KANTOR KECAMATAN MAJA', 'Instansi Pemerintah / Layanan Publik', 'rpl', 'Jl. Pasukan Sindangkasih No.4 Maja Kabupaten Majalengka', 'Doni Fardiansyah, S.STP', '081320712021', 'Aktif', '2026-09-25 00:46:27', '2026-09-25 00:46:27'),
(49, 'PERHUTANI MAJALENGKA', 'Instansi Pemerintah / Layanan Publik', 'rpl', 'Jl. Kehutanan No.205, Majalengka Kulon, Kec. Majalengka, Kabupaten Majalengka, Jawa Barat 45411', 'Suparno, S.Hut', '00000', 'Aktif', '2026-09-25 00:48:44', '2026-09-25 00:48:44'),
(50, 'PT. MEGA CENTRAL FINANCE (MCF)', 'Finance', 'rpl', 'Blok Pakuwon Rt. 014 Rw. 04 Kel. Cigasong Kec. Cigasong', 'Aas Laelasari, S.Pd.', '081322397919', 'Aktif', '2026-09-25 00:50:26', '2026-09-25 00:50:26'),
(51, 'DINAS KOMUNIKASI DAN INFORMATIKA MAJALENGKA', 'Instansi Pemerintah / Layanan Publik', 'rpl', 'Jl. Pangeran Muhamad, Simpeureum, Kec. Cigasong, Kabupaten Majalengka', 'belum di ketahui', '00000', 'Aktif', '2026-09-25 00:52:56', '2026-09-25 00:52:56'),
(52, 'FAKULTAS TEKNIK UNIVERSITAS MAJALENGKA', 'Jasa Pendidikan', 'rpl', 'Jl. Raya K H Abdul Halim No.103, Majalengka Kulon, Kec. Majalengka, Kabupaten Majalengka, Jawa Barat 45418', 'Dr. Indra A. Budiman, M.Pd.', '0000', 'Aktif', '2026-09-25 00:54:17', '2026-09-25 00:54:17'),
(53, 'BAWASLU MAJALENGKA', 'Instansi Pemerintah / Layanan Publik', 'rpl', 'Jl. Letkol Abd. Gani No.7, Majalengka Wetan, Kec. Majalengka, Kabupaten Majalengka, Jawa Barat 45418', 'Dede Rosada, S.H., SPd', '0233 (8292244)', 'Aktif', '2026-09-25 00:56:12', '2026-09-25 00:56:12'),
(54, 'DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU (DPMPTSP)', 'Instansi Pemerintah / Layanan Publik', 'rpl', 'Jl. K.H.Abdul Halim No.97, Majalengka Kulon, Kec. Majalengka, Kabupaten Majalengka', 'Johansyah, SE.', '(0233) 8286599', 'Aktif', '2026-09-25 00:58:01', '2026-09-25 00:58:01'),
(55, 'BANK BJB MAJALENGKA', 'Perbankan', 'rpl', 'Jl. Raya K H Abdul Halim No.224, Majalengka Kulon, Kec. Majalengka, Kab. Majalengka, Jawa Barat 45418', 'Marga Budikarsa Gazali', '\'0233 281156', 'Aktif', '2026-09-25 00:59:35', '2026-09-25 00:59:35'),
(56, 'Kepala Dinas Arsip dan Perpustakaan Daerah Kab. Majalengka', 'Instansi Pemerintah / Layanan Publik', 'rpl', 'cicenang, Kec. Cigasong, Kabupaten Majalengka, Jawa Barat 45476', ' Gun Gun Mochamad Dharmadi, S.H., M.Pd.', '00000', 'Aktif', '2026-09-25 01:00:57', '2026-09-25 01:00:57'),
(57, 'KPU MAJALENGKA', 'Instansi Pemerintah / Layanan Publik', 'rpl', 'Jl. Gerakan Koperasi I No.18 Kabupaten Majalengka Jawa Barat 45411', 'Teguh Fajar Putra Utama, M.Pd.', '(0233) 282480', 'Aktif', '2026-09-25 01:02:12', '2026-09-25 01:02:12'),
(58, 'DINAS PENDIDIKAN KABUPATEN MAJALENGKA', 'Instansi Pemerintah / Layanan Publik', 'rpl', 'Majalengka Wetan, Majalengka Sub-District, Majalengka Regency, West Java 45411', 'H. Rd. Muhammad Umar Ma\'ruf, S. Sos., M. Si. ', '0000', 'Aktif', '2026-09-25 01:03:21', '2026-09-25 01:03:21'),
(59, 'DINAS PARIWISATA MAJALENGKA', 'Instansi Pemerintah / Layanan Publik', 'rpl', 'Jl. Raya K H Abdul Halim No.333, Majalengka Wetan, Kec. Majalengka, Kabupaten Majalengka, Jawa Barat 45411', 'Dr. H. IDA HERIYANI, S.K.M., M.H.', '0000', 'Aktif', '2026-09-25 01:04:24', '2026-09-25 01:04:24'),
(60, 'DINAS SOSIAL KABUPATEN MAJALENGKA', 'Instansi Pemerintah / Layanan Publik', 'rpl', 'Jl. K.H. Abdul Halim No. 498 Majalengka', 'Ida Widanengsih, S.Kep., Ners ', '(0233)281122', 'Aktif', '2026-09-25 01:05:31', '2026-09-25 01:05:31'),
(61, 'SERVICE AB ELEKTRONIK', 'Perdagangan & service elektronika', 'te', 'Blok Suka Asih,Desa Banjaran Kec. Maja Kab. Majalengka', 'Abung Kusman', '89685620040', 'Aktif', '2026-09-25 01:06:47', '2026-09-25 01:06:47'),
(62, 'PT. WIJAYA KARYA BETON', 'Produsen konstruksi teknik', 'te', 'Jl. Raya Barat Burujul Kulon Kec. Jatiwangi Kab. Majalengka', 'Erwin Dewata', '082116212212', 'Aktif', '2026-09-25 01:08:44', '2026-09-25 01:08:44'),
(63, 'CALVIN COMPUTER', 'Perdagangan & service elektronika', 'te', 'Jl. Raya Ciborelang No.84 Desa Ciborelang Kec. Jatiwangi Kab. Majalengka', 'Nanda Mawansa', '81220838548', 'Aktif', '2026-09-25 01:11:25', '2026-09-25 01:11:25'),
(64, 'PT. METROPOLITAN JAYA RAYA', 'Jasa transportasi', 'te', 'Desa Tegalsari Kec. Maja Kab. Majalengka', 'Sutisna Sudianto', '0233 284335', 'Aktif', '2026-09-25 01:12:51', '2026-09-25 01:12:51'),
(65, '7SORA CONSULTANT', 'Konsultan audio, perdagangan & service elektronika', 'te', 'Desa Sindang Kec. Sindang Kab. Majalengka', 'Hj. Dian Novita', '087717913966', 'Aktif', '2026-09-25 01:14:13', '2026-09-25 01:14:13'),
(66, 'ENCANG MANDIRI', 'jasa service elektronika', 'te', 'Desa Salagedang Kec. Sukahaji Kab. Majalengka', 'Encang', '089674143899', 'Aktif', '2026-09-25 01:15:38', '2026-09-25 01:15:38'),
(67, 'WARINGIN SERVIS', 'jasa service elektronika', 'te', 'Desa Waringin Kec. Palasah Kab. Majalengka', 'Indra', '085316386779', 'Aktif', '2026-09-25 01:17:18', '2026-09-25 01:17:18'),
(68, 'PURTA VARIASI & AUDIO MOBIL', 'Perdagangan & jasa audio mobil', 'te', 'Jl. Siliwangi No.1, Jatipamor Kec. Panyingkiran Kab. Majalengka', 'Jery', '082214046016', 'Aktif', '2026-09-25 01:18:48', '2026-09-25 01:18:48'),
(69, 'PT. MIWA EKATAMA INDUSTRI', 'Produsen komponen elektronika', 'te', 'Jl. Raya Leuwiliang Baru, Kec. Ligung Kab. Majalengka', 'Suherman', '081324245112', 'Aktif', '2026-09-25 01:20:09', '2026-09-25 01:20:09'),
(70, 'GREENKOOL AC', 'jasa service AC & elektronika', 'te', 'Jl. K.H. Abdul Halim No.479, Tonjong Kec. Cigasong Kab. Majalengka', 'Abdul Gani', '085220844955', 'Aktif', '2026-09-25 01:21:32', '2026-09-25 01:21:32'),
(71, 'PT. RADJASYA GALUH PRATAMA', 'Kontraktor listrik & elektronika', 'te', 'Jl. K.H. Abdul Halim, Munjul Kec. Majalengka Kab. Majalengka', 'Maman Suhatman, S.T.', '082318814000', 'Aktif', '2026-09-25 01:22:48', '2026-09-25 01:22:48'),
(72, 'CERDAS MOTOR', 'Perdagangan & jasa audio mobil', 'te', 'Jl. Pasukan Sindangkasih No.84 Cigasong, Kec. Cigasong Kab. Majalengka', 'Yogi Permana, S.E.', '081395819858', 'Aktif', '2026-09-25 01:24:19', '2026-09-25 01:24:19'),
(73, 'A BURHAN SUBUR ELEKTRONIK', 'jasa service elektronika', 'te', 'Jl. Pejuang, Sindangkasih Kec. Majalengka Kab. Majalengka', 'Dadi Wahdaena', '08122432518', 'Aktif', '2026-09-25 01:25:57', '2026-09-25 01:25:57'),
(74, 'MUTIARA DIESEL', 'jasa service elektronika', 'te', 'Jl. Pejuang, Cicurug Kec. Majalengka Kab. Majalengka', 'Kadim', '085723294409', 'Aktif', '2026-09-25 01:27:43', '2026-09-25 01:27:43'),
(75, 'HECA PRIMA AUDIOWORK', 'Perdagangan & jasa audio mobil', 'te', 'Jl. Raya Pasar Cigasong, Kec. Cigasong Kab. Majalengka', 'Hendra Juniarto', '082318620284', 'Aktif', '2026-09-25 01:29:30', '2026-09-25 01:29:30'),
(76, 'CV. GEMILANG MANDIRI', 'Jasa sound system', 'te', 'Jl. Selapraja, Maja Selatan Kec. Maja Kab. Majalengka', 'Chandra Irawan', '085223331631', 'Aktif', '2026-09-25 01:30:53', '2026-09-25 01:30:53'),
(77, 'FAMILY ELEKTRONIK', 'Perdagangan & service elektronika', 'te', 'Komplek Pasar Maja Selatan, Kec. Maja Kab. Majalengka', 'Eka Rudianto', '08121474492', 'Aktif', '2026-09-25 01:32:05', '2026-09-25 01:32:05'),
(78, 'PT. MOMENTA AGRIKULTURA ', 'Budidaya Pertanian', 'at', 'Jl. Cisaroni,Cikahuripan-Lembang', 'Deddy Suhariyanto, S.P', '081121114443', 'Aktif', '2026-09-25 01:34:26', '2026-09-25 01:34:26'),
(79, 'BBPP LEMBANG ', 'Pusat Pelatihan Pertanian dan Pedesaan Swadaya', 'at', 'Jl. Kayu Ambon No. 82 Desa Kayu Ambon, Kec. Lembang, Kab. Bandung Barat', 'Dr. Ir. Ajat Jatnika, M.Sc. ', '(022) 2786234', 'Aktif', '2026-09-25 01:35:56', '2026-09-25 01:35:56'),
(80, 'P4S TANI MANDIRI ', 'Pusat Pelatihan Pertanian dan Pedesaan Swadaya', 'at', 'Jl. Raya Rajagaluh Desa Pajajar-Sindang', 'H. Jalil', '081324073544', 'Aktif', '2026-09-25 01:37:12', '2026-09-25 01:37:12'),
(81, 'TAMAN BUNGA', 'Budidaya Tanaman Hias', 'at', 'Desa Argalingga-Argapura', 'Dede Anwar Muklar, S.P', '082318370726', 'Aktif', '2026-09-25 01:38:15', '2026-09-25 01:38:15'),
(82, 'SAUNG HIDROPONIK  WA ADI ', 'Pusat Pelatihan Pertanian dan Pedesaan Swadaya', 'at', 'Desa Gunung Manik-Kecamatan Talaga, Kabupaten Majalengka', 'Adi Nuryanto, S.P', '081224557084', 'Aktif', '2026-09-25 01:39:17', '2026-09-25 01:39:17'),
(83, 'P4S AN NABAWIE AGROLESTARI ', 'Pusat Pelatihan Pertanian dan Pedesaan Swadaya', 'at', 'Desa Majasari- Kecamatan Palasah, Kabupaten Majalengka', 'Jajang Ade Rukmana, S.P', '082353819443', 'Aktif', '2026-09-25 01:40:46', '2026-09-25 01:40:46'),
(84, 'P4S OKIGARU PRIANGAN ', 'Pusat Pelatihan Pertanian dan Pedesaan Swadaya', 'at', 'Jl. Salawangi Desa Silihwangi-Bantarujeg', 'Dede Ahmad Ade Robi Amd,. ANT III', '085742607257', 'Aktif', '2026-09-25 01:41:55', '2026-09-25 01:41:55');

-- --------------------------------------------------------

--
-- Table structure for table `pkl_penempatan`
--

CREATE TABLE IF NOT EXISTS `pkl_penempatan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `siswa_id` int NOT NULL,
  `perusahaan_id` int NOT NULL,
  `pembimbing_guru` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_penempatan` enum('Draft','Disetujui','Selesai') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Draft',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_pkl_siswa` (`siswa_id`),
  KEY `fk_pkl_perusahaan` (`perusahaan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pkl_penempatan`
--

INSERT INTO `pkl_penempatan` (`id`, `siswa_id`, `perusahaan_id`, `pembimbing_guru`, `tanggal_mulai`, `tanggal_selesai`, `status_penempatan`, `created_at`, `updated_at`) VALUES
(1, 1, 35, 'JOKOWI', '2026-08-31', '2026-10-09', 'Disetujui', '2026-09-19 13:40:06', '2026-09-19 13:40:06');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE IF NOT EXISTS `siswa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nisn` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_siswa` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelas` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jurusan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_alumni` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nisn` (`nisn`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id`, `nisn`, `nama_siswa`, `kelas`, `jurusan`, `status_alumni`, `created_at`, `updated_at`) VALUES
(1, '1234567890', 'kayla', 'XII RPL 1', 'rpl', 1, '2026-09-16 11:31:26', '2026-09-16 11:31:26');

-- --------------------------------------------------------

--
-- Table structure for table `tracer_study`
--

CREATE TABLE IF NOT EXISTS `tracer_study` (
  `id` int NOT NULL AUTO_INCREMENT,
  `siswa_id` int NOT NULL,
  `tahun_lulus` year NOT NULL,
  `status_alumni` enum('Bekerja','Kuliah','Wirausaha','Mencari Kerja','menikah') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_instansi` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pendapatan_bulanan` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `siswa_id` (`siswa_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tracer_study`
--

INSERT INTO `tracer_study` (`id`, `siswa_id`, `tahun_lulus`, `status_alumni`, `nama_instansi`, `pendapatan_bulanan`, `created_at`, `updated_at`) VALUES
(2, 1, '2000', 'Bekerja', 'PT.HONDA MOTOR', 84764735, '2026-09-19 13:44:46', '2026-09-19 13:44:46');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `jabatan` varchar(50) DEFAULT 'Staf Humas',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `nama_lengkap`, `jabatan`, `created_at`) VALUES
(1, 'admin', 'admin123', 'Administrator Humas', 'Admin', '2026-09-08 09:21:53'),
(3, 'zahra', '250309', 'kayla', 'Staf Humas', '2026-09-16 05:48:43');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lowongan_kerja`
--
ALTER TABLE `lowongan_kerja`
  ADD CONSTRAINT `fk_loker_perusahaan` FOREIGN KEY (`perusahaan_id`) REFERENCES `perusahaan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pkl_penempatan`
--
ALTER TABLE `pkl_penempatan`
  ADD CONSTRAINT `fk_pkl_perusahaan` FOREIGN KEY (`perusahaan_id`) REFERENCES `perusahaan` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pkl_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tracer_study`
--
ALTER TABLE `tracer_study`
  ADD CONSTRAINT `fk_tracer_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
