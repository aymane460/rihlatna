<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>rihlatna</title>
    <link rel="stylesheet" href="../styles/header_footer.css">
    <?php if (isset($page_css)) : ?>
        <link rel="stylesheet" href="../styles/<?php echo htmlspecialchars($page_css); ?>">
    <?php endif; ?>
  
</head>
<body>
    <header class="navbar">
        <div class="logo"><img src="../images/rihlatna_logo.png" alt="rihlatna"></div>

        <nav class="nav-links">
            <a href="../pages/home.php">HOME</a>
            <a href="../pages/about.php">WHO WE ARE</a>
        </nav>

        <div class="nav-icons">
             <a href="../pages/reservations.php"><img src="../images/reservations.svg" alt="cart" class="icon"></a>
            
           
            <div class="user-menu">
                <img src="../images/user.svg" alt="user" class="icon" id="userIcon">
                <?php if (session_status() === PHP_SESSION_NONE) {
                             session_start();
                        }
                ?>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'customer'): ?>
                    <div class="dropdown-menu" id="userDropdown">
                        <a href="../pages/reservations.php">My Reservations</a>
                        <a href="../auth/logout.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">Logout</a>
                    </div>
                <?php else: ?>
                    <div id="authModal" class="modal">
                        <div class="modal-content">
                            <span class="close" id="closeModal">&times;</span>
                            <div class="tabs">
                                <button id="loginTab" class="active">Login</button>
                                <button id="signupTab">Sign Up</button>
                            </div>
                            
                            <form action="../auth/login.php" method="POST" id="loginForm">
                                <input type="email" name="email" placeholder="Email" required>
                                <input type="password" name="password" placeholder="Password" required>
                                <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                                <button type="submit">Login</button>
                            </form>

                            <form action="../auth/signup.php" method="POST" id="signupForm" style="display: none;">
                                <input type="text" name="first_name" placeholder="First Name" required>
                                <input type="text" name="last_name" placeholder="Last Name" required>
                                <input type="email" name="email" placeholder="Email" required>
                                <input type="password" name="password" placeholder="Password" required>
                                <input type="password" name="conferm_password" placeholder="Confirm Password" required>
                                <input type="tel" name="phone" placeholder="Phone number" required>
                                <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                                <button type="submit">Sign Up</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <script src="../js/header.js">
      
    </script>
</body>
</html>