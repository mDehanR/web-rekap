-- ============================================
-- Setup Database: rekap_nilai_db
-- Jalankan query ini di phpMyAdmin / MySQL CLI
-- ============================================

CREATE DATABASE IF NOT EXISTS rekap_nilai_db 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE rekap_nilai_db;

CREATE TABLE IF NOT EXISTS siswa_nilai (
    id           INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nama_siswa   VARCHAR(100) NOT NULL,
    mata_pelajaran VARCHAR(50) NOT NULL DEFAULT '',
    tugas        FLOAT        NOT NULL DEFAULT 0,
    uts          FLOAT        NOT NULL DEFAULT 0,
    uas          FLOAT        NOT NULL DEFAULT 0,
    akhir        FLOAT        NOT NULL DEFAULT 0,
    huruf        VARCHAR(3)   NOT NULL DEFAULT '',
    predikat     VARCHAR(20)  NOT NULL DEFAULT '',
    created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data contoh (opsional)
INSERT INTO siswa_nilai (nama_siswa, mata_pelajaran, tugas, uts, uas, akhir, huruf, predikat) VALUES
('Ahmad Rizky Pratama',   'Matematika', 85, 80, 90, 85.5,  'B+', 'Baik'),
('Siti Nurhaliza',        'Bahasa Indonesia', 90, 92, 95, 92.6,  'A',  'Sangat Baik'),
('Budi Santoso',          'IPAS', 70, 65, 75, 70.5,  'C+', 'Cukup'),
('Dewi Rahayu',           'Sejarah', 78, 82, 88, 83.4,  'B+', 'Baik'),
('Fajar Ramadhan',        'PPKN', 60, 55, 65, 60.5,  'D',  'Kurang');

-- Untuk database yang sudah ada, jalankan query berikut untuk menambahkan kolom mata_pelajaran:
-- ALTER TABLE siswa_nilai ADD COLUMN mata_pelajaran VARCHAR(50) NOT NULL DEFAULT '' AFTER nama_siswa;
