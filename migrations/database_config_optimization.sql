-- =============================================================================
-- TravelXP Database Configuration Optimization
-- Run this against your MySQL instance to fix Doctrine Doctor config warnings
-- =============================================================================

-- 1. Fix timezone mismatch (PHP uses Africa/Tunis, MySQL uses Africa/Lagos)
-- Option A: Set MySQL timezone to match PHP
SET GLOBAL time_zone = 'Africa/Tunis';
SET SESSION time_zone = 'Africa/Tunis';

-- Option B: If timezone tables aren't loaded, use UTC offset for Africa/Tunis (+01:00)
-- SET GLOBAL time_zone = '+01:00';
-- SET SESSION time_zone = '+01:00';

-- 2. Load timezone tables (run from OS command line, not MySQL console)
-- Windows (XAMPP): mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql -u root mysql
-- Linux: mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql -u root -p mysql

-- 3. Increase InnoDB buffer pool size (recommended: 128MB+ for dev, 512MB+ for prod)
SET GLOBAL innodb_buffer_pool_size = 134217728; -- 128MB

-- 4. Enable strict SQL modes to prevent silent data truncation
SET GLOBAL sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';

-- 5. Optimize InnoDB for development (faster writes, acceptable durability)
-- Keep value 1 in production for full ACID compliance
SET GLOBAL innodb_flush_log_at_trx_commit = 2;

-- =============================================================================
-- To make these changes persistent, add to my.cnf / my.ini:
-- =============================================================================
-- [mysqld]
-- default-time-zone = 'Africa/Tunis'
-- innodb_buffer_pool_size = 128M
-- sql_mode = STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION
-- innodb_flush_log_at_trx_commit = 2
