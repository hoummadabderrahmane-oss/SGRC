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
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL COMMENT 'ملاحظات',
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `updated_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
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

-- =====================================================
-- Sample Data for Testing
-- =====================================================

INSERT INTO `citizens` (`national_id`, `family_name`, `first_name`, `father_name`, `mother_name`, `birth_date`, `birth_place`, `gender`, `address`, `neighborhood`, `phone`, `notes`) VALUES
('Y196928', 'دريس', 'محمد', 'أحمد', 'فاطمة', '1980-09-13', 'المدينة', 'male', 'حي الزاوية ق.م', 'الزاوية', '0550123456', ''),
('Y507803', 'رشيد', 'عبدالله', 'محمد', 'عائشة', '1980-11-12', 'المدينة', 'male', 'حي النخلة ق.م', 'النخلة', '0550789012', ''),
('Y170602', 'امير', 'بلال', 'عبدالرحمن', 'خديجة', '1979-11-21', 'المدينة', 'male', 'حي النخلة ق.م', 'النخلة', '0550345678', ''),
('Y354205', 'ناجي', 'مي', 'الزاوية', 'زهرة', '1989-08-16', 'المدينة', 'female', 'حي الزاوية ق.م', 'الزاوية', '0550901234', ''),
('Y2599', 'امير', 'محمد', 'عبدالله', 'فاطمة', '1956-08-08', 'المدينة', 'male', 'حي النخلة ق.م', 'النخلة', '0550567890', ''),
('Y337563', 'امير', 'محمد', 'عبدالله', 'فاطمة', '1984-05-08', 'المدينة', 'male', 'حي النخلة ق.م', 'النخلة', '0550234567', ''),
('Y439761', 'صالح', 'دوار', 'القصور', 'عائشة', '1989-06-28', 'المدينة', 'male', 'حي القصور ق.م', 'القصور', '0550678901', ''),
('Y385373', 'صالح', 'سعادة', 'أحمد', 'خديجة', '1993-07-21', 'المدينة', 'female', 'حي الزاوية ق.م', 'الزاوية', '0550456789', '');

INSERT INTO `register_books` (`register_number`, `register_type`, `register_type_label`, `register_type_label_fr`, `year`, `page_count`, `status`, `location`) VALUES
('1980/530', 'birth', 'سجل الولادات', 'Registre des Naissances', 1980, 50, 'active', 'الأرشيف الرئيسي'),
('1980/654', 'birth', 'سجل الولادات', 'Registre des Naissances', 1980, 45, 'active', 'الأرشيف الرئيسي'),
('1979/620', 'birth', 'سجل الولادات', 'Registre des Naissances', 1979, 60, 'archived', 'المخزن الثانوي'),
('1989/06', 'birth', 'سجل الولادات', 'Registre des Naissances', 1989, 55, 'active', 'الأرشيف الرئيسي'),
('1963/184', 'family', 'سجل العائلة', 'Registre Familial', 1963, 100, 'archived', 'المخزن الثانوي'),
('1984/377', 'birth', 'سجل الولادات', 'Registre des Naissances', 1984, 48, 'active', 'الأرشيف الرئيسي'),
('1989/485', 'birth', 'سجل الولادات', 'Registre des Naissances', 1989, 52, 'active', 'الأرشيف الرئيسي'),
('1993/125', 'birth', 'سجل الولادات', 'Registre des Naissances', 1993, 50, 'active', 'الأرشيف الرئيسي');

INSERT INTO `register_pages` (`register_book_id`, `page_number`, `sequential_number`, `record_date`, `full_name`, `birth_date`, `birth_place`, `father_name`, `mother_name`, `family_name`, `address`, `id_number`, `notes`) VALUES
(1, 1, 1150, '2016-01-04', 'محمد بن أحمد دريس', '1980-09-13', 'المدينة', 'أحمد', 'فاطمة', 'دريس', 'حي الزاوية ق.م', 'Y196928', 'سجل الولادة رقم 1980/530'),
(2, 1, 1151, '2016-01-04', 'عبدالله بن محمد رشيد', '1980-11-12', 'المدينة', 'محمد', 'عائشة', 'رشيد', 'حي النخلة ق.م', 'Y507803', 'سجل الولادة رقم 1980/654'),
(3, 1, 1152, '2016-01-04', 'بلال بن عبدالرحمن امير', '1979-11-21', 'المدينة', 'عبدالرحمن', 'خديجة', 'امير', 'حي النخلة ق.م', 'Y170602', 'سجل الولادة رقم 1979/620'),
(4, 1, 1153, '2016-01-04', 'مي بنت الزاوية ناجي', '1989-08-16', 'المدينة', 'الزاوية', 'زهرة', 'ناجي', 'حي الزاوية ق.م', 'Y354205', 'سجل الولادة رقم 1989/06'),
(5, 1, 1154, '2016-01-04', 'محمد بن عبدالله امير', '1956-08-08', 'المدينة', 'عبدالله', 'فاطمة', 'امير', 'حي النخلة ق.م', 'Y2599', 'سجل العائلة رقم 1963/184');  
-- Add new columns
ALTER TABLE citizens 
ADD COLUMN file_number VARCHAR(50) NULL AFTER marital_status,
ADD COLUMN file_date VARCHAR(50) NULL AFTER file_number;

-- Remove old columns (optional - only if you don't need them)
ALTER TABLE citizens 
DROP COLUMN phone,
DROP COLUMN email;