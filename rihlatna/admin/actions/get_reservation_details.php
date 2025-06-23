<?php
session_start();
require_once '../includes/pdo.php';


if  (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'supervisor'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No reservation ID provided']);
    exit();
}

$reservation_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$reservation_id) {
    echo json_encode(['error' => 'Invalid reservation ID']);
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT r.*, 
               t.title as trip_title,
               t.start_date as trip_start_date,
               t.end_date as trip_end_date,
               t.price as price
        FROM reservations r
        JOIN trips t ON r.trip_id = t.trip_id
        WHERE r.reservation_id = ?
    ");
    $stmt->execute([$reservation_id]);
    $reservation = $stmt->fetch();

    if ($reservation) {
        $reservation['trip_start_date'] = date('M j, Y', strtotime($reservation['trip_start_date']));
        $reservation['trip_end_date'] = date('M j, Y', strtotime($reservation['trip_end_date']));
        $reservation['birthday'] = date('M j, Y', strtotime($reservation['birthday']));
        
        echo json_encode($reservation);
    } else {
        echo json_encode(['error' => 'Reservation not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error']);
}