<?php
session_start();

require '../includes/pdo.php';

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['role'] = $user['role'];
    
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
    
    if ($user['role'] === 'admin' || $user['role'] === 'supervisor') {
        $redirect = '../admin/pages/dashboard.php'; 
    } else {
        $redirect = $_POST['redirect_to'] ?? '../pages/home.php'; 
    }
    header("Location: $redirect");
    exit();
} else {
    $_SESSION['login_error'] = "Invalid email or password";
    header("Location: ../pages/home.php");
    exit();
}