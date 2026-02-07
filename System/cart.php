<?php
session_start();
include 'db_connection.php';

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding items to cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['food_name'], $_POST['food_price'], $_POST['food_quantity'])) {
    $food_name = $_POST['food_name'];
    $food_price = floatval($_POST['food_price']);
    $food_quantity = intval($_POST['food_quantity']);

    // Check if item is already in cart
    $item_found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['name'] == $food_name) {
            $item['quantity'] += $food_quantity; // Update quantity
            $item_found = true;
            break;
        }
    }

    // If item not found, add new item
    if (!$item_found) {
        $_SESSION['cart'][] = [
            'name' => $food_name,
            'price' => $food_price,
            'quantity' => $food_quantity
        ];
    }
}

// Handle remove item from cart
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    unset($_SESSION['cart'][$remove_id]);
}

// Fetch cart items from session
$cart_items = $_SESSION['cart'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="../../Styles/styles.css">
</head>
<body>
    
    <?php include 'header.php'; ?>

    <main>
        <div class="container">
            <h2>Your Cart</h2>
            <?php if (empty($cart_items)): ?>
                <p>Your cart is empty.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($cart_items as $id => $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            <td><a href="cart.php?remove=<?php echo $id; ?>" class="btn">Remove</a></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <a href="checkout.php" class="btn">Proceed to Checkout</a>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include 'footer.php'; ?>
    
</body>
</html>
