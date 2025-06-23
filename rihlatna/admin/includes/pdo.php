<?php
$user = 'root';
$dbName = 'rihlatna';
$host = 'localhost';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $ex) {
    die("Connection failed" . $ex-> getMessage());
}
?>