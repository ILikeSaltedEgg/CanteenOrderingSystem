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

// Fetch all users
$users_query = "SELECT * FROM users WHERE usertype != 'super_admin'";
$users_result = $conn->query($users_query);


// Handle account creation
if (isset($_POST['add_account'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $usertype = $_POST['usertype'];
    $section = $_POST['section'];
    $track = $_POST['track'];
    $valid = isset($_POST['valid']) ? 1 : 0;

    // Check if username or email is already taken
    $check_query = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "<script>alert('Error: Username or email is already taken.');</script>";
    } else {
        $add_user_query = "INSERT INTO users (username, email, password, usertype, section, track, school_valid) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($add_user_query);
        $stmt->bind_param("ssssssi", $username, $email, $password, $usertype, $section, $track, $valid);
        $stmt->execute();
        $stmt->close();
        header("Location: manage_user.php");
        exit();
    }
    $stmt->close();
}

// Handle account deletion
if (isset($_GET['delete_user_id'])) {
    $delete_id = $_GET['delete_user_id'];
    $delete_query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<script>alert('User deleted successfully.'); window.location='manage_user.php';</script>";
    } else {
        echo "<script>alert('Error deleting user.');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../../Styles/styles_admin.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h1>Manage Users</h1>
        </div>

        <section id="user-management" class="container">
            <h2>Add Account</h2>
            <form method="POST" action="">
                <label>Username:</label>
                <input type="text" name="username" required>
                
                <label>Email:</label>
                <input type="email" name="email" required>
                
                <label>Password:</label>
                <input type="password" name="password" required>
                
                <label>User Type:</label>
                <select name="usertype">
                    <option value="user">User</option>
                    <option value="staff">Staff</option>
                    <option value="super_admin">Super Admin</option>
                </select>
                
                <label>Section:</label>
                <input type="text" name="section" required>
                
                <label>Track:</label>
                <input type="text" name="track" required>
                
                <label>Valid:</label>
                <input type="checkbox" name="valid">
                
                <button type="submit" name="add_account">Add Account</button>
            </form>

            <h2>Manage Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>User Type</th>
                        <th>Section</th>
                        <th>Track</th>
                        <th>Valid</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['usertype']) ?></td>
                            <td><?= htmlspecialchars($user['section']) ?></td>
                            <td><?= htmlspecialchars($user['track']) ?></td>
                            <td><?= $user['school_valid'] ? 'Yes' : 'No' ?></td>
                            <td><?= htmlspecialchars($user['created_at']) ?></td>
                            <td>
                                <a href="javascript:void(0);" class="btn-delete" onclick="confirmDelete(<?= $user['id'] ?>)">Remove</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>
    </div>

    <script>
    function confirmDelete(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
            window.location.href = 'manage_user.php?delete_user_id=' + userId;
        }
    }
    </script>
    
</body>
</html>
