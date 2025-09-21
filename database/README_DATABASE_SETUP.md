# 📊 GRAVENUE DATABASE SETUP GUIDE

Platform Penyewaan Fasilitas SMKN 4 Malang

## 🚀 Quick Setup

### Prerequisites
- MySQL 5.7+ or MariaDB 10.3+
- PHP 8.1+
- phpMyAdmin (optional, for GUI)

### 1. Database Creation & Setup

#### Option A: Complete Setup (Recommended)
```bash
# Login to MySQL
mysql -u root -p

# Create database and import complete setup
source /path/to/Gravenue/database/setup_gravenue_db.sql

# Add additional sample data (optional)
source /path/to/Gravenue/database/additional_sample_data.sql
```

#### Option B: Manual Step-by-Step
```sql
-- 1. Create database
CREATE DATABASE gravenue_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gravenue_db;

-- 2. Run setup script
source setup_gravenue_db.sql

-- 3. Add sample data (optional)
source additional_sample_data.sql
```

#### Option C: Using phpMyAdmin
1. Open phpMyAdmin
2. Create new database: `gravenue_db`
3. Import `setup_gravenue_db.sql`
4. Import `additional_sample_data.sql` (optional)

### 2. Laravel Configuration

Update your `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gravenue_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Laravel Migration (Alternative)
```bash
# If you prefer Laravel migrations
php artisan migrate
php artisan db:seed
```

## 📋 Database Structure

### Core Tables

#### 1. **users** - User accounts
- `id`, `name`, `email`, `phone`, `password`
- `created_at`, `updated_at`

#### 2. **admins** - Admin accounts  
- `id`, `name`, `email`, `password`, `role`
- `created_at`, `updated_at`

#### 3. **facilities** - Available facilities
- `id`, `name`, `description`, `category`, `capacity`
- `price_per_day`, `location`, `features`, `image`, `status`
- `created_at`, `updated_at`

#### 4. **bookings** - Booking records
- `id`, `facility_id`, `user_id`, `user_name`, `user_email`
- `organization`, `event_name`, `booking_date`
- `start_time`, `end_time`, `participants`
- `total_price`, `status`, `additional_notes`
- `created_at`, `updated_at`

#### 5. **payments** - Payment records
- `id`, `booking_id`, `payment_amount`, `payment_method`
- `payment_status`, `payment_date`, `transaction_id`
- `notes`, `created_at`, `updated_at`

#### 6. **schedules** - Schedule management
- `id`, `facility_id`, `date`, `time_slot`
- `status`, `booking_id`, `notes`
- `created_at`, `updated_at`

## 👤 Default Login Credentials

### Admin Accounts
```
Email: admin@gravenue.com
Password: password
Role: super_admin

Email: admin@smkn4malang.sch.id  
Password: password
Role: admin
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

**⚠️ IMPORTANT: Change default passwords in production!**

## 📊 Sample Data Overview

### Facilities (15 facilities)
- **Event Space**: Aula Utama, Auditorium, Hall Serbaguna, Ruang Rapat
- **Classroom**: Ruang Kelas A1, A2, B1, C1, C2, D1 
- **Laboratory**: Lab Komputer, Lab Bahasa, Lab Kimia, Lab Fisika, Lab Biologi, Lab Elektronika, Lab Otomotif
- **Sports**: Lapangan Basket, Futsal, Voli, Badminton, Kolam Renang
- **Entertainment**: Studio Musik, Studio Video, Mini Theater, Ruang Gaming, Ruang Karaoke
- **Cafe**: Kafeteria, Kantin Besar, Cafe Mini

### Sample Bookings
- Historical bookings (March-April 2024)
- Future bookings (May 2024)
- Various statuses: pending, approved, completed

### Sample Users
- 11 user accounts with realistic data
- Mix of organizations and individual users
- Booking history for testing

## 🔧 Database Features

### Views
- `booking_summary` - Complete booking information
- `facility_utilization` - Usage statistics
- `monthly_revenue` - Revenue reports
- `revenue_by_category` - Category-wise revenue
- `daily_bookings` - Daily booking summary

### Stored Procedures
- `GetFacilityAvailability()` - Check availability
- `GetBookingStatistics()` - Booking stats
- `GenerateSchedules()` - Generate schedule slots

### Triggers
- Auto-create payment when booking created
- Update booking status when payment completed

### Indexes
- Optimized for common queries
- Foreign key relationships
- Performance indexes on search fields

## 💾 Backup & Restore

### Manual Backup
```bash
# Full backup
mysqldump -u root -p gravenue_db > gravenue_backup_$(date +%Y%m%d).sql

# Structure only
mysqldump -u root -p --no-data gravenue_db > gravenue_structure.sql

# Data only  
mysqldump -u root -p --no-create-info gravenue_db > gravenue_data.sql
```

### Restore
```bash
# Restore from backup
mysql -u root -p gravenue_db < gravenue_backup.sql
```

### Automated Backup Script
```bash
# Daily backup at 2 AM
0 2 * * * /path/to/gravenue_backup.sh
```

### Using Built-in Procedures
```sql
-- Create backup tables with timestamp
CALL CreateIncrementalBackup('2024-01-01 00:00:00');

-- Clean old backups (>30 days)
CALL CleanOldBackups();

-- Verify data integrity
CALL VerifyDataIntegrity();

-- Database maintenance
CALL DatabaseMaintenance();

-- Emergency restore
CALL EmergencyRestore('backup_suffix');
```

## 📈 Useful Queries

### Check Facility Availability
```sql
CALL GetFacilityAvailability(1, '2024-05-15');
```

### Monthly Revenue Report
```sql
SELECT * FROM monthly_revenue WHERE year = 2024;
```

### Popular Facilities
```sql
SELECT * FROM facility_utilization ORDER BY total_bookings DESC;
```

### User Activity
```sql
SELECT 
    u.name, 
    COUNT(b.id) as bookings,
    SUM(b.total_price) as total_spent
FROM users u 
LEFT JOIN bookings b ON u.id = b.user_id 
GROUP BY u.id 
ORDER BY bookings DESC;
```

### Daily Schedule
```sql
SELECT 
    f.name, 
    s.time_slot, 
    s.status,
    COALESCE(b.event_name, 'Available') as event
FROM facilities f
LEFT JOIN schedules s ON f.id = s.facility_id  
LEFT JOIN bookings b ON s.booking_id = b.id
WHERE s.date = CURDATE()
ORDER BY f.category, s.time_slot;
```

## 🔍 Testing & Verification

### Data Integrity Check
```sql
CALL VerifyDataIntegrity();
```

### Connection Test
```sql
SELECT 
    'Connection OK' as status,
    VERSION() as mysql_version,
    DATABASE() as current_db;
```

### Sample Data Verification
```sql
SELECT 
    'users' as table_name, COUNT(*) as records FROM users
UNION ALL
SELECT 'facilities', COUNT(*) FROM facilities  
UNION ALL
SELECT 'bookings', COUNT(*) FROM bookings
UNION ALL
SELECT 'payments', COUNT(*) FROM payments;
```

## 🚨 Troubleshooting

### Common Issues

#### 1. **Connection Error**
```bash
# Check MySQL service
sudo systemctl status mysql
sudo systemctl start mysql
```

#### 2. **Permission Denied**
```sql
-- Grant privileges
GRANT ALL PRIVILEGES ON gravenue_db.* TO 'username'@'localhost';
FLUSH PRIVILEGES;
```

#### 3. **Import Error**
```bash
# Check file path and permissions
chmod 644 setup_gravenue_db.sql

# Import with error logging
mysql -u root -p gravenue_db < setup_gravenue_db.sql 2> import_errors.log
```

#### 4. **Laravel Connection Error**
```bash
# Clear Laravel cache
php artisan config:clear
php artisan cache:clear

# Test connection
php artisan tinker
DB::connection()->getPdo();
```

### Performance Issues
```sql
-- Show slow queries
SHOW VARIABLES LIKE 'slow_query_log';

-- Analyze table performance
ANALYZE TABLE bookings;
OPTIMIZE TABLE bookings;

-- Check indexes
SHOW INDEX FROM bookings;
```

## 📝 Development Notes

### Adding New Features
1. Update database schema
2. Create migration files
3. Update model relationships
4. Test data integrity
5. Update documentation

### Best Practices
- Use transactions for data consistency
- Regular backups before schema changes
- Index frequently queried columns
- Validate data before insertion
- Monitor query performance

### Security Considerations
- Use prepared statements
- Sanitize user input
- Regular password updates
- Monitor for SQL injection
- Backup encryption

## 📞 Support

For database-related issues:
1. Check error logs: `/var/log/mysql/error.log`
2. Verify configuration: `.env` file
3. Test connection: `php artisan tinker`
4. Check privileges: MySQL user permissions
5. Review documentation: Laravel Database docs

## 📄 Files Included

- `setup_gravenue_db.sql` - Complete database setup
- `additional_sample_data.sql` - Extended sample data  
- `backup_restore.sql` - Backup/restore procedures
- `README_DATABASE_SETUP.md` - This documentation

---

**🎉 Database setup complete! Ready to run Gravenue application.**

Last updated: March 2024
Version: 1.0.0