<?php 
session_start();

$host = "localhost";
$username = "root";
$password = "";
$dbname = "au_canteen";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $user = trim($_POST["username"]);
    $pass = trim($_POST["password"]);
    $section = trim($_POST["section"]);

    if (empty($user) || empty($pass) || empty($section)) {
        echo "<script>alert('All fields are required!'); window.location.href='Register.php';</script>";
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Username already exists!'); window.location.href='Register.php';</script>";
        exit();
    }

    $stmt->close();

    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password, section) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user, $hashed_password, $section);

    if ($stmt->execute()) {
        $_SESSION["username"] = $user;
        echo "<script>alert('Registration successful!'); window.location.href='Research2.php';</script>";
    } else {
        echo "<script>alert('Registration failed! Please try again.'); window.location.href='Register.php';</script>";
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>

    <div class="top-header">
        <h1>Arellano University Jose Rizal Campus</h1>
        <h2>Online Canteen</h2>
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/8/8b/Arellano_University_logo.png/200px-Arellano_University_logo.png" alt="Logo" id="logo">
    </div>

    <div id="container">
        <div class="box-content">
            <h2>Sign Up</h2>
            <form action="register.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>

                <label for="section">Section:</label>
                <input type="text" id="section" name="section" placeholder="Enter your section" required>

                <div class="checkbox-container">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">I accept the terms and conditions</label>
                </div> 

                <button type="submit" class="submit-button" name="register">Register</button>
            </form>
            <p style="color: black;">Already have an account? <a href="login.php">Login</a></p>
            <p style="color: black;">Back to <a href="research1.php">Home</a></p>
        </div>
    </div>

</body>
</html>
