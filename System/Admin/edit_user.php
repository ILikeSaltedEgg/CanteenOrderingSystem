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

if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'super_admin') {
    echo "Redirecting to login...";
    header("Location: login.php");
    exit();
}

// Fetch user details
if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = intval($_POST['id']);
    $username = $_POST['username'];
    $email = $_POST['email'];
    $usertype = $_POST['usertype'];
    $section = $_POST['section'];
    $track = $_POST['track'];
    $valid = isset($_POST['valid']) ? 1 : 0;

    // Update user details
    $updateQuery = "UPDATE users SET username = ?, email = ?, usertype = ?, section = ?, track = ?, school_valid = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssssii", $username, $email, $usertype, $section, $track, $valid, $userId);
    if ($stmt->execute()) {
        echo "<script>alert('User updated successfully.'); window.location='manage_user.php';</script>";
    } else {
        echo "<script>alert('Error updating user.');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../../Styles/styles_admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h1>Edit User</h1>
        </div>

        <section class="container">
            <h2>Edit User Details</h2>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                
                <label>Username:</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                
                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                
                <label>User Type:</label>
                <select name="usertype">
                    <option value="user" <?= $user['usertype'] === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="staff" <?= $user['usertype'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                    <option value="super_admin" <?= $user['usertype'] === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                </select>
                
                <label>Section:</label>
                <input type="text" name="section" value="<?= htmlspecialchars($user['section']) ?>" required>
                
                <label>Track:</label>
                <input type="text" name="track" value="<?= htmlspecialchars($user['track']) ?>" required>
                
                <label>Valid:</label>
                <input type="checkbox" name="valid" <?= $user['school_valid'] ? 'checked' : '' ?>>
                
                <button type="submit" class="btn-edit">Update User</button>
            </form>
        </section>
    </div>
</body>
</html>
<?php $conn->close(); ?>