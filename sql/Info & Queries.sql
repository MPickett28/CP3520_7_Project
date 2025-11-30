USE roombookingsql;

-- ========= Instructors =========
INSERT INTO instructors (Iid, name, department, contact_info) VALUES
(1, 'Dr. Sarah White',  'Computer Science', '709-555-1001'),
(2, 'Dr. John Black',   'Mathematics',      '709-555-1002'),
(3, 'Dr. Emily Green',  'Engineering',      '709-555-1003'),
(4, 'Dr. Robert Brown', 'Business',         '709-555-1004');

-- ========= Admin =========
INSERT INTO admin (Aid, name, contact_info) VALUES
(1, 'Alice Admin',   '709-555-2001'),
(2, 'Bob Supervisor','709-555-2002');

-- ========= Rooms =========

INSERT INTO rooms (Rid, capacity, availability, location, tech_feature) VALUES
(1, 40, 1, 'Main Building 201',  'Projector, Whiteboard'),
(2, 20, 1, 'Science Block 102',  'Smartboard'),
(3, 35, 0, 'Business Center 301','Projector, Conference Mic'),
(4, 50, 1, 'Engineering Wing 210','Lab Equipment, HDMI Display');

-- ========= Courses =========
INSERT INTO courses (Cid, course_name, credit_hours, required_room_features) VALUES
(1, 'Database Systems', 3, 'Projector'),
(2, 'Calculus II',      4, 'Whiteboard'),
(3, 'Circuits Lab',     3, 'Lab Equipment'),
(4, 'Business Analytics',3,'Projector, Conference Mic');

-- ========= Teaches =========
INSERT INTO teaches (Iid, Cid) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4);

-- ========= Bookings =========
INSERT INTO bookings (Bid, date_created, start_time, end_time, status, Iid, Cid, Rid) VALUES
(1, '2025-01-12 08:00:00', '09:00:00', '10:30:00', 'approved', 1, 1, 1),
(2, '2025-02-15 10:00:00', '11:00:00', '12:30:00', 'pending',  2, 2, 2),
(3, '2025-03-05 13:00:00', '13:00:00', '14:30:00', 'approved',  3, 3, 4),
(4, '2025-04-09 09:00:00', '09:00:00', '10:30:00', 'rejected',  4, 4, 3);

-- ========= Room_Match =========
INSERT INTO room_match (requirements, Cid, Rid) VALUES
('Projector',       1, 1),
('Whiteboard',      2, 2),
('Lab Equipment',   3, 4),
('Conference Mic',  4, 3);

-- ========= Request =========
INSERT INTO request (Iid, Bid) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4);

-- ========= Approve_OR_Reject =========
INSERT INTO approve_or_reject (Aid, Bid, decision, decided_at) VALUES
(1, 1, 'approved', '2025-01-13 10:00:00'),
(2, 2, 'pending',  '2025-02-15 12:00:00'),
(1, 3, 'approved', '2025-03-06 14:00:00'),
(2, 4, 'rejected', '2025-04-09 11:00:00');

-- ======== Quick sanity checks ========
SELECT 'instructors' tbl, COUNT(*) FROM instructors UNION ALL
SELECT 'admin', COUNT(*) FROM admin UNION ALL
SELECT 'rooms', COUNT(*) FROM rooms UNION ALL
SELECT 'courses', COUNT(*) FROM courses UNION ALL
SELECT 'teaches', COUNT(*) FROM teaches UNION ALL
SELECT 'bookings', COUNT(*) FROM bookings UNION ALL
SELECT 'room_match', COUNT(*) FROM room_match UNION ALL
SELECT 'request', COUNT(*) FROM request UNION ALL
SELECT 'approve_or_reject', COUNT(*) FROM approve_or_reject;

-- Query #1 - Show all bookings older than September 1, 2025
SELECT * 
FROM bookings 
WHERE date_created < '2025-09-01 00:00:00';

-- Query #2 - Assign booking 1 and 2 to Instructor 1 (replace IDs if needed)
UPDATE bookings 
SET iid = 1 
WHERE bid IN (1, 2);

-- Query #3 - List and count bookings per instructor (similar to ticket/tech joins)
SELECT b.bid, i.iid, i.name 
FROM bookings b 
JOIN instructors i ON i.iid = b.iid;

SELECT COUNT(b.bid), i.iid, i.name 
FROM bookings b 
JOIN instructors i ON i.iid = b.iid 
GROUP BY i.iid;

SELECT COUNT(b.bid), i.iid, i.name 
FROM bookings b 
LEFT JOIN instructors i ON i.iid = b.iid 
GROUP BY i.iid;

SELECT COUNT(b.bid), i.name 
FROM bookings b 
JOIN instructors i ON i.iid = b.iid 
GROUP BY i.name;

SELECT COUNT(b.bid), i.name 
FROM instructors i 
JOIN bookings b ON i.iid = b.iid 
GROUP BY i.name;

SELECT COUNT(b.bid), i.name 
FROM instructors i 
JOIN bookings b ON b.iid = i.iid 
GROUP BY i.name;

-- Query #4 - Show booking counts per instructor using different join types
SELECT i.name, COUNT(b.bid) AS booking_count 
FROM instructors i 
JOIN bookings b ON b.iid = i.iid 
GROUP BY i.name;

SELECT i.name, COUNT(b.bid) AS booking_count 
FROM instructors i 
LEFT JOIN bookings b ON b.iid = i.iid 
GROUP BY i.name;

SELECT i.name, COUNT(b.bid) AS booking_count 
FROM instructors i 
RIGHT JOIN bookings b ON b.iid = i.iid 
GROUP BY i.name;

SELECT i.name, COUNT(b.bid) AS booking_count 
FROM instructors i 
RIGHT JOIN bookings b ON i.iid = b.iid 
GROUP BY i.name;

SELECT i.name, COUNT(b.bid) AS booking_count 
FROM instructors i 
LEFT JOIN bookings b ON i.iid = b.iid 
GROUP BY i.name;

SELECT i.name, COUNT(b.bid) AS booking_count 
FROM instructors i 
LEFT JOIN bookings b ON i.iid = b.iid 
GROUP BY i.name;

-- Query #5 - Update a booking’s status (example)
UPDATE bookings 
SET status = 'approved' 
WHERE bid = 1;

-- Query #6 - Status lookups (similar to ticket examples)
SELECT status 
FROM bookings 
WHERE bid = 2;

SELECT status AS status 
FROM bookings 
WHERE bid = 2;

SELECT status AS status 
FROM bookings 
WHERE status = 'pending';

SELECT bid, status AS status 
FROM bookings 
WHERE status = 'pending';

SELECT * 
FROM bookings;

-- Query #7 - Show all bookings not assigned to any instructor
SELECT * 
FROM bookings 
WHERE iid IS NULL;

-- Query #8 - Show bookings assigned to Instructor 1, sorted by creation date
SELECT bid, date_created, start_time, end_time, iid 
FROM bookings 
WHERE iid = 1 
ORDER BY date_created ASC;

-- Query #9 - Count bookings by status (open vs. closed analogy)
SELECT status, COUNT(*) AS booking_count 
FROM bookings 
GROUP BY status;

-- Query #10 - Show all instructors who currently have no bookings
SELECT i.iid, i.name 
FROM instructors i 
LEFT JOIN bookings b ON i.iid = b.iid 
WHERE b.bid IS NULL;
