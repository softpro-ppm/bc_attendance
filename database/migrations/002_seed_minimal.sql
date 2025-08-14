-- BC Attendance System - Seed Data
-- Insert sample data for testing

SET NAMES utf8mb4;

-- Insert sample constituencies
INSERT INTO constituencies (name, code, status) VALUES
('Constituency A', 'CONST_A', 'active'),
('Constituency B', 'CONST_B', 'active');

-- Insert sample mandals
INSERT INTO mandals (constituency_id, name, code, status) VALUES
(1, 'Mandal A1', 'MAND_A1', 'active'),
(1, 'Mandal A2', 'MAND_A2', 'active'),
(2, 'Mandal B1', 'MAND_B1', 'active'),
(2, 'Mandal B2', 'MAND_B2', 'active');

-- Insert sample batches
INSERT INTO batches (mandal_id, name, code, start_date, end_date, status) VALUES
(1, 'Batch 1', 'BATCH_1', '2024-01-01', '2024-06-30', 'active'),
(1, 'Batch 2', 'BATCH_2', '2024-07-01', '2024-12-31', 'active'),
(2, 'Batch 1', 'BATCH_1', '2024-01-01', '2024-06-30', 'active'),
(3, 'Batch 1', 'BATCH_1', '2024-01-01', '2024-06-30', 'active'),
(4, 'Batch 1', 'BATCH_1', '2024-01-01', '2024-06-30', 'active');

-- Insert sample candidates
INSERT INTO candidates (batch_id, reg_no, full_name, phone, gender, status) VALUES
(1, 'REG001', 'John Doe', '9876543210', 'M', 'active'),
(1, 'REG002', 'Jane Smith', '9876543211', 'F', 'active'),
(1, 'REG003', 'Bob Johnson', '9876543212', 'M', 'active'),
(2, 'REG004', 'Alice Brown', '9876543213', 'F', 'active'),
(2, 'REG005', 'Charlie Wilson', '9876543214', 'M', 'active'),
(3, 'REG006', 'Diana Davis', '9876543215', 'F', 'active'),
(3, 'REG007', 'Edward Miller', '9876543216', 'M', 'active'),
(4, 'REG008', 'Fiona Garcia', '9876543217', 'F', 'active'),
(5, 'REG009', 'George Martinez', '9876543218', 'M', 'active'),
(5, 'REG010', 'Helen Rodriguez', '9876543219', 'F', 'active');

-- Insert default settings
INSERT INTO settings (skey, svalue, description) VALUES
('app_name', 'BC Attendance System', 'Application name'),
('app_version', '1.0.0', 'Application version'),
('default_attendance_status', 'P', 'Default attendance status (P/A/L/E)'),
('working_days', 'Monday,Tuesday,Wednesday,Thursday,Friday', 'Working days of the week'),
('holidays', '', 'Comma-separated list of holidays (YYYY-MM-DD)'),
('attendance_reminder', '1', 'Enable attendance reminder (0/1)'),
('export_format', 'xlsx', 'Default export format (xlsx/csv)'),
('pagination_default', '20', 'Default pagination size'),
('session_timeout', '3600', 'Session timeout in seconds');

-- Insert sample attendance records for the last 7 days
INSERT INTO attendance (candidate_id, attn_date, status, notes) VALUES
-- Batch 1 candidates for last 7 days
(1, DATE_SUB(CURDATE(), INTERVAL 6 DAY), 'P', NULL),
(1, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'P', NULL),
(1, DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'A', 'Sick leave'),
(1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'P', NULL),
(1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'L', 'Traffic delay'),
(1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'P', NULL),
(1, CURDATE(), 'P', NULL),

(2, DATE_SUB(CURDATE(), INTERVAL 6 DAY), 'P', NULL),
(2, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'P', NULL),
(2, DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'P', NULL),
(2, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'P', NULL),
(2, DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'P', NULL),
(2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'A', 'Personal work'),
(2, CURDATE(), 'P', NULL),

(3, DATE_SUB(CURDATE(), INTERVAL 6 DAY), 'P', NULL),
(3, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'L', 'Late arrival'),
(3, DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'P', NULL),
(3, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'P', NULL),
(3, DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'P', NULL),
(3, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'P', NULL),
(3, CURDATE(), 'P', NULL);

-- Note: Admin user will be created by the installer with proper password hash
-- INSERT INTO users (username, password_hash, email, full_name) VALUES
-- ('admin', '$2y$10$REPLACE_WITH_BCRYPT', 'admin@example.com', 'System Administrator');
