<?php
$page_css = 'users.css';
include '../includes/sidebar.php';
require_once '../includes/pdo.php';

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    if ($delete_id == $_SESSION['user_id']) {
        $_SESSION['message'] = "You cannot delete your own account!";
        $_SESSION['message_type'] = "error";
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$delete_id]);
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = "User deleted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting user";
            $_SESSION['message_type'] = "error";
        }
    }
    

    header("Location: users.php");
    exit();
}

$query = "SELECT * FROM users WHERE role = 'customer' ORDER BY last_name, first_name";
$result = $pdo->query($query);
?>

<div class="main-content">
    <h1>Customer Management</h1>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert <?php echo $_SESSION['message_type']; ?>">
            <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            ?>
        </div>
    <?php endif; ?>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td class="actions">
                            
                            <a href="users.php?delete_id=<?php echo $user['user_id']; ?>" 
                               class="btn-delete" 
                               onclick="return confirm('Are you sure you want to delete this customer?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($result->rowCount() == 0): ?>
                    <tr>
                        <td colspan="6">No customers found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>