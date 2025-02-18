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
    // Check if the user is logged in
    if (!isset($_SESSION['email'])) {
        header("Location: login.php");
        exit();
    }

    // Retrieve payment method and other data
    $paymentMethod = $_POST['payment_method'] ?? '';
    $cartData = $_POST['cart_items'] ?? '[]'; // Ensure it's a valid JSON array
    $totalAmount = $_POST['total_amount'] ?? 0;
    $canteen = $_POST['canteen'] ?? '';

    // Decode cart items safely
    $cartItems = json_decode($cartData, true);
    if (!is_array($cartItems)) {
        $_SESSION['message'] = "Invalid cart data.";
        header("Location: research2.php");
        exit();
    }

    $totalAmount = floatval($totalAmount);
    $email = $_SESSION['email']; // Use email from session

    // Set timer duration based on payment method
    $timerDuration = match ($paymentMethod) {
        'gcash', 'paymaya' => 10, // 10 minutes
        'cash' => 15, // 15 minutes
        default => 0,
    };

    // Insert the order into the database
    $order_date = date('Y-m-d H:i:s');
    $order_status = 'Pending';

    // Prepare the SQL statement for inserting into the `orders` table
    $stmt = $conn->prepare("INSERT INTO orders (total_price, email, order_status, canteen, order_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("dssss", $totalAmount, $email, $order_status, $canteen, $order_date);

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id; // Get the ID of the newly inserted order
        $stmt->close();

        // Insert order items into the `order_items` table
        foreach ($cartItems as $item) {
            if (!isset($item['name'], $item['price'], $item['quantity'])) {
                continue; // Skip invalid items
            }

            $item_name = $item['name']; // Get the item name from the cart data
            $price = floatval($item['price']);
            $quantity = intval($item['quantity']);
            $total_price = $price * $quantity;

            // Prepare the SQL statement for inserting into the `order_items` table
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, item_name, price, quantity, total_price) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isdis", $order_id, $item_name, $price, $quantity, $total_price);
            $stmt->execute();
            $stmt->close();
        }

        // Set session variables for payment confirmation
        $_SESSION['payment_method'] = $paymentMethod;
        $_SESSION['total_amount'] = $totalAmount;
        $_SESSION['timer_duration'] = $timerDuration;
        $_SESSION['canteen'] = $canteen;

        // Set QR code URL based on payment method
        $_SESSION['qr_code_url'] = match ($paymentMethod) {
            'gcash' => "../assets/images/Gcash-qr.png",
            'paymaya' => "https://example.com/paymaya-qr",
            default => "",
        };

        $_SESSION['message'] = "Order placed successfully! Please scan the QR code to complete the payment.";
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