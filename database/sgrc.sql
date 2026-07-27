-- SGRC Database Schema
CREATE DATABASE IF NOT EXISTS sgrc_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sgrc_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                    full_name VARCHAR(100) NOT NULL,
                        role ENUM('admin', 'operator', 'viewer') DEFAULT 'operator',
                            is_active TINYINT(1) DEFAULT 1,
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                                    );

                                    -- Citizens table
                                    CREATE TABLE IF NOT EXISTS citizens (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                            national_id VARCHAR(20) NOT NULL UNIQUE,
                                                first_name VARCHAR(50) NOT NULL,
                                                    last_name VARCHAR(50) NOT NULL,
                                                        first_name_ar VARCHAR(50),
                                                            last_name_ar VARCHAR(50),
                                                                date_of_birth DATE NOT NULL,
                                                                    place_of_birth VARCHAR(100),
                                                                        gender ENUM('male', 'female') NOT NULL,
                                                                            address TEXT,
                                                                                phone VARCHAR(20),
                                                                                    email VARCHAR(100),
                                                                                        blood_type VARCHAR(5),
                                                                                            father_name VARCHAR(100),
                                                                                                mother_name VARCHAR(100),
                                                                                                    marital_status ENUM('single', 'married', 'divorced', 'widowed') DEFAULT 'single',
                                                                                                        photo_path VARCHAR(255),
                                                                                                            created_by INT,
                                                                                                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                                                                                                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                                                                                                        FOREIGN KEY (created_by) REFERENCES users(id)
                                                                                                                        );

                                                                                                                        -- Registers table (civil registry entries)
                                                                                                                        CREATE TABLE IF NOT EXISTS registers (
                                                                                                                            id INT AUTO_INCREMENT PRIMARY KEY,
                                                                                                                                register_number VARCHAR(50) NOT NULL UNIQUE,
                                                                                                                                    register_type ENUM('birth', 'death', 'marriage', 'divorce') NOT NULL,
                                                                                                                                        citizen_id INT,
                                                                                                                                            event_date DATE NOT NULL,
                                                                                                                                                event_place VARCHAR(100),
                                                                                                                                                    notes TEXT,
                                                                                                                                                        document_path VARCHAR(255),
                                                                                                                                                            scan_path VARCHAR(255),
                                                                                                                                                                status ENUM('active', 'archived', 'pending') DEFAULT 'active',
                                                                                                                                                                    created_by INT,
                                                                                                                                                                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                                                                                                                                                            FOREIGN KEY (citizen_id) REFERENCES citizens(id),
                                                                                                                                                                                FOREIGN KEY (created_by) REFERENCES users(id)
                                                                                                                                                                                );

                                                                                                                                                                                -- Certificates table
                                                                                                                                                                                CREATE TABLE IF NOT EXISTS certificates (
                                                                                                                                                                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                                                                                                                                                                        certificate_number VARCHAR(50) NOT NULL UNIQUE,
                                                                                                                                                                                            register_id INT,
                                                                                                                                                                                                certificate_type ENUM('birth', 'death', 'marriage', 'residence', 'nationality') NOT NULL,
                                                                                                                                                                                                    issue_date DATE NOT NULL,
                                                                                                                                                                                                        expiry_date DATE,
                                                                                                                                                                                                            status ENUM('valid', 'expired', 'revoked') DEFAULT 'valid',
                                                                                                                                                                                                                created_by INT,
                                                                                                                                                                                                                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                                                                                                                                                                                                        FOREIGN KEY (register_id) REFERENCES registers(id),
                                                                                                                                                                                                                            FOREIGN KEY (created_by) REFERENCES users(id)
                                                                                                                                                                                                                            );

                                                                                                                                                                                                                            -- Documents table
                                                                                                                                                                                                                            CREATE TABLE IF NOT EXISTS documents (
                                                                                                                                                                                                                                id INT AUTO_INCREMENT PRIMARY KEY,
                                                                                                                                                                                                                                    title VARCHAR(200) NOT NULL,
                                                                                                                                                                                                                                        file_path VARCHAR(255) NOT NULL,
                                                                                                                                                                                                                                            file_type VARCHAR(50),
                                                                                                                                                                                                                                                file_size INT,
                                                                                                                                                                                                                                                    category VARCHAR(50),
                                                                                                                                                                                                                                                        citizen_id INT,
                                                                                                                                                                                                                                                            uploaded_by INT,
                                                                                                                                                                                                                                                                uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                                                                                                                                                                                                                                                    FOREIGN KEY (citizen_id) REFERENCES citizens(id),
                                                                                                                                                                                                                                                                        FOREIGN KEY (uploaded_by) REFERENCES users(id)
                                                                                                                                                                                                                                                                        );

                                                                                                                                                                                                                                                                        -- Import history
                                                                                                                                                                                                                                                                        CREATE TABLE IF NOT EXISTS import_history (
                                                                                                                                                                                                                                                                            id INT AUTO_INCREMENT PRIMARY KEY,
                                                                                                                                                                                                                                                                                import_type VARCHAR(20) NOT NULL,
                                                                                                                                                                                                                                                                                    file_name VARCHAR(255),
                                                                                                                                                                                                                                                                                        records_processed INT DEFAULT 0,
                                                                                                                                                                                                                                                                                            records_success INT DEFAULT 0,
                                                                                                                                                                                                                                                                                                records_failed INT DEFAULT 0,
                                                                                                                                                                                                                                                                                                    error_log TEXT,
                                                                                                                                                                                                                                                                                                        imported_by INT,
                                                                                                                                                                                                                                                                                                            imported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                                                                                                                                                                                                                                                                                                FOREIGN KEY (imported_by) REFERENCES users(id)
                                                                                                                                                                                                                                                                                                                );

                                                                                                                                                                                                                                                                                                                -- Settings table
                                                                                                                                                                                                                                                                                                                CREATE TABLE IF NOT EXISTS settings (
                                                                                                                                                                                                                                                                                                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                                                                                                                                                                                                                                                                                                        setting_key VARCHAR(50) NOT NULL UNIQUE,
                                                                                                                                                                                                                                                                                                                            setting_value TEXT,
                                                                                                                                                                                                                                                                                                                                setting_group VARCHAR(30) DEFAULT 'general'
                                                                                                                                                                                                                                                                                                                                );

                                                                                                                                                                                                                                                                                                                                -- Insert default admin
                                                                                                                                                                                                                                                                                                                                INSERT INTO users (username, email, password_hash, full_name, role) VALUES 
                                                                                                                                                                                                                                                                                                                                ('admin', 'admin@sgrc.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin');

                                                                                                                                                                                                                                                                                                                                -- Default settings
                                                                                                                                                                                                                                                                                                                                INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
                                                                                                                                                                                                                                                                                                                                ('app_name', 'SGRC', 'general'),
                                                                                                                                                                                                                                                                                                                                ('app_language', 'fr', 'general'),
                                                                                                                                                                                                                                                                                                                                ('items_per_page', '20', 'general'),
                                                                                                                                                                                                                                                                                                                                ('enable_ocr', '1', 'features'),
                                                                                                                                                                                                                                                                                                                                ('backup_frequency', 'daily', 'backup');
                                                                                                                                                                                                                                                                                                                                