<?php

require '../includes/pdo.php';

$first = $_POST['first_name'];
$last = $_POST['last_name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);


$stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password) VALUES (?,?, ?, ?, ?)");
$stmt->execute([$first, $last, $email,$phone, $password]);


$redirect = $_POST['redirect_to'] ?? '../pages/home.php';
        header("Location: $redirect");
