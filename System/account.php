<?php
session_start();
require 'db_connection.php'; // Ensure this file contains the database connection logic

// Redirect to login if the user is not logged in
if (!isset($_SESSION['usertype'])) {
    header("Location: login.php");
    exit();
}

// Initialize user details
$user = [
    'username' => 'Unknown User',
    'section' => 'Not Set',
    'usertype' => $_SESSION['usertype'] ?? 'user',
];

// Fetch user details from the database
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $query = "SELECT username, section, grade_level, track FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user['username'] = $row['username'];
        $user['section'] = $row['section'];
        $_SESSION['grade_level'] = $row['grade_level'];
        $_SESSION['track'] = $row['track'];
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username'], $_POST['section'], $_POST['email'], $_POST['grade_level'], $_POST['track'])) {
        $username = $_POST['username'];
        $section = $_POST['section'];
        $email = $_POST['email'];
        $grade_level = $_POST['grade_level'];
        $track = $_POST['track'];

        // Update session variables
        $_SESSION['username'] = $username;
        $_SESSION['section'] = $section;
        $_SESSION['email'] = $email;
        $_SESSION['grade_level'] = $grade_level;
        $_SESSION['track'] = $track;

        // Update the database
        $updateQuery = "UPDATE users SET username = ?, section = ?, grade_level = ?, track = ? WHERE email = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sssss", $username, $section, $grade_level, $track, $email);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Profile updated successfully!";
        } else {
            $_SESSION['message'] = "Failed to update profile.";
        }
        $stmt->close();
        header("Location: account.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Dashboard</title>
    <link rel="stylesheet" href="../Styles/dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <a href="<?php echo isset($_SESSION['email']) ? 'research2.php' : 'research1.php'; ?>">
            <img src="https://upload.wikimedia.org/wikipedia/en/thumb/8/8b/Arellano_University_logo.png/200px-Arellano_University_logo.png" alt="Logo" id="logo">
        </a>
        <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
        <h2>Dashboard / Profile</h2>

        <svg id="hamburger" class="Header__toggle-svg" viewBox="0 0 60 40" width="40" height="40">
            <g stroke="#007bff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                <path id="top-line" d="M10,10 L50,10 Z"></path>
                <path id="middle-line" d="M10,20 L50,20 Z"></path>
                <path id="bottom-line" d="M10,30 L50,30 Z"></path>
            </g>
        </svg>

        <nav id="menu-options" class="menu-options">
            <a href="account.php">Account</a>
            <a href="Research2.php">Home</a>
            <a href="">Contact Staff</a>
            <a href="logout.php">Logout</a>
        </nav>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <section class="user-details">
            <h3>User Details</h3>
            <p><strong>Username:</strong><br> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Section:</strong><br> <?php echo htmlspecialchars($user['section']); ?></p>
            <p><strong>Email Address:</strong><br> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
            <p><strong>Grade Level:</strong><br> <?php echo htmlspecialchars($_SESSION['grade_level']); ?></p>
            <p><strong>Track/Strand (for SHS only):</strong><br> <?php echo htmlspecialchars($_SESSION['track']); ?></p>
        </section>

        <section class="time-details">
            <h3>Time Information</h3>
            <p><strong>First Access:</strong><br> 
                <?php echo date('l, d F Y, h:i A', $_SESSION['first_access'] ?? time()); ?>
            </p>
            <p><strong>Last Access:</strong><br> 
                <?php echo date('l, d F Y, h:i A', $_SESSION['last_access'] ?? time()); ?>
            </p>
        </section>

        <?php if ($_SESSION['usertype'] === 'user' || $_SESSION['usertype'] === 'admin'): ?>
            <section class="edit-profile">
                <h3>Edit Profile</h3>
                <form method="POST" action="account.php">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

                    <label for="section">Section:</label>
                    <input type="text" id="section" name="section" value="<?php echo htmlspecialchars($user['section']); ?>" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" required>
                    
                    <label for="grade_level">Grade Level:</label>
                    <input type="text" id="grade_level" name="grade_level" value="<?php echo htmlspecialchars($_SESSION['grade_level']); ?>" required>
                    
                    <label for="track">Track/Strand:</label>
                    <input type="text" id="track" name="track" value="<?php echo htmlspecialchars($_SESSION['track']); ?>" required>

                    <button type="submit">Update Profile</button>
                </form>
            </section>
        <?php endif; ?>

        <section class="privacy-policy">
            <h3>Privacy and Policies</h3>
            <p>Data retention summary</p>
        </section>
    </div>

    <script>
        document.getElementById("hamburger").addEventListener("click", function() {
            document.getElementById("menu-options").classList.toggle("active");
        });

        const hamburgerMenu = document.getElementById("hamburger");
        const menuOptions = document.getElementById("menu-options");

        hamburgerMenu.addEventListener("click", () => {
            menuOptions.classList.toggle("active");

            const [topLine, middleLine, bottomLine] = [
                document.getElementById("top-line"),
                document.getElementById("middle-line"),
                document.getElementById("bottom-line")
            ];

            if (menuOptions.classList.contains("active")) {
                topLine.style.transform = "translateY(10px) rotate(45deg)";
                middleLine.style.opacity = "0";
                bottomLine.style.transform = "translateY(-10px) rotate(-45deg)";
            } else {
                topLine.style.transform = "translateY(0) rotate(0)";
                middleLine.style.opacity = "1";
                bottomLine.style.transform = "translateY(0) rotate(0)";
            }
        });

        // Event listener to close the hamburger menu when clicking outside
        document.addEventListener("click", (e) => {
            if (!hamburgerMenu.contains(e.target) && !menuOptions.contains(e.target)) {
                menuOptions.classList.remove("active");

                document.getElementById("top-line").style.transform = "translateY(0) rotate(0)";
                document.getElementById("middle-line").style.opacity = "1";
                document.getElementById("bottom-line").style.transform = "translateY(0) rotate(0)";
            }
        });
    </script>
</body>
</html>