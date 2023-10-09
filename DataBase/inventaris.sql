-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2023 at 05:49 AM
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
-- Database: `inventaris`
--

-- --------------------------------------------------------

--
-- Table structure for table `inventaris`
--

CREATE TABLE `inventaris` (
  `id_inventaris` int(4) NOT NULL,
  `nama` text NOT NULL,
  `kondisi` text NOT NULL,
  `keterangan_inventaris` text NOT NULL,
  `jumlah` int(255) NOT NULL,
  `id_jenis` int(4) NOT NULL,
  `id_ruang` int(4) NOT NULL,
  `kode_inventaris` varchar(30) NOT NULL,
  `tanggal_tanggal` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_laporan_inventaris` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `inventaris`
--

INSERT INTO `inventaris` (`id_inventaris`, `nama`, `kondisi`, `keterangan_inventaris`, `jumlah`, `id_jenis`, `id_ruang`, `kode_inventaris`, `tanggal_tanggal`, `tanggal_laporan_inventaris`) VALUES
(6, 'test', 'tidak diketahui', 'Dapat Digunakan', 1, 3, 3, 'Tst-01', '2023-09-27 07:49:50', '2023-09-27');

-- --------------------------------------------------------

--
-- Table structure for table `jenis`
--

CREATE TABLE `jenis` (
  `id_jenis` int(4) NOT NULL,
  `nama_jenis` text NOT NULL,
  `kode_jenis` text NOT NULL,
  `keterangan` varchar(20) NOT NULL,
  `tanggal_jenis` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `jenis`
--

INSERT INTO `jenis` (`id_jenis`, `nama_jenis`, `kode_jenis`, `keterangan`, `tanggal_jenis`) VALUES
(1, 'Alternating Current', 'AC-01', 'Jenis Tersedia', '2023-09-26 19:00:58'),
(2, 'Kipas Angin', 'KA-01', 'Jenis Tersedia', '2023-09-26 19:01:18'),
(3, 'Meja Kayu', 'MK-01', 'Jenis Tersedia', '2023-09-26 19:01:45'),
(5, 'televisi', 'TV-01', 'Tidak Tersedia', '2023-09-26 19:40:30');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_peminjaman` int(4) NOT NULL,
  `id_inventaris` int(4) NOT NULL,
  `jumlah_pinjam` int(255) NOT NULL,
  `tanggal_peminjaman` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_pengembalian` datetime NOT NULL DEFAULT current_timestamp(),
  `pembuat_peminjaman` int(4) NOT NULL,
  `tanggal_laporan1` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id_peminjaman`, `id_inventaris`, `jumlah_pinjam`, `tanggal_peminjaman`, `tanggal_pengembalian`, `pembuat_peminjaman`, `tanggal_laporan1`) VALUES
(21, 6, 1, '2023-09-27 08:36:10', '2023-09-30 00:00:00', 2, '2023-09-27');

--
-- Triggers `peminjaman`
--
DELIMITER $$
CREATE TRIGGER `peminjaman_cancel` AFTER DELETE ON `peminjaman` FOR EACH ROW UPDATE inventaris SET jumlah = jumlah+old.jumlah_pinjam WHERE id_inventaris = old.id_inventaris
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `peminjaman_minus` AFTER INSERT ON `peminjaman` FOR EACH ROW UPDATE inventaris SET jumlah = jumlah-new.jumlah_pinjam WHERE id_inventaris = new.id_inventaris
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pengembalian`
--

CREATE TABLE `pengembalian` (
  `id_pengembalian` int(4) NOT NULL,
  `id_inventaris` int(4) NOT NULL,
  `jumlah_pengembalian` int(255) NOT NULL,
  `tanggal_pengembalian_pengembalian` datetime NOT NULL DEFAULT current_timestamp(),
  `pembuat_pengembalian` int(4) NOT NULL,
  `tanggal_laporan2` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pengembalian`
--

INSERT INTO `pengembalian` (`id_pengembalian`, `id_inventaris`, `jumlah_pengembalian`, `tanggal_pengembalian_pengembalian`, `pembuat_pengembalian`, `tanggal_laporan2`) VALUES
(8, 6, 1, '2023-09-27 08:36:23', 2, '2023-09-27');

--
-- Triggers `pengembalian`
--
DELIMITER $$
CREATE TRIGGER `pengembalian_cancel` AFTER DELETE ON `pengembalian` FOR EACH ROW update inventaris set jumlah = jumlah-old.jumlah_pengembalian WHERE id_inventaris = old.id_inventaris
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `pengembalian_positif` AFTER INSERT ON `pengembalian` FOR EACH ROW UPDATE inventaris SET jumlah= jumlah+new.jumlah_pengembalian WHERE id_inventaris=new.id_inventaris
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `petugas`
--

CREATE TABLE `petugas` (
  `id_petugas` int(4) NOT NULL,
  `nama_petugas` text NOT NULL,
  `nip` int(255) NOT NULL,
  `telp` varchar(20) NOT NULL,
  `alamat` text NOT NULL,
  `tanggal_petugas` datetime NOT NULL DEFAULT current_timestamp(),
  `jk` varchar(20) NOT NULL,
  `id_user_petugas` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `petugas`
--

INSERT INTO `petugas` (`id_petugas`, `nama_petugas`, `nip`, `telp`, `alamat`, `tanggal_petugas`, `jk`, `id_user_petugas`) VALUES
(2, 'Jofinson Lim', 1234567891, '08126458979', 'batam', '2023-09-26 18:33:52', 'Laki-Laki', 2),
(3, 'kepin', 1123456789, '08631253451', 'batam', '2023-09-26 18:35:23', 'Laki-Laki', 3),
(4, 'Norman Ang', 1223456789, '0856342798', 'batam', '2023-09-26 18:35:54', 'Laki-Laki', 4),
(6, 'coba', 378264232, '08367521321', 'batam', '2023-09-26 19:40:04', 'Perempuan', 6);

-- --------------------------------------------------------

--
-- Table structure for table `ruang`
--

CREATE TABLE `ruang` (
  `id_ruang` int(4) NOT NULL,
  `nama_ruang` text NOT NULL,
  `kode_ruang` text NOT NULL,
  `keterangan` varchar(30) NOT NULL,
  `tanggal_ruangan` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ruang`
--

INSERT INTO `ruang` (`id_ruang`, `nama_ruang`, `kode_ruang`, `keterangan`, `tanggal_ruangan`) VALUES
(2, 'Receptions ', 'R-01', 'Ruangan Tersedia', '2023-09-26 19:49:01'),
(3, 'Waiting room', 'WR-01', 'Ruangan Tersedia', '2023-09-26 19:57:18'),
(4, 'Meeting room ', 'MR-01', 'Ruangan Tersedia', '2023-09-26 19:57:28'),
(5, 'Office room ', 'OR-01', 'Tidak Tersedia', '2023-09-26 19:57:40');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` int(1) NOT NULL,
  `foto` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `level`, `foto`) VALUES
(2, 'jofinson', 'cccf3d86bddad524562a235e24ad4850', 1, '1695741068_d0e1d8e00a7427fedf7c.jpg'),
(3, 'kepin', 'cccf3d86bddad524562a235e24ad4850', 2, ''),
(4, 'norman', 'cccf3d86bddad524562a235e24ad4850', 3, ''),
(6, 'coba', 'cccf3d86bddad524562a235e24ad4850', 3, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inventaris`
--
ALTER TABLE `inventaris`
  ADD PRIMARY KEY (`id_inventaris`);

--
-- Indexes for table `jenis`
--
ALTER TABLE `jenis`
  ADD PRIMARY KEY (`id_jenis`),
  ADD UNIQUE KEY `nama_jenis` (`nama_jenis`) USING HASH,
  ADD UNIQUE KEY `kode_jenis` (`kode_jenis`) USING HASH;

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`);

--
-- Indexes for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD PRIMARY KEY (`id_pengembalian`);

--
-- Indexes for table `petugas`
--
ALTER TABLE `petugas`
  ADD PRIMARY KEY (`id_petugas`),
  ADD UNIQUE KEY `nip` (`nip`),
  ADD UNIQUE KEY `telp` (`telp`);

--
-- Indexes for table `ruang`
--
ALTER TABLE `ruang`
  ADD PRIMARY KEY (`id_ruang`),
  ADD UNIQUE KEY `nama_ruang` (`nama_ruang`) USING HASH,
  ADD UNIQUE KEY `kode_ruang` (`kode_ruang`) USING HASH;

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
-- AUTO_INCREMENT for table `inventaris`
--
ALTER TABLE `inventaris`
  MODIFY `id_inventaris` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `jenis`
--
ALTER TABLE `jenis`
  MODIFY `id_jenis` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_peminjaman` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `pengembalian`
--
ALTER TABLE `pengembalian`
  MODIFY `id_pengembalian` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `petugas`
--
ALTER TABLE `petugas`
  MODIFY `id_petugas` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ruang`
--
ALTER TABLE `ruang`
  MODIFY `id_ruang` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
