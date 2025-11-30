USE roombookingsql;
SET SQL_SAFE_UPDATES = 0;

-- delete only those seed rows (keeps FK integrity)
DELETE FROM approve_or_reject WHERE (Aid,Bid) IN ((1,1),(2,2),(1,3),(2,4));
DELETE FROM request          WHERE (Iid,Bid) IN ((1,1),(2,2),(3,3),(4,4));
DELETE FROM teaches          WHERE (Iid,Cid) IN ((1,1),(2,2),(3,3),(4,4));
DELETE FROM room_match       WHERE (Cid,Rid) IN ((1,1),(2,2),(3,4),(4,3));
DELETE FROM bookings         WHERE Bid IN (1,2,3,4);
DELETE FROM courses          WHERE Cid IN (1,2,3,4);
DELETE FROM rooms            WHERE Rid IN (1,2,3,4);
DELETE FROM admin            WHERE Aid IN (1,2);
DELETE FROM instructors      WHERE Iid IN (1,2,3,4);

SET SQL_SAFE_UPDATES = 1;
USE roombookingsql;


