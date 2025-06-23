<?php
$page_css = 'add_admin.css';
include '../includes/sidebar.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and has proper role
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'supervisor')) {
    header('Location: ../../pages/home.php'); // Redirect to login if not authorized
    exit();
}

// Determine allowed roles based on current user's role
$can_create_admin = ($_SESSION['role'] === 'admin');
?>
<div class="main-content">
<?php
include '../includes/pdo.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $firstName = $_POST['first_name'] ?? '';
    $lastName = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $plainPassword = $_POST['password'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $role = $_POST['role'] ?? 'supervisor'; // Default to supervisor
    
    // Validate allowed role
    if (!$can_create_admin && $role === 'admin') {
        $errors[] = "You don't have permission to create admin users";
    }
    
    // Validate inputs
    $errors = [];
    if (empty($firstName)) $errors[] = "First name is required";
    if (empty($lastName)) $errors[] = "Last name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($plainPassword) || strlen($plainPassword) < 8) $errors[] = "Password must be at least 8 characters";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (!in_array($role, ['supervisor', 'admin'])) $errors[] = "Invalid role selected";
    
    if (empty($errors)) {
        // Hash the password
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
        
        try {
            // Insert new user
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
            if ($e->getCode() == 23000) { // Duplicate entry error code
                $errors[] = "Email or phone number already exists";
            } else {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
        
        
    }
    
}
?>

<style>
    .main-content {
        padding: 2rem;
        max-width: 800px;
        margin: 0 auto;
    }
    
    .form-container {
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
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    label {
        display: block;
        margin-bottom: 0.5rem;
        color: #34495e;
        font-weight: 500;
    }
    
    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="tel"],
    select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }
    
    input:focus,
    select:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
    }
    
    .role-selector {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .role-option {
        flex: 1;
    }
    
    .role-option input[type="radio"] {
        display: none;
    }
    
    .role-option label {
        display: block;
        padding: 1rem;
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .role-option input[type="radio"]:checked + label {
        background: #3498db;
        color: white;
        border-color: #3498db;
    }
    
    button[type="submit"] {
        background: #3498db;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.3s;
        width: 100%;
    }
    
    button[type="submit"]:hover {
        background: #2980b9;
    }
</style>

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