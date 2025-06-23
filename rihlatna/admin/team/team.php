<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_css = 'team.css';
include '../includes/sidebar.php';
?>
<div class="main-content">
<?php
$page_css = 'team.css';
include '../includes/sidebar.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'supervisor')) {
    header('Location: ../../pages/home.php'); 
    exit();
}

$current_user_role = $_SESSION['role'];
$current_user_id = $_SESSION['user_id'];

include '../includes/pdo.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id_to_delete = (int)$_POST['delete_user'];
    
    try {
        $stmt = $pdo->prepare("SELECT role FROM users WHERE user_id = ?");
        $stmt->execute([$user_id_to_delete]);
        $user_to_delete = $stmt->fetch();
        
        if ($user_to_delete) {
            $role_to_delete = $user_to_delete['role'];
            
            $can_delete = false;
            
            if ($current_user_role === 'admin') {
                $can_delete = ($user_id_to_delete != $current_user_id);
            } elseif ($current_user_role === 'supervisor') {
                $can_delete = ($role_to_delete === 'supervisor' && $user_id_to_delete != $current_user_id);
            }
            
            if ($can_delete) {
                $delete_stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
                $delete_stmt->execute([$user_id_to_delete]);
                
                if ($delete_stmt->rowCount() > 0) {
                    $success = "User deleted successfully!";
                }
            } else {
                $errors[] = "You don't have permission to delete this user";
            }
        }
    } catch (PDOException $e) {
        $errors[] = "Database error: " . $e->getMessage();
    }
}

try {
    $query = "SELECT user_id, first_name, last_name, email, phone, role 
              FROM users 
              WHERE role IN ('admin', 'supervisor')
              ORDER BY role DESC, first_name ASC";
    $users = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Database error: " . $e->getMessage();
    $users = [];
}
?>

<div class="team-container">
    <h1>Team Management</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <?php if (empty($users)): ?>
        <p>No team members found.</p>
    <?php else: ?>
        <table class="user-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . htmlspecialchars($user['last_name'])); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td>
                            <span class="role-badge role-<?php echo htmlspecialchars($user['role']); ?>">
                                <?php echo htmlspecialchars(ucfirst($user['role'])); ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                <input type="hidden" name="delete_user" value="<?php echo $user['user_id']; ?>">
                                <button type="submit" class="delete-btn" 
                                    <?php 
                                        // Disable button if:
                                        // 1. Current user is trying to delete themselves
                                        // 2. Supervisor is trying to delete an admin
                                        // 3. Current user doesn't have delete permissions
                                        $disable = ($user['user_id'] == $current_user_id) || 
                                                 ($current_user_role === 'supervisor' && $user['role'] === 'admin') ||
                                                 ($user['user_id'] == 1); // Prevent deleting main admin (user_id 1)
                                        echo $disable ? 'disabled' : '';
                                    ?>>
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</div>

</div>
</body>
</html>