-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 29, 2025 at 01:58 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kesang`
--

-- --------------------------------------------------------

--
-- Table structure for table `asuransi`
--

CREATE TABLE `asuransi` (
  `no_asuransi` varchar(30) NOT NULL,
  `nik` varchar(16) DEFAULT NULL,
  `nama_asuransi` varchar(50) DEFAULT NULL,
  `jenis_asuransi` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `asuransi`
--

INSERT INTO `asuransi` (`no_asuransi`, `nik`, `nama_asuransi`, `jenis_asuransi`) VALUES
('AS013', '3201053012780011', 'AXA Mandiri', 'Kesehatan'),
('AS014', '3201053012790012', 'Manulife', 'Kesehatan'),
('AS015', '3201053012800013', 'Allianz', 'Kesehatan');

-- --------------------------------------------------------

--
-- Table structure for table `bpjs`
--

CREATE TABLE `bpjs` (
  `no_bpjs` varchar(30) NOT NULL,
  `nik` varchar(16) DEFAULT NULL,
  `status_peserta` varchar(20) DEFAULT NULL,
  `faskes_tingkat1` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bpjs`
--

INSERT INTO `bpjs` (`no_bpjs`, `nik`, `status_peserta`, `faskes_tingkat1`) VALUES
('BPJS0013', '3201053012780011', 'Aktif', 'Puskesmas Bogor Barat'),
('BPJS0014', '3201053012790012', 'Aktif', 'RSUD Kota Bogor'),
('BPJS0015', '3201053012800013', 'Aktif', 'Puskesmas Cilendek Barat');

-- --------------------------------------------------------

--
-- Table structure for table `disdukcapil`
--

CREATE TABLE `disdukcapil` (
  `nik` varchar(16) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P','','') DEFAULT NULL,
  `gol_darah` enum('A','B','AB','O') DEFAULT NULL,
  `alamat` varchar(100) DEFAULT NULL,
  `rt` varchar(3) DEFAULT NULL,
  `rw` varchar(3) DEFAULT NULL,
  `kelurahan` varchar(50) DEFAULT NULL,
  `kecamatan` varchar(50) DEFAULT NULL,
  `agama` varchar(20) DEFAULT NULL,
  `status_perkawinan` enum('KAWIN','BELUM KAWIN','','') DEFAULT NULL,
  `pekerjaan` varchar(50) DEFAULT NULL,
  `kewarganegaraan` varchar(10) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `disdukcapil`
--

INSERT INTO `disdukcapil` (`nik`, `nama`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `gol_darah`, `alamat`, `rt`, `rw`, `kelurahan`, `kecamatan`, `agama`, `status_perkawinan`, `pekerjaan`, `kewarganegaraan`, `foto`) VALUES
('3201053012780011', 'Ifal Al falaq', 'Bogor', '2008-09-16', 'L', 'B', 'Jl. Melati No. 25', '010', '006', 'Ciwaringin', 'Bogor Barat', 'Islam', 'BELUM KAWIN', 'Karyawan Swasta', 'WNI', 'uploads/profile.jpg'),
('3201053012790012', 'Alvinodiansyah', 'Jakarta', '2008-10-23', 'L', 'A', 'Jl. Dahlia No. 5', '011', '008', 'Sindangbarang', 'Bogor Barat', 'Islam', 'BELUM KAWIN', 'Mahasiswa', 'WNI', 'uploads/profile2.jpg'),
('3201053012800013', 'Farel Prasetia', 'Depok', '2008-05-29', 'L', 'O', 'Jl. Cempaka No. 18', '012', '009', 'Cilendek Barat', 'Bogor Barat', 'Islam', 'BELUM KAWIN', 'Guru', 'WNI', 'uploads/profile3.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `rawat_inap`
--

CREATE TABLE `rawat_inap` (
  `no_rekam_medis` varchar(30) NOT NULL,
  `nik` varchar(16) DEFAULT NULL,
  `tinggi_badan` int(11) DEFAULT NULL,
  `berat_badan` int(11) DEFAULT NULL,
  `diagnosa` varchar(100) DEFAULT NULL,
  `tingkat_penyakit` enum('Ringan','Sedang','Berat','') NOT NULL,
  `penyebab_sakit` varchar(30) NOT NULL,
  `nama_dokter` varchar(100) DEFAULT NULL,
  `tanggal` date NOT NULL DEFAULT current_timestamp(),
  `jenis_perawatan` enum('Rawat inap','Rawat jalan','','') NOT NULL,
  `jumlah_hari` int(3) NOT NULL,
  `rumah_sakit` varchar(50) NOT NULL,
  `status` enum('Sembuh','Belum Sembuh','','') NOT NULL,
  `penanggung_jawab_biaya` enum('BPJS','Asuransi','Mandiri','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rawat_inap`
--

INSERT INTO `rawat_inap` (`no_rekam_medis`, `nik`, `tinggi_badan`, `berat_badan`, `diagnosa`, `tingkat_penyakit`, `penyebab_sakit`, `nama_dokter`, `tanggal`, `jenis_perawatan`, `jumlah_hari`, `rumah_sakit`, `status`, `penanggung_jawab_biaya`) VALUES
('RM0119', '3201053012790012', 168, 55, 'Usus Buntu', 'Berat', 'Pola Makan', 'Dr.Citra', '2025-06-28', 'Rawat inap', 8, 'RS BANGSA SEJAHTERA', 'Sembuh', 'Asuransi'),
('RM016', '3201053012780011', 170, 58, 'Demam Berdarah', 'Ringan', 'Gigitan Nyamuk', 'Dr. Rudi', '2025-08-10', 'Rawat inap', 6, 'RSUD Bogor', 'Sembuh', 'BPJS'),
('RM018', '3201053012790012', 168, 55, 'Maag Kronis', 'Berat', 'Infeksi Bakteri', 'Dr. Ahmad', '2025-02-02', 'Rawat inap', 7, 'RS PMI Bogor', 'Sembuh', 'Asuransi');

-- --------------------------------------------------------

--
-- Table structure for table `rawat_jalan`
--

CREATE TABLE `rawat_jalan` (
  `no_rekam_medis` varchar(30) NOT NULL,
  `nik` varchar(16) DEFAULT NULL,
  `tinggi_badan` int(11) DEFAULT NULL,
  `berat_badan` int(11) DEFAULT NULL,
  `diagnosa` varchar(100) DEFAULT NULL,
  `tingkat_penyakit` enum('Ringan','Sedang','Berat','') NOT NULL,
  `penyebab_sakit` varchar(30) NOT NULL,
  `nama_dokter` varchar(100) DEFAULT NULL,
  `tanggal` date NOT NULL DEFAULT current_timestamp(),
  `jenis_perawatan` enum('Rawat inap','Rawat jalan','','') NOT NULL,
  `jumlah_datang` int(3) NOT NULL,
  `rumah_sakit` varchar(50) NOT NULL,
  `status` enum('Sembuh','Belum Sembuh','','') NOT NULL,
  `penanggung_jawab_biaya` enum('BPJS','Asuransi','Mandiri','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rawat_jalan`
--

INSERT INTO `rawat_jalan` (`no_rekam_medis`, `nik`, `tinggi_badan`, `berat_badan`, `diagnosa`, `tingkat_penyakit`, `penyebab_sakit`, `nama_dokter`, `tanggal`, `jenis_perawatan`, `jumlah_datang`, `rumah_sakit`, `status`, `penanggung_jawab_biaya`) VALUES
('RM017', '3201053012780011', 170, 70, 'Maag', 'Ringan', 'Pola Makan', 'Dr. Lina', '2025-08-22', 'Rawat jalan', 1, 'RS Salak', 'Belum Sembuh', 'Mandiri'),
('RM019', '3201053012790012', 168, 65, 'Alergi Kulit', 'Ringan', 'Debu', 'Dr. Santi', '2025-08-23', 'Rawat jalan', 2, 'RSUD Kota Bogor', 'Belum Sembuh', 'BPJS'),
('RM021', '3201053012800013', 173, 72, 'Migrain', 'Ringan', 'Stress', 'Dr. Laila', '2025-08-25', 'Rawat jalan', 2, 'RS PMI Bogor', 'Sembuh', 'Mandiri');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(10) NOT NULL,
  `nama_lengkap` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `no_telepon` varchar(15) NOT NULL,
  `nik` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_data_pasien`
-- (See below for the actual view)
--
CREATE TABLE `view_data_pasien` (
`nik` varchar(16)
,`nama_lengkap` varchar(100)
,`jenis_kelamin` enum('L','P','','')
,`tanggal_lahir` date
,`gol_darah` enum('A','B','AB','O')
,`foto` varchar(255)
,`tinggi_badan` int(11)
,`berat_badan` int(11)
,`no_bpjs` varchar(30)
,`no_asuransi` varchar(30)
);

-- --------------------------------------------------------

--
-- Structure for view `view_data_pasien`
--
DROP TABLE IF EXISTS `view_data_pasien`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_data_pasien`  AS SELECT `d`.`nik` AS `nik`, `d`.`nama` AS `nama_lengkap`, `d`.`jenis_kelamin` AS `jenis_kelamin`, `d`.`tanggal_lahir` AS `tanggal_lahir`, `d`.`gol_darah` AS `gol_darah`, max(`d`.`foto`) AS `foto`, coalesce(max(`ri`.`tinggi_badan`),max(`rj`.`tinggi_badan`)) AS `tinggi_badan`, coalesce(max(`ri`.`berat_badan`),max(`rj`.`berat_badan`)) AS `berat_badan`, max(`b`.`no_bpjs`) AS `no_bpjs`, max(`a`.`no_asuransi`) AS `no_asuransi` FROM ((((`disdukcapil` `d` left join `rawat_inap` `ri` on(`d`.`nik` = `ri`.`nik`)) left join `rawat_jalan` `rj` on(`d`.`nik` = `rj`.`nik`)) left join `bpjs` `b` on(`d`.`nik` = `b`.`nik`)) left join `asuransi` `a` on(`d`.`nik` = `a`.`nik`)) GROUP BY `d`.`nik`, `d`.`nama`, `d`.`jenis_kelamin`, `d`.`tanggal_lahir`, `d`.`gol_darah` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `asuransi`
--
ALTER TABLE `asuransi`
  ADD PRIMARY KEY (`no_asuransi`),
  ADD KEY `nik` (`nik`);

--
-- Indexes for table `bpjs`
--
ALTER TABLE `bpjs`
  ADD PRIMARY KEY (`no_bpjs`),
  ADD KEY `nik` (`nik`);

--
-- Indexes for table `disdukcapil`
--
ALTER TABLE `disdukcapil`
  ADD PRIMARY KEY (`nik`);

--
-- Indexes for table `rawat_inap`
--
ALTER TABLE `rawat_inap`
  ADD PRIMARY KEY (`no_rekam_medis`),
  ADD KEY `nik` (`nik`);

--
-- Indexes for table `rawat_jalan`
--
ALTER TABLE `rawat_jalan`
  ADD PRIMARY KEY (`no_rekam_medis`),
  ADD KEY `nik` (`nik`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `nik` (`nik`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `asuransi`
--
ALTER TABLE `asuransi`
  ADD CONSTRAINT `asuransi_ibfk_1` FOREIGN KEY (`nik`) REFERENCES `disdukcapil` (`nik`);

--
-- Constraints for table `bpjs`
--
ALTER TABLE `bpjs`
  ADD CONSTRAINT `bpjs_ibfk_1` FOREIGN KEY (`nik`) REFERENCES `disdukcapil` (`nik`);

--
-- Constraints for table `rawat_inap`
--
ALTER TABLE `rawat_inap`
  ADD CONSTRAINT `rawat_inap_ibfk_1` FOREIGN KEY (`nik`) REFERENCES `disdukcapil` (`nik`);

--
-- Constraints for table `rawat_jalan`
--
ALTER TABLE `rawat_jalan`
  ADD CONSTRAINT `rawat_jalan_ibfk_1` FOREIGN KEY (`nik`) REFERENCES `disdukcapil` (`nik`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
