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

function logActivity($conn, $user_id, $username, $action) {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, action, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $username, $action, $ip_address, $user_agent);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $pass = trim($_POST["password"]);
    $remember_me = isset($_POST["stay-logged-in"]) ? true : false;

    if (!preg_match("/^[a-zA-Z0-9._%+-]+@aujrc\.shs\.2024$/", $email)) {
        echo "<script>alert('Invalid email format.'); window.location.href='Login.php';</script>";
        exit();
    }

    if (empty($email) || empty($pass)) {
        echo "<script>alert('Both email and password are required!'); window.location.href='Login.php';</script>";
        exit();
    }

    $stmt = $conn->prepare("SELECT id, username, password, usertype FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];
        $usertype = $row['usertype'];
        $user_id = $row['id'];
        $username = $row['username'];

        if (password_verify($pass, $hashed_password)) {
            $_SESSION["email"] = $email;
            $_SESSION["usertype"] = $usertype;
            $_SESSION["username"] = $username;
            $_SESSION["user_id"] = $user_id;

            logActivity($conn, $user_id, $username, "User logged in");

            if ($remember_me) {
                $cookie_data = json_encode([
                    'email' => $email,
                    'hashed_password' => password_hash($pass, PASSWORD_DEFAULT)
                ]);
                setcookie('remember_me', $cookie_data, time() + (86400 * 30), "/");
            }

            // Redirect to 2FA verification for super_admin and staff
            if ($usertype == "super_admin" || $usertype == "staff") {

                $_SESSION['2fa_code'] = "123456"; // Static code for verification
                $_SESSION['2fa_user'] = $username;

                // Redirect to verify_2fa.php
                header("Location: verify_2fa.php");
                exit();
            } else {
                echo "<script>alert('Welcome, User!'); window.location.href='index.php';</script>";
            }
        } else {
            logActivity($conn, $user_id, $username, "Failed login attempt");
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
        function preFillEmail() {
            const email = localStorage.getItem('userEmail');
            if (email) {
                document.getElementById('email').value = email;
            }
        }

        function saveEmail() {
            const email = document.getElementById('email').value;
            if (email) {
                localStorage.setItem('userEmail', email);
            }
        }

        window.onload = preFillEmail;
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
            <form action="login.php" method="POST" onsubmit="saveEmail()">
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