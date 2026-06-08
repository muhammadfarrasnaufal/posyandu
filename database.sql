CREATE DATABASE IF NOT EXISTS `posyandu_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `posyandu_db`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fullname` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','user') NOT NULL DEFAULT 'user',
  `avatar` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `posyandu_records` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `nama` VARCHAR(150) NOT NULL,
  `jenis_kelamin` VARCHAR(20) NOT NULL,
  `tanggal_lahir` DATE NOT NULL,
  `berat_badan` DECIMAL(5,2) NOT NULL,
  `tinggi_badan` DECIMAL(5,2) NOT NULL,
  `tanggal_kunjungan` DATE NOT NULL,
  `catatan` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_posyandu_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`fullname`, `email`, `password`, `role`) VALUES
('Admin Posyandu', 'admin@posyandu.local', '$2y$10$TtqVmc0YlZ/MLYdeyLoi9.xKabexTEAkk344noLU2oQMfot/PHnbG', 'admin'),
('Pengguna Posyandu', 'user@posyandu.local', '$2y$10$xj5JsEWt1xHQmTHUSjsVUOey6Mcp/sknOfPwulWyxHX6zU8uR6TRW', 'user');

INSERT INTO `posyandu_records` (`user_id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `berat_badan`, `tinggi_badan`, `tanggal_kunjungan`, `catatan`) VALUES
(2, 'Aisyah', 'Perempuan', '2022-08-10', 8.2, 67.5, '2024-06-05', 'Imunisasi lengkap, berat badan stabil.'),
(2, 'Rafi', 'Laki-laki', '2021-11-23', 11.4, 78.0, '2024-06-05', 'Perlu kontrol gizi lagi 1 bulan ke depan.');

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(150) NOT NULL,
  `body` TEXT NOT NULL,
  `type` ENUM('info','success','warning','danger') NOT NULL DEFAULT 'info',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `immunization_schedules` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `child_name` VARCHAR(150) NOT NULL,
  `jenis_kelamin` VARCHAR(20) NOT NULL,
  `tanggal_lahir` DATE NOT NULL,
  `vaccine_name` VARCHAR(150) NOT NULL,
  `jadwal` DATE NOT NULL,
  `status` VARCHAR(50) NOT NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_schedule_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `immunization_schedules` (`user_id`, `child_name`, `jenis_kelamin`, `tanggal_lahir`, `vaccine_name`, `jadwal`, `status`, `notes`) VALUES
(2, 'Aisyah', 'Perempuan', '2022-08-10', 'MR', '2026-06-15', 'Menunggu', 'Jadwal imunisasi MR untuk Aisyah.'),
(2, 'Rafi', 'Laki-laki', '2021-11-23', 'DTP', '2026-06-20', 'Menunggu', 'Jadwal imunisasi DTP untuk Rafi.');
