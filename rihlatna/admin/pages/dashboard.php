<?php
session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'supervisor'])) {

    header('Location: ../../pages/home.php');
    exit();
}

$page_css = 'dashboard.css';
include '../includes/sidebar.php'; 
include '../includes/pdo.php';

$user_count = 0;
$reservation_count = 0;

try {
   
    $user_stmt = $pdo->query("SELECT COUNT(user_id) FROM users WHERE role = 'customer'");
    $user_count = $user_stmt->fetchColumn();
    $reservation_stmt = $pdo->query("SELECT COUNT(reservation_id) FROM reservations WHERE status = 'pending'");
    $reservation_count = $reservation_stmt->fetchColumn();

} catch (PDOException $e) {
    
    echo "Error fetching dashboard data: " . $e->getMessage();
}

$user_name = isset($_SESSION['first_name']) ? htmlspecialchars($_SESSION['first_name']) : 'Admin';

?>

<div class="main-content">
    <h1>Welcome back, <?php echo $user_name; ?>!</h1>

  
    <div class="cards-container">

      
        <div class="dashboard-card">
            <div class="card-content">
               
                <div class="card-details">
                    <p class="card-title">New Users</p>
                    <p class="card-count"><?php echo $user_count; ?></p>
                </div>
            </div>
            <div class="card-footer">
               
                <a href="users.php">View Details &rarr;</a>
            </div>
        </div>
        <div class="dashboard-card">
            <div class="card-content">
               
                <div class="card-details">
                    <p class="card-title">New Reservations</p>
                    <p class="card-count"><?php echo $reservation_count; ?></p>
                </div>
            </div>
            <div class="card-footer">
                <a href="reservations.php">Manage Reservations &rarr;</a>
            </div>
        </div>

    </div> 
</div> 

</body>
</html>
