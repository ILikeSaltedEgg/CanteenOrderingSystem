<link  rel="stylesheet" href="../../Styles/header.css">

<header>
        <div class="header-container">

            <div class="logo-title-container">
                <img src="https://upload.wikimedia.org/wikipedia/en/thumb/8/8b/Arellano_University_logo.png/200px-Arellano_University_logo.png" alt="Logo" id="logo">
                <h1 class="Title">Arellano Online Canteen</h1>

            </div>

            <nav>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="menu.php">Menu</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>

            <a href="cart.php" id="cart-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2 13h13l2-7H6"></path>
                </svg>
            </a>

            <?php if (isset($_SESSION["user_id"])): ?>
                <a href="user_profile.php" id="profile-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </a>
            <?php endif; ?>

            <a href="../Homepage/register.php" id="register-button">
                <button type="button">Register</button>
            </a>

            <?php if (isset($_SESSION["user_id"])): ?>
                <!-- Show Logout Button -->
                <a href="logout.php" id="logout-button">
                    <button type="button">Logout</button>
                </a>
            <?php else: ?>
                <!-- Show Login Button -->
                <a href="../Homepage/login.php" id="login-button">
                    <button type="button">Login</button>
                </a>
            <?php endif; ?>

        </div>
</header>