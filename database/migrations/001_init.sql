-- BC Attendance System - Initial Database Schema
-- MySQL 8.0+ compatible

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Users (single admin)
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(64) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  email VARCHAR(255) NULL,
  full_name VARCHAR(160) NULL,
  last_login TIMESTAMP NULL DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Constituencies
CREATE TABLE constituencies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(128) NOT NULL UNIQUE,
  code VARCHAR(32) NOT NULL UNIQUE,
  status ENUM('active','inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mandal Locations (belongs to constituency)
CREATE TABLE mandals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  constituency_id INT NOT NULL,
  name VARCHAR(128) NOT NULL,
  code VARCHAR(32) NOT NULL,
  status ENUM('active','inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_mandal (constituency_id, name),
  UNIQUE KEY uniq_mandal_code (constituency_id, code),
  CONSTRAINT fk_mandal_const FOREIGN KEY (constituency_id) REFERENCES constituencies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Batches (belongs to mandal)
CREATE TABLE batches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mandal_id INT NOT NULL,
  name VARCHAR(128) NOT NULL,
  code VARCHAR(32) NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NULL,
  status ENUM('active','inactive','completed') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_batch (mandal_id, name),
  UNIQUE KEY uniq_batch_code (mandal_id, code),
  CONSTRAINT fk_batch_mandal FOREIGN KEY (mandal_id) REFERENCES mandals(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Candidates (belongs to batch)
CREATE TABLE candidates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  batch_id INT NOT NULL,
  reg_no VARCHAR(64) NOT NULL,
  full_name VARCHAR(160) NOT NULL,
  phone VARCHAR(20) NULL,
  email VARCHAR(255) NULL,
  gender ENUM('M','F','O') NULL,
  status ENUM('active','inactive','dropped') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_candidate (batch_id, reg_no),
  CONSTRAINT fk_cand_batch FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Attendance (one record per candidate per date)
CREATE TABLE attendance (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  candidate_id INT NOT NULL,
  attn_date DATE NOT NULL,
  status ENUM('P','A','L','E') NOT NULL DEFAULT 'P', -- Present, Absent, Late, Excused/Leave
  notes VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_attn (candidate_id, attn_date),
  INDEX idx_attn_date (attn_date),
  INDEX idx_attn_status (status),
  CONSTRAINT fk_attn_candidate FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings (key/value)
CREATE TABLE settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  skey VARCHAR(64) NOT NULL UNIQUE,
  svalue TEXT NULL,
  description VARCHAR(255) NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit Log
CREATE TABLE audit_log (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  action VARCHAR(64) NOT NULL,
  entity VARCHAR(64) NOT NULL,
  entity_id VARCHAR(64) NOT NULL,
  details JSON NULL,
  ip_address VARCHAR(45) NULL,
  user_agent TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_audit_user (user_id),
  INDEX idx_audit_action (action),
  INDEX idx_audit_entity (entity),
  INDEX idx_audit_created (created_at),
  CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Login attempts tracking
CREATE TABLE login_attempts (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(64) NOT NULL,
  ip_address VARCHAR(45) NOT NULL,
  attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_login_username (username),
  INDEX idx_login_ip (ip_address),
  INDEX idx_login_attempted (attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
