<?php
session_start();
require_once '../includes/pdo.php';
$page_css = 'reservations.css';
include '../includes/sidebar.php';
?>
<div class="main-content">
<?php

if  (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'supervisor'])) {
    header("Location: ../../pages/home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $reservation_id = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
    $new_status = filter_input(INPUT_POST, 'new_status', FILTER_SANITIZE_STRING);

    if ($reservation_id && in_array($new_status, ['pending', 'confirmed', 'cancelled'])) {
        $stmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE reservation_id = ?");
        $stmt->execute([$new_status, $reservation_id]);
        
        $_SESSION['success'] = "Reservation status updated successfully!";
        header("Location: reservations.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid status update request";
    }
}

$stmt = $pdo->prepare("
    SELECT r.*, 
           u.first_name as user_first_name, 
           u.last_name as user_last_name,
           u.email as user_email,
           t.title as trip_title,
           t.start_date as trip_start_date,
           t.end_date as trip_end_date
    FROM reservations r
    JOIN users u ON r.user_id = u.user_id
    JOIN trips t ON r.trip_id = t.trip_id
    ORDER BY r.reservation_date DESC
");
$stmt->execute();
$reservations = $stmt->fetchAll();

$page_title = "Manage Reservations";

?>

<div class="admin-container">
    <h1>Manage Reservations</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <div class="reservations-table-container">
        <table class="reservations-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Trip</th>
                    <th>Dates</th>
                    <th>Reservation Date</th>
                    <th>Status</th>
                    <th>Details</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $reservation): ?>
                    <tr>
                        <td><?= $reservation['reservation_id'] ?></td>
                        <td>
                            <?= htmlspecialchars($reservation['user_first_name'] . ' ' . $reservation['user_last_name']) ?>
                            <br><small><?= htmlspecialchars($reservation['user_email']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($reservation['trip_title']) ?></td>
                        <td>
                            <?= date('M j, Y', strtotime($reservation['trip_start_date'])) ?> - 
                            <?= date('M j, Y', strtotime($reservation['trip_end_date'])) ?>
                        </td>
                        <td><?= date('M j, Y H:i', strtotime($reservation['reservation_date'])) ?></td>
                        <td>
                            <span class="status-badge status-<?= $reservation['status'] ?>">
                                <?= ucfirst($reservation['status']) ?>
                            </span>
                        </td>
                        <td>
                            <button class="view-details-btn" data-id="<?= $reservation['reservation_id'] ?>">
                                View Details
                            </button>
                        </td>
                        <td>
                            <form method="post" class="status-form">
                                <input type="hidden" name="reservation_id" value="<?= $reservation['reservation_id'] ?>">
                                <select name="new_status" class="status-select">
                                    <option value="pending" <?= $reservation['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="confirmed" <?= $reservation['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                    <option value="cancelled" <?= $reservation['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                                <button type="submit" name="update_status" class="update-status-btn">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="detailsModal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2>Reservation Details</h2>
        <div id="reservationDetails"></div>
    </div>
</div>

<script src="../js/reservations.js">

</script>
</div>
</body>
</html>
