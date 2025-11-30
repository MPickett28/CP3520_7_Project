<?php
require __DIR__ . '/includes/db.php';

$message = '';
$error = '';

// Handle new booking request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request') {
    $iid  = (int)$_POST['iid'];
    $cid  = (int)$_POST['cid'];
    $rid  = (int)$_POST['rid'];
    $date = $_POST['date'];  // YYYY-MM-DD
    $start = $_POST['start_time'];
    $end   = $_POST['end_time'];

    // Prevent double booking: check overlapping booking for same room & date
    $checkSql = "SELECT COUNT(*) AS cnt
                 FROM Bookings
                 WHERE Rid = :rid
                   AND DATE(Date_Created) = :date
                   AND Status <> 'rejected'
                   AND Start_Time < :end
                   AND End_Time   > :start";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([
        ':rid'   => $rid,
        ':date'  => $date,
        ':start' => $start,
        ':end'   => $end,
    ]);
    $row = $checkStmt->fetch();

    if ($row['cnt'] > 0) {
        $error = 'This room is already booked for an overlapping time.';
    } else {
        $insert = $pdo->prepare(
            "INSERT INTO Bookings (Bid, Date_Created, Start_Time, End_Time, Status, Iid, Cid, Rid)
             VALUES (NULL, :dateCreated, :start, :end, 'pending', :iid, :cid, :rid)"
        );
        $insert->execute([
            ':dateCreated' => $date . ' 00:00:00',
            ':start' => $start,
            ':end'   => $end,
            ':iid'   => $iid,
            ':cid'   => $cid,
            ':rid'   => $rid,
        ]);
        $message = 'Booking request submitted.';
    }
}

// Handle cancel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel') {
    $bid = (int)$_POST['bid'];
    $upd = $pdo->prepare("UPDATE Bookings SET Status = 'rejected' WHERE Bid = :bid");
    $upd->execute([':bid' => $bid]);
    $message = 'Booking cancelled (marked as rejected).';
}

// Data for dropdowns
$instructors = $pdo->query("SELECT Iid, name FROM Instructors ORDER BY name")->fetchAll();
$courses     = $pdo->query("SELECT Cid, Course_Name FROM Courses ORDER BY Course_Name")->fetchAll();
$rooms       = $pdo->query("SELECT Rid, Location FROM Rooms ORDER BY Rid")->fetchAll();

// Show all bookings (for simplicity)
$bookingsSql = "SELECT b.Bid, b.Date_Created, b.Start_Time, b.End_Time, b.Status,
                       i.name AS instructor, c.Course_Name AS course, r.Location AS room
                FROM Bookings b
                JOIN Instructors i ON b.Iid = i.Iid
                JOIN Courses c     ON b.Cid = c.Cid
                JOIN Rooms r       ON b.Rid = r.Rid
                ORDER BY b.Date_Created, b.Start_Time";
$bookingStmt = $pdo->query($bookingsSql);
$bookings = $bookingStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Instructor - Bookings</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>Classroom Booking System</h1>
</header>
<main>
    <nav>
        <a href="index.php">Home</a>
        <a href="instructor_rooms.php">View rooms</a>
        <a href="instructor_bookings.php">My bookings / Request</a>
    </nav>

    <h2>Request a Booking</h2>

    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="action" value="request">

        <label for="iid">Instructor:</label>
        <select name="iid" id="iid" required>
            <?php foreach ($instructors as $inst): ?>
                <option value="<?php echo $inst['Iid']; ?>"><?php echo htmlspecialchars($inst['name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="cid">Course:</label>
        <select name="cid" id="cid" required>
            <?php foreach ($courses as $course): ?>
                <option value="<?php echo $course['Cid']; ?>"><?php echo htmlspecialchars($course['Course_Name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="rid">Room:</label>
        <select name="rid" id="rid" required>
            <?php foreach ($rooms as $room): ?>
                <option value="<?php echo $room['Rid']; ?>"><?php echo htmlspecialchars($room['Location']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="date">Date (for simplicity, stored in Date_Created):</label>
        <input type="date" name="date" id="date" required>

        <label for="start_time">Start time:</label>
        <input type="time" name="start_time" id="start_time" required>

        <label for="end_time">End time:</label>
        <input type="time" name="end_time" id="end_time" required>

        <input type="submit" value="Submit booking request">
    </form>

    <h2>All Bookings</h2>
    <table>
        <tr>
            <th>Bid</th>
            <th>Date</th>
            <th>Time</th>
            <th>Instructor</th>
            <th>Course</th>
            <th>Room</th>
            <th>Status</th>
            <th>Cancel</th>
        </tr>
        <?php foreach ($bookings as $b): ?>
            <tr>
                <td><?php echo $b['Bid']; ?></td>
                <td><?php echo substr($b['Date_Created'], 0, 10); ?></td>
                <td><?php echo $b['Start_Time'] . ' - ' . $b['End_Time']; ?></td>
                <td><?php echo htmlspecialchars($b['instructor']); ?></td>
                <td><?php echo htmlspecialchars($b['course']); ?></td>
                <td><?php echo htmlspecialchars($b['room']); ?></td>
                <td class="status-<?php echo strtolower($b['Status']); ?>">
                    <?php echo htmlspecialchars($b['Status']); ?>
                </td>
                <td>
                    <?php if ($b['Status'] === 'pending' || $b['Status'] === 'approved'): ?>
                        <form method="post" style="margin:0;">
                            <input type="hidden" name="action" value="cancel">
                            <input type="hidden" name="bid" value="<?php echo $b['Bid']; ?>">
                            <input type="submit" value="Cancel">
                        </form>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>
</body>
</html>
