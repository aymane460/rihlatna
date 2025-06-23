<?php
session_start();

require '../includes/pdo.php';

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    // Set role in session for all users
    $_SESSION['role'] = $user['role'];
    
    // Only set user_id for customers
   
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
    
    // Redirect based on role
    if ($user['role'] === 'admin' || $user['role'] === 'supervisor') {
        $redirect = '../admin/pages/dashboard.php'; 
    } else {
        $redirect = $_POST['redirect_to'] ?? '../pages/home.php'; 
    }
    header("Location: $redirect");
    exit();
} else {
    // Invalid login handling
    $_SESSION['login_error'] = "Invalid email or password";
    header("Location: ../pages/home.php"); // Redirect back to login page
    exit();
}