<?php
session_start();
require_once '../includes/pdo.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../pages/home.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $required_fields = ['gender', 'first_name', 'last_name', 'birthday', 'cin', 'city', 'phone', 'email', 'trip_id'];
    $errors = [];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required";
        }
    }

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (!preg_match('/^[0-9]{10,15}$/', $_POST['phone'])) {
        $errors[] = "Invalid phone number format";
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT reservation_id FROM reservations WHERE cin = ? OR phone = ?");
            $stmt->execute([$_POST['cin'], $_POST['phone']]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = "A reservation with this CIN or phone number already exists";
                $_SESSION['errors'] = $errors;
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            }
            $stmt = $pdo->prepare("INSERT INTO reservations 
                (gender, first_name, last_name, birthday, cin, city, phone, email, 
                first_experience, user_id, trip_id, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            
            $stmt->execute([
                $_POST['gender'],
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['birthday'],
                $_POST['cin'],
                $_POST['city'],
                $_POST['phone'],
                $_POST['email'],
                isset($_POST['first_experience']) ? 1 : 0,
                $_SESSION['user_id'],
                $_POST['trip_id']
            ]);

            $pdo->commit();

            $_SESSION['success'] = "Reservation created successfully!";
            header("Location: ../pages/reservations.php");
            exit();

        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['errors'] = ["Database error: " . $e->getMessage()];
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
} else {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}