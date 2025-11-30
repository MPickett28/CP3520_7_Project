<?php
require __DIR__ . '/includes/db.php';

$message = '';

// Handle approve / reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bid = (int)$_POST['bid'];
    $decision = $_POST['decision'] === 'approved' ? 'approved' : 'rejected';

    $upd = $pdo->prepare("UPDATE Bookings SET Status = :status WHERE Bid = :bid");
    $upd->execute([':status' => $decision, ':bid' => $bid]);
    $message = "Booking #$bid marked as $decision.";
}

// Load bookings (pending first for convenience)
$sql = "SELECT b.Bid, b.Date_Created, b.Start_Time, b.End_Time, b.Status,
               i.name AS instructor, c.Course_Name AS course, r.Location AS room
        FROM Bookings b
        JOIN Instructors i ON b.Iid = i.Iid
        JOIN Courses c     ON b.Cid = c.Cid
        JOIN Rooms r       ON b.Rid = r.Rid
        ORDER BY FIELD(b.Status,'pending','approved','rejected'), b.Date_Created";
$bookings = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Approve/Reject Bookings</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>Classroom Booking System</h1>
</header>
<main>
    <nav>
        <a href="index.php">Home</a>
        <a href="admin_bookings.php">Approve / Reject bookings</a>
        <a href="admin_rooms.php">Manage rooms</a>
    </nav>

    <h2>Booking Requests</h2>

    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <table>
        <tr>
            <th>Bid</th>
            <th>Date</th>
            <th>Time</th>
            <th>Instructor</th>
            <th>Course</th>
            <th>Room</th>
            <th>Status</th>
            <th>Decision</th>
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
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="bid" value="<?php echo $b['Bid']; ?>">
                        <input type="hidden" name="decision" value="approved">
                        <input type="submit" value="Approve">
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="bid" value="<?php echo $b['Bid']; ?>">
                        <input type="hidden" name="decision" value="rejected">
                        <input type="submit" value="Reject">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>
</body>
</html>
