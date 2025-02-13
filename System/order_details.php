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

if (!isset($_GET['order_id'])) {
    header("Location: staff.php");
    exit();
}

$order_id = intval($_GET['order_id']);

$orderQuery = "
    SELECT 
        o.order_id, 
        o.username, 
        o.order_date, 
        o.total_price,
        o.item_name, 
        o.order_status
    FROM 
        orders o
    WHERE 
        o.order_id = ?
";


$stmt = $conn->prepare($orderQuery);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$orderResult = $stmt->get_result();
$order = $orderResult->fetch_assoc();
$stmt->close();

if (!$order) {
    header("Location: staff.php");
    exit();
}

$itemsQuery = "
    SELECT item_name, quantity, price
    FROM order_items
    WHERE order_id = ?
";
$stmt = $conn->prepare($itemsQuery);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$itemsResult = $stmt->get_result();
$items = $itemsResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="styles3.css">
    <style>
        body {
            color: black;
            font-family: Arial, sans-serif; 
        }

        .order-details-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.2); 
            border-radius: 5px;
            font-family: Arial, sans-serif; 
        }

        .order-details-container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: normal;
        }

        .order-details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px; 
        }

        .order-details-table th,
        .order-details-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .order-details-table th {
            background-color: #f2f2f2;
            color: #333;
        }

        .back-button {
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        .back-button a {
            text-decoration: none;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
        }

        .back-button a:hover {
            background-color: #45a049;
        }

    </style>
</head>
<body>

<div class="staff-header">
    <h1>Order Details</h1>
    <a href="staff.php" class="logout-button">Back to Orders</a>
</div>

<div class="order-details-container">
    <h2>Order #<?= htmlspecialchars($order['order_id']) ?></h2>
    <table class="order-details-table">
        <tr>
            <th>Username</th>
            <td><?= htmlspecialchars($order['username']) ?></td>
        </tr>
        <tr>
            <th>Order Date</th>
            <td><?= htmlspecialchars($order['order_date']) ?></td>
        </tr>
        <tr>
            <th>Total Price</th>
            <td>₱<?= htmlspecialchars($order['total_price']) ?></td>
        </tr>
        <tr>
            <th>Order Status</th>
            <td><?= htmlspecialchars($order['order_status']) ?></td>
        </tr>
    </table>

    <h3>Order Items</h3>
    <table class="order-details-table">
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                    <td>₱<?= htmlspecialchars($item['price']) ?></td>
                    <td>₱<?= htmlspecialchars($item['quantity'] * $item['price']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="back-button">
        <a href="staff.php">Back to Orders</a>
    </div>
</div>

</body>
</html>
