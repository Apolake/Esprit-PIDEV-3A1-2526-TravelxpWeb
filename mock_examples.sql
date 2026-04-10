-- TravelXP mock data seed
-- Creates 3 mock rows per entity table:
-- users, properties, offers, services, bookings, quests, user_quest_progress, trips, activities
-- Plus relation examples for booking_services, trip_participants, trip_activity_participants.

START TRANSACTION;

-- Clean previous mock rows (safe re-run)
DELETE FROM trip_activity_participants WHERE activity_id IN (9901, 9902, 9903) OR user_id IN (9101, 9102, 9103);
DELETE FROM trip_participants WHERE trip_id IN (9801, 9802, 9803) OR user_id IN (9101, 9102, 9103);
DELETE FROM booking_services WHERE booking_id IN (9501, 9502, 9503) OR service_id IN (9401, 9402, 9403);
DELETE FROM user_quest_progress WHERE id IN (9701, 9702, 9703) OR user_id IN (9101, 9102, 9103) OR quest_id IN (9601, 9602, 9603);

DELETE FROM activities WHERE id IN (9901, 9902, 9903);
DELETE FROM trips WHERE id IN (9801, 9802, 9803);
DELETE FROM bookings WHERE id IN (9501, 9502, 9503);
DELETE FROM offers WHERE id IN (9301, 9302, 9303);
DELETE FROM services WHERE id IN (9401, 9402, 9403);
DELETE FROM properties WHERE id IN (9201, 9202, 9203);
DELETE FROM quests WHERE id IN (9601, 9602, 9603);
DELETE FROM users WHERE id IN (9101, 9102, 9103);

INSERT INTO users (
    id, username, email, roles, password, birthday, bio, profile_image, balance,
    xp, level, streak, face_registered, totp_enabled, totp_secret, created_at, updated_at
) VALUES
    (9101, 'demo_alice', 'alice.demo@travelxp.local', '["ROLE_USER"]', '$2y$12$yhP.7b0QJ1loznORILzbd..OIR3ac.P7RGbbYKU6WdwAycfBDY0m6', '1998-03-14', 'Adventure traveler and foodie.', NULL, 240.50, 4, 2, 0, 0, 0, NULL, NOW(), NOW()),
    (9102, 'demo_bob', 'bob.demo@travelxp.local', '["ROLE_USER"]', '$2y$12$yhP.7b0QJ1loznORILzbd..OIR3ac.P7RGbbYKU6WdwAycfBDY0m6', '1995-07-22', 'Urban explorer who loves city stays.', NULL, 510.00, 6, 5, 0, 0, 0, NULL, NOW(), NOW()),
    (9103, 'demo_admin', 'admin.demo@travelxp.local', '["ROLE_ADMIN"]', '$2y$12$yhP.7b0QJ1loznORILzbd..OIR3ac.P7RGbbYKU6WdwAycfBDY0m6', '1992-11-09', 'Operations admin for mock data.', NULL, 980.75, 9, 7, 0, 0, 0, NULL, NOW(), NOW());

INSERT INTO properties (
    id, title, description, property_type, city, country, address, price_per_night,
    bedrooms, max_guests, images, is_active, created_at, updated_at
) VALUES
    (9201, 'Skyline Loft', 'Modern loft near downtown attractions.', 'Apartment', 'Tunis', 'Tunisia', '12 Avenue Habib Bourguiba', 180.00, 2, 4, NULL, 1, NOW(), NOW()),
    (9202, 'Palm Beach Villa', 'Seaside villa with private garden.', 'Villa', 'Hammamet', 'Tunisia', '8 Rue des Palmiers', 320.00, 4, 8, NULL, 1, NOW(), NOW()),
    (9203, 'Old Medina Riad', 'Traditional riad experience in the historic district.', 'House', 'Marrakech', 'Morocco', '19 Derb Ben Amrane', 210.00, 3, 6, NULL, 1, NOW(), NOW());

INSERT INTO offers (
    id, property_id, title, description, discount_percentage, start_date, end_date,
    is_active, created_at, updated_at
) VALUES
    (9301, 9201, 'Spring Escape', 'Seasonal discount for spring city breaks.', 12.50, '2026-04-01', '2026-06-30', 1, NOW(), NOW()),
    (9302, 9202, 'Family Summer Deal', 'Reduced rate for family bookings.', 18.00, '2026-06-01', '2026-09-15', 1, NOW(), NOW()),
    (9303, 9203, 'Weekend Culture Pass', 'Special weekend offer for cultural trips.', 10.00, '2026-04-15', '2026-10-31', 1, NOW(), NOW());

INSERT INTO services (
    id, provider_name, service_type, description, price, is_available, eco_friendly, created_at, updated_at
) VALUES
    (9401, 'GreenRide Transfers', 'Transport', 'Airport shuttle with electric vehicles.', 35.00, 1, 1, NOW(), NOW()),
    (9402, 'Sahara Chef Team', 'Catering', 'In-house dinner and breakfast package.', 55.00, 1, 0, NOW(), NOW()),
    (9403, 'City Guide Pro', 'Guide', 'Half-day guided city discovery tour.', 45.00, 1, 1, NOW(), NOW());

INSERT INTO bookings (
    id, property_id, user_id, booking_date, duration, total_price, status, created_at, updated_at
) VALUES
    (9501, 9201, 9101, '2026-05-10', 3, 512.50, 'confirmed', NOW(), NOW()),
    (9502, 9202, 9102, '2026-07-03', 5, 1475.00, 'pending', NOW(), NOW()),
    (9503, 9203, 9101, '2026-08-18', 2, 420.00, 'confirmed', NOW(), NOW());

INSERT INTO booking_services (booking_id, service_id) VALUES
    (9501, 9401),
    (9502, 9402),
    (9503, 9403);

INSERT INTO quests (
    id, title, description, goal, reward_xp, is_active, created_at, updated_at
) VALUES
    (9601, 'Book your first stay', 'Create one confirmed booking.', 1, 120, 1, NOW(), NOW()),
    (9602, 'Complete 3 bookings', 'Finish three booking cycles.', 3, 260, 1, NOW(), NOW()),
    (9603, 'Join 2 activities', 'Participate in at least two activities.', 2, 180, 1, NOW(), NOW());

INSERT INTO user_quest_progress (
    id, user_id, quest_id, progress, status, updated_at
) VALUES
    (9701, 9101, 9601, 1, 'completed', NOW()),
    (9702, 9102, 9602, 1, 'active', NOW()),
    (9703, 9101, 9603, 1, 'active', NOW());

INSERT INTO trips (
    id, user_id, trip_name, origin, destination, description, start_date, end_date, status,
    budget_amount, currency, total_expenses, total_xp_earned, notes, cover_image_url, parent_id, created_at, updated_at
) VALUES
    (9801, 9101, 'Mediterranean Escape', 'Tunis', 'Sicily', 'Island hopping and coastal food tour.', '2026-06-10', '2026-06-16', 'PLANNED', 2400.00, 'EUR', 0.00, 0, 'Main summer trip.', NULL, NULL, NOW(), NOW()),
    (9802, 9102, 'Atlas Adventure', 'Casablanca', 'Marrakech', 'Mountain trails and desert camp.', '2026-09-02', '2026-09-08', 'PLANNED', 1800.00, 'USD', 0.00, 0, 'Bring hiking gear.', NULL, NULL, NOW(), NOW()),
    (9803, 9101, 'Mediterranean Escape - Extension', 'Sicily', 'Malta', 'Optional extension with diving spots.', '2026-06-16', '2026-06-20', 'PLANNED', 900.00, 'EUR', 0.00, 0, 'Linked extension leg.', NULL, 9801, NOW(), NOW());

INSERT INTO trip_participants (trip_id, user_id) VALUES
    (9801, 9101),
    (9802, 9102),
    (9803, 9101);

INSERT INTO activities (
    id, trip_id, title, type, description, activity_date, start_time, end_time, location_name,
    transport_type, cost_amount, currency, xp_earned, status, created_at, updated_at
) VALUES
    (9901, 9801, 'Street Food Walk', 'Food', 'Guided evening tasting in old town.', '2026-06-11', '18:00:00', '21:00:00', 'Palermo Center', 'Walking', 38.00, 'EUR', 45, 'PLANNED', NOW(), NOW()),
    (9902, 9802, 'Sunrise Hike', 'Adventure', 'Early hike to scenic mountain viewpoints.', '2026-09-04', '05:30:00', '09:30:00', 'Atlas Ridge', 'Bus', 52.00, 'USD', 60, 'PLANNED', NOW(), NOW()),
    (9903, 9803, 'Harbor Dive Session', 'Adventure', 'Intro dive with certified instructors.', '2026-06-18', '10:00:00', '12:00:00', 'Valletta Harbor', 'Boat', 85.00, 'EUR', 80, 'PLANNED', NOW(), NOW());

INSERT INTO trip_activity_participants (activity_id, user_id) VALUES
    (9901, 9101),
    (9902, 9102),
    (9903, 9101);

COMMIT;
