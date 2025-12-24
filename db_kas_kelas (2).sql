-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 24, 2025 at 02:38 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_kas_kelas`
--

-- --------------------------------------------------------

--
-- Table structure for table `agenda`
--

CREATE TABLE `agenda` (
  `id_agenda` int NOT NULL,
  `judul` varchar(100) NOT NULL,
  `deskripsi` text,
  `tanggal` date NOT NULL,
  `dibuat_oleh` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `analysis_todo`
--

CREATE TABLE `analysis_todo` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `status` enum('todo','inprogress','done') NOT NULL DEFAULT 'todo',
  `due_date` date DEFAULT NULL,
  `position` int NOT NULL DEFAULT '0',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `analysis_todo`
--

INSERT INTO `analysis_todo` (`id`, `title`, `description`, `status`, `due_date`, `position`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Alhamdulillah', 'Test', 'todo', '2025-11-30', 1, 1, '2025-11-29 01:26:10', '2025-11-29 13:57:48');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int NOT NULL,
  `tema` varchar(255) NOT NULL,
  `isi` text NOT NULL,
  `pembuat` varchar(100) NOT NULL,
  `tanggal_posting` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `label` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `tema`, `isi`, `pembuat`, `tanggal_posting`, `label`) VALUES
(1, 'PGT Cup', 'Pembayaran Kas harus sesuai tanggal', 'Operator', '2025-11-07 08:12:53', 'PENTING'),
(2, 'Google Dev', 'Studi Banding', 'Operator', '2025-11-20 00:31:40', 'PENTING');

-- --------------------------------------------------------

--
-- Table structure for table `barcode_audit`
--

CREATE TABLE `barcode_audit` (
  `id` int NOT NULL,
  `barcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_user` int DEFAULT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barcode_audit`
--

INSERT INTO `barcode_audit` (`id`, `barcode`, `id_user`, `action`, `ip`, `user_agent`, `extra`, `created_at`) VALUES
(1, 'PAY-31-fa7274', 16, 'view_image', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-27 06:01:38'),
(2, 'PAY-31-fa7274', 16, 'view_image', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-27 06:04:09'),
(3, 'PAY-31-fa7274', 16, 'view_image', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-27 06:28:10'),
(4, 'PAY-31-fa7274', 16, 'view_image', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-27 06:28:26'),
(5, 'PAY-17-9b3c06', 16, 'lookup', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-27 07:38:19'),
(6, 'PAY-17-a8ecff', 16, 'lookup', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-27 08:04:56'),
(7, 'PAY-17-a8ecff', 16, 'lookup', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-27 08:15:37'),
(8, 'PAY-17-aea5d6', 16, 'lookup', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-27 08:28:00'),
(9, 'PAY-17-e7cf44', 16, 'lookup', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-27 09:00:14'),
(10, 'PAY-17-e7cf44', 16, 'lookup', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-27 09:04:55'),
(11, 'PAY-17-e7cf44', 16, 'lookup', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-27 09:05:17'),
(12, 'PAY-17-66c191', 16, 'lookup', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-28 04:28:58'),
(13, 'PAY-17-66c191', 16, 'lookup', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-28 04:29:16'),
(14, 'PAY-17-66c191', 16, 'lookup', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-28 04:29:46'),
(15, 'PAY-17-33d7a2', 17, 'lookup', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-01 07:13:35'),
(16, 'PAY-17-33d7a2', 17, 'lookup', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-01 07:16:02'),
(17, 'PAY-17-aab697', 16, 'lookup', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-01 09:53:59'),
(18, 'PAY-9-114ed2', 16, 'lookup', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-09 05:37:44');

-- --------------------------------------------------------

--
-- Table structure for table `calendar_event`
--

CREATE TABLE `calendar_event` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'other',
  `owner_id` int DEFAULT NULL,
  `participants` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `bg_color` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text_color` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `calendar_event`
--

INSERT INTO `calendar_event` (`id`, `title`, `description`, `start_datetime`, `end_datetime`, `type`, `owner_id`, `participants`, `created_by`, `created_at`, `updated_at`, `bg_color`, `text_color`) VALUES
(2, 'GAVIN', 'Test', '2025-11-28 17:00:00', '2025-11-28 18:00:00', 'meeting', 0, '7', 1, '2025-11-29 07:32:35', '2025-11-29 13:59:22', '#ff0505', '#ffffff');

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `id_chat` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `topic_id` int DEFAULT NULL,
  `pesan` text NOT NULL,
  `waktu` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `chat`
--

INSERT INTO `chat` (`id_chat`, `id_user`, `topic_id`, `pesan`, `waktu`) VALUES
(18, 16, 2, 'hai kak', '2025-11-09 03:32:40'),
(19, 16, 2, 'boleh minta nomer wa nya gak kak', '2025-11-09 03:33:51'),
(20, 16, 2, 'Hai asslamualaikum', '2025-11-13 03:13:54'),
(21, 16, 2, 'hi\n', '2025-11-13 03:36:15'),
(22, 16, 2, 'hai', '2025-11-20 06:10:08'),
(23, 16, 2, 'Assalamualaikum\n', '2025-11-23 14:04:35'),
(24, 16, 2, 'hai', '2025-11-29 11:44:45'),
(25, 1, 2, 'hai juga', '2025-11-29 11:50:44'),
(26, 1, 2, 'halo juga', '2025-11-29 12:02:18'),
(27, 16, 2, 'oke juga nih', '2025-11-29 12:07:12'),
(28, 17, 2, 'hai', '2025-12-01 04:10:21'),
(29, 17, 3, 'Ass', '2025-12-01 05:18:57'),
(30, 1, 3, 'Wass', '2025-12-01 05:19:31'),
(31, 16, 3, 'wass', '2025-12-01 05:20:22'),
(32, 4, 3, 'hai', '2025-12-01 05:59:11'),
(33, 17, 3, 'bang baka2', '2025-12-01 09:56:08'),
(34, 16, 3, 'siap bang hayu bakar', '2025-12-01 09:56:54');

-- --------------------------------------------------------

--
-- Table structure for table `discussion_categories`
--

CREATE TABLE `discussion_categories` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `discussion_categories`
--

INSERT INTO `discussion_categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Umum', 'Diskusi umum tentang apa saja.', '2025-11-09 02:34:09', '2025-11-09 02:34:09'),
(2, 'Pengumuman', 'Pengumuman penting dari operator atau admin.', '2025-11-09 02:34:09', '2025-11-09 02:34:09'),
(4, 'Sport', NULL, '2025-11-23 01:11:17', '2025-11-23 01:13:29');

-- --------------------------------------------------------

--
-- Table structure for table `discussion_posts`
--

CREATE TABLE `discussion_posts` (
  `id` int NOT NULL,
  `topic_id` int NOT NULL,
  `user_id` int NOT NULL,
  `parent_post_id` int DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discussion_topics`
--

CREATE TABLE `discussion_topics` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `category_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `is_pinned` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `discussion_topics`
--

INSERT INTO `discussion_topics` (`id`, `user_id`, `category_id`, `title`, `content`, `is_pinned`, `created_at`, `updated_at`) VALUES
(2, 16, 1, 'PGT CUP', 'Hai wkwkwkkwkwkwkwkwkwkwkwkkwkwkwkwkkwkwkwkwkkwkwk', 1, '2025-11-08 19:39:22', '2025-11-08 19:52:50'),
(3, 16, 1, 'Bakar Tahun Baru', 'P Bakar', 1, '2025-11-30 21:18:54', '2025-11-30 21:18:54');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_kegiatan`
--

CREATE TABLE `jadwal_kegiatan` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `category` varchar(50) NOT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `jadwal_kegiatan`
--

INSERT INTO `jadwal_kegiatan` (`id`, `title`, `description`, `start_datetime`, `end_datetime`, `category`, `created_by`, `created_at`) VALUES
(1, 'Festival Google Developers', 'Pendidikan', '2025-11-30 00:00:00', '2025-11-30 01:00:00', 'umum', 'Operator', '2025-11-20 00:12:03');

-- --------------------------------------------------------

--
-- Table structure for table `kas`
--

CREATE TABLE `kas` (
  `id_kas` int NOT NULL,
  `tanggal` date NOT NULL,
  `jenis` enum('pemasukan','pengeluaran') NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `keterangan` text,
  `id_kategori` int DEFAULT NULL,
  `dibuat_oleh` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kas`
--

INSERT INTO `kas` (`id_kas`, `tanggal`, `jenis`, `jumlah`, `keterangan`, `id_kategori`, `dibuat_oleh`) VALUES
(1, '2025-10-10', 'pemasukan', 15000.00, 'wajib', 1, 2),
(3, '2025-10-13', 'pengeluaran', 20000.00, 'Semir', 3, 16),
(5, '2025-11-05', 'pengeluaran', 28000.00, 'Maintance ', 3, 16),
(6, '2025-11-05', 'pengeluaran', 37500.00, 'Ultah Bu Ita', 3, 16),
(7, '2025-11-17', 'pengeluaran', 23000.00, 'Stop Kontak', 3, 16),
(9, '2025-11-24', 'pengeluaran', 25500.00, 'PGT Cup', 3, 16),
(10, '2025-11-26', 'pengeluaran', 50000.00, 'Futsal Badminton', 5, 16),
(11, '2025-11-27', 'pengeluaran', 75000.00, 'Bendera IT', 3, 16),
(12, '2025-12-03', 'pengeluaran', 26000.00, 'GAPIN', 3, 16),
(13, '2025-12-04', 'pengeluaran', 38000.00, 'GAPIN v2', 3, 16),
(14, '2025-12-09', 'pengeluaran', 18000.00, 'GAPIN v3', 3, 16),
(15, '2025-12-15', 'pengeluaran', 20000.00, 'DEPO Badminton', 3, 16),
(16, '2025-12-23', 'pengeluaran', 50000.00, 'Metis', 3, 16);

-- --------------------------------------------------------

--
-- Table structure for table `kas_kategori`
--

CREATE TABLE `kas_kategori` (
  `id_kategori` int NOT NULL,
  `nama` varchar(150) NOT NULL,
  `keterangan` text,
  `dibuat_oleh` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kas_kategori`
--

INSERT INTO `kas_kategori` (`id_kategori`, `nama`, `keterangan`, `dibuat_oleh`, `created_at`) VALUES
(1, 'Kas Bulanan', 'Wajib dibayar', 2, '2025-10-13 04:12:00'),
(3, 'Peralatan', 'Alat', 16, '2025-10-13 06:24:58'),
(5, 'IKTE', 'Himpunan Keluarga Elektro dan IT', 16, '2025-11-26 11:28:43');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `id_kas` int DEFAULT NULL,
  `id_kategori` int DEFAULT NULL,
  `status` enum('lunas','telat') NOT NULL,
  `tanggal_bayar` date NOT NULL,
  `jumlah` decimal(12,2) DEFAULT NULL,
  `bukti` varchar(255) DEFAULT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `barcode_image` varchar(255) DEFAULT NULL,
  `ditambahkan_oleh` int DEFAULT NULL,
  `dibuat_oleh` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_user`, `id_kas`, `id_kategori`, `status`, `tanggal_bayar`, `jumlah`, `bukti`, `barcode`, `barcode_image`, `ditambahkan_oleh`, `dibuat_oleh`) VALUES
(10, 9, 1, 1, 'lunas', '2025-10-08', 15000.00, 'pembayaran_9_20251008_022542.jpg', NULL, NULL, 16, NULL),
(11, 15, 1, 1, 'lunas', '2025-10-08', 15000.00, 'pembayaran_15_20251008_022641.jpg', NULL, NULL, 16, NULL),
(12, 5, 1, 1, 'lunas', '2025-10-08', 15000.00, 'pembayaran_5_20251008_022721.jpg', NULL, NULL, 16, NULL),
(13, 14, 1, 1, 'lunas', '2025-10-08', 15000.00, 'pembayaran_14_20251008_022746.jpg', NULL, NULL, 16, NULL),
(14, 8, 1, 1, 'lunas', '2025-10-09', 15000.00, NULL, NULL, NULL, 16, NULL),
(15, 12, 1, 1, 'lunas', '2025-10-09', 15000.00, NULL, NULL, NULL, 16, NULL),
(16, 3, 1, 1, 'telat', '2025-11-13', 15000.00, NULL, NULL, NULL, 16, NULL),
(17, 13, 1, 1, 'lunas', '2025-10-10', 15000.00, NULL, NULL, NULL, 16, NULL),
(18, 4, 1, 1, 'lunas', '2025-10-10', 15000.00, NULL, NULL, NULL, 16, NULL),
(19, 5, NULL, 1, 'lunas', '2025-11-05', 15000.00, NULL, NULL, NULL, 16, NULL),
(20, 8, NULL, 1, 'lunas', '2025-11-06', 15000.00, NULL, NULL, NULL, 16, NULL),
(21, 15, NULL, 1, 'lunas', '2025-11-10', 15000.00, NULL, NULL, NULL, 16, NULL),
(22, 17, 1, 1, 'lunas', '2025-10-10', 15000.00, NULL, NULL, NULL, 16, NULL),
(23, 7, 1, 1, 'telat', '2025-10-11', 15000.00, NULL, NULL, NULL, 16, NULL),
(24, 7, NULL, 1, 'lunas', '2025-11-09', 15000.00, NULL, NULL, NULL, 16, NULL),
(27, 3, 1, 1, 'lunas', '2025-10-10', 15000.00, NULL, NULL, NULL, 16, NULL),
(28, 3, NULL, 1, 'lunas', '2025-12-01', 15000.00, NULL, NULL, NULL, 16, NULL),
(29, 10, 1, 1, 'telat', '2025-10-15', 15000.00, NULL, NULL, NULL, 16, NULL),
(30, 10, NULL, 1, 'telat', '2025-11-19', 15000.00, NULL, NULL, NULL, 16, NULL),
(31, 10, NULL, 1, 'lunas', '2025-12-01', 15000.00, NULL, NULL, NULL, 16, NULL),
(32, 4, NULL, 1, 'telat', '2025-11-24', 15000.00, NULL, NULL, NULL, 16, NULL),
(33, 13, NULL, 1, 'telat', '2025-11-25', 15000.00, NULL, NULL, NULL, 16, NULL),
(50, 11, NULL, 1, 'telat', '2025-10-12', 15000.00, NULL, 'PAY-11-c3b927', 'barcode__P_A_Y_-_1_1_-_c_3_b_9_2_7_.png', 16, NULL),
(51, 11, NULL, 1, 'telat', '2025-11-12', 15000.00, NULL, 'PAY-11-24c91a', 'barcode__P_A_Y_-_1_1_-_2_4_c_9_1_a_.png', 16, NULL),
(52, 11, NULL, 1, 'lunas', '2025-12-08', 15000.00, NULL, 'PAY-11-6713af', 'barcode__P_A_Y_-_1_1_-_6_7_1_3_a_f_.png', 16, NULL),
(53, 14, NULL, 1, 'telat', '2025-11-12', 15000.00, NULL, 'PAY-14-ad7664', 'barcode__P_A_Y_-_1_4_-_a_d_7_6_6_4_.png', 16, NULL),
(54, 14, NULL, 1, 'lunas', '2025-12-09', 15000.00, NULL, 'PAY-14-01ba24', 'barcode__P_A_Y_-_1_4_-_0_1_b_a_2_4_.png', 16, NULL),
(55, 7, NULL, 1, 'lunas', '2025-12-09', 15000.00, NULL, 'PAY-7-40bbec', 'barcode__P_A_Y_-_7_-_4_0_b_b_e_c_.png', 16, NULL),
(56, 5, NULL, 1, 'lunas', '2025-12-09', 15000.00, NULL, 'PAY-5-28704b', 'barcode__P_A_Y_-_5_-_2_8_7_0_4_b_.png', 16, NULL),
(57, 15, NULL, 1, 'lunas', '2025-12-09', 15000.00, NULL, 'PAY-15-8c0be1', 'barcode__P_A_Y_-_1_5_-_8_c_0_b_e_1_.png', 16, NULL),
(58, 8, NULL, 1, 'lunas', '2025-12-09', 15000.00, NULL, 'PAY-8-3aedda', 'barcode__P_A_Y_-_8_-_3_a_e_d_d_a_.png', 16, NULL),
(59, 9, NULL, 1, 'telat', '2025-11-15', 15000.00, NULL, 'PAY-9-544796', 'barcode__P_A_Y_-_9_-_5_4_4_7_9_6_.png', 16, NULL),
(60, 9, NULL, 1, 'lunas', '2025-12-09', 15000.00, NULL, 'PAY-9-114ed2', 'barcode__P_A_Y_-_9_-_1_1_4_e_d_2_.png', 16, NULL),
(61, 4, NULL, 1, 'lunas', '2025-12-09', 15000.00, NULL, 'PAY-4-dcda87', 'barcode__P_A_Y_-_4_-_d_c_d_a_8_7_.png', 16, NULL),
(62, 12, NULL, 1, 'telat', '2025-11-23', 15000.00, NULL, 'PAY-12-507c5b', 'barcode__P_A_Y_-_1_2_-_5_0_7_c_5_b_.png', 16, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `private_chat`
--

CREATE TABLE `private_chat` (
  `id` int UNSIGNED NOT NULL,
  `sender_id` int UNSIGNED NOT NULL,
  `recipient_id` int UNSIGNED NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `private_chat`
--

INSERT INTO `private_chat` (`id`, `sender_id`, `recipient_id`, `message`, `created_at`) VALUES
(9, 1, 16, 'Assalamualaikum', '2025-11-29 21:35:48'),
(11, 16, 1, 'ya', '2025-12-01 08:03:56');

-- --------------------------------------------------------

--
-- Table structure for table `ranking`
--

CREATE TABLE `ranking` (
  `id_ranking` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `jumlah_rajinnya` int DEFAULT '0',
  `jumlah_telatnya` int DEFAULT '0',
  `poin` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ranking`
--

INSERT INTO `ranking` (`id_ranking`, `id_user`, `jumlah_rajinnya`, `jumlah_telatnya`, `poin`) VALUES
(1, 5, 3, 0, 30),
(2, 15, 3, 0, 30),
(3, 9, 2, 1, 15),
(4, 14, 2, 1, 15),
(5, 8, 3, 0, 30),
(6, 12, 1, 1, 5),
(7, 3, 2, 1, 15),
(8, 13, 1, 1, 5),
(9, 4, 2, 1, 15),
(10, 17, 1, 0, 10),
(11, 7, 2, 1, 15),
(12, 10, 1, 2, 0),
(13, 11, 1, 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `roadmap_item`
--

CREATE TABLE `roadmap_item` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `owner_id` int DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('planned','inprogress','blocked','done') NOT NULL DEFAULT 'planned',
  `progress` tinyint NOT NULL DEFAULT '0',
  `tags` varchar(255) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roadmap_item`
--

INSERT INTO `roadmap_item` (`id`, `title`, `description`, `owner_id`, `start_date`, `end_date`, `status`, `progress`, `tags`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Test', 'Test', NULL, '2025-11-29', '2025-11-30', 'planned', 64, NULL, 1, '2025-11-29 03:48:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` varchar(255) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`, `description`) VALUES
(1, 'jatuh_tempo_harian', '10', 'Tanggal jatuh tempo iuran bulanan (default: 10)'),
(2, 'iuran_perminggu', '10000', 'Jumlah iuran per minggu dalam rupiah'),
(3, 'nama_kelas', 'Kelas XII RPL 1', 'Nama kelas'),
(4, 'tahun_ajaran', '2024/2025', 'Tahun ajaran aktif');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','operator','user') NOT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `background_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama_lengkap`, `username`, `password`, `role`, `status`, `profile_picture`, `created_at`, `background_picture`) VALUES
(1, 'Ahmad Fauzi', 'Admin', '$2y$12$xvB3qFYk/llWuDOQTIjoUe1PY4vn1erQ8DHw06Y6j9DVCTsE2FsOK', 'admin', 'aktif', '1764424528_c45c9816a5a2.jpg', '2025-10-03 02:50:04', NULL),
(2, 'Dewi Tresnafuri', 'Ena', '$2y$12$9g8zAN09mvXNIj.Qvl4ziO2cbhxAqB9UnbDZa2/klKaK7rk/2eHoK', 'operator', 'aktif', NULL, '2025-10-03 02:58:37', NULL),
(3, 'Raihan Ananda Permadi', 'Raihan', '$2y$12$bwijfTXaevh7dG2JmNYY3OnrjbuEMNWyaqxdzaU1ThnDAme0csxLq', 'user', 'aktif', 'spider6.jpg', '2025-10-03 07:57:37', 'download (1).jpg'),
(4, 'Aprilia Dwiyani', 'April', '$2y$12$4S6NzHjwYlyfJgOsrZezleJ4Mv43ZFUlbFxp9zf.U0Q.Ywyiq/.LS', 'user', 'aktif', 'ninjago17.jpg', '2025-10-06 02:11:22', 'download (1).jpg'),
(5, 'Hagi Sugara Putra', 'Hagi', '$2y$12$9HIfFgZ.efppOkrxoNyncO.MkWcOO0RhHLEpgXbd/3kLmN1ocH6Vm', 'user', 'aktif', 'ninjago11.jpg', '2025-10-06 02:29:34', 'download (1).jpg'),
(6, 'Syawaludin Nopaliansyah', 'Syawal', '$2y$12$XTokEFNOKA2lKvMZwtrdFOCd8sskRr0cRgvno8wSzN/TJO0UOiVWK', 'user', 'aktif', 'spider8.jpg', '2025-10-06 02:30:18', 'download (1).jpg'),
(7, 'Azmi Anindya', 'Azmi', '$2y$12$gYXy6IhUC6hssCktjX2sV.A1Yokv7jM/rGjGbo/xkU02zyNE8b.gW', 'user', 'aktif', 'ninjagp15.jpg', '2025-10-06 02:30:50', 'download (1).jpg'),
(8, 'Danar Wahyu Sudrajat', 'Danar', '$2y$12$MldrtmBEH6s53t3FIB1djOwvYVkn7FjYe67wK9Y/A7Gd0U.ol.Xti', 'user', 'aktif', 'ninjago13.jpg', '2025-10-06 02:31:13', 'download (1).jpg'),
(9, 'Muhammad Pauzul Ulum', 'Ulum', '$2y$12$sgNBft.s/mDPafyqasJcoOHdBnZ71PJHFPPZI1CII6EwVJgHev/9q', 'user', 'aktif', 'spider4.jpg', '2025-10-06 02:34:18', 'download (1).jpg'),
(10, 'Erintya Dwi Pangestika', 'Erin', '$2y$12$exjghzY2BICQno.Sw0vgnu7SChGnL1TLN4dY4K9j91/U6WKe5Y3Jm', 'user', 'aktif', 'ninjago17.jpg', '2025-10-06 02:34:48', 'download (1).jpg'),
(11, 'Ferdi Febriansyah', 'Ferdi', '$2y$12$HUBrvtVJdcX7/uf3eVuT4OHvK/tcWhRnnPil.HYpYlACcD9yWIRji', 'user', 'aktif', 'ninjago13.jpg', '2025-10-06 02:35:27', 'download (1).jpg'),
(12, 'Muhammad Dzaky Ramadhani', 'Dzaky', '$2y$12$iRDNBa7aqVy4qBo5BJ/61OZR4IeRg93Tqi5PtfxQZx1oSYGPQcDO2', 'user', 'aktif', 'spider7.jpg', '2025-10-06 02:36:04', 'download (1).jpg'),
(13, 'Mohammad Alvin Makmun', 'Alvin', '$2y$12$NWzaTst2BcdvIdcUXKbulORHcTOt0trPPQmQYHJoiOAiH2F5KyUSC', 'user', 'aktif', 'spider12.jpg', '2025-10-06 02:36:30', 'download (1).jpg'),
(14, 'Najwan Caesar Firstiansyah', 'Najwan', '$2y$12$2bappoY4X4ehMABudwols.s1x3cS2e1WirkygjQy346QIEhWJTAZ2', 'user', 'aktif', 'spider3.jpg', '2025-10-06 02:38:47', 'download (1).jpg'),
(15, 'Tangguh Putra Mahardika', 'Tangguh', '$2y$12$m/Hc3MEXqVEDMWi7SGipXe5NvcT1pFCqKmmblhfNMuAhobvsWtHEW', 'user', 'aktif', 'spider10.jpg', '2025-10-06 02:39:16', 'download (1).jpg'),
(16, 'Ahmad Fauzi', 'Ahmad', '$2y$12$1eJ4Wxup5Ng2CY5T3OANd.ZhiuiTbPEIFayRzwG/hlS9PzRuKsXR6', 'operator', 'aktif', '1764424128_08412611e42a.jpg', '2025-10-06 14:20:50', NULL),
(17, 'Ahmad Fauzi', 'Fauzi', '$2y$12$XIEwxR8or1kNILltdGDCzOBJnml6MtnVfLZXSU9rMb.QuLFme9IaW', 'user', 'aktif', '1764581308_51bd72cdf987.jpg', '2025-11-05 06:25:41', 'download (1).jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agenda`
--
ALTER TABLE `agenda`
  ADD PRIMARY KEY (`id_agenda`),
  ADD KEY `dibuat_oleh` (`dibuat_oleh`);

--
-- Indexes for table `analysis_todo`
--
ALTER TABLE `analysis_todo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `barcode_audit`
--
ALTER TABLE `barcode_audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barcode` (`barcode`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `calendar_event`
--
ALTER TABLE `calendar_event`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id_chat`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `fk_chat_topic` (`topic_id`);

--
-- Indexes for table `discussion_categories`
--
ALTER TABLE `discussion_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `discussion_posts`
--
ALTER TABLE `discussion_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_id` (`topic_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_post_id` (`parent_post_id`);

--
-- Indexes for table `discussion_topics`
--
ALTER TABLE `discussion_topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `jadwal_kegiatan`
--
ALTER TABLE `jadwal_kegiatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kas`
--
ALTER TABLE `kas`
  ADD PRIMARY KEY (`id_kas`),
  ADD KEY `dibuat_oleh` (`dibuat_oleh`);

--
-- Indexes for table `kas_kategori`
--
ALTER TABLE `kas_kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD UNIQUE KEY `unique_barcode` (`barcode`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_kas` (`id_kas`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `dibuat_oleh` (`dibuat_oleh`);

--
-- Indexes for table `private_chat`
--
ALTER TABLE `private_chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `recipient_id` (`recipient_id`);

--
-- Indexes for table `ranking`
--
ALTER TABLE `ranking`
  ADD PRIMARY KEY (`id_ranking`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `roadmap_item`
--
ALTER TABLE `roadmap_item`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agenda`
--
ALTER TABLE `agenda`
  MODIFY `id_agenda` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `analysis_todo`
--
ALTER TABLE `analysis_todo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `barcode_audit`
--
ALTER TABLE `barcode_audit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `calendar_event`
--
ALTER TABLE `calendar_event`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id_chat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `discussion_categories`
--
ALTER TABLE `discussion_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `discussion_posts`
--
ALTER TABLE `discussion_posts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discussion_topics`
--
ALTER TABLE `discussion_topics`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `jadwal_kegiatan`
--
ALTER TABLE `jadwal_kegiatan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kas`
--
ALTER TABLE `kas`
  MODIFY `id_kas` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `kas_kategori`
--
ALTER TABLE `kas_kategori`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `private_chat`
--
ALTER TABLE `private_chat`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `ranking`
--
ALTER TABLE `ranking`
  MODIFY `id_ranking` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `roadmap_item`
--
ALTER TABLE `roadmap_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `agenda`
--
ALTER TABLE `agenda`
  ADD CONSTRAINT `agenda_ibfk_1` FOREIGN KEY (`dibuat_oleh`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `chat_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `fk_chat_topic` FOREIGN KEY (`topic_id`) REFERENCES `discussion_topics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discussion_posts`
--
ALTER TABLE `discussion_posts`
  ADD CONSTRAINT `discussion_posts_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `discussion_topics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discussion_posts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `discussion_posts_ibfk_3` FOREIGN KEY (`parent_post_id`) REFERENCES `discussion_posts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `discussion_topics`
--
ALTER TABLE `discussion_topics`
  ADD CONSTRAINT `discussion_topics_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `discussion_topics_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `discussion_categories` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `kas`
--
ALTER TABLE `kas`
  ADD CONSTRAINT `kas_ibfk_1` FOREIGN KEY (`dibuat_oleh`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`id_kas`) REFERENCES `kas` (`id_kas`);

--
-- Constraints for table `ranking`
--
ALTER TABLE `ranking`
  ADD CONSTRAINT `ranking_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
