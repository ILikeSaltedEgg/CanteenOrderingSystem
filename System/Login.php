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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST["username"]);
    $pass = trim($_POST["password"]);

    if (empty($user) || empty($pass)) {
        echo "<script>alert('Both username and password are required!'); window.location.href='Login.php';</script>";
        exit();
    }

    $stmt = $conn->prepare("SELECT password, usertype FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];
        $usertype = $row['usertype'];

        if (password_verify($pass, $hashed_password)) {
            $_SESSION["username"] = $user;
            $_SESSION["usertype"] = $usertype;

            if ($usertype == "super_admin") {
                echo "<script>alert('Welcome, Admin!'); window.location.href='../System/Admin/super_admin.php';</script>";
            } elseif ($usertype == "staff") {
                echo "<script>alert('Welcome, Staff!'); window.location.href='../System/Admin/staff.php';</script>";
            } else {
                echo "<script>alert('Welcome, User!'); window.location.href='Research2.php';</script>";
            }
        } else {
            echo "<script>alert('Incorrect password!'); window.location.href='Login.php';</script>";
        }
    } else {
        echo "<script>alert('Username does not exist!'); window.location.href='Login.php';</script>";
    }

    $stmt->close();
}

if (!isset($_SESSION['first_access'])) {
    $_SESSION['first_access'] = time(); 
}

$_SESSION['last_access'] = time(); 


$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="../Styles/styles1.css">
    <script>
        localStorage.setItem('userName', 'User Name');
    </script>
</head>
<body>

    <div class="top-header">
        <h1>Arellano University Jose Rizal Campus</h1>
        <h2>Online Canteen</h2>
        <a href="<?php echo isset($_SESSION['username']) ? 'research2.php' : 'research1.php'; ?>">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/8/8b/Arellano_University_logo.png/200px-Arellano_University_logo.png" alt="Logo" id="logo">
        </a>
    </div>

    <div id="container">
        <div class="box-content">
            <h2>Login</h2>
            <form action="login.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>

                <label for="section">Section:</label>
                <input type="text" id="section" name="section" placeholder="Enter your section" required>

                <div class="checkbox-container">
                    <input type="checkbox" id="stay-logged-in" name="stay-logged-in">
                    <label for="stay-logged-in">Remember Me</label>
                </div>                

                <button type="submit" class="submit-button">login</button>
                <p style="color: black;">Dont have an Account? <a href="register.php">Register</a></p>
            </form>

        </div>
    </div>

</body>
</html>
