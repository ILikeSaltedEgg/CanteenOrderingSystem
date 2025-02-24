<?php
session_start();

$host = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "au_canteen";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Redirect to login if the user is not logged in as staff
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'staff') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    session_destroy();
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
    JOIN users u ON o.email = u.email
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
    $order_status = $_POST['order_status'];

    $updateQuery = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $order_status, $order_id);

    if ($stmt->execute()) {
        $_SESSION['toast_message'] = "Order #$order_id status updated to '$order_status'";
        header("Location: staff.php");
        exit();
    } else {
        $_SESSION['toast_message'] = "Failed to update order status.";
    }
}



// Handle 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $updateQuery = "UPDATE orders SET order_status = 'in progress' WHERE order_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();
}

// Handle order acceptance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_order'], $_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    
    $acceptQuery = "UPDATE orders SET order_status = 'In Progress' WHERE order_id = ?";
    $stmt = $conn->prepare($acceptQuery);
    $stmt->bind_param("i", $order_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Order #{$order_id} has been accepted.";
    } else {
        $_SESSION['message'] = "Failed to accept order #{$order_id}.";
    }
    $stmt->close();
    header("Location: staff.php");
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
    header("Location: staff.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Panel</title>
    <link rel="stylesheet" text="text/css" href="../../Styles/styles3.css">
</head>
<body>

<div class="staff-header">
    <h1>Staff Panel - User Orders</h1>
    <a href="../../System/logout.php" class="logout-button">Logout</a>
</div>

<?php if (isset($_SESSION['message'])): ?>
    <div class="message">
        <?= htmlspecialchars($_SESSION['message']) ?>
        <?php unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>

<div class="dashboard">
    <div class="dashboard-item">
        <h3>Pending Orders</h3>
        <p><?= $statusCounts['Pending'] ?? 0 ?></p>
    </div>
    <div class="dashboard-item">
        <h3>In Progress</h3>
        <p><?= $statusCounts['In Progress'] ?? 0 ?></p>
    </div>
    <div class="dashboard-item">
        <h3>Completed Orders</h3>
        <p><?= $statusCounts['Completed'] ?? 0 ?></p>
    </div>
    <div class="dashboard-item">
        <h3>Cancelled Orders</h3>
        <p><?= $statusCounts['Cancelled'] ?? 0 ?></p>
    </div>
</div>

<div class="staff-container">
    <h2>All User Orders</h2>
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
            <th>Accept</th>
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
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']) ?>">
                        <select name="order_status">
                            <option value="Pending" <?= $order['order_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="In Progress" <?= $order['order_status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="Completed" <?= $order['order_status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="Cancelled" <?= $order['order_status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                </td>
                <td>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']) ?>">
                        <button type="submit" name="accept_order" class="accept-button">Accept</button>
                    </form>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']) ?>">
                        <button type="submit" name="delete_order" class="delete-button">Delete</button>
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
</div>

<script>
    function viewOrderDetails(orderId) { 
        window.location.href = "../../System/order_details.php?order_id=" + orderId;
    }
</script>

</body>
</html>