<?php
session_start();
require_once '../includes/pdo.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    $_SESSION['error'] = "You need to login as a customer to leave a review";
    header("Location: ../pages/login.php?redirect=" . urlencode($_SERVER['HTTP_REFERER']));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method";
    header("Location: ../pages/home.php");
    exit();
}

$required_fields = ['trip_id', 'rating', 'comment'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'] = "Please fill in all required fields";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
}

$trip_id = filter_input(INPUT_POST, 'trip_id', FILTER_VALIDATE_INT);
$rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'max_range' => 5]
]);
$comment = trim(htmlspecialchars($_POST['comment']));
$user_id = $_SESSION['user_id'];

if (!$trip_id || !$rating || strlen($comment) < 0) {
    $_SESSION['error'] = "Invalid input data";
    $_SESSION['form_data'] = $_POST;
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

try {
    $trip_stmt = $pdo->prepare("SELECT trip_id FROM trips WHERE trip_id = ?");
    $trip_stmt->execute([$trip_id]);
    if ($trip_stmt->rowCount() === 0) {
        $_SESSION['error'] = "Trip not found";
        header("Location: ../pages/home.php");
        exit();
    }

    $check_stmt = $pdo->prepare("SELECT review_id FROM reviews WHERE user_id = ? AND trip_id = ?");
    $check_stmt->execute([$user_id, $trip_id]);
    if ($check_stmt->rowCount() > 0) {
        $_SESSION['error'] = "You've already reviewed this trip";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $insert_stmt = $pdo->prepare("INSERT INTO reviews 
                                (rating_value, comment, user_id, trip_id, review_date) 
                                VALUES (?, ?, ?, ?, CURRENT_DATE)");
    $insert_stmt->execute([$rating, $comment, $user_id, $trip_id]);

    $_SESSION['success'] = "Thank you for your review!";
    header("Location: ../pages/trip_details.php?id=" . $trip_id);
    exit();

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while submitting your review. Please try again.";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}