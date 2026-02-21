<?php
session_start();

if (!isset($_SESSION['2fa_user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_code = $_POST['verification_code'];

    // Check if the entered code matches the static 2FA code
    if ($entered_code == $_SESSION['2fa_code']) {
        // Clear 2FA session data
        unset($_SESSION['2fa_code'], $_SESSION['2fa_user']);

        // Redirect based on user type
        if ($_SESSION['usertype'] == "super_admin") {
            header("Location: ../System/Admin/super_admin.php");
        } elseif ($_SESSION['usertype'] == "staff") {
            header("Location: ../System/Admin/staff.php");
        }
        exit();
    } else {
        echo "<script>alert('Invalid verification code.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication</title>
    <link rel="stylesheet" type="text/css" href="../Styles/styles1.css">
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
            <h2>Two-Factor Authentication</h2>
            <form method="POST">
                <label for="verification_code">Verification Code:</label>
                <input type="text" id="verification_code" name="verification_code" placeholder="Enter Code" required>
                <button type="submit" class="submit-button">Verify</button>
            </form>
        </div>
    </div>
</body>
</html>