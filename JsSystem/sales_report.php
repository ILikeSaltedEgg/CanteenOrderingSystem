<?php
include('../../System/db_connection.php'); // Connects to MySQL Database

// Fetch total revenue from completed orders
$query_revenue = "SELECT SUM(total_price) AS total_revenue FROM orders WHERE order_status = 'Completed'";
$result_revenue = $conn->query($query_revenue);
$row_revenue = $result_revenue->fetch_assoc();
$total_revenue = $row_revenue['total_revenue'] ?? 0;

// Fetch best-selling items
$query_best_sellers = "
    SELECT oi.item_name, SUM(oi.quantity) AS total_quantity 
    FROM order_items oi
    JOIN food_inventory fi ON oi.item_name = fi.item_name 
    GROUP BY oi.item_name 
    ORDER BY total_quantity DESC 
    LIMIT 5
";
$result_best_sellers = $conn->query($query_best_sellers);

// Fetch sales by date
$query_sales_trend = "
    SELECT DATE(order_date) AS order_day, SUM(total_price) AS daily_sales
    FROM orders
    WHERE order_status = 'Completed'
    GROUP BY DATE(order_date)
    ORDER BY order_day ASC
";
$result_sales_trend = $conn->query($query_sales_trend);
$sales_data = [];
while ($row = $result_sales_trend->fetch_assoc()) {
    $sales_data[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../Styles/styles_admin.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="container">
        <h2>Sales Report</h2>
        <h3>Total Revenue: PHP <?= number_format($total_revenue, 2) ?></h3>
        
        <h3>Best-Selling Items</h3>
        <ul>
            <?php while ($row = $result_best_sellers->fetch_assoc()): ?>
                <li><?= htmlspecialchars($row['item_name']) ?> - <?= $row['total_quantity'] ?> sold</li>
            <?php endwhile; ?>
        </ul>
        
        <h3>Sales Trend</h3>
        <canvas id="salesChart"></canvas>
    </div>

    <script>
        let salesData = <?= json_encode($sales_data) ?>;
        let dates = salesData.map(data => data.order_day);
        let revenues = salesData.map(data => data.daily_sales);
        
        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Daily Sales',
                    data: revenues,
                    borderColor: 'blue',
                    fill: false
                }]
            }
        });
    </script>
</body>
</html>
