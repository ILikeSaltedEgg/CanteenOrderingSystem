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
    $email = trim($_POST["email"]);
    $pass = trim($_POST["password"]);

    // Validate email format
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@aujrc\.shs\.2024$/", $email)) {
        echo "<script>alert('Invalid email format. Email must be in the format: example@aujrc.shs.2024'); window.location.href='Login.php';</script>";
        exit();
    }

    if (empty($email) || empty($pass)) {
        echo "<script>alert('Both email and password are required!'); window.location.href='Login.php';</script>";
        exit();
    }

    $stmt = $conn->prepare("SELECT password, usertype FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password']; // Hashed password from the database
        $usertype = $row['usertype'];
    
        if (password_verify($pass, $hashed_password)) { // Verify the password
            $_SESSION["email"] = $email;
            $_SESSION["usertype"] = $usertype;
    
            // Redirect based on usertype
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
        echo "<script>alert('Email does not exist!'); window.location.href='Login.php';</script>";
    }
    
}

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
        localStorage.setItem('userEmail', 'User Email');
    </script>
</head>
<body>

    <div class="top-header">
        <h1>Arellano University Jose Rizal Campus</h1>
        <h2>Online Canteen</h2>
        <a href="<?php echo isset($_SESSION['email']) ? 'research2.php' : 'research1.php'; ?>">
            <img src="https://upload.wikimedia.org/wikipedia/en/thumb/8/8b/Arellano_University_logo.png/200px-Arellano_University_logo.png" alt="Logo" id="logo">
        </a>
    </div>

    <div id="container">
        <div class="box-content">
            <h2>Login</h2>
            <form action="login.php" method="POST">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" placeholder="Enter your Email (example@aujrc.shs.2024)" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>

                <div class="checkbox-container">
                    <input type="checkbox" id="stay-logged-in" name="stay-logged-in">
                    <label for="stay-logged-in">Remember Me</label>
                </div>                

                <button type="submit" class="submit-button">Login</button>
            </form>
        </div>
    </div>

</body>
</html>