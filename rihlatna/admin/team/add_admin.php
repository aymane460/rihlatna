<?php
$page_css = 'add_admin.css';
include '../includes/sidebar.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'supervisor')) {
    header('Location: ../../pages/home.php'); 
    exit();
}

$can_create_admin = ($_SESSION['role'] === 'admin');
?>
<div class="main-content">
<?php
include '../includes/pdo.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'] ?? '';
    $lastName = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $plainPassword = $_POST['password'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $role = $_POST['role'] ?? 'supervisor'; 
    
    if (!$can_create_admin && $role === 'admin') {
        $errors[] = "You don't have permission to create admin users";
    }
    
    $errors = [];
    if (empty($firstName)) $errors[] = "First name is required";
    if (empty($lastName)) $errors[] = "Last name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($plainPassword) || strlen($plainPassword) < 8) $errors[] = "Password must be at least 8 characters";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (!in_array($role, ['supervisor', 'admin'])) $errors[] = "Invalid role selected";
    
    if (empty($errors)) {
   
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
        
        try {
         
            $stmt = $pdo->prepare("
                INSERT INTO users (first_name, last_name, email, password, phone, role) 
                VALUES (:first_name, :last_name, :email, :password, :phone, :role)
            ");
            
            $stmt->execute([
                ':first_name' => $firstName,
                ':last_name' => $lastName,
                ':email' => $email,
                ':password' => $hashedPassword,
                ':phone' => $phone,
                ':role' => $role
            ]);
            
            
            $success = "New $role user added successfully!";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { 
                $errors[] = "Email or phone number already exists";
            } else {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
        
        
    }
    
}
?>



<div class="form-container">
    <h1>Add New Staff User</h1>
    
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
    
    <form method="POST">
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>
        </div>
        
        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password (min 8 characters):</label>
            <input type="password" id="password" name="password" required minlength="8">
        </div>
        
        <div class="form-group">
            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" required>
        </div>
        
        <div class="form-group">
            <label>User Role:</label>
            <div class="role-selector">
                <div class="role-option">
                    <input type="radio" id="role_supervisor" name="role" value="supervisor" checked>
                    <label for="role_supervisor">Supervisor</label>
                </div>
                <?php if ($can_create_admin): ?>
                <div class="role-option">
                    <input type="radio" id="role_admin" name="role" value="admin">
                    <label for="role_admin">Administrator</label>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <button type="submit">Add User</button>
    </form>
</div>

</div>
</body>
</html>