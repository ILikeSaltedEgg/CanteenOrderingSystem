<?php 
session_start();

if (!isset($_SESSION['usertype'])) {
    header("Location: login.php");
    exit();
}

$user = [
    'username' => $_SESSION['username'] ?? 'Unknown User',
    'section' => $_SESSION['section'] ?? 'Not Set', 
    'usertype' => $_SESSION['usertype'] ?? 'user', 
];


if (!isset($_SESSION['email'])) {
    $_SESSION['email'] = 'sfDwG2024@yahoo.com';
    $_SESSION['country'] = 'Philippines';
    $_SESSION['grade_level'] = 'Grade 1E';
    $_SESSION['track'] = 'TECH-VOC/ICT';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && isset($_POST['country']) && isset($_POST['grade_level']) && isset($_POST['track'])) {
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['country'] = $_POST['country'];
        $_SESSION['grade_level'] = $_POST['grade_level'];
        $_SESSION['track'] = $_POST['track'];

        $_SESSION['message'] = "Profile updated successfully!";
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
        <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
        <h2>Dashboard / Profile</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <section class="user-details">
            <h3>User Details</h3>
            <p><strong>Email Address:</strong><br> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
            <p><strong>Country:</strong><br> <?php echo htmlspecialchars($_SESSION['country']); ?></p>
            <p><strong>Grade Level:</strong><br> <?php echo htmlspecialchars($_SESSION['grade_level']); ?></p>
            <p><strong>Track/Strand (for SHS only):</strong><br> <?php echo htmlspecialchars($_SESSION['track']); ?></p>
        </section>

        <section class="time-details">
        <h3>Time Information</h3>
        <p><strong>First Access:</strong><br> 
            <?php 
                echo date('l, d F Y, h:i A', $_SESSION['first_access'] ?? time()); 
            ?>
        </p>
        <p><strong>Last Access:</strong><br> 
            <?php 
                echo date('l, d F Y, h:i A', $_SESSION['last_access'] ?? time()); 
            ?>
        </p>
        </section>


        <?php if ($_SESSION['usertype'] === 'user' || $_SESSION['usertype'] === 'admin'): ?>
            <section class="edit-profile">
                <h3>Edit Profile</h3>
                <form method="POST" action="account.php">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" required>
                    
                    <label for="country">Country:</label>
                    <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($_SESSION['country']); ?>" required>
                    
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

    <script>
        document.getElementById("hamburger").addEventListener("click", function() {
            document.getElementById("menu-options").classList.toggle("active");
        });
    </script>
</body>
</html>