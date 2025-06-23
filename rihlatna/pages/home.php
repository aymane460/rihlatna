<?php 
include '../includes/pdo.php';

$stmt = $pdo->query("SELECT * FROM trips_categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT DISTINCT start_date FROM trips ORDER BY start_date ASC");
$departureDates = $stmt->fetchAll();

$sql = "SELECT * FROM trips WHERE 1=1";
$params = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!empty($_POST['categories']) && is_array($_POST['categories'])) {
        $placeholders = [];
        foreach ($_POST['categories'] as $index => $catId) {
            $key = ":category_" . $index;
            $placeholders[] = $key;
            $params[$key] = $catId;
        }
        $sql .= " AND trip_category_id IN (" . implode(", ", $placeholders) . ")";
    }

    if (!empty($_POST['hiking_levels']) && is_array($_POST['hiking_levels'])) {
        $placeholders = [];
        foreach ($_POST['hiking_levels'] as $index => $level) {
            $key = ":hiking_" . $index;
            $placeholders[] = $key;
            $params[$key] = $level;
        }
        $sql .= " AND hiking_level IN (" . implode(", ", $placeholders) . ")";
    }

    if (!empty($_POST['departure_dates']) && is_array($_POST['departure_dates'])) {
        $placeholders = [];
        foreach ($_POST['departure_dates'] as $index => $date) {
            $key = ":date_" . $index;
            $placeholders[] = $key;
            $params[$key] = date('Y-m-d', strtotime($date));
        }
        $sql .= " AND DATE(start_date) IN (" . implode(", ", $placeholders) . ")";
    }
}

$sql .= " ORDER BY start_date ASC"; 

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$trips = $stmt->fetchAll();
?>

<?php
$page_css = 'home.css';
include '../includes/header.php';
?>

<main>
   <div id="header_image">
    <img src="../images/header_image.png" alt="header image">
   </div>
   <div id="trips">
       <form action="home.php" method="post">
    <div class="filter-bar">
        <div class="custom-multiselect">
            <div class="select-box" onclick="toggleDropdown('categories-box')">
                Destination type
                <img src="../images/chevron.svg" class="arrow-icon" />
            </div>
            <div class="checkboxes" id="categories-box">
                <?php foreach ($categories as $category): ?>
                    <label>
                        <input type="checkbox" name="categories[]" value="<?= htmlspecialchars($category['trip_category_id']) ?>"
                            <?= (isset($_POST['categories']) && in_array($category['trip_category_id'], $_POST['categories'])) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="custom-multiselect">
            <div class="select-box" onclick="toggleDropdown('hiking-box')">
                Hiking Level
                <img src="../images/chevron.svg" class="arrow-icon" />
            </div>
            <div class="checkboxes" id="hiking-box">
                <label>
                    <input type="checkbox" name="hiking_levels[]" value="beginner"
                        <?= (isset($_POST['hiking_levels']) && in_array('beginner', $_POST['hiking_levels'])) ? 'checked' : '' ?>>
                    Beginner
                </label>
                <label>
                    <input type="checkbox" name="hiking_levels[]" value="advanced"
                        <?= (isset($_POST['hiking_levels']) && in_array('advanced', $_POST['hiking_levels'])) ? 'checked' : '' ?>>
                    Advanced
                </label>
            </div>
        </div>

        <div class="custom-multiselect">
            <div class="select-box" onclick="toggleDropdown('departure-box')">
                Departure Date
                <img src="../images/chevron.svg" class="arrow-icon" />
            </div>
            <div class="checkboxes" id="departure-box">
                <?php foreach ($departureDates as $date): ?>
                    <label>
                        <input type="checkbox" name="departure_dates[]" value="<?= htmlspecialchars($date['start_date']) ?>"
                            <?= (isset($_POST['departure_dates']) && in_array($date['start_date'], $_POST['departure_dates'])) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($date['start_date']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit" class="search-btn">
            <img src="../images/search.svg" alt="Search" />
        </button>
        <button type="button" class="reset-btn" onclick="resetFilters()">Reset</button>
    </div>
</form>


<?php if (count($trips) > 0): ?>
    <div class="trips-wrapper">
        <div class="scroll-controls">
            <button class="scroll-btn" onclick="scrollTrips(-1)"><img src="../images/chevron-left.svg" ></button>
            <button class="scroll-btn" onclick="scrollTrips(1)"><img src="../images/chevron-right.svg" ></button>
        </div>

        <div class="trips-container" id="trips-container">
            <?php foreach ($trips as $trip): ?>
                <div class="trip-card">
                    <img src="../uploads/<?= htmlspecialchars($trip['image_url']) ?>" alt="<?= htmlspecialchars($trip['title']) ?>" />
                    <h3><?= htmlspecialchars($trip['title']) ?></h3>
                    <p><img src="../images/location.svg" class="location_icon"> <?= htmlspecialchars($trip['location']) ?></p>
                    <?php
                        $start = new DateTime($trip['start_date']);
                        $end = new DateTime($trip['end_date']);
                        $days = $start->diff($end)->days + 1; 
                    ?>
                    <p><img src="../images/clock.svg" class="clock_icon"> <?= $days ?> Days</p>
                    <p><img src="../images/money.svg" class="money_icon"> <?= $trip['price'] ?> Dhs</p>
                    <p><img src="../images/calendar.svg" class="calendar_icon"> <?= date("d F, Y", strtotime($trip['start_date'])) ?></p>
                    <a href="trip_details.php?id=<?= $trip['trip_id'] ?>" class="details-btn">More Details</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php else: ?>
    <p class="no-results">No trips found matching your criteria.</p>
<?php endif; ?>
   </div>
</main>

<script src="../js/home.js">

</script>



<?php
include '../includes/footer.php';
?>