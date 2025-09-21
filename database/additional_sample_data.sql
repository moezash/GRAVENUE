-- ============================================
-- ADDITIONAL SAMPLE DATA FOR GRAVENUE
-- Platform Penyewaan Fasilitas SMKN 4 Malang
-- ============================================

USE gravenue_db;

-- ============================================
-- MORE SAMPLE USERS
-- ============================================
INSERT INTO users (name, email, phone, password) VALUES
('Siti Nurhaliza', 'siti@example.com', '081234567893', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Budi Santoso', 'budi@example.com', '081234567894', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Dewi Kartika', 'dewi@example.com', '081234567895', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Andi Pratama', 'andi@example.com', '081234567896', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Rina Sari', 'rina@example.com', '081234567897', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Agus Wijaya', 'agus@example.com', '081234567898', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Maya Indira', 'maya@example.com', '081234567899', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Rudi Hermawan', 'rudi@example.com', '081234567900', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- ============================================
-- MORE FACILITIES
-- ============================================
INSERT INTO facilities (name, description, category, capacity, price_per_day, location, features, status) VALUES
('Ruang Kelas C1', 'Ruang kelas modern dengan smart board', 'Classroom', 45, 350000.00, 'Lantai 1, Gedung C', 'AC, Smart Board, Meja Kursi 45, Internet', 'available'),
('Ruang Kelas C2', 'Ruang kelas dengan fasilitas multimedia', 'Classroom', 40, 320000.00, 'Lantai 1, Gedung C', 'AC, Proyektor, Sound System, Meja Kursi 40', 'available'),
('Ruang Kelas D1', 'Ruang kelas untuk workshop kreatif', 'Classroom', 30, 280000.00, 'Lantai 1, Gedung D', 'AC, Meja Kerja, Whiteboard, Toolkit Basic', 'available'),

('Lab Biologi', 'Laboratorium biologi dengan mikroskop dan specimen', 'Laboratory', 25, 750000.00, 'Lantai 3, Gedung D', 'AC, Mikroskop 15 unit, Specimen, Alat Praktik', 'available'),
('Lab Elektronika', 'Laboratorium elektronika untuk praktik teknik', 'Laboratory', 20, 950000.00, 'Lantai 3, Gedung E', 'AC, Oscilloscope, Multimeter, Breadboard, Komponen', 'available'),
('Lab Otomotif', 'Laboratorium otomotif dengan engine training', 'Laboratory', 15, 1200000.00, 'Gedung Workshop', 'Mesin Latih, Tools Otomotif, Lift Mobil, Kompressor', 'available'),

('Studio Musik', 'Studio musik kedap suara dengan instrumen lengkap', 'Entertainment', 12, 800000.00, 'Lantai 2, Gedung F', 'Kedap Suara, Piano, Drum, Gitar, Mixing Console', 'available'),
('Studio Video', 'Studio untuk produksi video dan fotografi', 'Entertainment', 8, 1000000.00, 'Lantai 3, Gedung F', 'Green Screen, Lighting, Camera, Audio Equipment', 'available'),
('Ruang Karaoke', 'Ruang karaoke untuk hiburan dan acara santai', 'Entertainment', 15, 400000.00, 'Lantai 1, Gedung G', 'Karaoke System, Sofa, TV LED 55", Lampu Disco', 'available'),

('Lapangan Voli', 'Lapangan voli outdoor standard internasional', 'Sports', 50, 300000.00, 'Area Olahraga Outdoor', 'Net Voli, Lampu Penerangan, Garis Lapangan', 'available'),
('Lapangan Badminton', 'Lapangan badminton indoor 2 court', 'Sports', 40, 450000.00, 'Gedung Olahraga', '2 Court, Net, Lampu LED, Matras', 'available'),
('Kolam Renang', 'Kolam renang semi olympic dengan fasilitas lengkap', 'Sports', 100, 1500000.00, 'Area Aquatic', 'Kolam 25m, Ruang Ganti, Shower, Penjaga Kolam', 'available'),

('Mini Theater', 'Mini theater untuk screening dan presentasi', 'Entertainment', 60, 1200000.00, 'Lantai 2, Gedung G', 'Layar Besar, Projector 4K, Sound System, Kursi Theater', 'available'),
('Ruang Gaming', 'Ruang gaming dengan PC gaming dan konsol', 'Entertainment', 20, 600000.00, 'Lantai 2, Gedung C', '10 PC Gaming, PlayStation, Xbox, Gaming Chair', 'available'),
('Rooftop Area', 'Area rooftop untuk acara outdoor dan gathering', 'Event Space', 80, 800000.00, 'Rooftop Gedung Utama', 'Open Space, Gazebo, BBQ Area, City View', 'available'),

('Kantin Besar', 'Kantin dengan kapasitas besar untuk acara makan', 'Cafe', 200, 1000000.00, 'Lantai 1, Gedung Kantin', 'Meja Kursi 200, Kitchen, Food Court Style', 'available'),
('Cafe Mini', 'Cafe kecil untuk meeting santai dan diskusi', 'Cafe', 25, 350000.00, 'Lantai 1, Gedung F', 'Meja Cafe, Sofa, Coffee Machine, Free WiFi', 'available');

-- ============================================
-- MORE BOOKINGS (HISTORICAL DATA)
-- ============================================
INSERT INTO bookings (facility_id, user_id, user_name, user_email, user_phone, organization, event_name, booking_date, start_time, end_time, participants, total_price, status) VALUES
-- Bookings for March 2024
(4, 4, 'Siti Nurhaliza', 'siti@example.com', '081234567893', 'Yayasan Bina Anak', 'Pelatihan Guru PAUD', '2024-03-05', '08:00:00', '16:00:00', 30, 250000.00, 'completed'),
(7, 5, 'Budi Santoso', 'budi@example.com', '081234567894', 'PT Maju Teknologi', 'Training IT Staff', '2024-03-12', '09:00:00', '17:00:00', 20, 700000.00, 'completed'),
(8, 6, 'Dewi Kartika', 'dewi@example.com', '081234567895', 'English Club', 'Workshop English Speaking', '2024-03-18', '13:00:00', '16:00:00', 25, 600000.00, 'completed'),
(13, 7, 'Andi Pratama', 'andi@example.com', '081234567896', 'Event Organizer', 'Wedding Reception', '2024-03-22', '18:00:00', '23:00:00', 150, 1500000.00, 'completed'),

-- Bookings for April 2024
(9, 8, 'Rina Sari', 'rina@example.com', '081234567897', 'Lab School', 'Praktikum Kimia SMA', '2024-04-02', '08:00:00', '15:00:00', 20, 900000.00, 'approved'),
(12, 9, 'Agus Wijaya', 'agus@example.com', '081234567898', 'Futsal Community', 'Tournament Futsal', '2024-04-08', '08:00:00', '18:00:00', 64, 600000.00, 'approved'),
(14, 10, 'Maya Indira', 'maya@example.com', '081234567899', 'Corporate Training', 'Leadership Workshop', '2024-04-15', '09:00:00', '16:00:00', 18, 400000.00, 'pending'),
(15, 11, 'Rudi Hermawan', 'rudi@example.com', '081234567900', 'Food Festival', 'Kuliner Nusantara', '2024-04-20', '10:00:00', '20:00:00', 120, 800000.00, 'pending'),

-- Bookings for May 2024 (Future)
(1, 4, 'Siti Nurhaliza', 'siti@example.com', '081234567893', 'Graduation Committee', 'Wisuda SMKN 4 Malang', '2024-05-15', '08:00:00', '12:00:00', 400, 2500000.00, 'pending'),
(2, 5, 'Budi Santoso', 'budi@example.com', '081234567894', 'Tech Conference', 'Digital Innovation Summit', '2024-05-22', '08:00:00', '17:00:00', 250, 2000000.00, 'pending'),
(19, 6, 'Dewi Kartika', 'dewi@example.com', '081234567895', 'Music School', 'Konser Musik Sekolah', '2024-05-25', '19:00:00', '22:00:00', 10, 800000.00, 'pending'),
(22, 7, 'Andi Pratama', 'andi@example.com', '081234567896', 'Swimming Club', 'Kejuaraan Renang Antar Sekolah', '2024-05-30', '07:00:00', '17:00:00', 80, 1500000.00, 'pending');

-- ============================================
-- UPDATE PAYMENTS FOR NEW BOOKINGS
-- ============================================
-- Note: Payments will be auto-created by trigger, but let's update some statuses

UPDATE payments
SET payment_status = 'paid', payment_method = 'bank_transfer', payment_date = '2024-03-04 10:00:00', transaction_id = 'TXN_2024030401'
WHERE booking_id = (SELECT id FROM bookings WHERE user_email = 'siti@example.com' AND booking_date = '2024-03-05');

UPDATE payments
SET payment_status = 'paid', payment_method = 'ewallet', payment_date = '2024-03-11 15:30:00', transaction_id = 'TXN_2024031101'
WHERE booking_id = (SELECT id FROM bookings WHERE user_email = 'budi@example.com' AND booking_date = '2024-03-12');

UPDATE payments
SET payment_status = 'paid', payment_method = 'qris', payment_date = '2024-03-17 14:20:00', transaction_id = 'TXN_2024031701'
WHERE booking_id = (SELECT id FROM bookings WHERE user_email = 'dewi@example.com' AND booking_date = '2024-03-18');

UPDATE payments
SET payment_status = 'paid', payment_method = 'bank_transfer', payment_date = '2024-03-20 09:45:00', transaction_id = 'TXN_2024032001'
WHERE booking_id = (SELECT id FROM bookings WHERE user_email = 'andi@example.com' AND booking_date = '2024-03-22');

UPDATE payments
SET payment_status = 'paid', payment_method = 'bank_transfer', payment_date = '2024-04-01 11:15:00', transaction_id = 'TXN_2024040102'
WHERE booking_id = (SELECT id FROM bookings WHERE user_email = 'rina@example.com' AND booking_date = '2024-04-02');

UPDATE payments
SET payment_status = 'paid', payment_method = 'ewallet', payment_date = '2024-04-07 16:30:00', transaction_id = 'TXN_2024040701'
WHERE booking_id = (SELECT id FROM bookings WHERE user_email = 'agus@example.com' AND booking_date = '2024-04-08');

-- ============================================
-- CREATE SAMPLE SCHEDULE DATA
-- ============================================
CREATE TABLE IF NOT EXISTS schedules (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    facility_id BIGINT UNSIGNED NOT NULL,
    date DATE NOT NULL,
    time_slot VARCHAR(20) NOT NULL,
    status ENUM('available', 'booked', 'maintenance') DEFAULT 'available',
    booking_id BIGINT UNSIGNED NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,

    INDEX idx_facility_date (facility_id, date),
    INDEX idx_date_slot (date, time_slot),
    UNIQUE KEY unique_facility_date_slot (facility_id, date, time_slot)
);

-- Generate schedule for next 3 months (sample data)
DELIMITER //
CREATE PROCEDURE GenerateSchedules()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE fac_id INT;
    DECLARE cur_date DATE;
    DECLARE end_date DATE;
    DECLARE cur_facility CURSOR FOR SELECT id FROM facilities WHERE status = 'available';
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    SET cur_date = CURDATE();
    SET end_date = DATE_ADD(CURDATE(), INTERVAL 90 DAY);

    OPEN cur_facility;
    facility_loop: LOOP
        FETCH cur_facility INTO fac_id;
        IF done THEN
            LEAVE facility_loop;
        END IF;

        SET cur_date = CURDATE();
        date_loop: WHILE cur_date <= end_date DO
            -- Morning slot
            INSERT IGNORE INTO schedules (facility_id, date, time_slot, status)
            VALUES (fac_id, cur_date, '08:00-12:00', 'available');

            -- Afternoon slot
            INSERT IGNORE INTO schedules (facility_id, date, time_slot, status)
            VALUES (fac_id, cur_date, '13:00-17:00', 'available');

            -- Evening slot
            INSERT IGNORE INTO schedules (facility_id, date, time_slot, status)
            VALUES (fac_id, cur_date, '18:00-22:00', 'available');

            SET cur_date = DATE_ADD(cur_date, INTERVAL 1 DAY);
        END WHILE date_loop;
    END LOOP facility_loop;
    CLOSE cur_facility;
END //
DELIMITER ;

-- Execute the procedure
CALL GenerateSchedules();

-- Update schedules based on existing bookings
UPDATE schedules s
JOIN bookings b ON s.facility_id = b.facility_id AND s.date = b.booking_date
SET s.status = 'booked', s.booking_id = b.id
WHERE b.status IN ('approved', 'pending');

-- ============================================
-- MAINTENANCE SCHEDULE
-- ============================================
INSERT INTO schedules (facility_id, date, time_slot, status, notes) VALUES
(1, '2024-04-30', '08:00-12:00', 'maintenance', 'Pembersihan dan maintenance sound system'),
(6, '2024-05-01', '13:00-17:00', 'maintenance', 'Update software dan maintenance PC'),
(11, '2024-05-02', '08:00-12:00', 'maintenance', 'Pengecatan lapangan dan perbaikan ring');

-- ============================================
-- USEFUL QUERIES FOR TESTING
-- ============================================

-- Check facility availability for specific date
SELECT
    f.name,
    f.category,
    DATE(s.date) as date,
    s.time_slot,
    s.status,
    CASE WHEN b.event_name IS NOT NULL THEN b.event_name ELSE 'Available' END as event
FROM facilities f
LEFT JOIN schedules s ON f.id = s.facility_id
LEFT JOIN bookings b ON s.booking_id = b.id
WHERE s.date = CURDATE()
ORDER BY f.category, f.name, s.time_slot;

-- Get booking statistics by month
SELECT
    MONTHNAME(booking_date) as month,
    COUNT(*) as total_bookings,
    COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
    SUM(CASE WHEN status = 'approved' THEN total_price ELSE 0 END) as revenue
FROM bookings
WHERE YEAR(booking_date) = 2024
GROUP BY MONTH(booking_date), MONTHNAME(booking_date)
ORDER BY MONTH(booking_date);

-- Get most popular facilities
SELECT
    f.name,
    f.category,
    COUNT(b.id) as booking_count,
    AVG(b.total_price) as avg_price,
    SUM(CASE WHEN b.status = 'approved' THEN b.total_price ELSE 0 END) as total_revenue
FROM facilities f
LEFT JOIN bookings b ON f.id = b.facility_id
GROUP BY f.id, f.name, f.category
HAVING booking_count > 0
ORDER BY booking_count DESC, total_revenue DESC;

-- Get user activity
SELECT
    u.name,
    u.email,
    COUNT(b.id) as total_bookings,
    SUM(b.total_price) as total_spent,
    MAX(b.booking_date) as last_booking
FROM users u
LEFT JOIN bookings b ON u.id = b.user_id
GROUP BY u.id, u.name, u.email
HAVING total_bookings > 0
ORDER BY total_bookings DESC;

-- ============================================
-- SAMPLE REPORTS DATA
-- ============================================

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

-- Daily booking report
CREATE VIEW daily_bookings AS
SELECT
    DATE(b.booking_date) as booking_date,
    COUNT(*) as total_bookings,
    SUM(b.total_price) as daily_revenue,
    COUNT(DISTINCT b.facility_id) as facilities_used,
    COUNT(DISTINCT b.user_id) as unique_users
FROM bookings b
GROUP BY DATE(b.booking_date)
ORDER BY booking_date DESC;

SELECT 'ADDITIONAL SAMPLE DATA INSERTED SUCCESSFULLY!' as MESSAGE;

-- Show updated counts
SELECT
    'users' as table_name, COUNT(*) as record_count FROM users
UNION ALL
SELECT 'facilities', COUNT(*) FROM facilities
UNION ALL
SELECT 'bookings', COUNT(*) FROM bookings
UNION ALL
SELECT 'payments', COUNT(*) FROM payments
UNION ALL
SELECT 'schedules', COUNT(*) FROM schedules;
