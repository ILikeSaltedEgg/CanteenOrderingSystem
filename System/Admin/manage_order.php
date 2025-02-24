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

// Fetch all orders with optional search
$search = isset($_GET['search']) ? $_GET['search'] : '';
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
    JOIN users u ON o.email = u.email
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.order_id LIKE ? OR u.username LIKE ? OR o.order_status LIKE ?
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
";

$stmt = $conn->prepare($orderQuery);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$searchParam = "%$search%";
$stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
$stmt->execute();
$orderResult = $stmt->get_result();

if ($orderResult && $orderResult->num_rows > 0) {
    $orders = $orderResult->fetch_all(MYSQLI_ASSOC);
} else {
    echo "No orders found in the database.<br>";
    echo "SQL Query: $orderQuery<br>";
    echo "Search Term: $search<br>";
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="../../Styles/styles_admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Sidebar Navigation -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">

        <div class="header">
            <h1>Order History</h1>
        </div>
        
        <section class="container">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search by Order ID, Username, or Status" value="<?= htmlspecialchars($search) ?>">
                <button type="submit"><i class="fas fa-search"></i> Search</button>
            </form>
        </section>

        <!-- Order History Table -->
        <section class="container">
            <h2>All Orders</h2>
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
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
<?php $conn->close(); ?>