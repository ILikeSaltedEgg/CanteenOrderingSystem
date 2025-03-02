<?php
session_start();
require 'db_connection.php';

$is_valid_to_order = false;
$username = "Guest"; 
$user_track = "N/A"; 
$user_section = "N/A"; 

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

// Fetch stock quantities for all items
$stockQuantities = [];
$stockQuery = "SELECT item_name, stock_quantity FROM food_inventory";
$stockResult = $conn->query($stockQuery);
if ($stockResult && $stockResult->num_rows > 0) {
    while ($row = $stockResult->fetch_assoc()) {
        $stockQuantities[$row['item_name']] = $row['stock_quantity'];
    }
}

// Fetch food items from the database
$foodQuery = "SELECT item_name, price, category, canteen, image_path FROM food_inventory";
$foodResult = $conn->query($foodQuery);

if (!$foodResult) {
    die("Error fetching food items: " . $conn->error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_items'], $_POST['total_amount'])) {
    $cart_items = json_decode($_POST['cart_items'], true);
    $total_amount = floatval($_POST['total_amount']);

    echo "<pre>";
    print_r($cart_items);
    echo "</pre>";

    // Insert the order into the database
    $insertOrderQuery = "INSERT INTO orders (email, total_price, order_status, note) VALUES (?, ?, 'pending', ?)";
    $stmt = $conn->prepare($insertOrderQuery);
    $stmt->bind_param("sd", $email, $total_amount);

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;

        // Insert order items into the database
        $insertOrderItemsQuery = "INSERT INTO order_items (order_id, item_name, price, quantity, canteen) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertOrderItemsQuery);

        // Prepare the stock update query
        $updateStockQuery = "UPDATE food_inventory SET stock_quantity = stock_quantity - ? WHERE item_name = ?";
        $stmtUpdateStock = $conn->prepare($updateStockQuery);

        if (!$stmtUpdateStock) {
            die("Error preparing stock update query: " . $conn->error);
        }

        foreach ($cart_items as $item) {
            // Check if the item has a valid quantity
            if (!isset($item['quantity']) || $item['quantity'] <= 0) {
                $_SESSION['message'] = "Invalid quantity for item: " . $item['name'];
                header("Location: research2.php");
                exit();
            }

            // Insert order item
            $stmt->bind_param("isdis", $order_id, $item['name'], $item['price'], $item['quantity'], $item['canteen']);
            $stmt->execute();

            // Updating stock quantity
            $stmtUpdateStock->bind_param("is", $item['quantity'], $item['name']);
            $stmtUpdateStock->execute();
        }

        $_SESSION['message'] = "Order placed successfully.";
        $stmt->close();
        $stmtUpdateStock->close();
    } else {
        $_SESSION['message'] = "Failed to place order.";
    }

    header("Location: research2.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafeteria</title>
    <link rel="stylesheet" type="text/css" href="../Styles/styles2.css">
</head>

<body>

    <input type="hidden" id="hidden-user-name" value="<?= htmlspecialchars($username) ?>">
    <input type="hidden" id="hidden-user-track" value="<?= htmlspecialchars($user_track) ?>">
    <input type="hidden" id="hidden-user-section" value="<?= htmlspecialchars($user_section) ?>">

    <?php include "../System/include/header.php"; ?>
    

    <main>
        <section class="menu-header"></section>

        <section id="Canteen-buttons">
            <button class="Canteen-button" onclick="filterItems('Canteen 1')">Canteen 1</button>
            <button class="Canteen-button" onclick="filterItems('Canteen 2')">Canteen 2</button>
            <button class="Canteen-button" onclick="filterItems('Canteen 3')">Canteen 3</button>
        </section>

        <section id="category-buttons">
            <button class="category-button" onclick="filterItems(currentCanteen, '')">All</button>
            <button class="category-button" onclick="filterItems(currentCanteen, 'meals')">Meals</button>
            <button class="category-button" onclick="filterItems(currentCanteen, 'snacks')">Snacks</button>
            <button class="category-button" onclick="filterItems(currentCanteen, 'drinks')">Drinks</button>
        </section>

        </section>
        <section id="container">
            <div id="menu-items">
            <?php
if ($foodResult->num_rows > 0) {
    while ($row = $foodResult->fetch_assoc()) {
        $itemName = $row['item_name'];
        $stock = $stockQuantities[$itemName] ?? 0; // Get stock quantity for the item
        ?>
        <div class="menu-item" data-category="<?= htmlspecialchars($row['category']) ?>" data-canteen="<?= htmlspecialchars($row['canteen']) ?>">
            <!-- Ensure the image path is correct -->
            <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="<?= htmlspecialchars($row['item_name']) ?>" class="food-image">
            <h3><?= htmlspecialchars($row['item_name']) ?></h3>
            <p>Price: ₱<?= htmlspecialchars($row['price']) ?></p>
            <div class="cart-controls">
                <!-- Plus/Minus Buttons for Quantity -->
                <div class="quantity-controls">
                    <button class="quantity-minus" onclick="updateQuantity(this, -1)">-</button>
                    <input type="text" class="quantity-input" value="1" min="1" readonly>
                    <button class="quantity-plus" onclick="updateQuantity(this, 1)">+</button>
                </div>
                <?php
                if ($stock > 0) {
                    echo '<button class="add-to-cart" 
                        onclick="addToCart(\'' . $itemName . '\', ' . $row['price'] . ', this)">
                        Add to Cart
                        </button>';
                } else {
                    echo '<button class="add-to-cart" disabled>
                        Out of Stock
                        </button>';
                }
                ?>
            </div>
        </div>
        <?php
    }
} else {
    echo "<p>No food items available.</p>";
}
?>
            </div>
        </section>

        <div id="cart-container">
                <h3>Your Order</h3>
            <div id="user-details">
            <?php if (isset($_SESSION['email'])): ?>
                        <p><strong>Name:</strong> <span id="user_name"><?= htmlspecialchars($username) ?></p>
                    <?php else: ?>
                        <p><strong>Name:</strong> <span id="user-name">[User Name]</span></p>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['email'])): ?>
                        <p><strong>Track:</strong> <span id="user_track"><?= htmlspecialchars($user_track) ?></p>
                    <?php else: ?>
                        <p><strong>Track:</strong> <span id="user-track">[User Track]</span></p>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['email'])): ?>
                        <p><strong>Section:</strong> <span id="user_section"><?= htmlspecialchars($user_section) ?></p>
                    <?php else: ?>
                        <p><strong>Section:</strong> <span id="user-section">[User Section]</span></p>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['email'])): ?>
                        <p><strong>Contact:</strong> <span id="user_section"><?= htmlspecialchars($user_contact) ?></p>
                    <?php else: ?>
                        <p><strong>Contact:</strong> <span id="user-section">[User Contact]</span></p>
                    <?php endif; ?>      
            </div>

            <ul id="cart-items"></ul>
            <p id="cart-total">Total: ₱0</p>
            <button id="clear-cart-button" onclick="clearCart()">Clear Cart</button>
            <button id="order-button" onclick="openModal()">Order Now</button>    
        </div>


        <div id="payment-modal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close-button" onclick="closeModal()">&times;</span>
                <h2>Complete Your Payment</h2>
                <!-- Single form that submits all data -->
                <form id="payment-form" action="process_payment.php" method="POST">
                    <div class="form-group">
                        <label for="payment-method">Payment Methods:</label>
                        <div class="payment-options">
                            <label class="radio-container">
                                <input type="radio" name="payment-method" value="gcash" required>
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/52/GCash_logo.svg/1280px-GCash_logo.svg.png" alt="Gcash">
                                <span class="radio-label">GCash</span>
                            </label>
                            <label class="radio-container">
                                <input type="radio" name="payment-method" value="paymaya">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/PayMaya_Logo.png/1200px-PayMaya_Logo.png" alt="PayMaya">
                                <span class="radio-label">PayMaya</span>
                            </label>
                            <label class="radio-container">
                                <input type="radio" name="payment-method" value="cash">
                                <span class="radio-label">Cash</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="total-amount">Total Amount:</label>
                        <input id="total-amount" name="total-amount" type="text" readonly>
                        <input type="hidden" id="total-amount-input" name="total_amount" value="0">
                    </div>

                    <!-- Note field -->
                    <div class="form-group">
                        <label for="note">Note:</label>
                        <textarea id="note" name="note"></textarea>
                    </div>

                     <div class="button-group">
                        <button type="submit">Pay Now</button>
                    </div>
                </form>
            </div>
        </div>
    
<input type="hidden" id="hidden-order-id" value="<?= $_SESSION['order_id'] ?? '' ?>">

<script>
    
    function checkOrderStatus() {
    const orderId = document.getElementById('hidden-order-id').value;
    if (!orderId) {
        console.error('Order ID is missing.');
        return;
    }

    console.log('Checking order status for order ID:', orderId); // Debugging

    fetch('check_order_status.php?order_id=' + orderId)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Order status response:', data); // Debugging
            if (data.status) {
                if (data.status === 'Pending') {
                    showToast('Your order is Pending!');
                } else if (data.status === 'In Progress') {
                    showToast('Your order is now in progress!');
                } else if (data.status === 'Completed') {
                    showToast('Your order is ready for pickup!');
                } else if (data.status === 'Cancelled') {
                    showToast('Your order has been cancelled.');
                }
            } else {
                console.error('Error:', data.error);
            }
        })
        .catch(error => console.error('Error checking order status:', error));
}

function showToast(message) {
    const toastContainer = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    toastContainer.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 100);
    setTimeout(() => { toast.classList.remove('show'); toast.remove(); }, 3000);
}

// Call checkOrderStatus periodically
setInterval(checkOrderStatus, 5000);

</script>
        
    </main>

    
    <script src="../JsSystem/script1.js"></script>
    <?php include '../System/include/footer.php'; ?>

        <div id="timer-box" style="display: none;">
            <p>Estimated Time: <span id="timer">00:00</span></p>
        </div>
    </main>
    
</body>

</html>