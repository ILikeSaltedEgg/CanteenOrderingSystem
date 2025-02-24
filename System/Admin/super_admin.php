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

// Fetch order counts for the pie chart
$statusCounts = [];
$statusQuery = "SELECT order_status, COUNT(*) AS count FROM orders GROUP BY order_status";
$statusResult = $conn->query($statusQuery);
if ($statusResult && $statusResult->num_rows > 0) {
    while ($row = $statusResult->fetch_assoc()) {
        $statusCounts[$row['order_status']] = $row['count'];
    }
}

// Fetch dashboard statistics
$total_users = $conn->query("SELECT COUNT(*) AS total_users FROM users")->fetch_assoc()['total_users'];
$online_users = $conn->query("SELECT COUNT(*) AS online_users FROM users WHERE last_activity > NOW() - INTERVAL 1 MINUTE")->fetch_assoc()['online_users'];
$total_orders = $conn->query("SELECT COUNT(*) AS total_orders FROM orders")->fetch_assoc()['total_orders'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <link rel="stylesheet" href="../../Styles/styles_admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
    .chart-container {
        width: 50%;
        max-width: 400px;
        margin: auto;
    }
    
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h1>Super Admin Dashboard</h1>
        </div>

        <div class="dashboard">
            <div class="dashboard-item">
                <i class="fas fa-users"></i>
                <h3>Total Users</h3>
                <p><?= $total_users ?></p>
            </div>
            <div class="dashboard-item">
                <i class="fas fa-user-check"></i>
                <h3>Online Users</h3>
                <p><?= $online_users ?></p>
            </div>
            <div class="dashboard-item">
                <i class="fas fa-shopping-cart"></i>
                <h3>Total Orders</h3>
                <p><?= $total_orders ?></p>
            </div>
        </div>

        <div class="chart-container">
            <h2>Order Status Breakdown</h2>
            <canvas id="orderChart"></canvas>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('orderChart').getContext('2d');
        const orderChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?= json_encode(array_keys($statusCounts)) ?>,
                datasets: [{
                    data: <?= json_encode(array_values($statusCounts)) ?>,
                    backgroundColor: ['#4CAF50', '#FFC107', '#F44336', '#2196F3', '#9C27B0']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true
            }
        });
    </script>
</body>
</html>
