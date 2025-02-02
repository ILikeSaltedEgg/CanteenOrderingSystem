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

if (!in_array($paymentMethod, ['gcash', 'paymaya', 'cash'])) {
    $paymentMethod = ''; 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['note'])) {
    $note = trim($_POST['note']);
    $_SESSION['note'] = $note; 
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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: rgb(50,122,163);
            background: linear-gradient(90deg, rgba(50,122,163,1) 21%, rgba(97,132,168,1) 80%);
        }
        .payment-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        .payment-container h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .payment-container p {
            font-size: 16px;
            margin-bottom: 15px;
        }
        .payment-container a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .payment-container a:hover {
            background-color: #0056b3;
        }
        .timer {
            font-size: 18px;
            margin-top: 20px;
            color: #d9534f;
            font-weight: bold;
        }
        .note-section {
            margin-top: 20px;
        }
        .note-section textarea {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }
        .note-section button {
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .note-section button:hover {
            background-color: #218838;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const timerBox = document.getElementById("timer-box");
            const timerDisplay = document.getElementById("timer-display");

            let timerMinutes = <?= json_encode($timerDuration); ?>;

            if (timerMinutes > 0) {
                let remainingTime = timerMinutes * 60; 

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
        <?php if ($paymentMethod === 'gcash'): ?>
            <h1>Pay with GCash</h1>
            <p>Total Amount: ₱<?php echo number_format($totalAmount, 2); ?></p>
            <p>Scan the QR code below using your GCash app to complete the payment:</p>
            <img src="<?= $qrCodeUrl ?>" alt="GCash QR Code" style="width: 200px; height: 200px;">
            <p>Once payment is made, please confirm with the staff.</p>
        <?php elseif ($paymentMethod === 'paymaya'): ?>
            <h1>Pay with PayMaya</h1>
            <p>Total Amount: ₱<?php echo number_format($totalAmount, 2); ?></p>
            <p>Scan the QR code below using your PayMaya app to complete the payment:</p>
            <img src="<?= $qrCodeUrl ?>" alt="PayMaya QR Code" style="width: 200px; height: 200px;">
            <p>Once payment is made, please confirm with the staff.</p>
        <?php elseif ($paymentMethod === 'cash'): ?>
            <h1>Pay with Cash</h1>
            <p>Total Amount: ₱<?php echo number_format($totalAmount, 2); ?></p>
            <p>Please proceed to the counter to pay the amount.</p>
        <?php else: ?>
            <h1>Payment Method Not Found</h1>
            <p>Please go back and select a valid payment method.</p>
            <a href="research2.php">Go Back</a>
        <?php endif; ?>

        <div id="timer-box" class="timer">
            <p id="timer-display"></p>
        </div>

    </div>
</body>
</html>
