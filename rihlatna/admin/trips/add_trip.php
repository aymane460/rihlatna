<?php
session_start();
include '../includes/pdo.php';

$errors = [];
$success_message = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);


$trip_title = $trip_price = $start_date = $end_date = $location = '';
$trip_category_id = $hiking_level = '';
$days = [];
$image_url = '';

if($_SERVER["REQUEST_METHOD"] == "POST") {

    $trip_title = trim($_POST['trip_title'] ?? '');
    $trip_price = trim($_POST['trip_price'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $trip_category_id = trim($_POST['trip_category_id'] ?? '');
    $hiking_level = trim($_POST['hiking_level'] ?? '');
    $location = trim($_POST['location'] ?? '');
    if (!empty($start_date) && !empty($end_date) && $start_date > $end_date) {
        $errors['date_range'] = "End date must be after start date.";
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] == UPLOAD_ERR_NO_FILE) {
        $errors['image'] = "Trip image is required.";
    } elseif ($_FILES['image']['error'] != UPLOAD_ERR_OK) {
        $errors['image'] = "Error uploading file.";
    } else {
        $target_dir = "../../uploads/trips/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($file_extension), $allowed_extensions)) {
            $errors['image'] = "Only JPG, JPEG, PNG & GIF files are allowed.";
        } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) { 
            $errors['image'] = "File size must be less than 5MB.";
        } else {
            $unique_filename = uniqid().'.'.$file_extension;
            $target_file = $target_dir.$unique_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_url = '/trips/' . $unique_filename;
            } else {
                $errors['image'] = "Sorry, there was an error uploading your file.";
            }
        }
    }


    if (isset($_POST['day_number']) && is_array($_POST['day_number'])) {
        foreach ($_POST['day_number'] as $index => $day_number) {
            $day_title = $_POST['day_title'][$index] ?? '';
            $activities = [];
            
            if (isset($_POST['activity_content'][$index]) && is_array($_POST['activity_content'][$index])) {
                foreach ($_POST['activity_content'][$index] as $activity_index => $content) {
                    $order = $_POST['activity_order'][$index][$activity_index] ?? ($activity_index + 1);
                    $activities[] = [
                        'content' => trim($content),
                        'order' => is_numeric($order) ? (int)$order : ($activity_index + 1)
                    ];
                }
            }
            
            $days[] = [
                'number' => $day_number,
                'title' => trim($day_title),
                'activities' => $activities
            ];
        }
    }

    if (empty($trip_title)) $errors['trip_title'] = "Trip title is required.";
    if (empty($trip_price)) $errors['trip_price'] = "Trip price is required.";
    if (!is_numeric($trip_price)) $errors['trip_price'] = "Trip price must be a number.";
    if (empty($start_date)) $errors['start_date'] = "Start date is required.";
    if (empty($end_date)) $errors['end_date'] = "End date is required.";
    if (empty($trip_category_id)) $errors['trip_category_id'] = "Trip category is required.";
    if (empty($hiking_level)) $errors['hiking_level'] = "Hiking level is required.";
    if (empty($location)) $errors['location'] = "Location is required.";
    if (empty($days)) $errors['days'] = "At least one day is required.";
    
    $day_numbers = [];
    foreach ($days as $day_index => $day) {
        if (empty($day['number'])) {
            $errors['day_number'][$day_index] = "Day number is required.";
        } elseif (!is_numeric($day['number'])) {
            $errors['day_number'][$day_index] = "Day number must be a number.";
        } elseif (in_array($day['number'], $day_numbers)) {
            $errors['day_number'][$day_index] = "Day numbers must be unique.";
        } else {
            $day_numbers[] = $day['number'];
        }
        
        if (empty($day['title'])) {
            $errors['day_title'][$day_index] = "Day title is required.";
        }
        
        if (empty($day['activities'])) {
            $errors['activities'][$day_index] = "Each day must have at least one activity.";
        } else {
            $activity_orders = [];
            foreach ($day['activities'] as $activity_index => $activity) {
                if (empty($activity['content'])) {
                    $errors['activity_content'][$day_index][$activity_index] = "Activity content is required.";
                }
                
                if (!is_numeric($activity['order'])) {
                    $errors['activity_order'][$day_index][$activity_index] = "Activity order must be a number.";
                } elseif (in_array($activity['order'], $activity_orders)) {
                    $errors['activity_order'][$day_index][$activity_index] = "Activity orders must be unique per day.";
                } else {
                    $activity_orders[] = $activity['order'];
                }
            }
        }
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("INSERT INTO trips 
                (title, price, start_date, end_date, hiking_level, location, image_url, trip_category_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $trip_title, $trip_price, $start_date, $end_date, 
                $hiking_level, $location, $image_url, $trip_category_id
            ]);
            $trip_id = $pdo->lastInsertId();
            
            foreach ($days as $day) {
             
                $stmt = $pdo->prepare("INSERT INTO trip_days 
                    (trip_id, day_number, day_title) 
                    VALUES (?, ?, ?)");
                $stmt->execute([$trip_id, $day['number'], $day['title']]);
                $day_id = $pdo->lastInsertId();
                
      
                foreach ($day['activities'] as $activity) {
                    $stmt = $pdo->prepare("INSERT INTO trip_activities 
                        (day_id, activity_content, activity_order) 
                        VALUES (?, ?, ?)");
                    $stmt->execute([
                        $day_id, 
                        $activity['content'], 
                        $activity['order']
                    ]);
                }
            }
            
            $pdo->commit();
            $_SESSION['success_message'] = "Trip added successfully!";
            header("Location: add_trip.php");
            exit();
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors['database'] = "Database error: ".$e->getMessage();
        }
    }
}

$page_css = 'add_trip.css';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>
    
    <?php if (isset($errors['database'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errors['database']) ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" id="tripForm">
     
        <h3>Trip Information</h3>
        <div class="form-group">
            <label>Trip Title</label>
            <input type="text" name="trip_title" value="<?= htmlspecialchars($trip_title) ?>">
            <?php if (isset($errors['trip_title'])): ?>
                <p class="error"><?= htmlspecialchars($errors['trip_title']) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>Price (Dhs)</label>
            <input type="number" name="trip_price" step="0.01" value="<?= htmlspecialchars($trip_price) ?>">
            <?php if (isset($errors['trip_price'])): ?>
                <p class="error"><?= htmlspecialchars($errors['trip_price']) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>Start Date</label>
            <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" id="startDate">
            <?php if (isset($errors['start_date'])): ?>
                <p class="error"><?= htmlspecialchars($errors['start_date']) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>End Date</label>
            <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" id="endDate">
            <?php if (isset($errors['end_date'])): ?>
                <p class="error"><?= htmlspecialchars($errors['end_date']) ?></p>
            <?php endif; ?>
            <?php if (isset($errors['date_range'])): ?>
                <p class="error"><?= htmlspecialchars($errors['date_range']) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>Category</label>
            <select name="trip_category_id">
                <option value="">Select a category</option>
                <option value="1" <?= $trip_category_id == '1' ? 'selected' : '' ?>>Beach</option>
                <option value="2" <?= $trip_category_id == '2' ? 'selected' : '' ?>>Mountain</option>
                <option value="3" <?= $trip_category_id == '3' ? 'selected' : '' ?>>Nature</option>
            </select>
            <?php if (isset($errors['trip_category_id'])): ?>
                <p class="error"><?= htmlspecialchars($errors['trip_category_id']) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>Hiking Level</label>
            <select name="hiking_level">
                <option value="">Select hiking level</option>
                <option value="beginner" <?= $hiking_level == 'beginner' ? 'selected' : '' ?>>Beginner</option>
                <option value="advanced" <?= $hiking_level == 'advanced' ? 'selected' : '' ?>>Advanced</option>
            </select>
            <?php if (isset($errors['hiking_level'])): ?>
                <p class="error"><?= htmlspecialchars($errors['hiking_level']) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" value="<?= htmlspecialchars($location) ?>">
            <?php if (isset($errors['location'])): ?>
                <p class="error"><?= htmlspecialchars($errors['location']) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>Trip Image (Max 5MB, JPG/PNG/GIF)</label>
            <input type="file" name="image" accept="image/jpeg,image/png,image/gif">
            <?php if (isset($errors['image'])): ?>
                <p class="error"><?= htmlspecialchars($errors['image']) ?></p>
            <?php endif; ?>
        </div>
        
        <h3>Days and Activities</h3>
        <?php if (isset($errors['days'])): ?>
            <p class="error"><?= htmlspecialchars($errors['days']) ?></p>
        <?php endif; ?>
        
        <div id="daysContainer">
            <?php if (empty($days)): ?>
                <div class="day-section" data-day-index="0">
                    <h4>Day 1</h4>
                    <div class="form-group">
                        <label>Day Number</label>
                        <input type="number" name="day_number[]" min="1" value="1">
                        <?php if (isset($errors['day_number'][0])): ?>
                            <p class="error"><?= htmlspecialchars($errors['day_number'][0]) ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label>Day Title</label>
                        <input type="text" name="day_title[]" value="">
                        <?php if (isset($errors['day_title'][0])): ?>
                            <p class="error"><?= htmlspecialchars($errors['day_title'][0]) ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="activities-container">
                        <div class="activity">
                            <div class="form-group">
                                <label>Activity Content</label>
                                <textarea name="activity_content[0][]"></textarea>
                                <?php if (isset($errors['activity_content'][0][0])): ?>
                                    <p class="error"><?= htmlspecialchars($errors['activity_content'][0][0]) ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label>Activity Order</label>
                                <input type="number" name="activity_order[0][]" min="1" value="1">
                                <?php if (isset($errors['activity_order'][0][0])): ?>
                                    <p class="error"><?= htmlspecialchars($errors['activity_order'][0][0]) ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <button type="button" class="remove-activity">Remove Activity</button>
                        </div>
                    </div>
                    <button type="button" class="add-activity">Add Activity</button>
                    <button type="button" class="remove-day">Remove Day</button>
                </div>
            <?php else: ?>
                <?php foreach ($days as $i => $day): ?>
                    <div class="day-section" data-day-index="<?= $i ?>">
                        <h4>Day <?= $i + 1 ?></h4>
                        <div class="form-group">
                            <label>Day Number</label>
                            <input type="number" name="day_number[]" min="1" value="<?= htmlspecialchars($day['number']) ?>">
                            <?php if (isset($errors['day_number'][$i])): ?>
                                <p class="error"><?= htmlspecialchars($errors['day_number'][$i]) ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label>Day Title</label>
                            <input type="text" name="day_title[]" value="<?= htmlspecialchars($day['title']) ?>">
                            <?php if (isset($errors['day_title'][$i])): ?>
                                <p class="error"><?= htmlspecialchars($errors['day_title'][$i]) ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="activities-container">
                            <?php foreach ($day['activities'] as $a_index => $activity): ?>
                                <div class="activity">
                                    <div class="form-group">
                                        <label>Activity Content</label>
                                        <textarea name="activity_content[<?= $i ?>][]"><?= htmlspecialchars($activity['content']) ?></textarea>
                                        <?php if (isset($errors['activity_content'][$i][$a_index])): ?>
                                            <p class="error"><?= htmlspecialchars($errors['activity_content'][$i][$a_index]) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Activity Order</label>
                                        <input type="number" name="activity_order[<?= $i ?>][]" min="1" value="<?= htmlspecialchars($activity['order']) ?>">
                                        <?php if (isset($errors['activity_order'][$i][$a_index])): ?>
                                            <p class="error"><?= htmlspecialchars($errors['activity_order'][$i][$a_index]) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <button type="button" class="remove-activity">Remove Activity</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <button type="button" class="add-activity">Add Activity</button>
                        <button type="button" class="remove-day">Remove Day</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <button type="button" id="addDay">Add Day</button>
        <button type="submit" name="add_trip_btn">Add Trip</button>
    </form>
</div>

<script src="../js/add_trip.js">
</script>


</body>
</html>