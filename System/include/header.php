<?php 

include "db_connection.php";
?>

<!DOCTYPE html>
<html lang="en">
<head><link rel="stylesheet" href="../Styles/header.css"></head>

<body>

<header class="top-header">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/8/8b/Arellano_University_logo.png/200px-Arellano_University_logo.png" alt="Logo" id="logo">
        <h1>Arellano University Jose Rizal Campus</h1>
        <h2>Online Canteen</h2>

        <div id="auth-container">
        <?php
        if (isset($_SESSION['username'])) {
            echo '<span id="user-name"><span id="user-display-name">' . htmlspecialchars($_SESSION['username']) . '</span>!</span>';
        } else {
            echo '<span id="user-name">Welcome, Guest!</span>';
            echo '<button id="login-button" onclick="location.href=\'login.php\'">Login</button>';
        }
        ?>
        </div>

        <svg id="hamburger" class="Header__toggle-svg" viewBox="0 0 60 40" width="40" height="40">
            <g stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                <path id="top-line" d="M10,10 L50,10 Z"></path>
                <path id="middle-line" d="M10,20 L50,20 Z"></path>
                <path id="bottom-line" d="M10,30 L50,30 Z"></path>
            </g>
        </svg>

        <nav id="menu-options" class="menu-options">
            <a href="account.php">Account</a>
            <?php
            if (isset($_SESSION['username'])) {
                echo '<a href="Research2.php">Home</a>';
            } else {
                echo '<a href="Research1.php">Home</a>';
            }
            ?>
            <a href="contact.php">Contact Staff</a>
            <a href="">About Us</a>
        </nav>
    </header>

<script src="../JsSystem/header.js"></script>

</body>

</html>