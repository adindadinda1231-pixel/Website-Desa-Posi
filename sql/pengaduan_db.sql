-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2026 at 06:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pengaduan_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', '$2y$10$F/SQ7r/QXXQPgSht5dYXrefz.blt9InELq5sXqpw/hYjBXyeXglim', '2025-10-29 12:27:07');

-- --------------------------------------------------------

--
-- Table structure for table `page_content`
--

CREATE TABLE `page_content` (
  `id` int(11) NOT NULL,
  `page_name` varchar(50) NOT NULL,
  `content_data` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `page_content`
--

INSERT INTO `page_content` (`id`, `page_name`, `content_data`, `updated_at`, `updated_by`) VALUES
(1, 'about', '{\n    \"hero_title\": \"Tentang Sistem Kami\",\n    \"hero_subtitle\": \"Mengenal lebih dekat sistem pengaduan masyarakat Desa Posi\",\n    \"main_title\": \"Sistem Pengaduan Masyarakat\",\n    \"main_description\": \"Platform digital untuk memudahkan warga Desa Posi, Kecamatan Bua dalam menyampaikan aspirasi, keluhan, dan laporan terkait layanan publik.\",\n    \"info_box\": \"Sistem ini dirancang khusus untuk meningkatkan transparansi dan responsivitas pemerintah desa terhadap kebutuhan masyarakat.\",\n    \"goal_1_title\": \"Transparansi\",\n    \"goal_1_desc\": \"Memudahkan warga melaporkan masalah secara terbuka dan transparan\",\n    \"goal_2_title\": \"Responsif\",\n    \"goal_2_desc\": \"Penanganan laporan yang cepat dan terorganisir\",\n    \"goal_3_title\": \"Akuntabilitas\",\n    \"goal_3_desc\": \"Tracking status laporan secara real-time\",\n    \"goal_4_title\": \"Partisipasi\",\n    \"goal_4_desc\": \"Meningkatkan partisipasi aktif masyarakat dalam pembangunan desa\",\n    \"category_1\": \"Infrastruktur\",\n    \"category_1_desc\": \"Jalan rusak, jembatan, fasilitas umum\",\n    \"category_2\": \"Kesehatan\",\n    \"category_2_desc\": \"Kebersihan lingkungan, posyandu\",\n    \"category_3\": \"Keamanan\",\n    \"category_3_desc\": \"Gangguan ketertiban, keamanan\",\n    \"category_4\": \"Lainnya\",\n    \"category_4_desc\": \"Lainnya\"\n}', '2025-11-09 02:24:23', NULL),
(2, 'contact', '{\n    \"hero_title\": \"Hubungi Kami\",\n    \"hero_subtitle\": \"Kami siap membantu Anda. Jangan ragu untuk menghubungi kami!\",\n    \"email\": \"desa.posi@gmail.com\",\n    \"email_response\": \"1-2 hari kerja\",\n    \"phone\": \"+6281327806639\",\n    \"phone_hours\": \"Senin-Jumat: 08:00-16:00 WITA\",\n    \"whatsapp\": \"6281327806639\",\n    \"whatsapp_hours\": \"Senin-Sabtu: 08:00-20:00 WITA\",\n    \"office_name\": \"Kantor Desa Posi\",\n    \"office_address\": \"Jl. Poros Desa No. 123\",\n    \"office_village\": \"Desa Posi, Kecamatan Bua\",\n    \"office_regency\": \"Kabupaten Luwu, Sulawesi Selatan\",\n    \"office_postal\": \"91991\",\n    \"maps_url\": \"https:\\/\\/www.google.com\\/maps\\/place\\/kantor+desa+posi\\/@-3.1139963,120.1150051,12z\\/data=!3m1!4b1!4m6!3m5!1s0x699dd0bea9d3f11d:0x747ff06ffd7cf799!8m2!3d-3.1139995!4d120.1974084!16s%2Fg%2F11wtjfc7n6?entry=ttu&g_ep=EgoyMDI1MTAyOS4yIKXMDSoASAFQAw%3D%3D\",\n    \"schedule_mon_thu\": \"08:00 - 16:00\",\n    \"schedule_fri\": \"08:00 - 15:00\",\n    \"schedule_sat\": \"09:00 - 12:00\",\n    \"schedule_sat_note\": \"Hanya untuk urusan mendesak\",\n    \"faq_1_q\": \"Berapa lama laporan saya diproses?\",\n    \"faq_1_a\": \"Biasanya 1-3 hari kerja setelah verifikasi\",\n    \"faq_2_q\": \"Apakah bisa laporan anonim?\",\n    \"faq_2_a\": \"Ya, centang opsi \\\"Anonim\\\" saat mengisi form\",\n    \"faq_3_q\": \"Bagaimana cara melacak laporan?\",\n    \"faq_3_a\": \"Gunakan kode Report ID di menu \\\"Lacak\\\"\",\n    \"faq_4_q\": \"Apakah ada biaya?\",\n    \"faq_4_a\": \"Tidak, semua layanan 100% gratis\"\n}', '2025-11-05 12:32:07', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `penduduk`
--

CREATE TABLE `penduduk` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `jk` enum('L','P') NOT NULL,
  `agama` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `report_id` varchar(50) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `kontak` varchar(100) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `isi` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('menunggu','diproses','selesai','ditolak') DEFAULT 'menunggu',
  `note_admin` text DEFAULT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `is_anonim` tinyint(1) DEFAULT 0,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `report_id`, `nama`, `kontak`, `kategori`, `isi`, `foto`, `status`, `note_admin`, `tanggal`, `updated_at`, `is_anonim`, `user_id`) VALUES
(9, 'REP202511019073', 'dinda', 'dinda@gmail.com', 'Keamanan', 'ada maling', NULL, 'selesai', 'iya siap', '2025-11-01 05:07:11', '2025-11-04 11:06:37', 0, NULL),
(12, 'REP202604255441', 'dinda', 'dinda@gmail.com', 'Infrastruktur', 'jalan rusak', NULL, 'menunggu', '', '2026-04-25 06:48:43', '2026-04-25 06:49:21', 0, 7),
(13, 'REP202604254835', 'dinda', 'dinda@gmail.com', 'Keamanan', 'tawuran', NULL, '', NULL, '2026-04-25 07:23:38', NULL, 0, 7),
(16, 'REP202604250348', 'iqfa', 'bangiqfa8@gmail.com', 'Lingkungan', 'sampah', NULL, 'menunggu', '', '2026-04-25 07:29:46', '2026-04-25 07:30:21', 0, 7);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `nomor_induk_kependudukan` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `nama_lengkap` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `password_hash`, `nomor_induk_kependudukan`, `created_at`, `nama_lengkap`) VALUES
(7, '$2y$10$n0u4JP0sBlAoe6yGmz11VOPa2Bdq7PdpNMRSQ5JZfhHh5T/iuEtSm', '1234567890123456', '2026-04-25 09:58:22', 'adinda mayli putri');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `page_content`
--
ALTER TABLE `page_content`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_name` (`page_name`);

--
-- Indexes for table `penduduk`
--
ALTER TABLE `penduduk`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`nama`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `report_id` (`report_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `page_content`
--
ALTER TABLE `page_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `penduduk`
--
ALTER TABLE `penduduk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11988;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
