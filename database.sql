-- =====================================================
-- SGRC - Système de Gestion des Registres de la Commune
-- Municipal Register Management System
-- Version: 1.0
-- Languages: Arabic (ar) + French (fr)
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------
-- Table: settings
-- -----------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(50) DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`) VALUES
('commune_name', 'بلدية المدينة', 'general'),
('commune_name_fr', 'Commune de la Ville', 'general'),
('commune_address', 'شارع الحرية، المدينة', 'general'),
('commune_address_fr', 'Rue de la Liberté, Ville', 'general'),
('commune_phone', '0234567890', 'general'),
('commune_email', 'contact@commune.dz', 'general'),
('default_language', 'ar', 'general'),
('theme', 'light', 'appearance'),
('logo_path', 'assets/img/logo.png', 'appearance'),
('items_per_page', '25', 'general'),
('session_timeout', '30', 'security'),
('backup_auto', '0', 'backup'),
('ocr_language', 'ara+fra', 'ocr');

-- -----------------------------------------------------
-- Table: users
-- -----------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `full_name_fr` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','operator','viewer') NOT NULL DEFAULT 'operator',
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `last_ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin: admin / admin123 (change after first login!)
INSERT INTO `users` (`username`, `full_name`, `full_name_fr`, `email`, `password_hash`, `role`) VALUES
('admin', 'المدير العام', 'Administrateur Général', 'admin@commune.dz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin');

-- -----------------------------------------------------
-- Table: activity_logs
-- -----------------------------------------------------
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `module` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: citizens
-- -----------------------------------------------------
DROP TABLE IF EXISTS `citizens`;
CREATE TABLE `citizens` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `national_id` varchar(20) DEFAULT NULL COMMENT 'رقم الهوية الوطنية',
  `family_name` varchar(100) NOT NULL COMMENT 'الاسم العائلي',
  `first_name` varchar(100) NOT NULL COMMENT 'الاسم الشخصي',
  `father_name` varchar(100) DEFAULT NULL COMMENT 'اسم الأب',
  `mother_name` varchar(100) DEFAULT NULL COMMENT 'اسم الأم',
  `birth_date` date DEFAULT NULL COMMENT 'تاريخ الميلاد',
  `birth_place` varchar(100) DEFAULT NULL COMMENT 'مكان الميلاد',
  `gender` enum('male','female') DEFAULT NULL COMMENT 'الجنس',
  `address` varchar(255) DEFAULT NULL COMMENT 'العنوان',
  `neighborhood` varchar(100) DEFAULT NULL COMMENT 'الحي',
  `photo_path` varchar(255) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL COMMENT 'ملاحظات',
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `updated_by` int(11) UNSIGNED DEFAULT NULL,
  `file_date` VARCHAR(50)  DEFAULT NULL,
  `file_number` VARCHAR(50)  DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `first_name_ar` VARCHAR(100) NULL AFTER `family_name`,
  `last_name_ar` VARCHAR(100) NULL AFTER `first_name_ar`,
  PRIMARY KEY (`id`),
  UNIQUE KEY `national_id` (`national_id`),
  KEY `family_name` (`family_name`),
  KEY `first_name` (`first_name`),
  KEY `birth_date` (`birth_date`),
  KEY `neighborhood` (`neighborhood`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `fk_citizens_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_citizens_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: register_books
-- سجلات البلدية (سجل الولادة، سجل العائلة، إلخ)
-- -----------------------------------------------------
DROP TABLE IF EXISTS `register_books`;
CREATE TABLE `register_books` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `register_number` varchar(50) NOT NULL COMMENT 'رقم السجل',
  `register_type` enum('birth','death','marriage','divorce','family','residence','other') NOT NULL DEFAULT 'birth' COMMENT 'نوع السجل',
  `register_type_label` varchar(100) DEFAULT NULL,
  `register_type_label_fr` varchar(100) DEFAULT NULL,
  `year` int(4) NOT NULL COMMENT 'سنة السجل',
  `page_count` int(5) DEFAULT 0 COMMENT 'عدد الصفحات',
  `status` enum('active','archived','closed') NOT NULL DEFAULT 'active',
  `location` varchar(100) DEFAULT NULL COMMENT 'مكان التخزين',
  `notes` text DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `register_number` (`register_number`),
  KEY `register_type` (`register_type`),
  KEY `year` (`year`),
  KEY `status` (`status`),
  CONSTRAINT `fk_books_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: register_pages
-- صفحات السجل (الولادات، الوفيات، إلخ)
-- -----------------------------------------------------
DROP TABLE IF EXISTS `register_pages`;
CREATE TABLE `register_pages` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `register_book_id` int(11) UNSIGNED NOT NULL,
  `page_number` int(5) NOT NULL COMMENT 'رقم الصفحة',
  `sequential_number` int(10) NOT NULL COMMENT 'الرقم الترتيبي',
  `record_date` date NOT NULL COMMENT 'تاريخ التسجيل',
  `full_name` varchar(200) NOT NULL COMMENT 'الاسم الكامل',
  `birth_date` date DEFAULT NULL COMMENT 'تاريخ الولادة',
  `birth_place` varchar(100) DEFAULT NULL COMMENT 'مكان الولادة',
  `father_name` varchar(100) DEFAULT NULL COMMENT 'اسم الأب',
  `mother_name` varchar(100) DEFAULT NULL COMMENT 'اسم الأم',
  `family_name` varchar(100) DEFAULT NULL COMMENT 'الاسم العائلي',
  `address` varchar(255) DEFAULT NULL COMMENT 'العنوان',
  `id_number` varchar(20) DEFAULT NULL COMMENT 'رقم الهوية',
  `notes` text DEFAULT NULL COMMENT 'ملاحظات',
  `scan_path` varchar(255) DEFAULT NULL COMMENT 'مسار المسح الضوئي',
  `ocr_text` text DEFAULT NULL COMMENT 'نص OCR',
  `ocr_confidence` decimal(5,2) DEFAULT NULL,
  `citizen_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'رابط بالمواطن',
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_page` (`register_book_id`, `page_number`, `sequential_number`),
  KEY `sequential_number` (`sequential_number`),
  KEY `record_date` (`record_date`),
  KEY `full_name` (`full_name`),
  KEY `citizen_id` (`citizen_id`),
  CONSTRAINT `fk_pages_book` FOREIGN KEY (`register_book_id`) REFERENCES `register_books` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pages_citizen` FOREIGN KEY (`citizen_id`) REFERENCES `citizens` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pages_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: documents
-- -----------------------------------------------------
DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `citizen_id` int(11) UNSIGNED DEFAULT NULL,
  `register_page_id` int(11) UNSIGNED DEFAULT NULL,
  `document_type` enum('identity','birth_certificate','residence','marriage','death','other') DEFAULT 'other',
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `uploaded_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `citizen_id` (`citizen_id`),
  KEY `register_page_id` (`register_page_id`),
  KEY `document_type` (`document_type`),
  CONSTRAINT `fk_docs_citizen` FOREIGN KEY (`citizen_id`) REFERENCES `citizens` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_docs_page` FOREIGN KEY (`register_page_id`) REFERENCES `register_pages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_docs_uploaded_by` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: certificates
-- -----------------------------------------------------
DROP TABLE IF EXISTS `certificates`;
CREATE TABLE `certificates` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `certificate_number` varchar(50) NOT NULL COMMENT 'رقم الشهادة',
  `certificate_type` enum('birth','residence','administrative','family','marriage','death','other') NOT NULL,
  `citizen_id` int(11) UNSIGNED NOT NULL,
  `register_page_id` int(11) UNSIGNED DEFAULT NULL,
  `issue_date` date NOT NULL COMMENT 'تاريخ الإصدار',
  `purpose` varchar(255) DEFAULT NULL COMMENT 'الغرض',
  `qr_code` varchar(255) DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `verified_at` datetime DEFAULT NULL,
  `issued_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `certificate_number` (`certificate_number`),
  KEY `citizen_id` (`citizen_id`),
  KEY `certificate_type` (`certificate_type`),
  KEY `issue_date` (`issue_date`),
  CONSTRAINT `fk_cert_citizen` FOREIGN KEY (`citizen_id`) REFERENCES `citizens` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cert_page` FOREIGN KEY (`register_page_id`) REFERENCES `register_pages` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_cert_issued_by` FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: imports
-- -----------------------------------------------------
DROP TABLE IF EXISTS `imports`;
CREATE TABLE `imports` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `file_type` enum('csv','excel','sql','zip') NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `records_total` int(11) DEFAULT 0,
  `records_success` int(11) DEFAULT 0,
  `records_failed` int(11) DEFAULT 0,
  `errors` text DEFAULT NULL,
  `imported_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `imported_by` (`imported_by`),
  CONSTRAINT `fk_imports_user` FOREIGN KEY (`imported_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: backups
-- -----------------------------------------------------
DROP TABLE IF EXISTS `backups`;
CREATE TABLE `backups` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `backup_type` enum('manual','automatic') NOT NULL DEFAULT 'manual',
  `tables` text DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `fk_backups_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: notifications
-- -----------------------------------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `is_read` (`is_read`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;


CREATE TABLE IF NOT EXISTS `registers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `citizen_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `citizen_id` (`citizen_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS import_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    module VARCHAR(50) NOT NULL,
    file_type VARCHAR(10) NOT NULL,
    file_path VARCHAR(500),
    total_records INT DEFAULT 0,
    records_processed INT DEFAULT 0,
    records_failed INT DEFAULT 0,
    status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    error_log TEXT,
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `import_history` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `filename` VARCHAR(255) NOT NULL,
  `module` VARCHAR(50) NOT NULL,
  `file_type` VARCHAR(10) NOT NULL,
  `file_path` VARCHAR(500),
  `total_records` INT DEFAULT 0,
  `records_processed` INT DEFAULT 0,
  `records_failed` INT DEFAULT 0,
  `status` ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
  `error_log` TEXT,
  `uploaded_by` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `completed_at` TIMESTAMP NULL,
  KEY `uploaded_by` (`uploaded_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
