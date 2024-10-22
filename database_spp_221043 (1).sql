-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2024 at 02:07 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

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
(3, 'udin', 'orangtua', 'udin2@gmail.com', '344c999a63cd55b3035cbf76c2691f88');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran_221043`
--

CREATE TABLE `pembayaran_221043` (
  `id_221043` int(11) NOT NULL,
  `siswa_id_221043` int(11) DEFAULT NULL,
  `spp_id_221043` int(11) DEFAULT NULL,
  `tanggal_bayar_221043` date NOT NULL,
  `status_221043` enum('pending','confirmed') DEFAULT 'pending',
  `orangtua_id_221043` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `siswa_221043`
--

CREATE TABLE `siswa_221043` (
  `id_221043` int(11) NOT NULL,
  `nama_221043` varchar(255) NOT NULL,
  `username_221043` varchar(255) NOT NULL,
  `email_221043` varchar(255) NOT NULL,
  `kelas_221043` varchar(50) NOT NULL,
  `alamat_221043` text DEFAULT NULL,
  `orangtua_id_221043` int(11) DEFAULT NULL,
  `password_221043` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa_221043`
--

INSERT INTO `siswa_221043` (`id_221043`, `nama_221043`, `username_221043`, `email_221043`, `kelas_221043`, `alamat_221043`, `orangtua_id_221043`, `password_221043`) VALUES
(1, 'siswa', 'siswa', 'siswa@gmail.com', 'Kelas 1', 'jalanan', 3, 'bcd724d15cde8c47650fda962968f102');

-- --------------------------------------------------------

--
-- Table structure for table `spp_221043`
--

CREATE TABLE `spp_221043` (
  `id_221043` int(11) NOT NULL,
  `tahun_221043` year(4) NOT NULL,
  `bulan_221043` varchar(20) NOT NULL,
  `jumlah_221043` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  ADD KEY `spp_id_221043` (`spp_id_221043`),
  ADD KEY `orangtua_id_221043` (`orangtua_id_221043`);

--
-- Indexes for table `siswa_221043`
--
ALTER TABLE `siswa_221043`
  ADD PRIMARY KEY (`id_221043`),
  ADD KEY `orangtua_id_221043` (`orangtua_id_221043`);

--
-- Indexes for table `spp_221043`
--
ALTER TABLE `spp_221043`
  ADD PRIMARY KEY (`id_221043`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_221043`
--
ALTER TABLE `admin_221043`
  MODIFY `id_221043` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orangtua_221043`
--
ALTER TABLE `orangtua_221043`
  MODIFY `id_221043` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pembayaran_221043`
--
ALTER TABLE `pembayaran_221043`
  MODIFY `id_221043` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `siswa_221043`
--
ALTER TABLE `siswa_221043`
  MODIFY `id_221043` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `spp_221043`
--
ALTER TABLE `spp_221043`
  MODIFY `id_221043` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pembayaran_221043`
--
ALTER TABLE `pembayaran_221043`
  ADD CONSTRAINT `pembayaran_221043_ibfk_1` FOREIGN KEY (`siswa_id_221043`) REFERENCES `siswa_221043` (`id_221043`),
  ADD CONSTRAINT `pembayaran_221043_ibfk_2` FOREIGN KEY (`spp_id_221043`) REFERENCES `spp_221043` (`id_221043`),
  ADD CONSTRAINT `pembayaran_221043_ibfk_3` FOREIGN KEY (`orangtua_id_221043`) REFERENCES `orangtua_221043` (`id_221043`);

--
-- Constraints for table `siswa_221043`
--
ALTER TABLE `siswa_221043`
  ADD CONSTRAINT `siswa_221043_ibfk_1` FOREIGN KEY (`orangtua_id_221043`) REFERENCES `orangtua_221043` (`id_221043`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
