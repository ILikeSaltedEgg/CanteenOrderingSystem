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

// Debug: Check POST data
echo "<pre>";
print_r($_POST);
echo "</pre>";

// Function to log activities
function logActivity($conn, $user_id, $username, $action) {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, action, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $username, $action, $ip_address, $user_agent);
    $stmt->execute();
    $stmt->close();
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
    $note = $_POST['note'] ?? ''; // Retrieve the note from the form
    $cart_items = json_decode($_POST['cart_items'], true) ?? [];

    // Debug: Check the note value
    echo "Note: " . $note;

    // Validate required fields
    if (empty($paymentMethod)) {
        $_SESSION['message'] = "Payment method is required.";
        header("Location: index.php");
        exit();
    }

    if (empty($totalAmount) || $totalAmount <= 0) {
        $_SESSION['message'] = "Invalid total amount.";
        header("Location: index.php");
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

    // Get user details
    $userQuery = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
    $userQuery->bind_param("s", $email);
    $userQuery->execute();
    $userResult = $userQuery->get_result();
    $userQuery->close();

    if ($userResult->num_rows === 0) {
        $_SESSION['message'] = "User not found.";
        header("Location: index.php");
        exit();
    }

    $user = $userResult->fetch_assoc();
    $user_id = $user['id'];
    $username = $user['username'];
    
    // Insert order into `orders` table
    $stmt = $conn->prepare("INSERT INTO orders (total_price, email, order_status, canteen, order_date, note) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("dsssss", $totalAmount, $email, $order_status, $canteen, $order_date, $note);

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id; // Get the ID of the newly inserted order
        $stmt->close();

        // Log order placement
        logActivity($conn, $user_id, $username, "User placed an order (Order ID: $order_id)");

        // Insert order items into the `order_items` table
        if (is_array($cart_items) && !empty($cart_items)) {
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, item_name, price, quantity, total_price) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($cart_items as $item) {
                if (!isset($item['name'], $item['price'], $item['quantity'])) continue;

                $item_name = $item['name'];
                $price = floatval($item['price']);
                $quantity = intval($item['quantity']);
                $total_price = $price * $quantity;

                $stmt->bind_param("isdis", $order_id, $item_name, $price, $quantity, $total_price);
                $stmt->execute();

                // Log each item added to the order
                logActivity($conn, $user_id, $username, "Added item to order (Order ID: $order_id, Item: $item_name, Quantity: $quantity)");

                // Update stock quantity in `food_inventory`
                $updateStockQuery = "UPDATE food_inventory SET stock_quantity = stock_quantity - ? WHERE item_name = ?";
                $stmtUpdateStock = $conn->prepare($updateStockQuery);
                $stmtUpdateStock->bind_param("is", $item['quantity'], $item['name']);
                $stmtUpdateStock->execute();
                $stmtUpdateStock->close();
            }
            $stmt->close();
        }

        // Set session variables for payment confirmation
        $_SESSION['payment_method'] = $paymentMethod;
        $_SESSION['total_amount'] = $totalAmount;
        $_SESSION['timer_duration'] = $timerDuration;
        $_SESSION['canteen'] = $canteen;
        $_SESSION['note'] = $note; // Store the note in the session
        $_SESSION['order_id'] = $order_id;

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
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>