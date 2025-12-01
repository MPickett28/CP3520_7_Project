<?php
require __DIR__ . '/includes/db.php';

include 'includes/db.php';

/** @var PDO $pdo */

$capacity = isset($_GET['capacity']) ? (int)$_GET['capacity'] : 0;
$feature  = isset($_GET['feature']) ? trim($_GET['feature']) : '';
$onlyAvailable = isset($_GET['only_available']) ? true : false;

// Build query with optional filters
$sql = "SELECT * FROM Rooms WHERE 1=1";
$params = [];

if ($onlyAvailable) {
    $sql .= " AND Availability = 1";
}

if ($capacity > 0) {
    $sql .= " AND Capacity >= :cap";
    $params[':cap'] = $capacity;
}

if ($feature !== '') {
    $sql .= " AND Tech_Feature LIKE :feat";
    $params[':feat'] = '%' . $feature . '%';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rooms = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Instructor - View Rooms</title>
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

    <h2>Available Rooms</h2>

    <form method="get">
        <label for="capacity">Minimum capacity:</label>
        <input type="number" name="capacity" id="capacity" min="0" value="<?php echo htmlspecialchars($capacity); ?>">

        <label for="feature">Technology feature contains:</label>
        <input type="text" name="feature" id="feature" value="<?php echo htmlspecialchars($feature); ?>">

        <label>
            <input type="checkbox" name="only_available" <?php echo $onlyAvailable ? 'checked' : ''; ?>>
            Only show available rooms
        </label>

        <input type="submit" value="Search rooms">
    </form>

    <table>
        <tr>
            <th>Room ID</th>
            <th>Capacity</th>
            <th>Location</th>
            <th>Features</th>
            <th>Availability</th>
        </tr>
        <?php foreach ($rooms as $room): ?>
            <tr>
                <td><?php echo $room['Rid']; ?></td>
                <td><?php echo $room['Capacity']; ?></td>
                <td><?php echo htmlspecialchars($room['Location']); ?></td>
                <td><?php echo htmlspecialchars($room['Tech_Feature']); ?></td>
                <td><?php echo $room['Availability'] ? 'Available' : 'Not available'; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>
</body>
</html>
