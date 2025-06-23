<?php
session_start();
require_once '../includes/pdo.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../pages/home.php");
    exit();
}

$reservation_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT r.*, t.title as trip_title 
                      FROM reservations r
                      JOIN trips t ON r.trip_id = t.trip_id
                      WHERE r.reservation_id = ? AND r.user_id = ? AND r.status = 'pending'");
$stmt->execute([$reservation_id, $_SESSION['user_id']]);
$reservation = $stmt->fetch();

if (!$reservation) {
    $_SESSION['message'] = "Reservation not found or cannot be edited";
    $_SESSION['message_type'] = "error";
    header("Location: reservations.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required_fields = ['gender', 'first_name', 'last_name', 'birthday', 'cin', 'city', 'phone', 'email'];
    $errors = [];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required";
        }
    }

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("UPDATE reservations SET
                gender = ?, first_name = ?, last_name = ?, birthday = ?,
                cin = ?, city = ?, phone = ?, email = ?, first_experience = ?
                WHERE reservation_id = ? AND user_id = ?");
            
            $success = $stmt->execute([
                $_POST['gender'],
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['birthday'],
                $_POST['cin'],
                $_POST['city'],
                $_POST['phone'],
                $_POST['email'],
                isset($_POST['first_experience']) ? 1 : 0,
                $reservation_id,
                $_SESSION['user_id']
            ]);
            
            if ($success && $stmt->rowCount() > 0) {
                $pdo->commit();
                $_SESSION['message'] = "Reservation updated successfully";
                $_SESSION['message_type'] = "success";
                header("Location: reservations.php");
                exit();
            } else {
                $errors[] = "No changes were made";
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

$page_css = 'edit_reservation.css';
include '../includes/header.php';
?>

<div class="container">
    <h1>Edit Reservation: <?= htmlspecialchars($reservation['trip_title']) ?></h1>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="post" class="reservation-form">
        <div class="form-row">
            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="">Select gender</option>
                    <option value="male" <?= $reservation['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                    <option value="female" <?= $reservation['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" 
                       value="<?= htmlspecialchars($reservation['first_name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" 
                       value="<?= htmlspecialchars($reservation['last_name']) ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="birthday">Birthday</label>
                <input type="date" id="birthday" name="birthday" 
                       value="<?= htmlspecialchars($reservation['birthday']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="cin">CIN</label>
                <input type="text" id="cin" name="cin" 
                       value="<?= htmlspecialchars($reservation['cin']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" 
                       value="<?= htmlspecialchars($reservation['city']) ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" 
                       value="<?= htmlspecialchars($reservation['phone']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" 
                       value="<?= htmlspecialchars($reservation['email']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="first_experience">First Experience?</label>
                <select id="first_experience" name="first_experience" required>
                    <option value="1" <?= $reservation['first_experience'] ? 'selected' : '' ?>>Yes</option>
                    <option value="0" <?= !$reservation['first_experience'] ? 'selected' : '' ?>>No</option>
                </select>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-edit">Update Reservation</button>
            <a href="reservations.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>