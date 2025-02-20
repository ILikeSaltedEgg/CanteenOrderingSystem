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

// Fetch order counts for the dashboard
$statusCounts = [];
$statusQuery = "
    SELECT order_status, COUNT(*) AS count
    FROM orders
    GROUP BY order_status
";
$statusResult = $conn->query($statusQuery);
if ($statusResult && $statusResult->num_rows > 0) {
    while ($row = $statusResult->fetch_assoc()) {
        $statusCounts[$row['order_status']] = $row['count'];
    }
}

// Fetch all orders
$orders = [];
$orderQuery = "
    SELECT o.order_id, 
           u.username, 
           u.section, 
           u.track,
           o.order_date, 
           o.total_price,  
           o.order_status, 
           o.canteen,  
           o.note,  -- Fetch the note from the orders table
           GROUP_CONCAT(oi.item_name SEPARATOR ', ') AS item_names, 
           GROUP_CONCAT(oi.quantity SEPARATOR ', ') AS quantities
    FROM orders o
    JOIN users u ON o.username = u.username
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
";

$orderResult = $conn->query($orderQuery);
if ($orderResult && $orderResult->num_rows > 0) {
    $orders = $orderResult->fetch_all(MYSQLI_ASSOC);
}

// Fetch all users
$users_query = "SELECT * FROM users";
$users_result = $conn->query($users_query);

// Fetch dashboard statistics
$total_users_query = "SELECT COUNT(*) AS total_users FROM users";
$total_users = $conn->query($total_users_query)->fetch_assoc()['total_users'];

$online_users_query = "SELECT COUNT(*) AS online_users FROM users WHERE last_activity > NOW() - INTERVAL 5 MINUTE";
$online_users = $conn->query($online_users_query)->fetch_assoc()['online_users'];

$total_orders_query = "SELECT COUNT(*) AS total_orders FROM orders";
$total_orders = $conn->query($total_orders_query)->fetch_assoc()['total_orders'];

// Handle order status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['order_status'])) {
    $order_id = intval($_POST['order_id']);
    $order_status = $conn->real_escape_string($_POST['order_status']);

    $updateQuery = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $order_status, $order_id);
    $stmt->execute();
    $stmt->close();
    header("Location: super_admin.php");
    exit();
}

// Handle order deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'], $_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    $deleteQuery = "DELETE FROM orders WHERE order_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $order_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Order #{$order_id} has been deleted.";
    } else {
        $_SESSION['message'] = "Failed to delete order #{$order_id}.";
    }
    $stmt->close();
    header("Location: super_admin.php");
    exit();
}

// Handle user validation
if (isset($_POST['validate_users'])) {
    $validate_query = "UPDATE users SET school_valid = 1 WHERE email LIKE '%@arellano.edu%'";
    $conn->query($validate_query);
    header("Location: super_admin.php");
    exit();
}

if (isset($_POST['validate_user'])) {
    $user_id = (int) $_POST['user_id'];
    $validate_user_query = "UPDATE users SET school_valid = 1 WHERE id = ?";
    $stmt = $conn->prepare($validate_user_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
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
    <link rel="stylesheet" href="../../Styles/styles_admin.css">
    <style>
        .dashboard {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .dashboard-item {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            flex: 1;
            margin: 0 10px;
        }
        .dashboard-item h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .dashboard-item p {
            margin: 10px 0 0;
            font-size: 24px;
            font-weight: bold;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Super Admin Panel</h1>
        <a href="../../System/logout.php" class="logout">Logout</a>
    </div>

    <!-- Dashboard -->
    <div class="dashboard">
        <div class="dashboard-item">
            <h3>Total Users</h3>
            <p><?= $total_users ?></p>
        </div>
        <div class="dashboard-item">
            <h3>Online Users</h3>
            <p><?= $online_users ?></p>
        </div>
        <div class="dashboard-item">
            <h3>Total Orders</h3>
            <p><?= $total_orders ?></p>
        </div>
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
                        <th>Email</th>
                        <th>User Type</th>
                        <th>Section</th>
                        <th>Track</th>
                        <th>Valid</th>
                        <th>Validate</th>
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
                            <td>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" name="validate_user" class="btn-validate-user">Validate</button>
                                </form>
                            </td>
                            <td><?= htmlspecialchars($user['created_at']) ?></td>
                            <td>
                                <a href="super_admin.php?delete_user_id=<?= $user['id'] ?>" class="btn-delete">Remove</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>

        <section class="order-management">
            <h2>Manage Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Username</th>
                        <th>Section</th>
                        <th>Track</th>
                        <th>Order Date</th>
                        <th>Total Price</th>
                        <th>Order Status</th>
                        <th>Item Names</th>
                        <th>Quantities</th>
                        <th>Canteen</th>
                        <th>Note</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['order_id']) ?></td>
                                <td><?= htmlspecialchars($order['username']) ?></td>
                                <td><?= htmlspecialchars($order['section']) ?></td>
                                <td><?= htmlspecialchars($order['track']) ?></td>
                                <td><?= htmlspecialchars($order['order_date']) ?></td>
                                <td>₱<?= htmlspecialchars($order['total_price']) ?></td>
                                <td><?= htmlspecialchars($order['order_status']) ?></td>
                                <td><?= htmlspecialchars($order['item_names']) ?></td>
                                <td><?= htmlspecialchars($order['quantities']) ?></td>
                                <td><?= htmlspecialchars($order['canteen']) ?></td>
                                <td><?= htmlspecialchars($order['note']) ?></td>
                                <td>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                        <select name="order_status">
                                            <option value="Pending" <?= $order['order_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="In Progress" <?= $order['order_status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                            <option value="Completed" <?= $order['order_status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                            <option value="Cancelled" <?= $order['order_status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                        <button type="submit">Update</button>
                                    </form>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                        <button type="submit" name="delete_order" class="btn-delete">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="12">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>