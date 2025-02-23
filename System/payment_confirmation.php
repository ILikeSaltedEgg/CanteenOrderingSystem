<?php
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Retrieve session variables
$paymentMethod = $_SESSION['payment_method'] ?? '';
$totalAmount = $_SESSION['total_amount'] ?? 0;
$timerDuration = $_SESSION['timer_duration'] ?? 0;
$qrCodeUrl = $_SESSION['qr_code_url'] ?? '';
$message = $_SESSION['message'] ?? '';
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

// Handle form submission (if a note is added)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['note'])) {
    $note = trim($_POST['note']);
    $email = $_SESSION['email'];

    // Insert the note into the database (if needed)
    if (!empty($note)) {
        $stmt = $conn->prepare("INSERT INTO order_notes (order_id, note) VALUES (?, ?)");
        $stmt->bind_param("is", $_SESSION['order_id'], $note);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect to the staff page
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
        <h3>Note: We will re-order the food you order if you do not get the food that you order in 30 minutes</h3>
        <p>Total Amount: ₱<?= number_format($totalAmount, 2); ?></p>
        <p>Canteen: <?= htmlspecialchars($canteen); ?></p>

        <?php if ($paymentMethod === 'gcash' || $paymentMethod === 'paymaya'): ?>
            <p>Scan the QR code below to complete the payment:</p>
            <img src="<?= htmlspecialchars($qrCodeUrl) ?>" alt="QR Code" style="width: 200px; height: 200px;">
        <?php elseif ($paymentMethod === 'cash'): ?>
            <p>Please proceed to the counter to pay.</p>
        <?php else: ?>
            <p>Invalid payment method.</p>
        <?php endif; ?>

        <!-- Display order items -->
        <h2>Order Items</h2>
        <ul>
            <?php foreach ($order_items as $item): ?>
                <li>
                    <?= htmlspecialchars($item['item_name']); ?> -
                    <?= htmlspecialchars($item['quantity']); ?>x -
                    ₱<?= number_format($item['price'] * $item['quantity'], 2); ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <p>Download the receipt:</p> <a href="invoice.php" target="_blank">Receipt?</a>

        <p>Buying again? Want to order something else?</p>
        <a href="research2.php">Go Back</a>

        <input type="hidden" id="hidden-order-id" value="<?= $_SESSION['order_id'] ?? '' ?>">

        <script>
            
        function checkOrderStatus() {
        const orderId = document.getElementById('hidden-order-id').value;
        if (!orderId) return;

        fetch('check_order_status.php?order_id=' + orderId)
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    if (data.status === 'Pending') {
                        showToast('Your order is Pending!');
                    } else if (data.status === 'In Progress') {
                        showToast('Your order is now in progress!');
                    } else if (data.status === 'Completed') {
                        showToast('Your order is ready for pickup!');
                    } else if (data.status === 'Cancelled') {
                        showToast('Your order is ready for pickup!');
                    }
                } else {
                    console.error('Error:', data.error);
                }
            })
            .catch(error => console.error('Error checking order status:', error));
        }

        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }


        setInterval(checkOrderStatus, 5000);
        </script>

        <div id="timer-box" class="timer">
            <p id="timer-display"></p>
        </div>
    </div>
</body>
</html>