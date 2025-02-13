<?php session_start(); 

include "db_connection.php";
?>

<!DOCTYPE html>
<html lang="en">
<head><link rel="stylesheet" href="../Styles/header.css"></head>

<body>

<header class="top-header">
        <a href="<?php echo isset($_SESSION['username']) ? 'research2.php' : 'research1.php'; ?>">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/8/8b/Arellano_University_logo.png/200px-Arellano_University_logo.png" alt="Logo" id="logo">
        </a>
        <h1>Arellano University Jose Rizal Campus</h1>
        <h2>Online Canteen</h2>
        <div id="auth-container">
        <?php
        if (isset($_SESSION['username'])) { 
            echo '<span id="user-name"><span id="user-display-name">' . htmlspecialchars($_SESSION['username']) . '</span>!</span>';
        } else {
            echo '<a href="../System/register.php"><button type="button" id="register-button" class="register-button">Register</button></a>';
        }
        ?>
        </div>

        <?php
        if (isset($_SESSION['username'])) {
            echo '<svg id="hamburger" class="Header__toggle-svg" viewBox="0 0 60 40" width="40" height="40">
                    <g stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                        <path id="top-line" d="M10,10 L50,10 Z"></path>
                        <path id="middle-line" d="M10,20 L50,20 Z"></path>
                        <path id="bottom-line" d="M10,30 L50,30 Z"></path>
                    </g>
                </svg>';
        } else {
            echo '<a href="../System/login.php"><button type="button" id="login-button" class="login-button">Login</button></a>';
            
        }
        ?>



        <nav id="menu-options" class="menu-options">
            <a href="account.php">Account</a>
            <a href="Research2.php">Home</a>
            <a href="contact.php">Contact Staff</a>
            <a href="../System/logout.php" class="logout-button">Logout</a>
        </nav>
</header>

<script src="../JsSystem/header.js"></script>

</body>

</html>