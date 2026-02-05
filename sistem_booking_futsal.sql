-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 05, 2026 at 07:56 PM
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
-- Database: `sistem_booking_futsal`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `role` enum('admin') NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `email`, `password`, `nama_admin`, `role`) VALUES
(1, 'admin1@gmail.com', '$2y$10$ZWIyrTxmMuCCiUmo7wSFzO/wUVzpVOewV09/vOJ1Qji0swm.j4Y4O', 'Admin Trinity', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `id_booking` int(11) NOT NULL,
  `kode_booking` varchar(15) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_lapangan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `total_harga` int(11) NOT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'menunggu',
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `tgl_input` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`id_booking`, `kode_booking`, `id_user`, `id_lapangan`, `tanggal`, `jam_mulai`, `jam_selesai`, `total_harga`, `bukti_transfer`, `catatan`, `status`, `bukti_bayar`, `tgl_input`) VALUES
(1, 'BK20260205161', 4, 2, '2026-02-06', '08:00:00', '00:00:00', 150000, NULL, NULL, 'dikonfirmasi', 'BUKTI_BK20260205161_1770316852.png', '2026-02-05 18:40:25'),
(2, 'BK20260205803', 4, 3, '2026-02-06', '09:00:00', '00:00:00', 50000, NULL, NULL, 'dikonfirmasi', 'BUKTI_BK20260205803_1770316895.png', '2026-02-05 18:41:17'),
(3, 'BK20260205997', 4, 1, '2026-02-06', '20:00:00', '00:00:00', 150000, NULL, NULL, 'dikonfirmasi', 'BUKTI_BK20260205997_1770317620.png', '2026-02-05 18:53:13'),
(4, 'BK20260205518', 4, 1, '2026-02-06', '12:00:00', '00:00:00', 150000, NULL, NULL, 'batal', 'BUKTI_BK20260205518_1770317720.png', '2026-02-05 18:55:02');

-- --------------------------------------------------------

--
-- Table structure for table `lapangan`
--

CREATE TABLE `lapangan` (
  `id_lapangan` int(11) NOT NULL,
  `nama_lapangan` varchar(50) NOT NULL,
  `jenis` enum('futsal','badminton') NOT NULL,
  `harga_per_jam` int(11) NOT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lapangan`
--

INSERT INTO `lapangan` (`id_lapangan`, `nama_lapangan`, `jenis`, `harga_per_jam`, `foto`) VALUES
(1, 'Lapangan Futsal A', 'futsal', 150000, '1769773520_lapangan_1768971686.jpg'),
(2, 'Lapangan Futsal B', 'futsal', 150000, 'lapangan_1768973806.jpg'),
(3, 'Lapangan Badminton 1', 'badminton', 50000, 'lapangan_1768973901.jpg'),
(4, 'Lapangan Badminton 2', 'badminton', 50000, 'lapangan_1768973909.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user') DEFAULT 'user',
  `no_hp` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama`, `email`, `password`, `role`, `no_hp`, `created_at`) VALUES
(4, 'Theresia', 'shintatheresia51@gmail.com', '$2y$10$spAFT8638MR4fd.BWNQbVu7pty5qB26ODzOV1q7JgKqBLIOc1Vx0q', 'user', '087777777777', '2026-01-22 10:23:11'),
(7, 'Amel', 'amel2@gmail.com', '$2y$10$X.FkmhXbQZQ4vjSE3UoYiOQkSJcbIf9z5puqbWfVtVM8afF8E8Mbu', 'user', NULL, '2026-01-24 15:11:53'),
(8, 'Delya', 'delya2@gmail.com', '$2y$10$th6prVrAltNRHLsgpRYYnOkc7NEyVl0ALi1IHC13N86qE18NeiT0.', 'user', NULL, '2026-01-24 15:12:14'),
(9, 'Cha', 'cha@gmail.com', '$2y$10$0ruLJcJPLBhR9Fxd/0G.yOpy7UTk2olDK3Xc3Fjzbn5M1GYtRsg5a', 'user', NULL, '2026-01-24 15:17:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`email`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id_booking`),
  ADD UNIQUE KEY `kode_booking` (`kode_booking`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_lapangan` (`id_lapangan`);

--
-- Indexes for table `lapangan`
--
ALTER TABLE `lapangan`
  ADD PRIMARY KEY (`id_lapangan`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id_booking` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lapangan`
--
ALTER TABLE `lapangan`
  MODIFY `id_lapangan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id_lapangan`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
