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
ALTER TABLE citizens
ADD COLUMN gender ENUM('Male','Female') NULL,
ADD COLUMN nationality VARCHAR(100),
ADD COLUMN neighborhood VARCHAR(150),
ADD COLUMN register_book VARCHAR(50),
ADD COLUMN page_number INT,
ADD COLUMN marital_status VARCHAR(50),
ADD COLUMN created_by INT,
ADD COLUMN updated_by INT;

                            
)
CREATE TABLE register_books (
        id INT AUTO_INCREMENT PRIMARY KEY,
            book_number VARCHAR(50) NOT NULL,
                title VARCHAR(255),
                    year YEAR NOT NULL,
                        neighborhood VARCHAR(150),
                            total_pages INT DEFAULT 0,
                                description TEXT,
                                    status ENUM('Active','Archived') DEFAULT 'Active',
                                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                                        );

)
CREATE TABLE register_pages (
        id INT AUTO_INCREMENT PRIMARY KEY,
            book_id INT NOT NULL,
                page_number INT NOT NULL,
                    image VARCHAR(255),
                        ocr_text LONGTEXT,
                            verified TINYINT(1) DEFAULT 0,
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                    FOREIGN KEY (book_id) REFERENCES register_books(id)
                                            ON DELETE CASCADE
                                            );

)
