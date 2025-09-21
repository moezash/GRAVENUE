-- ============================================
-- GRAVENUE DATABASE SETUP SCRIPT (MariaDB Compatible)
-- Platform Penyewaan Fasilitas SMKN 4 Malang
-- Compatible with MySQL 5.7+ and MariaDB 10.1+
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS gravenue_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gravenue_db;

-- ============================================
-- DROP TABLES (if exists) - for clean setup
-- ============================================
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS facilities;
DROP TABLE IF EXISTS admins;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- 1. USERS TABLE
-- ============================================
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20) NULL,
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;

-- ============================================
-- 2. ADMINS TABLE
-- ============================================
CREATE TABLE admins (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'admin',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_email (email)
) ENGINE=InnoDB;

-- ============================================
-- 3. FACILITIES TABLE
-- ============================================
CREATE TABLE facilities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100) NOT NULL,
    capacity INT NOT NULL DEFAULT 0,
    price_per_day DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    location VARCHAR(255) NULL,
    features TEXT NULL,
    image VARCHAR(255) NULL,
    status ENUM('available', 'unavailable', 'maintenance') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;

-- ============================================
-- 4. BOOKINGS TABLE
-- ============================================
CREATE TABLE bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    facility_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    user_name VARCHAR(100) NOT NULL,
    user_email VARCHAR(100) NOT NULL,
    user_phone VARCHAR(20) NOT NULL,
    organization VARCHAR(100) NULL,
    event_name VARCHAR(150) NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NULL,
    end_time TIME NULL,
    participants INT NOT NULL DEFAULT 1,
    total_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    additional_notes TEXT NULL,
    status ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,

    INDEX idx_facility_id (facility_id),
    INDEX idx_user_id (user_id),
    INDEX idx_booking_date (booking_date),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;

-- ============================================
-- 5. PAYMENTS TABLE
-- ============================================
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    payment_amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('bank_transfer', 'ewallet', 'qris', 'cash') NULL,
    payment_status ENUM('pending', 'paid', 'paid_dummy', 'failed', 'refunded') DEFAULT 'pending',
    payment_date TIMESTAMP NULL,
    transaction_id VARCHAR(255) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,

    INDEX idx_booking_id (booking_id),
    INDEX idx_payment_status (payment_status),
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;

-- ============================================
-- INSERT SAMPLE DATA
-- ============================================

-- Sample Admin Users (password is 'password' hashed with Laravel bcrypt)
INSERT INTO admins (name, email, password, role) VALUES
('Admin Gravenue', 'admin@gravenue.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin'),
('Admin SMKN 4', 'admin@smkn4malang.sch.id', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Sample Regular Users (password is 'password' hashed with Laravel bcrypt)
INSERT INTO users (name, email, phone, password) VALUES
('John Doe', 'john@example.com', '081234567890', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Jane Smith', 'jane@example.com', '081234567891', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Ahmad Rahman', 'ahmad@example.com', '081234567892', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Siti Nurhaliza', 'siti@example.com', '081234567893', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Budi Santoso', 'budi@example.com', '081234567894', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Dewi Kartika', 'dewi@example.com', '081234567895', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample Facilities
INSERT INTO facilities (name, description, category, capacity, price_per_day, location, features, status) VALUES
('Aula Utama', 'Aula besar dengan kapasitas 500 orang, dilengkapi dengan sound system dan proyektor', 'Event Space', 500, 2500000.00, 'Lantai 1, Gedung Utama', 'AC, Sound System, Proyektor, Panggung, Kursi 500', 'available'),
('Auditorium', 'Auditorium modern dengan fasilitas lengkap untuk seminar dan presentasi', 'Event Space', 300, 2000000.00, 'Lantai 2, Gedung Utama', 'AC, Sound System, Proyektor, Lighting, Kursi Theater', 'available'),
('Ruang Kelas A1', 'Ruang kelas standar dengan fasilitas pembelajaran modern', 'Classroom', 40, 300000.00, 'Lantai 1, Gedung A', 'AC, Proyektor, Whiteboard, Meja Kursi 40', 'available'),
('Ruang Kelas A2', 'Ruang kelas dengan kapasitas sedang, cocok untuk workshop', 'Classroom', 35, 250000.00, 'Lantai 1, Gedung A', 'AC, Proyektor, Whiteboard, Meja Kursi 35', 'available'),
('Ruang Kelas B1', 'Ruang kelas besar untuk pelatihan dan seminar kecil', 'Classroom', 50, 400000.00, 'Lantai 1, Gedung B', 'AC, Proyektor, Whiteboard, Meja Kursi 50', 'available'),
('Lab Komputer 1', 'Laboratorium komputer dengan 30 unit PC terbaru', 'Computer Lab', 30, 800000.00, 'Lantai 2, Gedung C', 'AC, 30 PC, Proyektor, Internet, Software Lengkap', 'available'),
('Lab Komputer 2', 'Laboratorium komputer untuk pembelajaran programming', 'Computer Lab', 25, 700000.00, 'Lantai 2, Gedung C', 'AC, 25 PC, Proyektor, Internet, Development Tools', 'available'),
('Lab Bahasa', 'Laboratorium bahasa dengan fasilitas audio-visual modern', 'Language Lab', 35, 600000.00, 'Lantai 3, Gedung B', 'AC, Audio System, Headset 35, Proyektor, Booth', 'available'),
('Lab Kimia', 'Laboratorium kimia lengkap dengan peralatan eksperimen', 'Laboratory', 25, 900000.00, 'Lantai 2, Gedung D', 'AC, Peralatan Lab, Fume Hood, Safety Equipment', 'available'),
('Lab Fisika', 'Laboratorium fisika dengan alat praktek lengkap', 'Laboratory', 30, 850000.00, 'Lantai 2, Gedung D', 'AC, Peralatan Lab, Alat Ukur, Proyektor', 'available'),
('Lapangan Basket', 'Lapangan basket outdoor dengan standar internasional', 'Sports', 100, 500000.00, 'Area Olahraga', 'Ring Basket, Lampu Penerangan, Tribune', 'available'),
('Lapangan Futsal', 'Lapangan futsal indoor dengan lantai sintetis', 'Sports', 80, 600000.00, 'Gedung Olahraga', 'Gawang, Lampu LED, Ruang Ganti', 'available'),
('Hall Serbaguna', 'Hall serbaguna untuk berbagai acara dan kegiatan', 'Event Space', 200, 1500000.00, 'Lantai 1, Gedung E', 'AC, Sound System, Panggung Kecil, Kursi Plastik', 'available'),
('Ruang Rapat', 'Ruang rapat eksekutif dengan fasilitas modern', 'Event Space', 20, 400000.00, 'Lantai 3, Gedung Utama', 'AC, Meja Rapat, Proyektor, TV LED, Whiteboard', 'available'),
('Kafeteria', 'Area kafeteria untuk acara makan bersama', 'Cafe', 150, 800000.00, 'Lantai 1, Gedung F', 'Meja Kursi, Dapur Kecil, Dispenser, AC', 'available');

-- Sample Bookings
INSERT INTO bookings (facility_id, user_id, user_name, user_email, user_phone, organization, event_name, booking_date, start_time, end_time, participants, total_price, status) VALUES
(1, 1, 'John Doe', 'john@example.com', '081234567890', 'PT. Teknologi Maju', 'Seminar Teknologi 2024', '2024-03-15', '08:00:00', '17:00:00', 200, 2500000.00, 'approved'),
(3, 2, 'Jane Smith', 'jane@example.com', '081234567891', 'Yayasan Pendidikan', 'Workshop Guru', '2024-03-20', '09:00:00', '15:00:00', 35, 300000.00, 'pending'),
(6, 3, 'Ahmad Rahman', 'ahmad@example.com', '081234567892', 'SMK Teknologi', 'Pelatihan Programming', '2024-03-25', '08:00:00', '16:00:00', 25, 800000.00, 'approved'),
(2, 1, 'John Doe', 'john@example.com', '081234567890', 'Komunitas IT', 'Tech Talk Series', '2024-04-10', '14:00:00', '18:00:00', 150, 2000000.00, 'pending'),
(11, 2, 'Jane Smith', 'jane@example.com', '081234567891', 'Club Basket Sekolah', 'Turnamen Basket Antar Sekolah', '2024-04-05', '08:00:00', '17:00:00', 80, 500000.00, 'approved'),
(4, 4, 'Siti Nurhaliza', 'siti@example.com', '081234567893', 'Yayasan Bina Anak', 'Pelatihan Guru PAUD', '2024-05-05', '08:00:00', '16:00:00', 30, 250000.00, 'pending'),
(7, 5, 'Budi Santoso', 'budi@example.com', '081234567894', 'PT Maju Teknologi', 'Training IT Staff', '2024-05-12', '09:00:00', '17:00:00', 20, 700000.00, 'pending'),
(8, 6, 'Dewi Kartika', 'dewi@example.com', '081234567895', 'English Club', 'Workshop English Speaking', '2024-05-18', '13:00:00', '16:00:00', 25, 600000.00, 'pending');

-- Sample Payments (manually created to avoid trigger issues)
INSERT INTO payments (booking_id, payment_amount, payment_method, payment_status, payment_date, transaction_id) VALUES
(1, 2500000.00, 'bank_transfer', 'paid', '2024-03-10 10:30:00', 'TXN_2024031001'),
(2, 300000.00, 'ewallet', 'pending', NULL, NULL),
(3, 800000.00, 'bank_transfer', 'paid', '2024-03-22 14:15:00', 'TXN_2024032201'),
(4, 2000000.00, 'qris', 'pending', NULL, NULL),
(5, 500000.00, 'bank_transfer', 'paid', '2024-04-01 09:45:00', 'TXN_2024040101'),
(6, 250000.00, 'ewallet', 'pending', NULL, NULL),
(7, 700000.00, 'bank_transfer', 'pending', NULL, NULL),
(8, 600000.00, 'qris', 'pending', NULL, NULL);

-- ============================================
-- CREATE VIEWS FOR REPORTING
-- ============================================

-- View for booking summary
CREATE VIEW booking_summary AS
SELECT
    b.id,
    b.event_name,
    b.user_name,
    b.user_email,
    f.name AS facility_name,
    f.category AS facility_category,
    b.booking_date,
    b.start_time,
    b.end_time,
    b.participants,
    b.total_price,
    b.status AS booking_status,
    p.payment_status,
    p.payment_method,
    b.created_at
FROM bookings b
JOIN facilities f ON b.facility_id = f.id
LEFT JOIN payments p ON b.id = p.booking_id
ORDER BY b.created_at DESC;

-- View for facility utilization
CREATE VIEW facility_utilization AS
SELECT
    f.id,
    f.name,
    f.category,
    f.capacity,
    f.price_per_day,
    COUNT(b.id) as total_bookings,
    COUNT(CASE WHEN b.status = 'approved' THEN 1 END) as approved_bookings,
    COUNT(CASE WHEN b.status = 'pending' THEN 1 END) as pending_bookings,
    SUM(CASE WHEN b.status = 'approved' THEN b.total_price ELSE 0 END) as total_revenue
FROM facilities f
LEFT JOIN bookings b ON f.id = b.facility_id
GROUP BY f.id, f.name, f.category, f.capacity, f.price_per_day
ORDER BY total_bookings DESC;

-- View for monthly revenue
CREATE VIEW monthly_revenue AS
SELECT
    YEAR(b.booking_date) as year,
    MONTH(b.booking_date) as month,
    MONTHNAME(b.booking_date) as month_name,
    COUNT(b.id) as total_bookings,
    SUM(b.total_price) as total_revenue,
    AVG(b.total_price) as average_booking_value
FROM bookings b
WHERE b.status = 'approved'
GROUP BY YEAR(b.booking_date), MONTH(b.booking_date)
ORDER BY year DESC, month DESC;

-- Revenue by category report
CREATE VIEW revenue_by_category AS
SELECT
    f.category,
    COUNT(b.id) as total_bookings,
    SUM(CASE WHEN b.status = 'approved' THEN b.total_price ELSE 0 END) as total_revenue,
    AVG(CASE WHEN b.status = 'approved' THEN b.total_price ELSE 0 END) as avg_revenue,
    COUNT(CASE WHEN b.status = 'pending' THEN 1 END) as pending_bookings
FROM facilities f
LEFT JOIN bookings b ON f.id = b.facility_id
GROUP BY f.category
ORDER BY total_revenue DESC;

-- ============================================
-- CREATE INDEXES FOR PERFORMANCE
-- ============================================

-- Additional composite indexes for better performance
CREATE INDEX idx_bookings_date_status ON bookings(booking_date, status);
CREATE INDEX idx_bookings_facility_date ON bookings(facility_id, booking_date);
CREATE INDEX idx_payments_status_date ON payments(payment_status, created_at);
CREATE INDEX idx_facilities_category_status ON facilities(category, status);

-- ============================================
-- USEFUL FUNCTIONS (MariaDB/MySQL Compatible)
-- ============================================

-- Function to check if facility is available on a specific date
-- Note: Using simple SELECT instead of stored procedure for compatibility

-- Sample query to check facility availability:
-- SELECT
--     f.id,
--     f.name,
--     f.capacity,
--     f.status,
--     CASE
--         WHEN f.status != 'available' THEN 'Facility not available'
--         WHEN EXISTS(
--             SELECT 1 FROM bookings b
--             WHERE b.facility_id = f.id
--             AND b.booking_date = '2024-05-15'
--             AND b.status IN ('approved', 'pending')
--         ) THEN 'Already booked'
--         ELSE 'Available'
--     END as availability_status
-- FROM facilities f
-- WHERE f.id = 1;

-- ============================================
-- SAMPLE USEFUL QUERIES
-- ============================================

-- Check all facility availability for a specific date
-- SELECT
--     f.name,
--     f.category,
--     f.capacity,
--     CASE
--         WHEN f.status != 'available' THEN 'Unavailable'
--         WHEN EXISTS(
--             SELECT 1 FROM bookings b
--             WHERE b.facility_id = f.id
--             AND b.booking_date = CURDATE()
--             AND b.status IN ('approved', 'pending')
--         ) THEN 'Booked'
--         ELSE 'Available'
--     END as status
-- FROM facilities f
-- ORDER BY f.category, f.name;

-- Get booking statistics
-- SELECT
--     COUNT(*) as total_bookings,
--     COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_bookings,
--     COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_bookings,
--     COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_bookings,
--     SUM(CASE WHEN status = 'approved' THEN total_price ELSE 0 END) as total_revenue
-- FROM bookings
-- WHERE booking_date >= '2024-01-01' AND booking_date <= '2024-12-31';

-- Get most popular facilities
-- SELECT
--     f.name,
--     f.category,
--     COUNT(b.id) as booking_count,
--     AVG(b.total_price) as avg_price,
--     SUM(CASE WHEN b.status = 'approved' THEN b.total_price ELSE 0 END) as total_revenue
-- FROM facilities f
-- LEFT JOIN bookings b ON f.id = b.facility_id
-- GROUP BY f.id, f.name, f.category
-- HAVING booking_count > 0
-- ORDER BY booking_count DESC, total_revenue DESC;

-- ============================================
-- DATA VERIFICATION QUERIES
-- ============================================

-- Verify setup completion
SELECT 'GRAVENUE DATABASE SETUP COMPLETED SUCCESSFULLY!' as MESSAGE;

-- Show created tables
SHOW TABLES;

-- Show sample data counts
SELECT
    'users' as table_name, COUNT(*) as record_count FROM users
UNION ALL
SELECT 'admins', COUNT(*) FROM admins
UNION ALL
SELECT 'facilities', COUNT(*) FROM facilities
UNION ALL
SELECT 'bookings', COUNT(*) FROM bookings
UNION ALL
SELECT 'payments', COUNT(*) FROM payments;

-- Show views
SELECT 'booking_summary' as view_name, COUNT(*) as records FROM booking_summary
UNION ALL
SELECT 'facility_utilization', COUNT(*) FROM facility_utilization
UNION ALL
SELECT 'monthly_revenue', COUNT(*) FROM monthly_revenue
UNION ALL
SELECT 'revenue_by_category', COUNT(*) FROM revenue_by_category;

-- ============================================
-- DEFAULT LOGIN CREDENTIALS
-- ============================================
/*
ADMIN LOGIN:
Email: admin@gravenue.com
Password: password

Email: admin@smkn4malang.sch.id
Password: password

USER LOGIN:
Email: john@example.com
Password: password

Email: jane@example.com
Password: password

Email: ahmad@example.com
Password: password

Email: siti@example.com
Password: password

Email: budi@example.com
Password: password

Email: dewi@example.com
Password: password

Note: All sample accounts use "password" as the default password
Change these in production!
*/

-- ============================================
-- SETUP COMPLETE
-- ============================================
SELECT 'Database setup completed successfully!' as status,
       'Ready for Laravel application!' as message,
       '6 users, 15 facilities, 8 bookings created' as data_summary;
