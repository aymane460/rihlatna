<?php
session_start();
include '../includes/pdo.php';

if (isset($_GET['delete'])) {
    $trip_id = $_GET['delete'];
    
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE ta FROM trip_activities ta
                              JOIN trip_days td ON ta.day_id = td.day_id
                              WHERE td.trip_id = ?");
        $stmt->execute([$trip_id]);

        $stmt = $pdo->prepare("DELETE FROM trip_days WHERE trip_id = ?");
        $stmt->execute([$trip_id]);

        $stmt = $pdo->prepare("SELECT image_url FROM trips WHERE trip_id = ?");
        $stmt->execute([$trip_id]);
        $image_url = $stmt->fetchColumn();

        $stmt = $pdo->prepare("DELETE FROM trips WHERE trip_id = ?");
        $stmt->execute([$trip_id]);
        
        $pdo->commit();

        if ($image_url) {
            $file_path = "../uploads/" . $image_url;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        $_SESSION['success_message'] = "Trip deleted successfully!";
        header("Location: trips.php");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = "Error deleting trip: " . $e->getMessage();
        header("Location: trips.php");
        exit();
    }
}

$trips = [];
$stmt = $pdo->query("SELECT * FROM trips ORDER BY start_date DESC");
while ($trip = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $trips[] = $trip;
}

$page_css = 'trips.css';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <h2>Trip Management</h2>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success_message']) ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error_message']) ?></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    
    <div class="trip-list">
        <?php if (empty($trips)): ?>
            <div class="no-trips">
                <p>No trips found. <a href="add_trip.php">Add a new trip</a></p>
            </div>
        <?php else: ?>
            <?php foreach ($trips as $trip): ?>
                <?php
                $stmt_days = $pdo->prepare("SELECT * FROM trip_days WHERE trip_id = ? ORDER BY day_number");
                $stmt_days->execute([$trip['trip_id']]);
                $days = $stmt_days->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($days as &$day) {
                    $stmt_activities = $pdo->prepare("SELECT * FROM trip_activities 
                                                    WHERE day_id = ? ORDER BY activity_order");
                    $stmt_activities->execute([$day['day_id']]);
                    $day['activities'] = $stmt_activities->fetchAll(PDO::FETCH_ASSOC);
                }
                ?>
                
                <div class="trip-card">
                    <div class="trip-header" onclick="toggleDetails(this)">
                        <div class="trip-image">
                            <img src="../../uploads/<?= htmlspecialchars($trip['image_url']) ?>" alt="<?= htmlspecialchars($trip['title']) ?>">
                        </div>
                        <div class="trip-info">
                            <h3><?= htmlspecialchars($trip['title']) ?></h3>
                            <p><strong>Location:</strong> <?= htmlspecialchars($trip['location']) ?></p>
                            <p><strong>Dates:</strong> <?= date('M j, Y', strtotime($trip['start_date'])) ?> - <?= date('M j, Y', strtotime($trip['end_date'])) ?></p>
                            <p><strong>Price:</strong> <?= number_format($trip['price'], 2) ?> Dhs</p>
                            <p><strong>Category:</strong> <?= getCategoryName($trip['trip_category_id']) ?></p>
                            <p><strong>Hiking Level:</strong> <?= ucfirst($trip['hiking_level']) ?></p>
                        </div>
                        <div class="trip-actions">
                            <a href="edit_trip.php?id=<?= $trip['trip_id'] ?>" class="btn btn-edit">Edit</a>
                            <a href="trips.php?delete=<?= $trip['trip_id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this trip?')">Delete</a>
                        </div>
                    </div>
                    
                    <div class="trip-details">
                        <h4>Itinerary</h4>
                        <div class="days-container">
                            <?php foreach ($days as $day): ?>
                                <div class="day">
                                    <h5>Day <?= $day['day_number'] ?>: <?= htmlspecialchars($day['day_title']) ?></h5>
                                    <div class="activities">
                                        <?php foreach ($day['activities'] as $activity): ?>
                                            <div class="activity">
                                                <p><strong>Activity #<?= $activity['activity_order'] ?>:</strong> <?= htmlspecialchars($activity['activity_content']) ?></p>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggleDetails(header) {
        const details = header.nextElementSibling;
        details.classList.toggle('active');
    }
</script>

<?php
function getCategoryName($category_id) {
    $categories = [
        1 => 'Beach',
        2 => 'Mountain',
        3 => 'Nature'
    ];
    return $categories[$category_id] ?? 'Unknown';
}
?>
</body>
</html>