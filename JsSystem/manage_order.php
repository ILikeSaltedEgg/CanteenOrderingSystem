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
           o.note,  
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

// Handle order status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['order_status'])) {
    $order_id = intval($_POST['order_id']);
    $order_status = $conn->real_escape_string($_POST['order_status']);

    $updateQuery = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $order_status, $order_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_order.php");
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
    header("Location: manage_order.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="../../Styles/styles_admin.css">
</head>
<body>
    <!-- Sidebar Navigation -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h1>Manage Orders</h1>
        </div>

        <!-- Order Management -->
        <section id="order-management" class="container">
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