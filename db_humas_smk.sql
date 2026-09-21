-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 21, 2026 at 03:44 AM
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kontak`
--

INSERT INTO `kontak` (`id_kontak`, `nama`, `email`, `subjek`, `pesan`, `tanggal_kirim`, `status`) VALUES
(1, 'haha', 'kaylanurlela7@gmail.com', 'jkjhuioi', 'nbh', '2026-09-16 14:28:38', 'Sudah Dibaca'),
(2, 'haha', 'kaylanurlela7@gmail.com', 'jkjhuioi', 'nb', '2026-09-16 14:28:50', 'Sudah Dibaca'),
(3, 'haha', 'kaylanurlela7@gmail.com', 'jkjhuioi', 'hg', '2026-09-16 14:35:25', 'Sudah Dibaca'),
(4, 'haha', 'kaylanurlela7@gmail.com', 'jkjhuioi', 'haloww', '2026-09-17 11:21:51', 'Sudah Dibaca');

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(41, 'BAPENDA MAJALENGKA (SAMSAT MAJALENGKA)', 'Instansi Pemerintah / Layanan Publik', 'Teknik Jaringan Komputer dan Telekomunikasi', 'Jl. K.H. Abdul Halim No.88, Majalengka', 'H. Dwi Yudhi Ginanto Rahman, S.P., M.A.P.', '00000', 'Aktif', '2026-09-19 13:35:50', '2026-09-19 13:35:50');

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
(3, 'zahra', '$2y$10$JpwdjwakbmGsQcv3/ZekzODaZ6TBzSEKO8nLog.9zgtFms6/IRlPm', 'kayla', 'Staf Humas', '2026-09-16 05:48:43');

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
