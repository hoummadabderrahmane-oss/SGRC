# SGRC - Civil Registry Management System

A comprehensive PHP-based civil registry management system for managing citizens, registrations, certificates, and documents.

## Features

- **Citizen Management**: Full CRUD operations with photo upload
- **Civil Registers**: Birth, death, marriage, divorce registrations
- **Certificates**: Generate and manage official certificates
- **Document Management**: Upload and organize documents with OCR support
- **Import/Export**: CSV, Excel, PDF, and SQL import/export
- **Multi-language**: Arabic and French support
- **User Management**: Role-based access control (Admin, Operator, Viewer)
- **Reports**: Statistical dashboards and reports
- **Backups**: Automated database backups

## Requirements

- PHP &gt;= 7.4
- MySQL &gt;= 5.7
- Apache/Nginx with mod_rewrite
- Composer

## Installation

1. Clone the repository
2. Run `composer install`
3. Create database and import `database/sgrc.sql`
4. Configure `config/database.php` with your credentials
5. Set permissions for `uploads/` and `backups/` directories
6. Default login: `admin` / `password`

## Directory Structure
