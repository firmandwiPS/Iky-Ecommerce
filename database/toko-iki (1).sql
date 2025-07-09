-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 09, 2025 at 12:44 AM
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
-- Database: `toko-iki`
--

-- --------------------------------------------------------

--
-- Table structure for table `akun`
--

CREATE TABLE `akun` (
  `id_akun` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `level` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `akun`
--

INSERT INTO `akun` (`id_akun`, `nama`, `username`, `email`, `password`, `level`) VALUES
(18, 'admin', 'admin', 'Admin@gmail.com', '$2y$10$rAEf9i/gSH64uKefDhApdeIIC1xuGvBlvIRmt27CaugLgnWk.vvcm', '1');

-- --------------------------------------------------------

--
-- Table structure for table `data_keuangan`
--

CREATE TABLE `data_keuangan` (
  `id` int NOT NULL,
  `tanggal` date NOT NULL,
  `total_masuk` double DEFAULT '0',
  `total_keluar` double DEFAULT '0',
  `saldo` double DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data_uang_keluar`
--

CREATE TABLE `data_uang_keluar` (
  `id` int NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text NOT NULL,
  `jumlah` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `data_uang_keluar`
--

INSERT INTO `data_uang_keluar` (`id`, `tanggal`, `keterangan`, `jumlah`) VALUES
(2, '2025-07-08', 'bensin', 11000);

-- --------------------------------------------------------

--
-- Table structure for table `data_uang_masuk`
--

CREATE TABLE `data_uang_masuk` (
  `id` int NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text NOT NULL,
  `jumlah` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `data_uang_masuk`
--

INSERT INTO `data_uang_masuk` (`id`, `tanggal`, `keterangan`, `jumlah`) VALUES
(3, '2025-07-08', 'seponsor', 300000),
(4, '2025-07-08', 'pesanan', 5000000);

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
--

CREATE TABLE `keranjang` (
  `id` int NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `menu_id` int NOT NULL,
  `jumlah` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `keranjang`
--

INSERT INTO `keranjang` (`id`, `session_id`, `menu_id`, `jumlah`) VALUES
(23, 'gghjk574gkug3k0lhv1ieha9on', 8, 1),
(24, '9f5rp0eb5ul83j2mcea4oc9ecu', 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `makanan`
--

CREATE TABLE `makanan` (
  `id` int NOT NULL,
  `nama_makanan` varchar(100) NOT NULL,
  `harga` int NOT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `deskripsi` text,
  `gambar` varchar(255) DEFAULT NULL,
  `stok` int NOT NULL DEFAULT '0',
  `recommended` varchar(10) NOT NULL DEFAULT 'Tidak'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `makanan`
--

INSERT INTO `makanan` (`id`, `nama_makanan`, `harga`, `kategori`, `deskripsi`, `gambar`, `stok`, `recommended`) VALUES
(7, 'cireng', 2000, 'Snack', 'enak', '6861d425925ce-Malaz.png', 20, 'Ya'),
(8, 'baso', 15000, 'Makanan', 'enak', '6861d443d1705-Kiko.jpg', 30, 'Ya'),
(9, 'es teh ', 3000, 'Minuman', 'enak', '686243447c799-A logo of a crow.jpeg', 20, 'Ya'),
(10, 'pangsit', 9000, 'Snack', 'enak', '686a0cd4aa714-Screenshot 2025-05-16 185818.png', 20, 'Ya'),
(11, 'mie ayam', 20000, 'Makanan', 'enak', '6862474d4a8e2-Screenshot 2025-06-30 141246.png', 20, 'Tidak');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id` int NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `no_wa` varchar(20) NOT NULL,
  `detail` text NOT NULL,
  `total` int NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Sedang diproses',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id`, `nama_pelanggan`, `no_wa`, `detail`, `total`, `status`, `created_at`) VALUES
(13, 'firman', '08626277463', 'mie ayam x3, baso x4', 120000, 'Dibatalkan', '2025-07-06 05:38:19'),
(15, 'firman', '08626277463', 'pangsit x1', 9000, 'Sedang diproses', '2025-07-08 10:41:55'),
(16, 'firman', '0878978678', 'cireng x1, baso x1, es teh  x3', 26000, 'Selesai', '2025-07-08 12:30:23');

-- --------------------------------------------------------

--
-- Table structure for table `ulasan`
--

CREATE TABLE `ulasan` (
  `id` int NOT NULL,
  `makanan_id` int NOT NULL,
  `nama_pengulas` varchar(100) NOT NULL,
  `ulasan` text NOT NULL,
  `kritik` text,
  `saran` text,
  `rating` tinyint NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ulasan`
--

INSERT INTO `ulasan` (`id`, `makanan_id`, `nama_pengulas`, `ulasan`, `kritik`, `saran`, `rating`, `tanggal`) VALUES
(3, 11, 'cahya', 'well', '', 'enakk', 4, '2025-07-01 08:24:19'),
(4, 8, 'lardi', 'enak', '', '', 4, '2025-07-01 17:42:44'),
(5, 10, 'vani', 'Enak well', 'okh', 'lebih enk lgi ', 4, '2025-07-06 14:05:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `akun`
--
ALTER TABLE `akun`
  ADD PRIMARY KEY (`id_akun`);

--
-- Indexes for table `data_keuangan`
--
ALTER TABLE `data_keuangan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_uang_keluar`
--
ALTER TABLE `data_uang_keluar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_uang_masuk`
--
ALTER TABLE `data_uang_masuk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `makanan`
--
ALTER TABLE `makanan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_makanan_id` (`makanan_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `akun`
--
ALTER TABLE `akun`
  MODIFY `id_akun` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `data_keuangan`
--
ALTER TABLE `data_keuangan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `data_uang_keluar`
--
ALTER TABLE `data_uang_keluar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `data_uang_masuk`
--
ALTER TABLE `data_uang_masuk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `makanan`
--
ALTER TABLE `makanan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `ulasan`
--
ALTER TABLE `ulasan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD CONSTRAINT `fk_ulasan_makanan` FOREIGN KEY (`makanan_id`) REFERENCES `makanan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
