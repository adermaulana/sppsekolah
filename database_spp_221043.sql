-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2024 at 10:11 AM
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
-- Database: `database_spp_221043`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_221043`
--

CREATE TABLE `admin_221043` (
  `id_221043` int(11) NOT NULL,
  `name_221043` varchar(255) NOT NULL,
  `username_221043` varchar(255) NOT NULL,
  `password_221043` varchar(255) NOT NULL,
  `email_221043` varchar(255) NOT NULL,
  `created_at_221043` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_221043`
--

INSERT INTO `admin_221043` (`id_221043`, `name_221043`, `username_221043`, `password_221043`, `email_221043`, `created_at_221043`) VALUES
(1, 'admin', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@gmail.com', '2024-10-06 07:07:17');

-- --------------------------------------------------------

--
-- Table structure for table `kelas_221043`
--

CREATE TABLE `kelas_221043` (
  `id_221043` int(11) NOT NULL,
  `kelas_221043` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas_221043`
--

INSERT INTO `kelas_221043` (`id_221043`, `kelas_221043`) VALUES
(3, 'Kelas 1'),
(4, 'Kelas 2'),
(5, 'Kelas 4');

-- --------------------------------------------------------

--
-- Table structure for table `orangtua_221043`
--

CREATE TABLE `orangtua_221043` (
  `id_221043` int(11) NOT NULL,
  `nama_221043` varchar(255) NOT NULL,
  `username_221043` varchar(255) NOT NULL,
  `email_221043` varchar(255) NOT NULL,
  `password_221043` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orangtua_221043`
--

INSERT INTO `orangtua_221043` (`id_221043`, `nama_221043`, `username_221043`, `email_221043`, `password_221043`) VALUES
(16, 'orangtua', 'orangtua', 'orangtua@gmail.com', '344c999a63cd55b3035cbf76c2691f88'),
(17, 'gann', 'gan', 'gan@gmail.com', 'f1253bc7b6c0b1d62eb9b97cfebf0f63');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran_221043`
--

CREATE TABLE `pembayaran_221043` (
  `id_221043` int(11) NOT NULL,
  `siswa_id_221043` int(11) DEFAULT NULL,
  `spp_id_221043` int(11) DEFAULT NULL,
  `tanggal_bayar_221043` date DEFAULT NULL,
  `bukti_pembayaran_221043` varchar(255) DEFAULT NULL,
  `bulan_221043` varchar(255) NOT NULL,
  `status_221043` enum('pending','lunas') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran_221043`
--

INSERT INTO `pembayaran_221043` (`id_221043`, `siswa_id_221043`, `spp_id_221043`, `tanggal_bayar_221043`, `bukti_pembayaran_221043`, `bulan_221043`, `status_221043`) VALUES
(2, 8, 3, NULL, 'uploads/Screenshot (2).png', 'Januari', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `siswa_221043`
--

CREATE TABLE `siswa_221043` (
  `id_221043` int(11) NOT NULL,
  `nama_221043` varchar(255) NOT NULL,
  `username_221043` varchar(255) NOT NULL,
  `id_kelas_221043` int(11) NOT NULL,
  `alamat_221043` text DEFAULT NULL,
  `orangtua_id_221043` int(11) DEFAULT NULL,
  `password_221043` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa_221043`
--

INSERT INTO `siswa_221043` (`id_221043`, `nama_221043`, `username_221043`, `id_kelas_221043`, `alamat_221043`, `orangtua_id_221043`, `password_221043`) VALUES
(8, 'siswa', 'siswa', 3, 'siswa', 16, 'bcd724d15cde8c47650fda962968f102'),
(9, 'gun', 'gun', 3, 'gun', 17, '5161ebb0cce4b7987ba8b6935d60a180');

-- --------------------------------------------------------

--
-- Table structure for table `spp_221043`
--

CREATE TABLE `spp_221043` (
  `id_221043` int(11) NOT NULL,
  `id_kelas_221043` int(11) NOT NULL,
  `biaya_221043` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `spp_221043`
--

INSERT INTO `spp_221043` (`id_221043`, `id_kelas_221043`, `biaya_221043`) VALUES
(3, 3, 2333),
(4, 5, 40000),
(5, 4, 30000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_221043`
--
ALTER TABLE `admin_221043`
  ADD PRIMARY KEY (`id_221043`),
  ADD UNIQUE KEY `username_221043` (`username_221043`),
  ADD UNIQUE KEY `email_221043` (`email_221043`);

--
-- Indexes for table `kelas_221043`
--
ALTER TABLE `kelas_221043`
  ADD PRIMARY KEY (`id_221043`);

--
-- Indexes for table `orangtua_221043`
--
ALTER TABLE `orangtua_221043`
  ADD PRIMARY KEY (`id_221043`),
  ADD UNIQUE KEY `email_221043` (`email_221043`);

--
-- Indexes for table `pembayaran_221043`
--
ALTER TABLE `pembayaran_221043`
  ADD PRIMARY KEY (`id_221043`),
  ADD KEY `siswa_id_221043` (`siswa_id_221043`),
  ADD KEY `spp_id_221043` (`spp_id_221043`);

--
-- Indexes for table `siswa_221043`
--
ALTER TABLE `siswa_221043`
  ADD PRIMARY KEY (`id_221043`),
  ADD KEY `orangtua_id_221043` (`orangtua_id_221043`),
  ADD KEY `id_kelas_221043` (`id_kelas_221043`);

--
-- Indexes for table `spp_221043`
--
ALTER TABLE `spp_221043`
  ADD PRIMARY KEY (`id_221043`),
  ADD KEY `id_kelas_221043` (`id_kelas_221043`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_221043`
--
ALTER TABLE `admin_221043`
  MODIFY `id_221043` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kelas_221043`
--
ALTER TABLE `kelas_221043`
  MODIFY `id_221043` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orangtua_221043`
--
ALTER TABLE `orangtua_221043`
  MODIFY `id_221043` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `pembayaran_221043`
--
ALTER TABLE `pembayaran_221043`
  MODIFY `id_221043` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `siswa_221043`
--
ALTER TABLE `siswa_221043`
  MODIFY `id_221043` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `spp_221043`
--
ALTER TABLE `spp_221043`
  MODIFY `id_221043` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pembayaran_221043`
--
ALTER TABLE `pembayaran_221043`
  ADD CONSTRAINT `pembayaran_221043_ibfk_1` FOREIGN KEY (`siswa_id_221043`) REFERENCES `siswa_221043` (`id_221043`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pembayaran_221043_ibfk_2` FOREIGN KEY (`spp_id_221043`) REFERENCES `spp_221043` (`id_221043`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `siswa_221043`
--
ALTER TABLE `siswa_221043`
  ADD CONSTRAINT `siswa_221043_ibfk_1` FOREIGN KEY (`orangtua_id_221043`) REFERENCES `orangtua_221043` (`id_221043`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `siswa_221043_ibfk_2` FOREIGN KEY (`id_kelas_221043`) REFERENCES `kelas_221043` (`id_221043`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `spp_221043`
--
ALTER TABLE `spp_221043`
  ADD CONSTRAINT `spp_221043_ibfk_1` FOREIGN KEY (`id_kelas_221043`) REFERENCES `kelas_221043` (`id_221043`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
