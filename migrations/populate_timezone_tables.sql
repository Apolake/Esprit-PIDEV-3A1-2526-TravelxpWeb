-- ============================================================================
-- Populate MySQL Timezone Tables for Africa/Tunis
-- ============================================================================
-- Required for named timezone support on Windows/XAMPP (MariaDB)
-- Africa/Tunis = CET (UTC+1), no DST since 2009
-- ============================================================================

-- Ensure tables exist and are clean for our entry
DELETE FROM mysql.time_zone_transition WHERE Time_zone_id IN (
    SELECT Time_zone_id FROM mysql.time_zone_name WHERE Name = 'Africa/Tunis'
);
DELETE FROM mysql.time_zone_transition_type WHERE Time_zone_id IN (
    SELECT Time_zone_id FROM mysql.time_zone_name WHERE Name = 'Africa/Tunis'
);
DELETE FROM mysql.time_zone_name WHERE Name = 'Africa/Tunis';
DELETE FROM mysql.time_zone_name WHERE Name = 'CET';

-- Insert timezone record
INSERT INTO mysql.time_zone (Use_leap_seconds) VALUES ('N');
SET @tz_id = LAST_INSERT_ID();

-- Register named timezone aliases
INSERT INTO mysql.time_zone_name (Name, Time_zone_id) VALUES ('Africa/Tunis', @tz_id);
INSERT INTO mysql.time_zone_name (Name, Time_zone_id) VALUES ('CET', @tz_id);

-- Define the timezone type: UTC+1, no DST
INSERT INTO mysql.time_zone_transition_type
    (Time_zone_id, Transition_type_id, `Offset`, Is_DST, Abbreviation)
VALUES
    (@tz_id, 0, 3600, 0, 'CET');

-- Add a single transition anchoring the timezone from epoch
INSERT INTO mysql.time_zone_transition
    (Time_zone_id, Transition_time, Transition_type_id)
VALUES
    (@tz_id, -2147483648, 0);

-- Also add UTC for reference
INSERT INTO mysql.time_zone (Use_leap_seconds) VALUES ('N');
SET @utc_id = LAST_INSERT_ID();
INSERT INTO mysql.time_zone_name (Name, Time_zone_id) VALUES ('UTC', @utc_id);
INSERT INTO mysql.time_zone_transition_type
    (Time_zone_id, Transition_type_id, `Offset`, Is_DST, Abbreviation)
VALUES
    (@utc_id, 0, 0, 0, 'UTC');
INSERT INTO mysql.time_zone_transition
    (Time_zone_id, Transition_time, Transition_type_id)
VALUES
    (@utc_id, -2147483648, 0);

-- Flush tables to apply
FLUSH TABLES;

-- Verify
SELECT Name, Time_zone_id FROM mysql.time_zone_name ORDER BY Name;
