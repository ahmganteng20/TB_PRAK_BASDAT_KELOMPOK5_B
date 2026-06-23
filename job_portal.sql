-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for job_portal
CREATE DATABASE IF NOT EXISTS `job_portal` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `job_portal`;

-- Dumping structure for table job_portal.lamaran
DROP TABLE IF EXISTS `lamaran`;
CREATE TABLE IF NOT EXISTS `lamaran` (
  `id_lamaran` int(11) NOT NULL AUTO_INCREMENT,
  `id_pelamar` int(11) DEFAULT NULL,
  `id_lowongan` int(11) DEFAULT NULL,
  `tanggal_lamar` date DEFAULT NULL,
  `status_lamaran` enum('pending','diterima','ditolak') DEFAULT 'pending',
  PRIMARY KEY (`id_lamaran`),
  UNIQUE KEY `pelamar_lowongan_unique` (`id_pelamar`,`id_lowongan`),
  KEY `id_pelamar` (`id_pelamar`),
  KEY `id_lowongan` (`id_lowongan`),
  CONSTRAINT `lamaran_ibfk_1` FOREIGN KEY (`id_pelamar`) REFERENCES `pelamar` (`id_pelamar`) ON DELETE CASCADE,
  CONSTRAINT `lamaran_ibfk_2` FOREIGN KEY (`id_lowongan`) REFERENCES `lowongan` (`id_lowongan`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table job_portal.lamaran: ~0 rows (approximately)

-- Dumping structure for table job_portal.lowongan
DROP TABLE IF EXISTS `lowongan`;
CREATE TABLE IF NOT EXISTS `lowongan` (
  `id_lowongan` int(11) NOT NULL AUTO_INCREMENT,
  `id_perusahaan` int(11) DEFAULT NULL,
  `judul_lowongan` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `kuota` int(11) DEFAULT NULL,
  `status` enum('open','closed') DEFAULT 'open',
  `tanggal_posting` date DEFAULT NULL,
  PRIMARY KEY (`id_lowongan`),
  KEY `id_perusahaan` (`id_perusahaan`),
  CONSTRAINT `lowongan_ibfk_1` FOREIGN KEY (`id_perusahaan`) REFERENCES `perusahaan` (`id_perusahaan`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table job_portal.lowongan: ~1 rows (approximately)
INSERT INTO `lowongan` (`id_lowongan`, `id_perusahaan`, `judul_lowongan`, `deskripsi`, `kuota`, `status`, `tanggal_posting`) VALUES
	(1, 1, 'Frontend Developer', 'Menguasai HTML CSS', 1, 'open', '2026-06-07');

-- Dumping structure for table job_portal.pelamar
DROP TABLE IF EXISTS `pelamar`;
CREATE TABLE IF NOT EXISTS `pelamar` (
  `id_pelamar` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  PRIMARY KEY (`id_pelamar`),
  UNIQUE KEY `id_user_2` (`id_user`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `pelamar_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table job_portal.pelamar: ~1 rows (approximately)
INSERT INTO `pelamar` (`id_pelamar`, `id_user`, `nama_lengkap`, `no_hp`, `alamat`) VALUES
	(1, 3, 'Fauziah Zaenudin', '089876543211', 'Garut');

-- Dumping structure for table job_portal.perusahaan
DROP TABLE IF EXISTS `perusahaan`;
CREATE TABLE IF NOT EXISTS `perusahaan` (
  `id_perusahaan` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `nama_perusahaan` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_perusahaan`),
  UNIQUE KEY `id_user_2` (`id_user`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `perusahaan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table job_portal.perusahaan: ~1 rows (approximately)
INSERT INTO `perusahaan` (`id_perusahaan`, `id_user`, `nama_perusahaan`, `alamat`, `telepon`) VALUES
	(1, 2, 'PT Maju Jaya', 'Bandung', '081234567891');

-- Dumping structure for table job_portal.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role` enum('admin','perusahaan','pelamar') DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table job_portal.users: ~3 rows (approximately)
INSERT INTO `users` (`id_user`, `email`, `password`, `role`) VALUES
	(1, 'admin@gmail.com', '123', 'admin'),
	(2, 'majujaya@gmail.com', '456', 'perusahaan'),
	(3, 'faufau@gmail.com', '789', 'pelamar');

-- Dumping structure for trigger job_portal.cek_kuota_lowongan
DROP TRIGGER IF EXISTS `cek_kuota_lowongan`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER cek_kuota_lowongan AFTER INSERT ON lamaran
FOR EACH ROW
BEGIN
DECLARE total_pendaftar INT;
DECLARE batas_kuota INT;
SELECT COUNT(*) INTO total_pendaftar FROM lamaran WHERE id_lowongan = NEW.id_lowongan;
SELECT kuota INTO batas_kuota FROM lowongan WHERE id_lowongan = NEW.id_lowongan;
IF total_pendaftar >= batas_kuota THEN
UPDATE lowongan SET status = 'closed' WHERE id_lowongan = NEW.id_lowongan;
END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
