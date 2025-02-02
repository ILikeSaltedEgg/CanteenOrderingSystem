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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $paymentMethod = $_POST['payment-method'] ?? '';
    $cartData = $_POST['cart'] ?? '';
    $totalAmount = $_POST['total-amount'] ?? 0;

    $cartItems = json_decode($cartData, true);
    $totalAmount = htmlspecialchars($totalAmount);

    $username = $_SESSION['username'] ?? ''; 

    if (empty($username)) {
        header("Location: login.php");
        exit();
    }

    $timerDuration = 0;
    switch ($paymentMethod) {
        case 'gcash':
        case 'paymaya':
            $timerDuration = 10;  
            break;
        case 'cash':
            $timerDuration = 15;  
            break;
        default:
            $timerDuration = 0; 
            break;
    }

    $order_date = date('Y-m-d H:i:s');
    $order_status = 'Pending'; 

    $stmt = $conn->prepare("INSERT INTO orders (order_date, total_price, username, order_status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $order_date, $totalAmount, $username, $order_status);
    if ($stmt->execute()) {
        $order_id = $stmt->insert_id; 
        $stmt->close();

        foreach ($cartItems as $item) {
            $item_name = $item['name'];
            $price = $item['price'];
            $quantity = $item['quantity'];
            $total_price = $price * $quantity;

            $stmt = $conn->prepare("INSERT INTO order_items (order_id, item_name, price, quantity, total_price) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isdis", $order_id, $item_name, $price, $quantity, $total_price);
            $stmt->execute();
            $stmt->close();
        }

        if ($paymentMethod == 'gcash') {
            $qrCodeUrl = "https://example.com/gcash-qr";
        } elseif ($paymentMethod == 'paymaya') {
            $qrCodeUrl = "https://example.com/paymaya-qr";
        } else {
            $qrCodeUrl = "https://example.com/cash-payment";
        }

        $_SESSION['payment_method'] = $paymentMethod; 
        $_SESSION['message'] = "Order placed successfully! Please scan the QR code to complete the payment.";
        $_SESSION['qr_code_url'] = $qrCodeUrl; 

        header("Location: payment_confirmation.php");
        exit();
    } else {
        $_SESSION['message'] = "Failed to place the order. Please try again.";
        header("Location: research2.php"); 
        exit();
    }
} else {
    header("Location: research2.php");
    exit();
}
?>
