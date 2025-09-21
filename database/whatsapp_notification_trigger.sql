-- =====================================================
-- TRIGGER NOTIFIKASI WHATSAPP OTOMATIS
-- SMKN 4 Malang - Gravenue
-- =====================================================

DELIMITER $$

-- Stored Procedure untuk mengirim notifikasi WhatsApp
CREATE PROCEDURE `send_whatsapp_notification`(
    IN booking_id INT,
    IN new_status VARCHAR(20)
)
BEGIN
    DECLARE php_command TEXT;
    DECLARE script_path VARCHAR(255);
    
    -- Path ke script PHP (sesuaikan dengan lokasi project Anda)
    SET script_path = '/Applications/XAMPP/xamppfiles/htdocs/Gravenue/public/send_notification.php';
    
    -- Hanya kirim notifikasi untuk status approved dan rejected
    IF new_status IN ('approved', 'rejected') THEN
        -- Buat command untuk menjalankan script PHP
        SET php_command = CONCAT('php ', script_path, ' ', booking_id, ' ', new_status, ' > /dev/null 2>&1 &');
        
        -- Jalankan command (asynchronous)
        SET @sql = php_command;
        
        -- Log aktivitas trigger
        INSERT INTO notification_logs (
            booking_id, 
            phone_number, 
            message, 
            type, 
            status, 
            created_at, 
            updated_at
        ) VALUES (
            booking_id,
            'trigger_initiated',
            CONCAT('Trigger initiated for booking ', booking_id, ' with status ', new_status),
            CONCAT('booking_', new_status),
            'pending',
            NOW(),
            NOW()
        );
        
        -- Eksekusi command PHP (ini akan menjalankan script secara asynchronous)
        -- Catatan: Untuk keamanan, pastikan MySQL user memiliki permission untuk menjalankan system commands
        -- Atau gunakan alternative method seperti queue job
    END IF;
END$$

-- Trigger AFTER UPDATE pada tabel bookings
CREATE TRIGGER `booking_status_notification_trigger`
    AFTER UPDATE ON `bookings`
    FOR EACH ROW
BEGIN
    -- Cek apakah status berubah dari pending ke approved/rejected
    IF OLD.status != NEW.status AND NEW.status IN ('approved', 'rejected') THEN
        -- Panggil stored procedure untuk mengirim notifikasi
        CALL send_whatsapp_notification(NEW.id, NEW.status);
    END IF;
END$$

DELIMITER ;

-- =====================================================
-- ALTERNATIF: Menggunakan Event Scheduler MySQL
-- (Jika system() tidak tersedia atau tidak diizinkan)
-- =====================================================

-- Aktifkan event scheduler
SET GLOBAL event_scheduler = ON;

-- Event untuk memproses notifikasi pending setiap 30 detik
DELIMITER $$

CREATE EVENT IF NOT EXISTS `process_pending_notifications`
ON SCHEDULE EVERY 30 SECOND
STARTS CURRENT_TIMESTAMP
DO
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE booking_id INT;
    DECLARE notification_type VARCHAR(50);
    DECLARE php_command TEXT;
    
    -- Cursor untuk mengambil notifikasi pending
    DECLARE notification_cursor CURSOR FOR
        SELECT nl.booking_id, nl.type
        FROM notification_logs nl
        WHERE nl.status = 'pending' 
        AND nl.phone_number = 'trigger_initiated'
        AND nl.created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
        LIMIT 10;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN notification_cursor;
    
    notification_loop: LOOP
        FETCH notification_cursor INTO booking_id, notification_type;
        
        IF done THEN
            LEAVE notification_loop;
        END IF;
        
        -- Update status menjadi processing
        UPDATE notification_logs 
        SET status = 'processing', updated_at = NOW()
        WHERE booking_id = booking_id 
        AND type = notification_type 
        AND phone_number = 'trigger_initiated'
        AND status = 'pending';
        
        -- Buat command PHP
        SET php_command = CONCAT(
            'php /Applications/XAMPP/xamppfiles/htdocs/Gravenue/public/send_notification.php ',
            booking_id, ' ',
            CASE 
                WHEN notification_type = 'booking_approved' THEN 'approved'
                WHEN notification_type = 'booking_rejected' THEN 'rejected'
                ELSE 'unknown'
            END,
            ' > /dev/null 2>&1 &'
        );
        
        -- Untuk development, Anda bisa menggunakan file log sebagai alternatif
        -- INSERT INTO notification_logs (booking_id, phone_number, message, type, status, created_at, updated_at)
        -- VALUES (booking_id, 'system', php_command, 'system_command', 'logged', NOW(), NOW());
        
    END LOOP;
    
    CLOSE notification_cursor;
END$$

DELIMITER ;

-- =====================================================
-- QUERY UNTUK MONITORING DAN TESTING
-- =====================================================

-- Lihat semua notifikasi
-- SELECT * FROM notification_logs ORDER BY created_at DESC;

-- Lihat notifikasi pending
-- SELECT * FROM notification_logs WHERE status = 'pending';

-- Test trigger dengan update status booking
-- UPDATE bookings SET status = 'approved' WHERE id = 1;

-- Hapus trigger jika perlu
-- DROP TRIGGER IF EXISTS booking_status_notification_trigger;
-- DROP PROCEDURE IF EXISTS send_whatsapp_notification;
-- DROP EVENT IF EXISTS process_pending_notifications;

-- =====================================================
-- CATATAN PENTING:
-- 1. Pastikan MySQL user memiliki permission untuk system commands
-- 2. Sesuaikan path script PHP dengan lokasi project Anda
-- 3. Untuk production, pertimbangkan menggunakan queue system Laravel
-- 4. Test terlebih dahulu di development environment
-- 5. Daftarkan token Fonnte di file .env: FONNTE_TOKEN=your_token_here
-- =====================================================
