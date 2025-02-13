<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$paymentMethod = $_SESSION['payment_method'] ?? '';
$totalAmount = $_SESSION['total_amount'] ?? 0;
$timerDuration = $_SESSION['timer_duration'] ?? 0;
$qrCodeUrl = $_SESSION['qr_code_url'] ?? '';
$message = $_SESSION['message'] ?? '';
$order_name = $_SESSION['order_name'] ?? 'No order name available';
$canteen = $_SESSION['canteen'] ?? 'Unknown Canteen';
$order_items = $_SESSION['order_items'] ?? [];

// Ensure payment method is valid
if (!in_array($paymentMethod, ['gcash', 'paymaya', 'cash'])) {
    $paymentMethod = '';
}

// Database connection
$host = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "au_canteen";
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Order insertion logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['note'])) {
    $note = trim($_POST['note']);
    $_SESSION['note'] = $note;
    
    $username = $_SESSION['username'];
    $order_status = 'Pending';
    $order_date = date('Y-m-d H:i:s');

    // Insert order into the database
    $stmt = $conn->prepare("INSERT INTO orders (username, total_price, order_status, canteen, order_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdsss", $username, $totalAmount, $order_status, $canteen, $order_date);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insert order items
    foreach ($order_items as $item) {
        if (!isset($item['name'], $item['price'], $item['quantity'])) {
            continue;
        }
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, item_name, price, quantity) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isdi", $order_id, $item['name'], $item['price'], $item['quantity']);
        $stmt->execute();
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
    <title>Payment Confirmation</title>
    <link rel="stylesheet" href="../Styles/styles5.css">
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const timerDisplay = document.getElementById("timer-display");
            let remainingTime = <?= json_encode($timerDuration * 60); ?>;

            if (remainingTime > 0) {
                const interval = setInterval(() => {
                    const minutes = Math.floor(remainingTime / 60);
                    const seconds = remainingTime % 60;
                    timerDisplay.textContent = `Time Remaining: ${minutes}m ${seconds}s`;
                    remainingTime--;

                    if (remainingTime < 0) {
                        clearInterval(interval);
                        alert("Time is up!");
                        timerDisplay.textContent = "Time is up!";
                    }
                }, 1000);
            }
        });
    </script>
</head>
<body>
    <div class="payment-container">
        <h1>Payment Confirmation</h1>
        <p>Order Name: <?= htmlspecialchars($order_name); ?></p>
        <p>Total Amount: ₱<?= number_format($totalAmount, 2); ?></p>
        <p>Download the receipt:</p> <a href="invoice.php" target="_blank">Receipt?</a>

        <?php if ($paymentMethod === 'gcash' || $paymentMethod === 'paymaya'): ?>
            <p>Scan the QR code below to complete the payment:</p>
            <img src="<?= htmlspecialchars($qrCodeUrl) ?>" alt="QR Code" style="width: 200px; height: 200px;">
        <?php elseif ($paymentMethod === 'cash'): ?>
            <p>Please proceed to the counter to pay.</p>
        <?php else: ?>
            <p>Invalid payment method.</p>
        <?php endif; ?>

        <p>Buying again? Want to order something else?</p>
        <a href="research2.php">Go Back</a>

        <div id="timer-box" class="timer">
            <p id="timer-display"></p>
        </div>
    </div>
</body>
</html>
