-- ============================================
-- GRAVENUE DATABASE BACKUP & RESTORE SCRIPTS
-- Platform Penyewaan Fasilitas SMKN 4 Malang
-- ============================================

-- ============================================
-- BACKUP PROCEDURES
-- ============================================

-- Create backup directory (run this in MySQL command line or phpMyAdmin)
-- Note: Adjust path according to your system

-- Full Database Backup
-- mysqldump -u root -p gravenue_db > gravenue_backup_$(date +%Y%m%d_%H%M%S).sql

-- Backup with structure only
-- mysqldump -u root -p --no-data gravenue_db > gravenue_structure_$(date +%Y%m%d).sql

-- Backup data only
-- mysqldump -u root -p --no-create-info gravenue_db > gravenue_data_$(date +%Y%m%d).sql

-- ============================================
-- MANUAL BACKUP SCRIPT
-- ============================================

-- Create backup tables with current date suffix
SET @backup_suffix = DATE_FORMAT(NOW(), '%Y%m%d_%H%i%s');

-- Backup Users Table
SET @sql = CONCAT('CREATE TABLE users_backup_', @backup_suffix, ' AS SELECT * FROM users');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Backup Admins Table
SET @sql = CONCAT('CREATE TABLE admins_backup_', @backup_suffix, ' AS SELECT * FROM admins');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Backup Facilities Table
SET @sql = CONCAT('CREATE TABLE facilities_backup_', @backup_suffix, ' AS SELECT * FROM facilities');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Backup Bookings Table
SET @sql = CONCAT('CREATE TABLE bookings_backup_', @backup_suffix, ' AS SELECT * FROM bookings');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Backup Payments Table
SET @sql = CONCAT('CREATE TABLE payments_backup_', @backup_suffix, ' AS SELECT * FROM payments');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- RESTORE PROCEDURES
-- ============================================

-- To restore from backup file:
-- mysql -u root -p gravenue_db < gravenue_backup_YYYYMMDD_HHMMSS.sql

-- ============================================
-- CLEAN BACKUP PROCEDURE
-- ============================================

DELIMITER //
CREATE PROCEDURE CleanOldBackups()
BEGIN
    -- Variables for dynamic SQL
    DECLARE done INT DEFAULT FALSE;
    DECLARE table_name VARCHAR(255);
    DECLARE sql_stmt TEXT;

    -- Cursor to find backup tables older than 30 days
    DECLARE backup_cursor CURSOR FOR
        SELECT TABLE_NAME
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = 'gravenue_db'
        AND TABLE_NAME REGEXP '_backup_[0-9]{8}_[0-9]{6}$'
        AND STR_TO_DATE(
            SUBSTRING(TABLE_NAME, -15, 8),
            '%Y%m%d'
        ) < DATE_SUB(CURDATE(), INTERVAL 30 DAY);

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN backup_cursor;

    cleanup_loop: LOOP
        FETCH backup_cursor INTO table_name;
        IF done THEN
            LEAVE cleanup_loop;
        END IF;

        SET sql_stmt = CONCAT('DROP TABLE IF EXISTS ', table_name);
        SET @sql = sql_stmt;
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;

        SELECT CONCAT('Dropped old backup table: ', table_name) as message;

    END LOOP cleanup_loop;

    CLOSE backup_cursor;
END //
DELIMITER ;

-- ============================================
-- INCREMENTAL BACKUP PROCEDURE
-- ============================================

DELIMITER //
CREATE PROCEDURE CreateIncrementalBackup(IN last_backup_date DATETIME)
BEGIN
    DECLARE backup_suffix VARCHAR(20);
    SET backup_suffix = DATE_FORMAT(NOW(), '%Y%m%d_%H%i%s');

    -- Backup new/modified users
    SET @sql = CONCAT('CREATE TABLE users_incremental_', backup_suffix, ' AS
                      SELECT * FROM users WHERE updated_at > "', last_backup_date, '"');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    -- Backup new/modified bookings
    SET @sql = CONCAT('CREATE TABLE bookings_incremental_', backup_suffix, ' AS
                      SELECT * FROM bookings WHERE updated_at > "', last_backup_date, '"');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    -- Backup new/modified payments
    SET @sql = CONCAT('CREATE TABLE payments_incremental_', backup_suffix, ' AS
                      SELECT * FROM payments WHERE updated_at > "', last_backup_date, '"');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    SELECT CONCAT('Incremental backup created with suffix: ', backup_suffix) as message;
END //
DELIMITER ;

-- ============================================
-- DATA VERIFICATION PROCEDURE
-- ============================================

DELIMITER //
CREATE PROCEDURE VerifyDataIntegrity()
BEGIN
    -- Check for orphaned bookings (bookings without facilities)
    SELECT 'ORPHANED BOOKINGS:' as check_type, COUNT(*) as count
    FROM bookings b
    LEFT JOIN facilities f ON b.facility_id = f.id
    WHERE f.id IS NULL;

    -- Check for orphaned payments (payments without bookings)
    SELECT 'ORPHANED PAYMENTS:' as check_type, COUNT(*) as count
    FROM payments p
    LEFT JOIN bookings b ON p.booking_id = b.id
    WHERE b.id IS NULL;

    -- Check for bookings without payments
    SELECT 'BOOKINGS WITHOUT PAYMENTS:' as check_type, COUNT(*) as count
    FROM bookings b
    LEFT JOIN payments p ON b.id = p.booking_id
    WHERE p.id IS NULL;

    -- Check for inconsistent booking-payment amounts
    SELECT 'AMOUNT MISMATCHES:' as check_type, COUNT(*) as count
    FROM bookings b
    INNER JOIN payments p ON b.id = p.booking_id
    WHERE b.total_price != p.payment_amount;

    -- Check for future bookings with past dates
    SELECT 'INVALID DATES:' as check_type, COUNT(*) as count
    FROM bookings
    WHERE booking_date < CURDATE() AND status = 'pending';

    -- Summary statistics
    SELECT 'TOTAL RECORDS:' as summary;
    SELECT 'Users' as table_name, COUNT(*) as count FROM users
    UNION ALL
    SELECT 'Admins', COUNT(*) FROM admins
    UNION ALL
    SELECT 'Facilities', COUNT(*) FROM facilities
    UNION ALL
    SELECT 'Bookings', COUNT(*) FROM bookings
    UNION ALL
    SELECT 'Payments', COUNT(*) FROM payments;

END //
DELIMITER ;

-- ============================================
-- DATABASE MAINTENANCE PROCEDURES
-- ============================================

DELIMITER //
CREATE PROCEDURE DatabaseMaintenance()
BEGIN
    -- Optimize tables
    OPTIMIZE TABLE users;
    OPTIMIZE TABLE admins;
    OPTIMIZE TABLE facilities;
    OPTIMIZE TABLE bookings;
    OPTIMIZE TABLE payments;

    -- Analyze tables for better query performance
    ANALYZE TABLE users;
    ANALYZE TABLE admins;
    ANALYZE TABLE facilities;
    ANALYZE TABLE bookings;
    ANALYZE TABLE payments;

    -- Update table statistics
    SELECT 'DATABASE MAINTENANCE COMPLETED' as message;

    -- Show table sizes
    SELECT
        table_name,
        ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
    FROM information_schema.TABLES
    WHERE table_schema = 'gravenue_db'
    ORDER BY (data_length + index_length) DESC;

END //
DELIMITER ;

-- ============================================
-- EMERGENCY RESTORE PROCEDURES
-- ============================================

DELIMITER //
CREATE PROCEDURE EmergencyRestore(IN backup_suffix VARCHAR(20))
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SHOW ERRORS;
        SELECT 'RESTORE FAILED - TRANSACTION ROLLED BACK' as error_message;
    END;

    START TRANSACTION;

    -- Restore users
    SET @sql = CONCAT('INSERT IGNORE INTO users SELECT * FROM users_backup_', backup_suffix);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    -- Restore facilities
    SET @sql = CONCAT('INSERT IGNORE INTO facilities SELECT * FROM facilities_backup_', backup_suffix);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    -- Restore bookings
    SET @sql = CONCAT('INSERT IGNORE INTO bookings SELECT * FROM bookings_backup_', backup_suffix);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    -- Restore payments
    SET @sql = CONCAT('INSERT IGNORE INTO payments SELECT * FROM payments_backup_', backup_suffix);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    COMMIT;
    SELECT CONCAT('EMERGENCY RESTORE COMPLETED FROM BACKUP: ', backup_suffix) as success_message;

END //
DELIMITER ;

-- ============================================
-- EXPORT DATA TO CSV
-- ============================================

-- Export bookings to CSV (adjust path as needed)
-- SELECT * FROM booking_summary
-- INTO OUTFILE '/tmp/gravenue_bookings_export.csv'
-- FIELDS TERMINATED BY ','
-- ENCLOSED BY '"'
-- LINES TERMINATED BY '\n';

-- Export facilities to CSV
-- SELECT * FROM facilities
-- INTO OUTFILE '/tmp/gravenue_facilities_export.csv'
-- FIELDS TERMINATED BY ','
-- ENCLOSED BY '"'
-- LINES TERMINATED BY '\n';

-- Export revenue report to CSV
-- SELECT * FROM revenue_by_category
-- INTO OUTFILE '/tmp/gravenue_revenue_report.csv'
-- FIELDS TERMINATED BY ','
-- ENCLOSED BY '"'
-- LINES TERMINATED BY '\n';

-- ============================================
-- BACKUP AUTOMATION SCRIPT (FOR CRON)
-- ============================================

/*
Create a shell script: gravenue_backup.sh

#!/bin/bash
DB_USER="root"
DB_PASS="your_password"
DB_NAME="gravenue_db"
BACKUP_DIR="/path/to/backups"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory if not exists
mkdir -p $BACKUP_DIR

# Full backup
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/gravenue_full_$DATE.sql

# Compress backup
gzip $BACKUP_DIR/gravenue_full_$DATE.sql

# Remove backups older than 30 days
find $BACKUP_DIR -name "gravenue_full_*.sql.gz" -mtime +30 -delete

# Log backup completion
echo "$(date): Backup completed - gravenue_full_$DATE.sql.gz" >> $BACKUP_DIR/backup.log

Add to crontab for daily backup at 2 AM:
0 2 * * * /path/to/gravenue_backup.sh
*/

-- ============================================
-- RESTORE VERIFICATION QUERIES
-- ============================================

-- Check data consistency after restore
CREATE VIEW restore_verification AS
SELECT
    'Data Consistency Check' as check_name,
    (SELECT COUNT(*) FROM users) as user_count,
    (SELECT COUNT(*) FROM facilities) as facility_count,
    (SELECT COUNT(*) FROM bookings) as booking_count,
    (SELECT COUNT(*) FROM payments) as payment_count,
    (SELECT COUNT(*) FROM bookings WHERE status = 'approved') as approved_bookings,
    (SELECT SUM(total_price) FROM bookings WHERE status = 'approved') as total_revenue;

-- ============================================
-- BACKUP MONITORING
-- ============================================

-- Create backup log table
CREATE TABLE IF NOT EXISTS backup_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    backup_type ENUM('full', 'incremental', 'manual') NOT NULL,
    backup_name VARCHAR(255) NOT NULL,
    backup_size BIGINT NULL,
    status ENUM('started', 'completed', 'failed') NOT NULL,
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time TIMESTAMP NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Procedure to log backup operations
DELIMITER //
CREATE PROCEDURE LogBackupOperation(
    IN backup_type VARCHAR(20),
    IN backup_name VARCHAR(255),
    IN operation_status VARCHAR(20),
    IN notes TEXT
)
BEGIN
    INSERT INTO backup_log (backup_type, backup_name, status, notes)
    VALUES (backup_type, backup_name, operation_status, notes);
END //
DELIMITER ;

-- ============================================
-- USAGE INSTRUCTIONS
-- ============================================

/*
BACKUP PROCEDURES USAGE:

1. Full Manual Backup:
   - Run the manual backup script section
   - Tables will be created with timestamp suffix

2. Incremental Backup:
   CALL CreateIncrementalBackup('2024-01-01 00:00:00');

3. Clean Old Backups:
   CALL CleanOldBackups();

4. Verify Data Integrity:
   CALL VerifyDataIntegrity();

5. Database Maintenance:
   CALL DatabaseMaintenance();

6. Emergency Restore:
   CALL EmergencyRestore('20240315_143022');

7. Command Line Backup:
   mysqldump -u root -p gravenue_db > backup_file.sql

8. Command Line Restore:
   mysql -u root -p gravenue_db < backup_file.sql

RECOMMENDED BACKUP SCHEDULE:
- Daily: Incremental backup at 2 AM
- Weekly: Full backup on Sunday at 1 AM
- Monthly: Archive backup to external storage
- Clean old backups: Monthly

IMPORTANT NOTES:
- Always test restore procedures on a copy
- Verify backup integrity regularly
- Store backups in multiple locations
- Document restore procedures for team
- Monitor backup log table for issues
*/

SELECT 'BACKUP AND RESTORE PROCEDURES CREATED SUCCESSFULLY!' as MESSAGE;
