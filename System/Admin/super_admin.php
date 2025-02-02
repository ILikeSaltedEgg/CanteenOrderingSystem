<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'super_admin') {
    echo "Redirecting to login...";
    header("Location: login.php");
    exit();
}

$orders_query = "SELECT o.order_id, u.username, o.total_price, o.order_date 
                 FROM orders o 
                 JOIN users u ON o.username = u.username 
                 ORDER BY o.order_date DESC"; 
$orders_result = mysqli_query($conn, $orders_query);

if (!$orders_result) {
    die("Query failed: " . mysqli_error($conn));
}

$users_query = "SELECT * FROM users";
$users_result = mysqli_query($conn, $users_query);

if (!$users_result) {
    die("Query failed: " . mysqli_error($conn));
}

if (isset($_POST['validate_users'])) {
    $validate_query = "UPDATE users SET school_valid = CASE
                       WHEN username LIKE '%@arellano.edu%' THEN 1
                       ELSE 0 END";
    if (!mysqli_query($conn, $validate_query)) {
        die("Validation query failed: " . mysqli_error($conn));
    }
    header("Location: super_admin.php");
    exit();
}

if (isset($_POST['add_order'])) {
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $order_details = mysqli_real_escape_string($conn, $_POST['order_details']);

    $check_valid_query = "SELECT school_valid FROM users WHERE id = '$user_id'";
    $check_valid_result = mysqli_query($conn, $check_valid_query);
    $valid_user = mysqli_fetch_assoc($check_valid_result);

    if ($valid_user['school_valid'] == 0) {
        echo "You must be validated to place an order.";
        exit();
    }

    $add_order_query = "INSERT INTO orders (user_id, order_details) VALUES ('$user_id', '$order_details')";
    if (!mysqli_query($conn, $add_order_query)) {
        die("Add order query failed: " . mysqli_error($conn));
    }
    header("Location: super_admin.php");
    exit();
}

if (isset($_GET['delete_order_id'])) {
    $order_id = (int) $_GET['delete_order_id'];
    $delete_order_query = "DELETE FROM orders WHERE order_id = '$order_id'";
    if (!mysqli_query($conn, $delete_order_query)) {
        die("Delete order query failed: " . mysqli_error($conn));
    }
    header("Location: super_admin.php");
    exit();
}

if (isset($_GET['delete_user_id'])) {
    $user_id = (int) $_GET['delete_user_id'];
    $delete_user_query = "DELETE FROM users WHERE id = '$user_id'";
    if (!mysqli_query($conn, $delete_user_query)) {
        die("Delete user query failed: " . mysqli_error($conn));
    }
    header("Location: super_admin.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Panel</title>
    <link rel="stylesheet" href="styles_admin.css">
</head>
<body>
    <div class="header">
        <h1>Super Admin Panel</h1>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="container">
        <section class="user-management">
            <h2>Manage Users</h2>
            <form method="POST" action="">
                <button type="submit" name="validate_users" class="btn-validate">Validate Users</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>User Type</th>
                        <th>Section</th>
                        <th>Valid</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($users_result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['usertype']); ?></td>
                            <td><?php echo htmlspecialchars($user['section']); ?></td>
                            <td><?php echo $user['school_valid'] ? 'Yes' : 'No'; ?></td>
                            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                            <td>
                                <a href="super_admin.php?delete_user_id=<?php echo $user['id']; ?>" class="btn-delete">Remove</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>

        <section class="order-management">
            <h2>Manage Orders</h2>
            <form method="POST" action="">
                <label for="user_id">User ID:</label>
                <input type="number" name="user_id" id="user_id" required>
                <label for="order_details">Order Details:</label>
                <textarea name="order_details" id="order_details" rows="4" required></textarea>
                <button type="submit" name="add_order" class="btn-add">Add Order</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>User</th>
                        <th>Order Details</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = mysqli_fetch_assoc($orders_result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($order['username']); ?></td>
                            <td><?php echo htmlspecialchars($order['total_price']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td>
                                <a href="super_admin.php?delete_order_id=<?php echo $order['order_id']; ?>" class="btn-delete">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
