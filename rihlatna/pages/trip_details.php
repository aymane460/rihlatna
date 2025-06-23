<?php
include '../includes/pdo.php';

if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit();
}

$tripId = $_GET['id'];

$stmt = $pdo->prepare("SELECT t.*, tc.name AS category_name 
                      FROM trips t
                      JOIN trips_categories tc ON t.trip_category_id = tc.trip_category_id
                      WHERE t.trip_id = ?");
$stmt->execute([$tripId]);
$trip = $stmt->fetch();

if (!$trip) {
    header("Location: home.php");
    exit();
}

$start = new DateTime($trip['start_date']);
$end = new DateTime($trip['end_date']);
$duration = $start->diff($end)->days + 1;
$daysStmt = $pdo->prepare("SELECT * FROM trip_days WHERE trip_id = ? ORDER BY day_number");
$daysStmt->execute([$tripId]);
$days = $daysStmt->fetchAll();

foreach ($days as &$day) {
    $activitiesStmt = $pdo->prepare("SELECT * FROM trip_activities WHERE day_id = ? ORDER BY activity_order");
    $activitiesStmt->execute([$day['day_id']]);
    $day['activities'] = $activitiesStmt->fetchAll();
}

$reviewsStmt = $pdo->prepare("SELECT r.*, u.first_name, u.last_name 
                             FROM reviews r
                             JOIN users u ON r.user_id = u.user_id
                             WHERE r.trip_id = ?
                             ORDER BY r.review_date DESC");
$reviewsStmt->execute([$tripId]);
$reviews = $reviewsStmt->fetchAll();

$page_css = 'trip_details.css';
include '../includes/header.php';
?>

<main class="trip-details-page">
    <div class="trip-header">
        <div class="trip-image">
            <img src="../uploads/<?= htmlspecialchars($trip['image_url']) ?>" alt="<?= htmlspecialchars($trip['title']) ?>">
        </div>
        
        <div class="trip-basic-info">
            <h1><?= htmlspecialchars($trip['title']) ?></h1>
            
            <div class="trip-meta">
                <span class="duration"><img src="../images/clock.svg"> <?= $duration ?> Days</span>
                <span class="price"><img src="../images/money.svg"> <?= number_format($trip['price'], 2) ?> Dhs</span>
            </div>
            
            <div class="trip-dates">
                <p><img src="../images/calendar.svg"> <?= date('d F, Y', strtotime($trip['start_date'])) ?> - <?= date('d F, Y', strtotime($trip['end_date'])) ?></p>
                <p><img src="../images/location.svg"> <?= htmlspecialchars($trip['location']) ?></p>
            </div>
        </div>
    </div>

    <div class="trip-content">
        <section class="trip-itinerary">
            <h2>Trip Itinerary</h2>
            
            <?php if (!empty($days)): ?>
                <div class="itinerary-days">
                    <?php foreach ($days as $day): ?>
                        <div class="day-card">
                            <h3>Day <?= $day['day_number'] ?>: <?= htmlspecialchars($day['day_title']) ?></h3>
                            
                            <?php if (!empty($day['activities'])): ?>
                                <div class="activities-list">
                                    <?php foreach ($day['activities'] as $activity): ?>
                                        <div class="activity-item">
                                            <div class="activity-order"><?= $activity['activity_order'] ?></div>
                                            <div class="activity-content"><?= nl2br(htmlspecialchars($activity['activity_content'])) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-itinerary">No itinerary details available yet.</p>
            <?php endif; ?>
        </section>

        <section class="trip-reviews">
            <h2>Reviews</h2>
            
            <?php if (empty($reviews)): ?>
                <p class="no-reviews">No reviews yet. Be the first to review this trip!</p>
            <?php else: ?>
                <div class="reviews-list">
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <span class="reviewer-name"><?= htmlspecialchars($review['first_name'] . ' ' . $review['last_name']) ?></span>
                                    <span class="review-date"><?= date('d F, Y', strtotime($review['review_date'])) ?></span>
                                </div>
                                <div class="review-rating">
                                    <?php 
                                    $fullStars = $review['rating_value'];
                                    $emptyStars = 5 - $fullStars;
                                    echo str_repeat('★', $fullStars) . str_repeat('☆', $emptyStars);
                                    ?>
                                </div>
                            </div>
                            <div class="review-comment">
                                <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'customer'): ?>
                <div class="add-review">
                    <h3>Add Your Review</h3>
                    <form method="post" action="../actions/add_review.php">
                        <input type="hidden" name="trip_id" value="<?= $trip['trip_id'] ?>">
                        
                        <div class="form-group">
                            <label>Rating</label>
                            <div class="rating-stars">
                                <input type="hidden" id="rating-value" name="rating" value="0">
                                <span class="star" data-value="1">★</span>
                                <span class="star" data-value="2">★</span>
                                <span class="star" data-value="3">★</span>
                                <span class="star" data-value="4">★</span>
                                <span class="star" data-value="5">★</span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="comment">Your Review</label>
                            <textarea id="comment" name="comment" rows="4" required></textarea>
                        </div>
                        
                        <button type="submit" class="submit-review-btn">Submit Review</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="login-prompt">
                    <p>Please login to leave a review.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <section class="reservation-section">
        <h2>Book This Trip</h2>
        
        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'customer'): ?>
            <form method="post" action="../actions/make_reservation.php" class="reservation-form">
                <input type="hidden" name="trip_id" value="<?= $trip['trip_id'] ?>">
                <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="birthday">Birthday</label>
                        <input type="date" id="birthday" name="birthday" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cin">CIN</label>
                        <input type="text" id="cin" name="cin" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="first_experience">First Experience?</label>
                        <select id="first_experience" name="first_experience" required>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="reserve-btn">Book Now</button>
            </form>
        <?php else: ?>
            <div class="login-prompt">
                <p>Please login to book this trip.</p>
            </div>
        <?php endif; ?>
    </section>
</main>

<script src="../js/trip_details.js">

</script>

<?php include '../includes/footer.php'; ?>