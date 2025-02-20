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
    $totalAmount = $_POST['total_amount'] ?? 0;
    $canteen = $_POST['canteen'] ?? '';
    $note = $_POST['note'] ?? '';

    // Validate required fields
    if (empty($paymentMethod)) {
        $_SESSION['message'] = "Payment method is required.";
        header("Location: research2.php");
        exit();
    }

    if (empty($totalAmount) || $totalAmount <= 0) {
        $_SESSION['message'] = "Invalid total amount.";
        header("Location: research2.php");
        exit();
    }

    // Set timer duration based on payment method
    $timerDuration = match ($paymentMethod) {
        'gcash', 'paymaya' => 10, // 10 minutes
        'cash' => 15, // 15 minutes
        default => 0,
    };

    // Insert the order into the database
    $order_date = date('Y-m-d H:i:s');
    $order_status = 'Pending';
    $email = $_SESSION['email']; // Use email from session

    // Prepare the SQL statement for inserting into the `orders` table
    $stmt = $conn->prepare("INSERT INTO orders (total_price, email, order_status, canteen, order_date, note) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("dsssss", $totalAmount, $email, $order_status, $canteen, $order_date, $note);

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id; // Get the ID of the newly inserted order
        $stmt->close();

        // Insert order items into the `order_items` table
        $cartData = $_POST['cart_items'] ?? '[]'; // Ensure it's a valid JSON array
        $cartItems = json_decode($cartData, true) ?? [];

        if (is_array($cartItems) && !empty($cartItems)) {
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, item_name, price, quantity, total_price) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($cartItems as $item) {
                if (!isset($item['name'], $item['price'], $item['quantity'])) continue;

                $item_name = $item['name'];
                $price = floatval($item['price']);
                $quantity = intval($item['quantity']);
                $total_price = $price * $quantity;

                $stmt->bind_param("isdis", $order_id, $item_name, $price, $quantity, $total_price);
                $stmt->execute();
            }
            $stmt->close();
        }

        // Set session variables for payment confirmation
        $_SESSION['payment_method'] = $paymentMethod;
        $_SESSION['total_amount'] = $totalAmount;
        $_SESSION['timer_duration'] = $timerDuration;
        $_SESSION['canteen'] = $canteen;
        $_SESSION['note'] = $note;

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
