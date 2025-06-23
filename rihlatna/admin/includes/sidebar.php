<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../styles/sidebar.css">
    <?php if (isset($page_css)) : ?>
        <link rel="stylesheet" href="../styles/<?php echo htmlspecialchars($page_css); ?>">
    <?php endif; ?>
</head>
<body>
    
<div class="sidebar">
  <div class="sidebar-header">
    <h2><img src="../images/admin.svg" alt="Admin" class="sidebar-icon"> Admin Panel</h2>
  </div>
  <ul class="sidebar-menu">
    <li>
      <a href="../pages/dashboard.php">
        <img src="../images/dashboard.svg" alt="Dashboard" class="sidebar-icon">
        <span>Dashboard</span>
      </a>
    </li>
    
  
    <li class="has-submenu">
      <a href="javascript:void(0)" class="submenu-toggle">
        <img src="../images/trips.svg" alt="Trips" class="sidebar-icon">
        <span>Trips Management</span>
        <img src="../images/plus.svg" alt="Toggle" class="toggle-icon">
      </a>
      <ul class="submenu">
        <li>
          <a href="../trips/trips.php">
            <img src="../images/view.svg" alt="List" class="sidebar-icon">
            <span>View All Trips</span>
          </a>
        </li>
        <li>
          <a href="../trips/add_trip.php">
            <img src="../images/add.svg" alt="Add" class="sidebar-icon">
            <span>Add New Trip</span>
          </a>
        </li>
      </ul>
    </li>
    

    <li class="has-submenu">
      <a href="javascript:void(0)" class="submenu-toggle">
        <img src="../images/team.svg" alt="Team" class="sidebar-icon">
        <span>Team Management</span>
        <img src="../images/plus.svg" alt="Toggle" class="toggle-icon">
      </a>
      <ul class="submenu">
        <li>
          <a href="../team/team.php">
            <img src="../images/view.svg" alt="List" class="sidebar-icon">
            <span>View All Team</span>
          </a>
        </li>
        <li>
          <a href="../team/add_admin.php">
            <img src="../images/user_plus.svg" alt="Add User" class="sidebar-icon">
            <span>Add New Admin</span>
          </a>
        </li>
      </ul>
    </li>



     <li>
      <a href="../pages/users.php">
        <img src="../images/users.svg" alt="Reservations" class="sidebar-icon">
        <span>Users Management</span>
      </a>
    </li>
    
    <li>
      <a href="../pages/reservations.php">
        <img src="../images/reservations.svg" alt="Reservations" class="sidebar-icon">
        <span>Reservations</span>
      </a>
    </li>
  </ul>
  <div class="sidebar-footer">
    <a href="../pages/logout.php">
      <img src="../images/logout.svg" alt="Logout" class="sidebar-icon">
      <span>Logout</span>
    </a>
  </div>
</div>

<script>

document.querySelectorAll('.submenu-toggle').forEach(toggle => {
    toggle.addEventListener('click', function() {
        const submenu = this.parentElement.querySelector('.submenu');
        const icon = this.querySelector('.toggle-icon');
        submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
        if (submenu.style.display === 'block') {
            icon.src = icon.src.replace('plus.svg', 'minus.svg');
        } else {
            icon.src = icon.src.replace('minus.svg', 'plus.svg');
        }
    });
});
</script>