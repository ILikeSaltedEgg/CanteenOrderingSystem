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

// Handle marking a message as done
if (isset($_GET['mark_as_done']) && is_numeric($_GET['mark_as_done'])) {
    $message_id = intval($_GET['mark_as_done']);
    $stmt = $conn->prepare("UPDATE messages SET status = 'done' WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $stmt->close();
    header("Location: super_admin.php"); // Refresh the page
    exit();
}

// Handle deleting a message
if (isset($_GET['delete_message']) && is_numeric($_GET['delete_message'])) {
    $message_id = intval($_GET['delete_message']);
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $stmt->close();
    header("Location: super_admin.php"); // Refresh the page
    exit();
}

// Fetch messages
$messages = [];
$messageQuery = "SELECT * FROM messages ORDER BY created_at DESC";
$messageResult = $conn->query($messageQuery);

if ($messageResult && $messageResult->num_rows > 0) {
    $messages = $messageResult->fetch_all(MYSQLI_ASSOC);
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
        .messages-container {
            margin: 20px;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            position: relative;
        }
        .message.unread {
            background: #f9f9f9;
        }
        .message.read {
            background: #e9e9e9;
        }
        .message.done {
            background: #d4edda;
        }
        .message-actions {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .message-actions a {
            margin-left: 10px;
            color: #333;
            text-decoration: none;
        }
        .message-actions a:hover {
            color: #007BFF;
        }
        .message-actions .delete-btn {
            color: #dc3545;
        }
        .message-actions .delete-btn:hover {
            color: #c82333;
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

        <div class="messages-container">
            <h2>Messages from Users</h2>
            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="message <?= $message['status'] ?>">
                        <div class="message-actions">
                            <!-- Mark as Done Button -->
                            <a href="super_admin.php?mark_as_done=<?= $message['id'] ?>" title="Mark as Done">
                                <i class="fas fa-check"></i>
                            </a>
                            <!-- Delete Button -->
                            <a href="super_admin.php?delete_message=<?= $message['id'] ?>" class="delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this message?');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                        <p><strong>From:</strong> <?= htmlspecialchars($message['username']) ?></p>
                        <p><strong>Message:</strong> <?= htmlspecialchars($message['message']) ?></p>
                        <p><small>Sent on: <?= $message['created_at'] ?></small></p>
                        <p><small>Status: <?= ucfirst($message['status']) ?></small></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No messages found.</p>
            <?php endif; ?>
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