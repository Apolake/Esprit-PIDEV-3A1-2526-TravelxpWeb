-- ============================================================================
-- Database Configuration Optimization for TravelXP
-- ============================================================================
-- This script fixes the following Doctrine Doctor issues:
--   🔴 Timezone mismatch between MySQL and PHP
--   🟠 InnoDB buffer pool size too small (16MB → 256MB)
--   🟠 Missing SQL Strict Mode Settings
--   🟠 MySQL using SYSTEM timezone (ambiguous)
--   🟠 MySQL timezone tables not loaded
--
-- INSTRUCTIONS:
--   1. Run the SESSION/GLOBAL commands below immediately.
--   2. Update your XAMPP my.ini file (C:\xampp\mysql\bin\my.ini) with the
--      [mysqld] settings shown at the bottom, then restart MySQL.
--   3. Load timezone tables (Windows-specific, see note below).
-- ============================================================================

-- ─── Step 1: Immediate session fix (runs on current connection) ─────────────
SET time_zone = '+01:00';
SET SESSION sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';

-- ─── Step 2: Global fix (persists until MySQL restart, requires SUPER) ──────
SET GLOBAL time_zone = '+01:00';
SET GLOBAL sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
SET GLOBAL innodb_buffer_pool_size = 268435456;  -- 256MB (was 16MB)

-- ─── Step 3: Verify settings ────────────────────────────────────────────────
SELECT
    @@global.time_zone        AS global_tz,
    @@session.time_zone       AS session_tz,
    @@global.sql_mode         AS sql_mode,
    ROUND(@@global.innodb_buffer_pool_size / 1048576) AS buffer_pool_mb;

-- ============================================================================
-- PERSISTENT CONFIGURATION (add to C:\xampp\mysql\bin\my.ini under [mysqld])
-- ============================================================================
--
-- [mysqld]
-- # Timezone: match PHP's Africa/Tunis (UTC+1)
-- default-time-zone = '+01:00'
--
-- # Strict SQL mode to prevent silent data corruption
-- sql_mode = STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION
--
-- # InnoDB buffer pool: 256MB (dev), increase to 512MB-1GB for production
-- innodb_buffer_pool_size = 256M
--
-- ============================================================================
-- TIMEZONE TABLES (Windows)
-- ============================================================================
-- On Windows/XAMPP, timezone tables must be loaded manually:
--
--   1. Download the timezone SQL from:
--      https://dev.mysql.com/downloads/timezones.html
--      (Choose "timezone_posix.sql" for POSIX-compatible zones)
--
--   2. Import into MySQL:
--      C:\xampp\mysql\bin\mysql -u root mysql < timezone_posix.sql
--
--   3. Verify:
--      SELECT COUNT(*) FROM mysql.time_zone_name;
--      -- Should return 500+ rows
--
-- After loading timezone tables, you can use named timezones:
--   SET time_zone = 'Africa/Tunis';
--   SELECT CONVERT_TZ(NOW(), 'UTC', 'Africa/Tunis');
-- ============================================================================
