<?php session_start();
if (!isset($_SESSION['2fa_user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_code = $_POST['verification_code'];

    if ($entered_code == $_SESSION['2fa_code']) {
        $_SESSION['username'] = $_SESSION['2fa_user'];
        unset($_SESSION['2fa_code'], $_SESSION['2fa_user']); 
        header("Location: super_admin.php");
        exit();
    } else {
        echo "Invalid verification code.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication</title>
</head>
<body>
    <h2>Enter the Verification Code</h2>
    <form method="POST">
        <input type="text" name="verification_code" required placeholder="Enter Code">
        <button type="submit">Verify</button>
    </form>
</body>
</html>
