<?php
session_start();
require_once '../includes/pdo.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE reservation_id = ? AND user_id = ? AND status = 'pending'");
    $stmt->execute([$delete_id, $_SESSION['user_id']]);
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['message'] = "Reservation deleted successfully";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Cannot delete reservation - it may already be confirmed or doesn't exist";
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: reservations.php");
    exit();
}

$stmt = $pdo->prepare("SELECT r.*, t.title as trip_title, t.start_date, t.end_date, t.price, t.image_url 
                      FROM reservations r
                      JOIN trips t ON r.trip_id = t.trip_id
                      WHERE r.user_id = ?
                      ORDER BY r.reservation_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$reservations = $stmt->fetchAll();

$message = $_SESSION['message'] ?? null;
$message_type = $_SESSION['message_type'] ?? null;
unset($_SESSION['message'], $_SESSION['message_type']);
?>

<?php 
$page_css = 'reservations.css';
include '../includes/header.php';
?>
    
<div class="container">
    <h1>My Reservations</h1>
    
    <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    
    <?php if (empty($reservations)): ?>
        <div class="empty-state">
            <p>You don't have any reservations yet.</p>
            <a href="trips.php" class="btn-browse">Browse Available Trips</a>
        </div>
    <?php else: ?>
        <?php foreach ($reservations as $reservation): ?>
            <div class="reservation-card">
                <img src="../uploads<?= htmlspecialchars($reservation['image_url']) ?>" alt="<?= htmlspecialchars($reservation['trip_title']) ?>" class="trip-image">
                
                <div class="reservation-content">
                    <div class="reservation-header">
                        <h3 class="trip-title"><?= htmlspecialchars($reservation['trip_title']) ?></h3>
                        <span class="reservation-status status-<?= $reservation['status'] ?>">
                            <?= ucfirst($reservation['status']) ?>
                        </span>
                    </div>
                    
                    <div class="reservation-details">
                        <div class="detail-group">
                            <label>Trip Date</label>
                            <p><?= date('M j, Y', strtotime($reservation['start_date'])) ?> - <?= date('M j, Y', strtotime($reservation['end_date'])) ?></p>
                        </div>
                        
                        <div class="detail-group">
                            <label>Price</label>
                            <p>$<?= number_format($reservation['price'], 2) ?></p>
                        </div>
                        
                        <div class="detail-group">
                            <label>Reservation Date</label>
                            <p><?= date('M j, Y H:i', strtotime($reservation['reservation_date'])) ?></p>
                        </div>
                        
                        <div class="detail-group">
                            <label>Traveler</label>
                            <p><?= htmlspecialchars($reservation['first_name'] . ' ' . $reservation['last_name']) ?></p>
                        </div>
                    </div>
                    
                    <?php if ($reservation['status'] === 'pending'): ?>
                        <div class="pending-notice">
                            <p>We will contact you soon to confirm your reservation.</p>
                        </div>
                        <div class="reservation-actions">
                            <a href="edit_reservation.php?id=<?= $reservation['reservation_id'] ?>" class="btn btn-edit">Edit</a>
                            <a href="reservations.php?delete_id=<?= $reservation['reservation_id'] ?>" 
                               class="btn btn-delete" 
                               onclick="return confirm('Are you sure you want to delete this reservation?')">Delete</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>