-- ============================================
-- MARIADB COMPATIBILITY FIX SCRIPT
-- Fix for: Column count of mysql.proc is wrong. Expected 21, found 20
-- Platform: Gravenue - SMKN 4 Malang
-- ============================================

-- Step 1: Fix MySQL/MariaDB system tables compatibility
-- This error occurs when MariaDB version is upgraded but system tables are not updated

-- Check current MariaDB version
SELECT VERSION() as current_version;

-- ============================================
-- SOLUTION 1: Run mysql_upgrade (Recommended)
-- ============================================

-- Run this command in terminal (outside of MySQL):
-- sudo mysql_upgrade -u root -p
--
-- Or for newer MariaDB versions:
-- sudo mariadb-upgrade -u root -p
--
-- Then restart MariaDB:
-- sudo systemctl restart mariadb
-- or
-- sudo systemctl restart mysql

-- ============================================
-- SOLUTION 2: Manual system table fix
-- ============================================

-- If mysql_upgrade doesn't work, try manual fix:
-- USE mysql;
--
-- -- Check current proc table structure
-- DESCRIBE proc;
--
-- -- If column count is wrong, recreate the table
-- -- Backup current proc table first
-- CREATE TABLE proc_backup AS SELECT * FROM proc;
--
-- -- Drop and recreate proc table (DANGEROUS - use with caution)
-- -- DROP TABLE proc;
-- -- This will be automatically recreated with correct structure

-- ============================================
-- SOLUTION 3: Alternative Database Setup (No Stored Procedures)
-- ============================================

-- Use this alternative setup if stored procedures continue to cause issues
USE gravenue_db;

-- Instead of stored procedures, use these prepared queries for common functions:

-- ============================================
-- FUNCTION 1: Check Facility Availability
-- ============================================
-- Use this query to check if a facility is available:
/*
SELECT
    f.id,
    f.name,
    f.capacity,
    f.status,
    CASE
        WHEN f.status != 'available' THEN 'Facility not available'
        WHEN EXISTS(
            SELECT 1 FROM bookings b
            WHERE b.facility_id = f.id
            AND b.booking_date = '2024-05-15'  -- Replace with desired date
            AND b.status IN ('approved', 'pending')
        ) THEN 'Already booked'
        ELSE 'Available'
    END as availability_status
FROM facilities f
WHERE f.id = 1;  -- Replace with facility ID
*/

-- ============================================
-- FUNCTION 2: Get Booking Statistics
-- ============================================
-- Use this query for booking statistics:
/*
SELECT
    COUNT(*) as total_bookings,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_bookings,
    COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_bookings,
    COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_bookings,
    SUM(CASE WHEN status = 'approved' THEN total_price ELSE 0 END) as total_revenue
FROM bookings
WHERE booking_date >= '2024-01-01'  -- Replace with start date
  AND booking_date <= '2024-12-31'; -- Replace with end date
*/

-- ============================================
-- FUNCTION 3: Get Monthly Revenue
-- ============================================
-- Use this query for monthly revenue:
/*
SELECT
    YEAR(booking_date) as year,
    MONTH(booking_date) as month,
    MONTHNAME(booking_date) as month_name,
    COUNT(*) as total_bookings,
    SUM(total_price) as total_revenue,
    AVG(total_price) as average_booking_value
FROM bookings
WHERE status = 'approved'
  AND YEAR(booking_date) = 2024  -- Replace with desired year
GROUP BY YEAR(booking_date), MONTH(booking_date)
ORDER BY year DESC, month DESC;
*/

-- ============================================
-- FUNCTION 4: Get Facility Utilization
-- ============================================
-- Use this query for facility utilization:
/*
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
*/

-- ============================================
-- ALTERNATIVE TRIGGERS (Simplified)
-- ============================================

-- Drop existing triggers if any
DROP TRIGGER IF EXISTS after_booking_insert;
DROP TRIGGER IF EXISTS after_payment_update;

-- Simple trigger to auto-create payments (if MariaDB supports it)
DELIMITER $$

CREATE TRIGGER after_booking_insert
AFTER INSERT ON bookings
FOR EACH ROW
BEGIN
    INSERT INTO payments (booking_id, payment_amount, payment_status)
    VALUES (NEW.id, NEW.total_price, 'pending');
END$$

DELIMITER ;

-- ============================================
-- TROUBLESHOOTING QUERIES
-- ============================================

-- Check MariaDB/MySQL version and configuration
SELECT
    VERSION() as version,
    @@sql_mode as sql_mode,
    @@storage_engine as default_engine;

-- Check table engines
SELECT
    TABLE_NAME,
    ENGINE,
    TABLE_ROWS,
    DATA_LENGTH,
    INDEX_LENGTH
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'gravenue_db';

-- Verify foreign key constraints
SELECT
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'gravenue_db'
  AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Check for any corrupted tables
CHECK TABLE users, admins, facilities, bookings, payments;

-- Repair tables if needed (use with caution)
-- REPAIR TABLE users, admins, facilities, bookings, payments;

-- ============================================
-- LARAVEL SPECIFIC FIXES
-- ============================================

-- If using Laravel, you might need to adjust these settings:
-- In your .env file, try these configurations:

-- For older MariaDB versions:
-- DB_CONNECTION=mysql
-- DB_STRICT=false
-- DB_ENGINE=MyISAM

-- For newer MariaDB versions:
-- DB_CONNECTION=mysql
-- DB_STRICT=true
-- DB_ENGINE=InnoDB

-- ============================================
-- PERFORMANCE OPTIMIZATION
-- ============================================

-- Optimize tables after fixing compatibility issues
OPTIMIZE TABLE users;
OPTIMIZE TABLE admins;
OPTIMIZE TABLE facilities;
OPTIMIZE TABLE bookings;
OPTIMIZE TABLE payments;

-- Update table statistics
ANALYZE TABLE users;
ANALYZE TABLE admins;
ANALYZE TABLE facilities;
ANALYZE TABLE bookings;
ANALYZE TABLE payments;

-- ============================================
-- VERIFICATION TESTS
-- ============================================

-- Test basic functionality
SELECT 'Testing basic SELECT...' as test;
SELECT COUNT(*) as user_count FROM users;
SELECT COUNT(*) as facility_count FROM facilities;
SELECT COUNT(*) as booking_count FROM bookings;
SELECT COUNT(*) as payment_count FROM payments;

-- Test JOINs
SELECT 'Testing JOINs...' as test;
SELECT
    b.event_name,
    f.name as facility_name,
    u.name as user_name
FROM bookings b
JOIN facilities f ON b.facility_id = f.id
LEFT JOIN users u ON b.user_id = u.id
LIMIT 5;

-- Test complex queries
SELECT 'Testing complex queries...' as test;
SELECT
    f.category,
    COUNT(b.id) as booking_count,
    SUM(b.total_price) as revenue
FROM facilities f
LEFT JOIN bookings b ON f.id = b.facility_id
GROUP BY f.category;

-- ============================================
-- SUCCESS MESSAGE
-- ============================================

SELECT 'MariaDB compatibility fixes applied successfully!' as status;
SELECT 'If issues persist, run: sudo mysql_upgrade -u root -p' as recommendation;
SELECT 'Then restart MariaDB service' as next_step;

-- ============================================
-- COMMON MARIADB COMMANDS FOR REFERENCE
-- ============================================

/*
TERMINAL COMMANDS:

1. Check MariaDB status:
   sudo systemctl status mariadb

2. Restart MariaDB:
   sudo systemctl restart mariadb

3. Run upgrade:
   sudo mysql_upgrade -u root -p

4. Login to MariaDB:
   mysql -u root -p

5. Check error logs:
   sudo tail -f /var/log/mysql/error.log

6. Grant privileges:
   GRANT ALL PRIVILEGES ON gravenue_db.* TO 'username'@'localhost';
   FLUSH PRIVILEGES;

7. Create new user:
   CREATE USER 'gravenue_user'@'localhost' IDENTIFIED BY 'password';
   GRANT ALL PRIVILEGES ON gravenue_db.* TO 'gravenue_user'@'localhost';
   FLUSH PRIVILEGES;

8. Backup database:
   mysqldump -u root -p gravenue_db > gravenue_backup.sql

9. Restore database:
   mysql -u root -p gravenue_db < gravenue_backup.sql

10. Show processes:
    SHOW PROCESSLIST;
*/
