<?php
require __DIR__ . '/includes/db.php';

$message = '';

include 'includes/db.php';

/** @var PDO $pdo */

// Update room
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rid = (int)$_POST['rid'];
    $capacity = (int)$_POST['capacity'];
    $availability = isset($_POST['availability']) ? 1 : 0;
    $features = $_POST['tech_feature'];

    $upd = $pdo->prepare("UPDATE Rooms
                          SET Capacity = :cap,
                              Availability = :avail,
                              Tech_Feature = :feat
                          WHERE Rid = :rid");
    $upd->execute([
        ':cap'   => $capacity,
        ':avail' => $availability,
        ':feat'  => $features,
        ':rid'   => $rid,
    ]);
    $message = "Room #$rid updated.";
}

// Load rooms
$rooms = $pdo->query("SELECT * FROM Rooms ORDER BY Rid")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Rooms</title>
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

    <h2>Manage Rooms</h2>

    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <table>
        <tr>
            <th>Room ID</th>
            <th>Capacity</th>
            <th>Location</th>
            <th>Features</th>
            <th>Available?</th>
            <th>Update</th>
        </tr>
        <?php foreach ($rooms as $room): ?>
            <tr>
                <form method="post">
                    <td><?php echo $room['Rid']; ?>
                        <input type="hidden" name="rid" value="<?php echo $room['Rid']; ?>">
                    </td>
                    <td>
                        <input type="number" name="capacity" value="<?php echo $room['Capacity']; ?>" min="0">
                    </td>
                    <td><?php echo htmlspecialchars($room['Location']); ?></td>
                    <td>
                        <input type="text" name="tech_feature"
                               value="<?php echo htmlspecialchars($room['Tech_Feature']); ?>">
                    </td>
                    <td style="text-align:center;">
                        <input type="checkbox" name="availability" <?php echo $room['Availability'] ? 'checked' : ''; ?>>
                    </td>
                    <td>
                        <input type="submit" value="Save">
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
    </table>
</main>
</body>
</html>
