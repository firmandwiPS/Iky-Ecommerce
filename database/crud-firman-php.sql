-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 03, 2025 at 08:58 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crud-firman-php`
--

-- --------------------------------------------------------

--
-- Table structure for table `akun`
--

CREATE TABLE `akun` (
  `id_akun` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `level` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `akun`
--

INSERT INTO `akun` (`id_akun`, `nama`, `username`, `email`, `password`, `level`) VALUES
(18, 'admin', 'admin', 'Admin@gmail.com', '$2y$10$rAEf9i/gSH64uKefDhApdeIIC1xuGvBlvIRmt27CaugLgnWk.vvcm', '1'),
(19, 'barang', 'barang', 'kemed2@gmail.com', '$2y$10$G3034KnFSJmk63rfM.Vfu.YpIjj0Gs83Tw.ZmTiMoe2CJWfcgE3O6', '2'),
(21, 'mahasiswa', 'mahasiswa', 'ffirmansetiawan07@gmail.com', '$2y$10$irFF6tQgpkBmJ7Dzjc7oWuwVbv05V1YwsPzZ.Cb6MHxGvBXjGetG.', '3');

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `jumlah` int(50) NOT NULL,
  `harga` int(50) NOT NULL,
  `barcode` varchar(15) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `nama`, `jumlah`, `harga`, `barcode`, `tanggal`) VALUES
(3, 'skateboard', 1, 100000, '', '2025-01-23 00:36:33'),
(6, 'sabun mandi', 5, 50000, '', '2025-01-21 01:24:05'),
(7, 'meja', 2, 600000, '', '2025-01-21 01:28:05'),
(13, 'kartus', 1, 2000, '828835', '2025-01-23 04:22:36'),
(14, 'raket', 1, 50000, '282170', '2025-02-03 00:40:41'),
(15, 'hanger', 51, 10000, '965021', '2025-01-31 06:58:56'),
(20, 'parfum', 1, 1000000, '213781', '2025-02-03 00:40:08');

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id_mahasiswa` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `prodi` varchar(50) NOT NULL,
  `jk` varchar(10) NOT NULL,
  `telepon` varchar(30) NOT NULL,
  `alamat` text NOT NULL,
  `email` varchar(30) NOT NULL,
  `foto` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`id_mahasiswa`, `nama`, `prodi`, `jk`, `telepon`, `alamat`, `email`, `foto`) VALUES
(2, 'Bayu Galaksi', 'Teknik Mesin', 'Laki-Laki', '08723345697', '', 'galaksisisisi@gmail.com', 'foto.jpg'),
(18, ' suban', 'Teknik Mesin', 'Laki Laki', '3434456445', 'gdgd', 'Admin@gmail.com', '6791b94b44ca9.jpg'),
(19, 'cuyyy', 'Teknik Vidio', 'Perempuan', '088980807071', '   jln', 'gen22dut@gmail.com', ''),
(20, 'bimab', 'Teknik Informatika', 'Laki Laki', '1123131231', '  hohoho', 'Admin@gmail.com', '679af5642bf57.jpg'),
(24, 'daya ', 'Teknik Vidio', 'Perempuan', '089423342526', 'jln kita putus', 'daya@gmail.com', '67a010c78b7d4.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `pegawai`
--

CREATE TABLE `pegawai` (
  `id_pegawai` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telepon` varchar(100) NOT NULL,
  `alamat` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pegawai`
--

INSERT INTO `pegawai` (`id_pegawai`, `nama`, `jabatan`, `email`, `telepon`, `alamat`) VALUES
(1, 'eko', 'Direktur', 'eko@gmail.com', '08778665349', 'Palembang'),
(2, 'winarto', 'wakil Direktur', 'win@gmail.com', '08775233468', 'Bangka');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `akun`
--
ALTER TABLE `akun`
  ADD PRIMARY KEY (`id_akun`);

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id_mahasiswa`);

--
-- Indexes for table `pegawai`
--
ALTER TABLE `pegawai`
  ADD PRIMARY KEY (`id_pegawai`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `akun`
--
ALTER TABLE `akun`
  MODIFY `id_akun` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id_mahasiswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `pegawai`
--
ALTER TABLE `pegawai`
  MODIFY `id_pegawai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
