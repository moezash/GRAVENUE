# 🔧 SOLUTION: MariaDB Error #1558 Fix

**Error**: `Column count of mysql.proc is wrong. Expected 21, found 20. Created with MariaDB 100108, now running 100428. Please use mysql_upgrade to fix this error`

## 🚨 Problem Analysis

This error occurs when:
- MariaDB has been upgraded from an older version (10.1.8) to a newer version (10.4.28)
- System tables haven't been updated to match the new MariaDB version
- The `mysql.proc` table structure is outdated

## ✅ Solution 1: Run MySQL Upgrade (Recommended)

### Step 1: Stop MariaDB Service
```bash
sudo systemctl stop mariadb
# or
sudo systemctl stop mysql
```

### Step 2: Run MySQL Upgrade
```bash
# For newer MariaDB versions (10.4+)
sudo mariadb-upgrade -u root -p

# For older versions or if mariadb-upgrade doesn't exist
sudo mysql_upgrade -u root -p
```

### Step 3: Restart MariaDB
```bash
sudo systemctl start mariadb
sudo systemctl status mariadb
```

### Step 4: Test Connection
```bash
mysql -u root -p
```

## ✅ Solution 2: Alternative Database Setup (No Stored Procedures)

If the upgrade doesn't work, use the simplified database setup:

### Use the Fixed SQL File
Instead of `setup_gravenue_db.sql`, use:
```bash
mysql -u root -p < database/setup_gravenue_db_fixed.sql
```

This version:
- ✅ No problematic stored procedures
- ✅ Compatible with all MariaDB versions
- ✅ Uses simple queries instead of procedures
- ✅ All core functionality preserved

## ✅ Solution 3: Manual System Table Fix

⚠️ **DANGER**: Only use if other solutions fail and you have backups!

### Step 1: Backup Current Database
```bash
mysqldump -u root -p --all-databases > full_backup.sql
```

### Step 2: Fix System Tables
```sql
-- Login to MySQL
mysql -u root -p

-- Switch to mysql database
USE mysql;

-- Backup proc table
CREATE TABLE proc_backup AS SELECT * FROM proc;

-- Check current structure
DESCRIBE proc;

-- Force recreation of system tables
-- This is done by the mysql_upgrade command
```

### Step 3: Run Upgrade Again
```bash
sudo mysql_upgrade -u root -p --force
```

## ✅ Solution 4: Fresh Installation Approach

If all else fails, clean installation:

### Step 1: Export Only Data
```bash
# Export structure and data separately
mysqldump -u root -p --no-create-info gravenue_db > gravenue_data_only.sql
mysqldump -u root -p --no-data gravenue_db > gravenue_structure_only.sql
```

### Step 2: Drop and Recreate Database
```sql
DROP DATABASE IF EXISTS gravenue_db;
```

### Step 3: Use Fixed Setup Script
```bash
mysql -u root -p < database/setup_gravenue_db_fixed.sql
```

## 🔍 Verification Steps

After applying any solution:

### 1. Test Database Connection
```sql
SELECT VERSION() as version;
SHOW DATABASES;
USE gravenue_db;
SHOW TABLES;
```

### 2. Test Basic Queries
```sql
-- Test data retrieval
SELECT COUNT(*) FROM users;
SELECT COUNT(*) FROM facilities;
SELECT COUNT(*) FROM bookings;

-- Test joins
SELECT b.event_name, f.name as facility_name
FROM bookings b
JOIN facilities f ON b.facility_id = f.id
LIMIT 5;
```

### 3. Test Laravel Connection
```bash
cd /path/to/Gravenue
php artisan tinker

# In tinker:
DB::connection()->getPdo();
DB::table('users')->count();
```

## 🛠️ Alternative Queries Instead of Stored Procedures

Since stored procedures caused issues, use these direct queries:

### Check Facility Availability
```sql
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
            AND b.booking_date = '2024-05-15'
            AND b.status IN ('approved', 'pending')
        ) THEN 'Already booked'
        ELSE 'Available'
    END as availability_status
FROM facilities f
WHERE f.id = 1;
```

### Get Booking Statistics
```sql
SELECT
    COUNT(*) as total_bookings,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_bookings,
    COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_bookings,
    SUM(CASE WHEN status = 'approved' THEN total_price ELSE 0 END) as total_revenue
FROM bookings
WHERE booking_date >= '2024-01-01' AND booking_date <= '2024-12-31';
```

### Get Monthly Revenue
```sql
SELECT
    YEAR(booking_date) as year,
    MONTH(booking_date) as month,
    MONTHNAME(booking_date) as month_name,
    COUNT(*) as total_bookings,
    SUM(total_price) as total_revenue
FROM bookings
WHERE status = 'approved'
GROUP BY YEAR(booking_date), MONTH(booking_date)
ORDER BY year DESC, month DESC;
```

## 📋 Laravel Configuration Updates

Update your `.env` file for better compatibility:

### For MariaDB 10.4+
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gravenue_db
DB_USERNAME=root
DB_PASSWORD=your_password
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

### Laravel Database Config (Optional)
Add to `config/database.php`:
```php
'mysql' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'gravenue_db'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => false,  // Set to false for compatibility
    'engine' => 'InnoDB',
    'modes' => [
        'ONLY_FULL_GROUP_BY',
        'STRICT_TRANS_TABLES',
        'NO_ZERO_IN_DATE',
        'NO_ZERO_DATE',
        'ERROR_FOR_DIVISION_BY_ZERO',
        'NO_AUTO_CREATE_USER',
    ],
],
```

## 🔐 Default Login Credentials

After successful setup:

### Admin Accounts
```
Email: admin@gravenue.com
Password: password

Email: admin@smkn4malang.sch.id
Password: password
```

### User Accounts
```
Email: john@example.com
Password: password

Email: jane@example.com
Password: password

Email: ahmad@example.com
Password: password
```

## 📊 Database Structure Overview

The fixed database includes:
- ✅ 6 sample users
- ✅ 2 admin accounts
- ✅ 15 facilities (various categories)
- ✅ 8 sample bookings
- ✅ Payment records
- ✅ Views for reporting
- ✅ Proper indexes and relationships

## ⚠️ Prevention for Future

To avoid this issue in the future:

1. **Always run `mysql_upgrade`** after MariaDB updates
2. **Backup before upgrades**: `mysqldump -u root -p --all-databases > backup.sql`
3. **Test in development first** before upgrading production
4. **Monitor logs** for compatibility warnings
5. **Use Laravel migrations** instead of direct SQL when possible

## 🆘 If Problems Persist

### Check Logs
```bash
sudo tail -f /var/log/mysql/error.log
sudo tail -f /var/log/mariadb/mariadb.log
```

### Check MariaDB Configuration
```bash
mysql -u root -p
SHOW VARIABLES LIKE 'version%';
SHOW VARIABLES LIKE 'sql_mode';
```

### Complete Reinstall (Last Resort)
```bash
# Remove MariaDB completely
sudo apt-get remove --purge mariadb-server mariadb-client
sudo apt-get autoremove
sudo apt-get autoclean

# Reinstall
sudo apt-get install mariadb-server mariadb-client

# Secure installation
sudo mysql_secure_installation
```

## ✅ Success Indicators

You'll know it's fixed when:
- ✅ No error messages when connecting to MySQL/MariaDB
- ✅ All queries execute without errors
- ✅ Laravel can connect to database
- ✅ Views and tables are accessible
- ✅ Sample data is present and queryable

---

**🎉 After following these solutions, your Gravenue database should be working perfectly with MariaDB!**

Need help? Check the logs and verify each step was completed successfully.