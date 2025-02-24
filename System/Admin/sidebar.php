<?php $current_page = basename($_SERVER['PHP_SELF']); ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<div class="sidebar">
    <div class="sidebar-header">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/8/8b/Arellano_University_logo.png/200px-Arellano_University_logo.png" alt="Logo" id="logo">
        <h2>Admin Panel</h2>
    </div>
    <ul class="sidebar-menu">
        <li><a href="super_admin.php" class="<?= ($current_page == 'super_admin.php') ? 'active' : ''; ?>"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="manage_user.php" class="<?= ($current_page == 'manage_user.php') ? 'active' : ''; ?>"><i class="fas fa-users"></i> Manage Users</a></li>
        <li><a href="manage_order.php" class="<?= ($current_page == 'manage_order.php') ? 'active' : ''; ?>"><i class="fas fa-receipt"></i> Order History</a></li>
        <li><a href="manage_food.php" class="<?= ($current_page == 'manage_food.php') ? 'active' : ''; ?>"><i class="fas fa-utensils"></i> Manage Foods</a></li>
        <!--<li><a href="sales_report.php" class="<?= ($current_page == 'sales_report.php') ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i> Sales Report</a></li> -->
        <li><a href="activity_log.php" class="<?= ($current_page == 'activity_log.php') ? 'active' : ''; ?>"><i class="fas fa-eye"></i> Activity Log</a></li>


    </ul>

    <div class="logout-container">
        <a href="../../System/logout.php" class="logout">
            <div class="sign">
                <svg viewBox="0 0 512 512">
                    <path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path>
                </svg>
            </div>
            <div class="text">Logout</div>
        </a>
    </div>
</div>
