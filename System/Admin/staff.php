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

$orders = [];

$orderQuery = "
    SELECT o.order_id, 
           u.username, 
           o.order_date, 
           o.total_price,  
           o.order_status, 
           o.canteen,  
           GROUP_CONCAT(oi.item_name SEPARATOR ', ') AS item_names, 
           GROUP_CONCAT(oi.quantity SEPARATOR ', ') AS quantities, 
           order_notes.note AS order_note
    FROM orders o
    JOIN users u ON o.username = u.username
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    LEFT JOIN order_notes ON o.order_id = order_notes.order_id
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
";

$orderResult = $conn->query($orderQuery);
if ($orderResult && $orderResult->num_rows > 0) {
    $orders = $orderResult->fetch_all(MYSQLI_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['order_status'])) {
    $order_id = intval($_POST['order_id']);
    $order_status = $conn->real_escape_string($_POST['order_status']);

    $updateQuery = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $order_status, $order_id);
    $stmt->execute();
    $stmt->close();
    header("Location: staff.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accept_order'], $_POST['order_id'])) {
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
    }

    if (isset($_POST['delete_order'], $_POST['order_id'])) {
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
    }

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

<!-- Dashboard -->
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
                    <td><?= htmlspecialchars($order['order_date']) ?></td>
                    <td>₱<?= htmlspecialchars($order['total_price']) ?></td>
                    <td><?= htmlspecialchars($order['order_status']) ?></td>
                    <td><?= htmlspecialchars($order['item_names']) ?></td>
                    <td><?= htmlspecialchars($order['quantities']) ?></td>
                    <td><?= htmlspecialchars($order['canteen']) ?></td> 
                    <td>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']) ?>">
                            <textarea name="order_note" rows="4"><?= htmlspecialchars($order['order_note']) ?></textarea>
                        </form>
                    </td>
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
                        <button class="view-details-button" onclick="viewOrderDetails(<?= htmlspecialchars($order['order_id']) ?>)">View Details</button>
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
                <td colspan="11">No orders found.</td>
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