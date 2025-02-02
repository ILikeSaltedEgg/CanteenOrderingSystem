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

$orders = [];
$orderQuery = "
    SELECT o.order_id, 
           o.order_name, 
           u.username, 
           o.order_date, 
           o.total_price,  -- Correct column (total_price instead of price)
           o.order_status, 
           GROUP_CONCAT(CONCAT(oi.item_name, ' (', oi.quantity, ')') SEPARATOR ', ') AS food_items, 
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['order_note'])) {
    $order_id = intval($_POST['order_id']);
    $order_note = $conn->real_escape_string($_POST['order_note']);

    $updateNoteQuery = "UPDATE orders SET note = ? WHERE order_id = ?";
    $stmt = $conn->prepare($updateNoteQuery);
    $stmt->bind_param("si", $order_note, $order_id);
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
    <link rel="stylesheet" href="styles3.css">
    <style>
        .food-items-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .food-items-list li {
            margin: 5px 0;
        }
        .view-details-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        .view-details-button:hover {
            background-color: #45a049;
        }

        .accept-button {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
        }

        .accept-button:hover {
            background-color: #45a049;
        }

        .delete-button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .delete-button:hover {
            background-color: #e53935;
        }

    </style>
</head>
<body>

<div class="staff-header">
    <h1>Staff Panel - User Orders</h1>
    <a href="staff.php?logout=true" class="logout-button">Logout</a>
</div>

<?php if (isset($_SESSION['message'])): ?>
    <div class="message">
        <?= htmlspecialchars($_SESSION['message']) ?>
        <?php unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>

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
                <th>Order Name</th>
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
                    <td><?= htmlspecialchars($order['order_date']) ?></td>
                    <td>₱<?= htmlspecialchars($order['total_price']) ?></td>
                    <td><?= htmlspecialchars($order['order_status']) ?></td>
                    <td><?= htmlspecialchars($order['order_name']) ?></td>
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
                <td colspan="8">No orders found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function viewOrderDetails(orderId) { 
        window.location.href = `order_details.php?order_id=${orderId}`;
    }
</script>

</body>
</html>
