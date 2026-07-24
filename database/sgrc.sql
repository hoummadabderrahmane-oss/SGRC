CREATE DATABASE sgrc
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
            fullname VARCHAR(150) NOT NULL,
                email VARCHAR(150) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                        role ENUM('admin','manager','employee','viewer') DEFAULT 'viewer',
                            language ENUM('fr','ar') DEFAULT 'fr',
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                                );
CREATE TABLE citizens (
        id INT AUTO_INCREMENT PRIMARY KEY,
            registration_number VARCHAR(50) UNIQUE,
                family_name_ar VARCHAR(100),
                    first_name_ar VARCHAR(100),
                        family_name_fr VARCHAR(100),
                            first_name_fr VARCHAR(100),
                                father_name VARCHAR(100),
                                    mother_name VARCHAR(100),
                                        cin VARCHAR(20),
                                            birth_certificate_number VARCHAR(50),
                                                birth_date DATE,
                                                    profession VARCHAR(100),
                                                        address TEXT,
                                                            phone VARCHAR(30),
                                                                notes TEXT,
                                                                    photo VARCHAR(255),
                                                                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                                                            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                                                                            );
CREATE TABLE scanned_pages (
        id INT AUTO_INCREMENT PRIMARY KEY,
            citizen_id INT,
                page_number INT,
                    file_name VARCHAR(255),
                        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            FOREIGN KEY (citizen_id) REFERENCES citizens(id)
CREATE TABLE activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
                action TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (user_id) REFERENCES users(id)
                        );
)                                  ON DELETE CASCADE
                                    );

)
)
)


)