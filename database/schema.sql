-- Megawin Switch Gear QMS Database Schema

-- Role Table
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    permissions JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    role_id INT NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    signature LONGTEXT NULL,
    otp_code VARCHAR(6) NULL,
    otp_expires_at TIMESTAMP NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Customer Table
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    company_name VARCHAR(150) NOT NULL,
    contact_name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Department Table
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    code VARCHAR(10) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Project Table
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    name VARCHAR(150) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT NOT NULL,
    start_date DATE,
    end_date DATE,
    status ENUM('planned', 'active', 'completed', 'on_hold') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dynamic Forms Template Table
CREATE TABLE IF NOT EXISTS templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    name VARCHAR(100) NOT NULL,
    module VARCHAR(50) NOT NULL, -- IQC, Bus Bar, CTBT, BCB, Assembly, Final, Dispatch
    fields JSON NOT NULL, -- Field layout, validation, conditional rules
    version INT DEFAULT 1,
    is_active TINYINT(1) DEFAULT 1,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Job Management Table
CREATE TABLE IF NOT EXISTS jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    job_id_code VARCHAR(50) UNIQUE NOT NULL, -- Barcode/System Code
    customer_id INT NOT NULL,
    template_id INT NOT NULL,
    department_id INT NOT NULL,
    project_id INT NOT NULL,
    assigned_engineer_id INT NULL,
    report_name VARCHAR(150) NOT NULL,
    priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    inspection_date DATE NOT NULL,
    status ENUM('created', 'assigned', 'inspection_in_progress', 'pending_review', 'completed', 'archived') DEFAULT 'created',
    approval_status ENUM('pending', 'reviewer_approved', 'manager_approved', 'rework_requested', 'rejected') DEFAULT 'pending',
    remarks TEXT,
    qr_token VARCHAR(64) UNIQUE NOT NULL, -- Token for public review page
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (template_id) REFERENCES templates(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (project_id) REFERENCES projects(id),
    FOREIGN KEY (assigned_engineer_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dynamic Attributes Values for Jobs
CREATE TABLE IF NOT EXISTS job_attributes (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    field_value LONGTEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    UNIQUE KEY uq_job_field (job_id, field_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Document Management Table
CREATE TABLE IF NOT EXISTS documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    job_id INT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    file_size INT NOT NULL,
    version INT DEFAULT 1,
    uploaded_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE SET NULL,
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Approval Workflow History Log
CREATE TABLE IF NOT EXISTS approval_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL, -- e.g., 'submit', 'review_approve', 'manager_approve', 'request_rework'
    comments TEXT,
    digital_signature LONGTEXT NULL, -- base64 signature
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings Table for Global Configuration
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ======================================================
-- DEFAULT DATABASE SEED DATA
-- ======================================================

-- Seed Roles
INSERT INTO roles (id, name, permissions) VALUES 
(1, 'Admin', '{"all": true}'),
(2, 'Quality Engineer', '{"inspect": true}')
ON DUPLICATE KEY UPDATE name=name;

-- Seed Users (default admin user, password: bcrypt hash for admin123)
INSERT INTO users (id, role_id, username, email, password_hash) VALUES 
(1, 1, 'admin', 'admin@megawin.com', 'admin123')
ON DUPLICATE KEY UPDATE username=username;

-- Seed Customers
INSERT INTO customers (id, company_name, contact_name, email, phone, address) VALUES 
(1, 'BHEL Switchgears', 'Amit Kumar', 'amit@bhel.com', '9876543210', 'Bhopal Industrial Area'),
(2, 'L&T Switchgear Division', 'Vijay Singh', 'vijay@lt.com', '9876543211', 'Mumbai Factory Complex')
ON DUPLICATE KEY UPDATE company_name=company_name;

-- Seed Departments
INSERT INTO departments (id, name, code) VALUES 
(1, 'IPQC Quality Department', 'IPQC'),
(2, 'IQC Assembly Department', 'IQC')
ON DUPLICATE KEY UPDATE name=name;

-- Seed Projects
INSERT INTO projects (id, name, code, customer_id) VALUES 
(1, 'Megawin 33KV Substation Project', 'PROJ-33KV', 1),
(2, '11KV Panel Supply Contract', 'PROJ-11KV', 2)
ON DUPLICATE KEY UPDATE name=name;

-- Seed Templates
INSERT INTO templates (id, name, module, fields) VALUES 
(1, 'Ammeter Quality Test Template', 'Incoming QC', '[]'),
(2, 'Bus Bar Connection Template', 'Bus Bar', '[]')
ON DUPLICATE KEY UPDATE name=name;

