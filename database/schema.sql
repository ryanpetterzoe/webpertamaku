-- ============================================================
-- Database: websmk
-- Website SMK Pertamaku - Full Featured School Website
-- ============================================================

CREATE DATABASE IF NOT EXISTS `websmk` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `websmk`;

-- ========================
-- TABEL: admins
-- ========================
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('superadmin','admin','operator') DEFAULT 'admin',
  `avatar` VARCHAR(255) DEFAULT NULL,
  `last_login` DATETIME DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABEL: settings
-- ========================
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `key` VARCHAR(100) UNIQUE NOT NULL,
  `value` TEXT,
  `group` VARCHAR(50) DEFAULT 'general',
  `label` VARCHAR(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABEL: pages
-- ========================
CREATE TABLE IF NOT EXISTS `pages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(200) UNIQUE NOT NULL,
  `content` LONGTEXT,
  `meta_description` TEXT,
  `is_published` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABEL: news (berita)
-- ========================
CREATE TABLE IF NOT EXISTS `news` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(250) NOT NULL,
  `slug` VARCHAR(250) UNIQUE NOT NULL,
  `excerpt` TEXT,
  `content` LONGTEXT,
  `image` VARCHAR(255) DEFAULT NULL,
  `category` VARCHAR(100) DEFAULT 'Berita',
  `author` VARCHAR(100) DEFAULT 'Admin',
  `views` INT DEFAULT 0,
  `is_published` TINYINT(1) DEFAULT 1,
  `published_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABEL: gallery
-- ========================
CREATE TABLE IF NOT EXISTS `gallery` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT,
  `image` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) DEFAULT 'Umum',
  `is_published` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABEL: teachers (guru)
-- ========================
CREATE TABLE IF NOT EXISTS `teachers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `nip` VARCHAR(50) DEFAULT NULL,
  `position` VARCHAR(150) DEFAULT NULL,
  `subject` VARCHAR(150) DEFAULT NULL,
  `education` VARCHAR(200) DEFAULT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `sort_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABEL: staff
-- ========================
CREATE TABLE IF NOT EXISTS `staff` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `nip` VARCHAR(50) DEFAULT NULL,
  `position` VARCHAR(150) DEFAULT NULL,
  `department` VARCHAR(150) DEFAULT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `sort_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABEL: programs (jurusan)
-- ========================
CREATE TABLE IF NOT EXISTS `programs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(200) NOT NULL,
  `code` VARCHAR(20) DEFAULT NULL,
  `description` LONGTEXT,
  `image` VARCHAR(255) DEFAULT NULL,
  `icon` VARCHAR(100) DEFAULT 'fas fa-laptop-code',
  `quota` INT DEFAULT 36,
  `is_active` TINYINT(1) DEFAULT 1,
  `sort_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABEL: achievements (prestasi)
-- ========================
CREATE TABLE IF NOT EXISTS `achievements` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(250) NOT NULL,
  `description` TEXT,
  `image` VARCHAR(255) DEFAULT NULL,
  `level` ENUM('sekolah','kabupaten','provinsi','nasional','internasional') DEFAULT 'sekolah',
  `year` YEAR DEFAULT NULL,
  `is_published` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABEL: testimonials
-- ========================
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `position` VARCHAR(150) DEFAULT NULL,
  `content` TEXT NOT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `rating` TINYINT DEFAULT 5,
  `is_published` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABEL: agenda/events
-- ========================
CREATE TABLE IF NOT EXISTS `agenda` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(250) NOT NULL,
  `description` TEXT,
  `location` VARCHAR(200) DEFAULT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE DEFAULT NULL,
  `is_published` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABEL: contacts (pesan masuk)
-- ========================
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `subject` VARCHAR(250) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABEL: SPMB - registrations
-- ========================
CREATE TABLE IF NOT EXISTS `spmb_registrations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `registration_number` VARCHAR(20) UNIQUE NOT NULL,
  `academic_year` VARCHAR(20) NOT NULL,
  `full_name` VARCHAR(150) NOT NULL,
  `nick_name` VARCHAR(50) DEFAULT NULL,
  `gender` ENUM('L','P') NOT NULL,
  `birth_place` VARCHAR(100) DEFAULT NULL,
  `birth_date` DATE DEFAULT NULL,
  `religion` VARCHAR(50) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `rt` VARCHAR(5) DEFAULT NULL,
  `rw` VARCHAR(5) DEFAULT NULL,
  `village` VARCHAR(100) DEFAULT NULL,
  `district` VARCHAR(100) DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `province` VARCHAR(100) DEFAULT NULL,
  `postal_code` VARCHAR(10) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `school_origin` VARCHAR(200) DEFAULT NULL,
  `nisn` VARCHAR(20) DEFAULT NULL,
  `un_score` DECIMAL(5,2) DEFAULT NULL,
  `program_id` INT DEFAULT NULL,
  `program_choice2` INT DEFAULT NULL,
  `father_name` VARCHAR(150) DEFAULT NULL,
  `father_job` VARCHAR(100) DEFAULT NULL,
  `father_phone` VARCHAR(20) DEFAULT NULL,
  `mother_name` VARCHAR(150) DEFAULT NULL,
  `mother_job` VARCHAR(100) DEFAULT NULL,
  `guardian_name` VARCHAR(150) DEFAULT NULL,
  `guardian_phone` VARCHAR(20) DEFAULT NULL,
  `parent_income` VARCHAR(50) DEFAULT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `doc_kk` VARCHAR(255) DEFAULT NULL,
  `doc_akta` VARCHAR(255) DEFAULT NULL,
  `doc_ijazah` VARCHAR(255) DEFAULT NULL,
  `doc_raport` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('pending','verifikasi','diterima','ditolak') DEFAULT 'pending',
  `notes` TEXT DEFAULT NULL,
  `verified_by` INT DEFAULT NULL,
  `verified_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`program_id`) REFERENCES `programs`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABEL: spmb_settings
-- ========================
CREATE TABLE IF NOT EXISTS `spmb_settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `academic_year` VARCHAR(20) NOT NULL,
  `open_date` DATE NOT NULL,
  `close_date` DATE NOT NULL,
  `announcement_date` DATE DEFAULT NULL,
  `quota_total` INT DEFAULT 144,
  `is_active` TINYINT(1) DEFAULT 1,
  `info` TEXT DEFAULT NULL,
  `requirements` LONGTEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABEL: sliders (hero banner)
-- ========================
CREATE TABLE IF NOT EXISTS `sliders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) DEFAULT NULL,
  `subtitle` TEXT DEFAULT NULL,
  `image` VARCHAR(255) NOT NULL,
  `button_text` VARCHAR(100) DEFAULT NULL,
  `button_url` VARCHAR(255) DEFAULT NULL,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABEL: social media links
-- ========================
CREATE TABLE IF NOT EXISTS `social_media` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `platform` VARCHAR(50) NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `icon` VARCHAR(100) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- DATA DEFAULT
-- ========================

-- Admin default (password: admin123)
INSERT INTO `admins` (`name`, `username`, `email`, `password`, `role`) VALUES
('Super Admin', 'admin', 'admin@smkpertamaku.sch.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin');

-- Settings default
INSERT INTO `settings` (`key`, `value`, `group`, `label`) VALUES
('school_name', 'SMK Pertamaku', 'general', 'Nama Sekolah'),
('school_tagline', 'Mencetak Generasi Terampil dan Berkarakter', 'general', 'Tagline'),
('school_address', 'Jl. Pendidikan No. 1, Kota Anda', 'general', 'Alamat'),
('school_phone', '(021) 1234567', 'general', 'Telepon'),
('school_email', 'info@smkpertamaku.sch.id', 'general', 'Email'),
('school_website', 'www.smkpertamaku.sch.id', 'general', 'Website'),
('school_npsn', '12345678', 'general', 'NPSN'),
('school_accreditation', 'A', 'general', 'Akreditasi'),
('school_logo', '', 'general', 'Logo Sekolah'),
('school_logo_dark', '', 'general', 'Logo Sekolah (Dark)'),
('school_favicon', '', 'general', 'Favicon'),
('about_vision', 'Menjadi SMK unggulan yang menghasilkan lulusan berkompeten, berkarakter, dan siap kerja di era global.', 'about', 'Visi'),
('about_mission', '1. Menyelenggarakan pendidikan berkualitas berbasis kompetensi\n2. Membangun karakter siswa yang berakhlak mulia\n3. Menjalin kemitraan dengan dunia industri\n4. Mengembangkan potensi siswa secara optimal\n5. Menciptakan lingkungan belajar yang kondusif', 'about', 'Misi'),
('about_history', 'SMK Pertamaku didirikan pada tahun 2000 dengan tekad kuat untuk mencetak generasi muda yang terampil dan siap kerja. Dengan fasilitas lengkap dan tenaga pengajar berpengalaman, kami terus berkembang menjadi sekolah kejuruan pilihan masyarakat.', 'about', 'Sejarah'),
('principal_name', 'Drs. Ahmad Sulaiman, M.Pd', 'about', 'Nama Kepala Sekolah'),
('principal_photo', '', 'about', 'Foto Kepala Sekolah'),
('principal_message', 'Selamat datang di website resmi SMK Pertamaku. Kami berkomitmen untuk memberikan pendidikan terbaik bagi putra-putri Anda.', 'about', 'Sambutan Kepala Sekolah'),
('footer_about', 'SMK Pertamaku adalah sekolah menengah kejuruan unggulan yang berkomitmen mencetak lulusan berkompeten dan berkarakter.', 'footer', 'Tentang di Footer'),
('meta_title', 'SMK Pertamaku - Sekolah Menengah Kejuruan Unggulan', 'seo', 'Meta Title'),
('meta_description', 'Website resmi SMK Pertamaku. Pendaftaran siswa baru, informasi jurusan, berita dan kegiatan sekolah.', 'seo', 'Meta Description'),
('ga_code', '', 'seo', 'Google Analytics Code'),
('whatsapp_number', '6281234567890', 'contact', 'Nomor WhatsApp'),
('maps_embed', '', 'contact', 'Google Maps Embed URL'),
('stats_students', '750', 'stats', 'Jumlah Siswa'),
('stats_teachers', '45', 'stats', 'Jumlah Guru'),
('stats_programs', '4', 'stats', 'Jumlah Jurusan'),
('stats_alumni', '2000', 'stats', 'Jumlah Alumni'),
('theme_default', 'light', 'appearance', 'Tema Default'),
('accent_primary', '#2563eb', 'appearance', 'Warna Primer'),
('accent_dark',    '#1d4ed8', 'appearance', 'Warna Primer (Gelap)');

-- Programs default
INSERT INTO `programs` (`name`, `code`, `description`, `icon`, `quota`, `sort_order`) VALUES
('Teknik Komputer dan Jaringan', 'TKJ', 'Program keahlian yang mempelajari instalasi jaringan komputer, troubleshooting hardware, dan administrasi server.', 'fas fa-network-wired', 36, 1),
('Rekayasa Perangkat Lunak', 'RPL', 'Program keahlian pengembangan aplikasi web, mobile, dan desktop menggunakan berbagai bahasa pemrograman modern.', 'fas fa-laptop-code', 36, 2),
('Teknik Audio Video', 'TAV', 'Program keahlian yang mempelajari elektronika, sistem audio, perangkat video dan multimedia.', 'fas fa-tv', 36, 3),
('Akuntansi dan Keuangan Lembaga', 'AKL', 'Program keahlian yang mempelajari pencatatan keuangan, perpajakan, dan manajemen keuangan perusahaan.', 'fas fa-calculator', 36, 4);

-- SPMB Settings default
INSERT INTO `spmb_settings` (`academic_year`, `open_date`, `close_date`, `announcement_date`, `quota_total`, `is_active`, `info`, `requirements`) VALUES
('2025/2026', '2025-01-01', '2025-06-30', '2025-07-15', 144, 1,
'Penerimaan Peserta Didik Baru (PPDB) SMK Pertamaku Tahun Ajaran 2025/2026 telah dibuka. Daftarkan diri Anda sekarang!',
'1. Lulusan SMP/MTs/sederajat\n2. Usia maksimal 21 tahun\n3. Sehat jasmani dan rohani\n4. Berkelakuan baik\n5. Memiliki NISN yang aktif');

-- Social Media
INSERT INTO `social_media` (`platform`, `url`, `icon`, `is_active`) VALUES
('Facebook', 'https://facebook.com/smkpertamaku', 'fab fa-facebook-f', 1),
('Instagram', 'https://instagram.com/smkpertamaku', 'fab fa-instagram', 1),
('YouTube', 'https://youtube.com/@smkpertamaku', 'fab fa-youtube', 1),
('Twitter/X', 'https://twitter.com/smkpertamaku', 'fab fa-x-twitter', 1);

-- Sliders default
INSERT INTO `sliders` (`title`, `subtitle`, `image`, `button_text`, `button_url`, `sort_order`) VALUES
('Selamat Datang di SMK Pertamaku', 'Mencetak Generasi Terampil, Berkarakter, dan Siap Kerja di Era Digital', 'slider1.jpg', 'Daftar Sekarang', '/spmb', 1),
('Jurusan Unggulan Kami', 'Pilih jurusan sesuai minat dan bakatmu. 4 Program Keahlian tersedia.', 'slider2.jpg', 'Lihat Jurusan', '/jurusan', 2),
('Raih Prestasi Bersama Kami', 'Ribuan alumni sukses telah membuktikan kualitas pendidikan di SMK Pertamaku', 'slider3.jpg', 'Tentang Kami', '/tentang', 3);

-- Sample News
INSERT INTO `news` (`title`, `slug`, `excerpt`, `content`, `category`, `author`, `is_published`) VALUES
('Penerimaan Peserta Didik Baru Tahun 2025/2026 Telah Dibuka', 'ppdb-2025-2026-dibuka', 'SMK Pertamaku resmi membuka pendaftaran siswa baru untuk tahun ajaran 2025/2026. Pendaftaran dapat dilakukan secara online melalui website ini.', '<p>SMK Pertamaku dengan bangga mengumumkan pembukaan resmi Penerimaan Peserta Didik Baru (PPDB) untuk tahun ajaran 2025/2026.</p><p>Pendaftaran dapat dilakukan secara online melalui website ini pada menu SPMB. Kuota yang tersedia adalah 144 siswa untuk 4 jurusan.</p><p>Segera daftarkan diri Anda sebelum batas waktu pendaftaran berakhir!</p>', 'Pengumuman', 'Admin', 1),
('SMK Pertamaku Raih Juara 1 LKS Tingkat Provinsi', 'juara-lks-provinsi-2025', 'Siswa SMK Pertamaku berhasil meraih juara 1 dalam Lomba Kompetensi Siswa (LKS) tingkat provinsi bidang Web Technology.', '<p>Kebanggaan luar biasa dirasakan seluruh keluarga besar SMK Pertamaku. Satu siswa dari jurusan RPL berhasil meraih Juara 1 dalam ajang LKS tingkat Provinsi.</p><p>Prestasi gemilang ini merupakan hasil kerja keras dan dedikasi tinggi selama berbulan-bulan latihan intensif.</p>', 'Prestasi', 'Admin', 1),
('Workshop Industri 4.0 Bersama PT. Maju Teknologi', 'workshop-industri-40', 'SMK Pertamaku menggelar workshop bertema Industri 4.0 bekerja sama dengan PT. Maju Teknologi Indonesia.', '<p>SMK Pertamaku kembali menunjukkan komitmennya dalam mempersiapkan siswa menghadapi era industri 4.0 dengan menggelar workshop khusus.</p><p>Workshop ini diikuti oleh 100 siswa pilihan dari berbagai jurusan dan memberikan wawasan berharga tentang tren teknologi masa depan.</p>', 'Kegiatan', 'Admin', 1);

-- Sample Achievements
INSERT INTO `achievements` (`title`, `description`, `level`, `year`) VALUES
('Juara 1 LKS Provinsi - Web Technology', 'Meraih juara 1 dalam Lomba Kompetensi Siswa bidang Web Technology tingkat provinsi', 'provinsi', 2025),
('Juara 2 Olimpiade Matematika Kabupaten', 'Siswa SMK Pertamaku meraih posisi runner-up olimpiade matematika tingkat kabupaten', 'kabupaten', 2024),
('Akreditasi A dari BAN-SM', 'SMK Pertamaku berhasil mempertahankan akreditasi A dari Badan Akreditasi Nasional', 'nasional', 2024),
('Sekolah Adiwiyata Tingkat Nasional', 'Penghargaan Sekolah Adiwiyata dari Kementerian Lingkungan Hidup', 'nasional', 2023);

-- Sample Testimonials
INSERT INTO `testimonials` (`name`, `position`, `content`, `rating`) VALUES
('Budi Santoso', 'Alumni RPL - Angkatan 2022, Software Engineer di Gojek', 'SMK Pertamaku memberikan fondasi yang kuat dalam pemrograman. Skills yang saya dapat di sekolah langsung bisa diaplikasikan di dunia kerja.', 5),
('Siti Rahayu', 'Alumni AKL - Angkatan 2021, Staff Akunting di Bank BRI', 'Guru-guru di SMK Pertamaku sangat profesional dan perhatian. Saya sangat bersyukur pernah belajar di sini.', 5),
('Ahmad Fauzi', 'Orang Tua Siswa', 'Puas dengan perkembangan anak saya sejak masuk SMK Pertamaku. Sekolah ini benar-benar serius mendidik siswa.', 5);

-- Sample Agenda
INSERT INTO `agenda` (`title`, `description`, `location`, `start_date`, `end_date`) VALUES
('Batas Akhir Pendaftaran PPDB', 'Segera daftarkan diri sebelum batas waktu berakhir', 'Online/Sekolah', '2025-06-30', '2025-06-30'),
('Pengumuman Hasil Seleksi PPDB', 'Pengumuman hasil seleksi PPDB tahun ajaran 2025/2026', 'Website Sekolah', '2025-07-15', '2025-07-15'),
('Hari Pertama Masuk Sekolah', 'Masa Pengenalan Lingkungan Sekolah (MPLS)', 'SMK Pertamaku', '2025-07-21', '2025-07-25');

-- Sample Teachers
INSERT INTO `teachers` (`name`, `nip`, `position`, `subject`, `education`, `sort_order`) VALUES
('Drs. Ahmad Sulaiman, M.Pd', '196501011990031001', 'Kepala Sekolah', '-', 'S2 Manajemen Pendidikan', 1),
('Ir. Budi Hartono, M.T', '197003151995031002', 'Ketua Jurusan TKJ', 'Jaringan Komputer', 'S2 Teknik Informatika', 2),
('Siti Aminah, S.Kom, M.Cs', '197805202003122001', 'Ketua Jurusan RPL', 'Pemrograman Web', 'S2 Ilmu Komputer', 3),
('Hendra Wijaya, S.T', '198201102008011003', 'Guru Produktif TAV', 'Elektronika', 'S1 Teknik Elektro', 4),
('Dewi Kusuma, S.E, M.M', '197910082005012002', 'Ketua Jurusan AKL', 'Akuntansi', 'S2 Manajemen', 5);
