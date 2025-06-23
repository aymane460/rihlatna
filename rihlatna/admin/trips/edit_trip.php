<?php
session_start();
include '../includes/pdo.php';


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid trip ID";
    header("Location: trips.php");
    exit();
}

$trip_id = $_GET['id'];


$stmt = $pdo->prepare("SELECT * FROM trips WHERE trip_id = ?");
$stmt->execute([$trip_id]);
$trip = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trip) {
    $_SESSION['error_message'] = "Trip not found";
    header("Location: trips.php");
    exit();
}


$days = [];
$stmt_days = $pdo->prepare("SELECT * FROM trip_days WHERE trip_id = ? ORDER BY day_number");
$stmt_days->execute([$trip_id]);
while ($day = $stmt_days->fetch(PDO::FETCH_ASSOC)) {
    $stmt_activities = $pdo->prepare("SELECT * FROM trip_activities 
                                    WHERE day_id = ? ORDER BY activity_order");
    $stmt_activities->execute([$day['day_id']]);
    $day['activities'] = $stmt_activities->fetchAll(PDO::FETCH_ASSOC);
    $days[] = $day;
}

$errors = [];
$success_message = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    $image_url = $trip['image_url'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image']['error'] != UPLOAD_ERR_OK) {
            $errors['image'] = "Error uploading file.";
        } else {
            $target_dir = "../../uploads/";
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
                    if ($image_url) {
                        $old_file = "../".$image_url;
                        if (file_exists($old_file)) {
                            unlink($old_file);
                        }
                    }
                    $image_url = '/trips/'.$unique_filename;
                } else {
                    $errors['image'] = "Sorry, there was an error uploading your file.";
                }
            }
        }
    }


    $posted_days = [];
    if (isset($_POST['day_number']) && is_array($_POST['day_number'])) {
        foreach ($_POST['day_number'] as $index => $day_number) {
            $day_id = $_POST['day_id'][$index] ?? null;
            $day_title = $_POST['day_title'][$index] ?? '';
            $activities = [];
            
            if (isset($_POST['activity_content'][$index]) && is_array($_POST['activity_content'][$index])) {
                foreach ($_POST['activity_content'][$index] as $activity_index => $content) {
                    $activity_id = $_POST['activity_id'][$index][$activity_index] ?? null;
                    $order = $_POST['activity_order'][$index][$activity_index] ?? ($activity_index + 1);
                    $activities[] = [
                        'id' => $activity_id,
                        'content' => trim($content),
                        'order' => is_numeric($order) ? (int)$order : ($activity_index + 1)
                    ];
                }
            }
            
            $posted_days[] = [
                'id' => $day_id,
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
    if (empty($posted_days)) $errors['days'] = "At least one day is required.";
    
    $day_numbers = [];
    foreach ($posted_days as $day_index => $day) {
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
            
            $stmt = $pdo->prepare("UPDATE trips SET 
                title = ?, price = ?, start_date = ?, end_date = ?, 
                hiking_level = ?, location = ?, image_url = ?, trip_category_id = ?
                WHERE trip_id = ?");
            $stmt->execute([
                $trip_title, $trip_price, $start_date, $end_date, 
                $hiking_level, $location, $image_url, $trip_category_id,
                $trip_id
            ]);
            
            $existing_day_ids = array_column($days, 'day_id');
            $updated_day_ids = [];
            
            foreach ($posted_days as $day) {
                if ($day['id']) {
                    $stmt = $pdo->prepare("UPDATE trip_days SET 
                        day_number = ?, day_title = ?
                        WHERE day_id = ? AND trip_id = ?");
                    $stmt->execute([
                        $day['number'], $day['title'],
                        $day['id'], $trip_id
                    ]);
                    $day_id = $day['id'];
                } else {
                    $stmt = $pdo->prepare("INSERT INTO trip_days 
                        (trip_id, day_number, day_title) 
                        VALUES (?, ?, ?)");
                    $stmt->execute([
                        $trip_id, $day['number'], $day['title']
                    ]);
                    $day_id = $pdo->lastInsertId();
                }
                $updated_day_ids[] = $day_id;
                
                $existing_activity_ids = [];
                foreach ($days as $existing_day) {
                    if ($existing_day['day_id'] == $day['id']) {
                        $existing_activity_ids = array_column($existing_day['activities'], 'activity_id');
                        break;
                    }
                }
                $updated_activity_ids = [];
                
                foreach ($day['activities'] as $activity) {
                    if ($activity['id']) {
                        $stmt = $pdo->prepare("UPDATE trip_activities SET 
                            activity_content = ?, activity_order = ?
                            WHERE activity_id = ? AND day_id = ?");
                        $stmt->execute([
                            $activity['content'], $activity['order'],
                            $activity['id'], $day_id
                        ]);
                        $updated_activity_ids[] = $activity['id'];
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO trip_activities 
                            (day_id, activity_content, activity_order) 
                            VALUES (?, ?, ?)");
                        $stmt->execute([
                            $day_id, $activity['content'], $activity['order']
                        ]);
                    }
                }
                
                $activities_to_delete = array_diff($existing_activity_ids, $updated_activity_ids);
                if (!empty($activities_to_delete)) {
                    $placeholders = implode(',', array_fill(0, count($activities_to_delete), '?'));
                    $stmt = $pdo->prepare("DELETE FROM trip_activities 
                                          WHERE activity_id IN ($placeholders)");
                    $stmt->execute(array_values($activities_to_delete));
                }
            }
            
            $days_to_delete = array_diff($existing_day_ids, $updated_day_ids);
            if (!empty($days_to_delete)) {
                $placeholders = implode(',', array_fill(0, count($days_to_delete), '?'));
                $stmt = $pdo->prepare("DELETE FROM trip_days 
                                      WHERE day_id IN ($placeholders)");
                $stmt->execute(array_values($days_to_delete));
            }
            
            $pdo->commit();
            $_SESSION['success_message'] = "Trip updated successfully!";
            header("Location: edit_trip.php?id=$trip_id");
            exit();
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors['database'] = "Database error: ".$e->getMessage();
        }
    }
    
    $trip = array_merge($trip, [
        'title' => $trip_title,
        'price' => $trip_price,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'trip_category_id' => $trip_category_id,
        'hiking_level' => $hiking_level,
        'location' => $location,
        'image_url' => $image_url
    ]);
    
    $days = $posted_days;
}

$page_css = 'edit_trip.css';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <h2>Edit Trip: <?= htmlspecialchars($trip['title']) ?></h2>
    
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>
    
    <?php if (isset($errors['database'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errors['database']) ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" id="tripForm">
        <input type="hidden" name="trip_id" value="<?= $trip_id ?>">
        
        <h3>Trip Information</h3>
        <div class="form-group">
            <label>Trip Title</label>
            <input type="text" name="trip_title" value="<?= htmlspecialchars($trip['title'] ?? '') ?>">
            <?php if (isset($errors['trip_title'])): ?>
                <p class="error"><?= htmlspecialchars($errors['trip_title']) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>Price (Dhs)</label>
            <input type="number" name="trip_price" step="0.01" value="<?= htmlspecialchars($trip['price'] ?? '') ?>">
            <?php if (isset($errors['trip_price'])): ?>
                <p class="error"><?= htmlspecialchars($errors['trip_price']) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>Start Date</label>
            <input type="date" name="start_date" value="<?= htmlspecialchars($trip['start_date'] ?? '') ?>" id="startDate">
            <?php if (isset($errors['start_date'])): ?>
                <p class="error"><?= htmlspecialchars($errors['start_date']) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>End Date</label>
            <input type="date" name="end_date" value="<?= htmlspecialchars($trip['end_date'] ?? '') ?>" id="endDate">
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
                <option value="1" <?= ($trip['trip_category_id'] ?? '') == '1' ? 'selected' : '' ?>>Beach</option>
                <option value="2" <?= ($trip['trip_category_id'] ?? '') == '2' ? 'selected' : '' ?>>Mountain</option>
                <option value="3" <?= ($trip['trip_category_id'] ?? '') == '3' ? 'selected' : '' ?>>Nature</option>
            </select>
            <?php if (isset($errors['trip_category_id'])): ?>
                <p class="error"><?= htmlspecialchars($errors['trip_category_id']) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>Hiking Level</label>
            <select name="hiking_level">
                <option value="">Select hiking level</option>
                <option value="beginner" <?= ($trip['hiking_level'] ?? '') == 'beginner' ? 'selected' : '' ?>>Beginner</option>
                <option value="advanced" <?= ($trip['hiking_level'] ?? '') == 'advanced' ? 'selected' : '' ?>>Advanced</option>
            </select>
            <?php if (isset($errors['hiking_level'])): ?>
                <p class="error"><?= htmlspecialchars($errors['hiking_level']) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" value="<?= htmlspecialchars($trip['location'] ?? '') ?>">
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
            <?php if (!empty($trip['image_url'])): ?>
                <div class="current-image">
                    <p>Current Image:</p>
                    <img src="../../uploads<?= htmlspecialchars($trip['image_url']) ?>" style="max-width: 200px; max-height: 150px;">
                </div>
            <?php endif; ?>
        </div>
        
        <h3>Days and Activities</h3>
        <?php if (isset($errors['days'])): ?>
            <p class="error"><?= htmlspecialchars($errors['days']) ?></p>
        <?php endif; ?>
        
        <div id="daysContainer">
            <?php if (!empty($days)): ?>
                <?php foreach ($days as $i => $day): ?>
                    <div class="day-section" data-day-index="<?= $i ?>">
                        <h4>Day <?= $i + 1 ?></h4>
                        <input type="hidden" name="day_id[]" value="<?= htmlspecialchars($day['day_id'] ?? '') ?>">
                        
                        <div class="form-group">
                            <label>Day Number</label>
                            <input type="number" name="day_number[]" min="1" value="<?= htmlspecialchars($day['day_number'] ?? ($i + 1)) ?>">
                            <?php if (isset($errors['day_number'][$i])): ?>
                                <p class="error"><?= htmlspecialchars($errors['day_number'][$i]) ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label>Day Title</label>
                            <input type="text" name="day_title[]" value="<?= htmlspecialchars($day['day_title'] ?? '') ?>">
                            <?php if (isset($errors['day_title'][$i])): ?>
                                <p class="error"><?= htmlspecialchars($errors['day_title'][$i]) ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="activities-container">
                            <?php foreach (($day['activities'] ?? []) as $a_index => $activity): ?>
                                <div class="activity">
                                    <input type="hidden" name="activity_id[<?= $i ?>][]" value="<?= htmlspecialchars($activity['activity_id'] ?? '') ?>">
                                    
                                    <div class="form-group">
                                        <label>Activity Content</label>
                                        <textarea name="activity_content[<?= $i ?>][]"><?= htmlspecialchars($activity['activity_content'] ?? '') ?></textarea>
                                        <?php if (isset($errors['activity_content'][$i][$a_index])): ?>
                                            <p class="error"><?= htmlspecialchars($errors['activity_content'][$i][$a_index]) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Activity Order</label>
                                        <input type="number" name="activity_order[<?= $i ?>][]" min="1" value="<?= htmlspecialchars($activity['activity_order'] ?? ($a_index + 1)) ?>">
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
            <?php else: ?>
                <div class="day-section" data-day-index="0">
                    <h4>Day 1</h4>
                    <input type="hidden" name="day_id[]" value="">
                    
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
                            <input type="hidden" name="activity_id[0][]" value="">
                            
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
            <?php endif; ?>
        </div>
        
        <button type="button" id="addDay">Add Day</button>
        <button type="submit" name="update_trip_btn">Update Trip</button>
    </form>
</div>

<script src="../js/edit_trip.js">
</script>


</body>
</html>