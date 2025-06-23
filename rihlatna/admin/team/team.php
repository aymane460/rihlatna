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

// Start session and check authorization

// Redirect if not logged in or not admin/supervisor
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'supervisor')) {
    header('Location: ../../pages/home.php'); // Redirect to login if not authorized
    exit();
}

$current_user_role = $_SESSION['role'];
$current_user_id = $_SESSION['user_id'];

include '../includes/pdo.php';

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id_to_delete = (int)$_POST['delete_user'];
    
    try {
        // Get the role of user we're trying to delete
        $stmt = $pdo->prepare("SELECT role FROM users WHERE user_id = ?");
        $stmt->execute([$user_id_to_delete]);
        $user_to_delete = $stmt->fetch();
        
        if ($user_to_delete) {
            $role_to_delete = $user_to_delete['role'];
            
            // Validate deletion permissions
            $can_delete = false;
            
            if ($current_user_role === 'admin') {
                // Admin can delete both admins and supervisors (but not themselves)
                $can_delete = ($user_id_to_delete != $current_user_id);
            } elseif ($current_user_role === 'supervisor') {
                // Supervisor can only delete other supervisors
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

// Fetch all admins and supervisors
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

<style>
    .main-content {
        padding: 2rem;
        max-width: 100%;
      
    }
    
    .team-container {
        background: #fff;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    h1 {
        color: #2c3e50;
        margin-bottom: 1.5rem;
        font-size: 1.8rem;
    }
    
    .error {
        color: #e74c3c;
        background: #fde8e8;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1.5rem;
        border-left: 4px solid #e74c3c;
    }
    
    .success {
        color: #27ae60;
        background: #e8f8f0;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1.5rem;
        border-left: 4px solid #27ae60;
    }
    
    .user-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1.5rem;
    }
    
    .user-table th {
        background: #f8f9fa;
        padding: 1rem;
        text-align: left;
        border-bottom: 2px solid #ddd;
    }
    
    .user-table td {
        padding: 1rem;
        border-bottom: 1px solid #eee;
    }
    
    .user-table tr:last-child td {
        border-bottom: none;
    }
    
    .user-table tr:hover {
        background: #f8f9fa;
    }
    
    .role-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .role-admin {
        background: #3498db;
        color: white;
    }
    
    .role-supervisor {
        background: #2ecc71;
        color: white;
    }
    
    .delete-btn {
        background: #e74c3c;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .delete-btn:hover {
        background: #c0392b;
    }
    
    .delete-btn:disabled {
        background: #95a5a6;
        cursor: not-allowed;
    }
</style>

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