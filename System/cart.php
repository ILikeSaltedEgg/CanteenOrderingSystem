<?php
session_start();

include 'db_connection.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: research2.php");
    exit();
}

if (isset($_SESSION['email'])) { 
    $email = $_SESSION['email'];

    // Fetch username and school_valid status
    $query = "SELECT username, track, section, contact_number, school_valid FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $username = $user_data['username']; // Fetch username
        $user_track = $user_data['track']; // Fetch track
        $user_section = $user_data['section']; // Fetch section
        $user_contact = $user_data['contact_number']; // Fetch contact number
        $is_valid_to_order = (bool) $user_data['school_valid']; // Checking if school_valid is 1
    } else {
        die("Error fetching user data: " . mysqli_error($conn));
    }
    $stmt->close();

    // Store the fetched values in session variables
    $_SESSION['username'] = $username;
    $_SESSION['track'] = $user_track;
    $_SESSION['section'] = $user_section;
}

$cart = $_SESSION['cart'];
$totalAmount = $_SESSION['totalAmount'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" type="text/css" href="../Styles/styles2.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .cart-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 28px;
        }

        /* User Details Section */
        #user-details {
            background: #f1f3f5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        #user-details p {
            margin: 5px 0;
            font-size: 16px;
            color: #555;
        }

        #user-details strong {
            color: #333;
        }

        /* Cart Items Section */
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            transition: background-color 0.3s ease;
        }

        .cart-item:hover {
            background-color: #f9f9f9;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            margin-left: 15px;
        }

        .cart-item div {
            flex: 1;
        }

        .cart-item h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        .cart-item p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }

        /* Payment Method Section */
        .payment-method {
            margin-top: 20px;
            padding: 15px;
            background: #f1f3f5;
            border-radius: 8px;
        }

        .payment-method h3 {
            margin: 0 0 15px;
            font-size: 20px;
            color: #333;
        }

        .payment-options {
            display: flex;
            gap: 15px;
        }

        .payment-options label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .payment-options img {
            width: 50px;
            height: 30px;
            object-fit: contain;
        }

        /* Total Section */
        .cart-total {
            text-align: right;
            font-size: 20px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
        }

        .cart-total p {
            margin: 0;
            color: #333;
        }

        /* Buttons */
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 25px;
            background: #007BFF;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background: #0056b3;
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            max-width: 90%;
        }

        .close-button {
            float: right;
            font-size: 24px;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .button-group {
            text-align: right;
        }

        .button-group button {
            padding: 10px 20px;
            background: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .button-group button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <header class="top-header">
        <?php include '../System/include/header.php'; ?>
    </header>

    <main>
        <div class="cart-container">
            <h2>Your Cart</h2>
            <div id="user-details">
                <?php if (isset($_SESSION['email'])): ?>
                    <p><strong>Name:</strong> <?= htmlspecialchars($username) ?></p>
                    <p><strong>Track:</strong> <?= htmlspecialchars($user_track) ?></p>
                    <p><strong>Section:</strong> <?= htmlspecialchars($user_section) ?></p>
                    <p><strong>Contact:</strong> <?= htmlspecialchars($user_contact) ?></p>
                <?php else: ?>
                    <p><strong>Name:</strong> [User Name]</p>
                    <p><strong>Track:</strong> [User Track]</p>
                    <p><strong>Section:</strong> [User Section]</p>
                    <p><strong>Contact:</strong> [User Contact]</p>
                <?php endif; ?>
            </div>
            <?php foreach ($cart as $item): ?>
                <div class="cart-item">
                    <div>
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <p>Canteen: <?= htmlspecialchars($item['canteen']) ?></p>
                        <p>Quantity: <?= htmlspecialchars($item['quantity']) ?></p>
                        <p>Price: ₱<?= htmlspecialchars($item['price']) ?></p>
                    </div>
                    <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                </div>
            <?php endforeach; ?>

            <!-- Payment Method Section -->
            <div class="payment-method">
                <h3>Select Payment Method</h3>
                <div class="payment-options">
                    <label>
                        <input type="radio" name="payment-method" value="gcash" required>
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/52/GCash_logo.svg/1280px-GCash_logo.svg.png" alt="GCash">
                        <span>GCash</span>
                    </label>
                    <label>
                        <input type="radio" name="payment-method" value="paymaya">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/PayMaya_Logo.png/1200px-PayMaya_Logo.png" alt="PayMaya">
                        <span>PayMaya</span>
                    </label>
                    <label>
                        <input type="radio" name="payment-method" value="cash">
                        <span>Cash</span>
                    </label>
                </div>
            </div>

            <!-- Total Section -->
            <div class="cart-total">
                <p>Total: ₱<?= number_format($totalAmount, 2) ?></p>
            </div>

            <!-- Buttons -->
            <div class="btn-container">
                <button class="btn btn-secondary" onclick="window.location.href='index.php'">Continue Shopping</button>
                <button class="btn" onclick="openModal()">Pay Now</button>
            </div>
        </div>

        <!-- Payment Modal -->
        <div id="payment-modal" class="modal">
            <div class="modal-content">
                <span class="close-button" onclick="closeModal()">&times;</span>
                <h2>Complete Your Payment</h2>
                <form id="payment-form" action="process_payment.php" method="POST">
                    <div class="form-group">
                        <label for="total-amount">Total Amount:</label>
                        <input id="total-amount" name="total-amount" type="text" value="₱<?= number_format($totalAmount, 2) ?>" readonly>
                        <input type="hidden" id="total-amount-input" name="total_amount" value="<?= $totalAmount ?>">
                    </div>
                    <div class="form-group">
                        <label for="note">Note:</label>
                        <textarea id="note" name="note" placeholder="Add a note (optional)"></textarea>
                    </div>
                    <div class="button-group">
                        <button type="submit">Pay Now</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Open and close modal functions
        function openModal() {
            document.getElementById('payment-modal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('payment-modal').style.display = 'none';
        }
    </script>
</body>
</html>