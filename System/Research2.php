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

<header class="top-header">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/8/8b/Arellano_University_logo.png/200px-Arellano_University_logo.png" alt="Logo" id="logo">
        <h1>Arellano University Jose Rizal Campus</h1>
        <h2>Online Canteen</h2>
        <div id="auth-container">
        <?php 
        if (isset($_SESSION['email'])): 
            $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; 
        ?>
            <span id="user-name">
                Welcome, <span id="user-display-name"><?= htmlspecialchars($username) ?></span>
            </span>
        <?php else: ?>
            <span id="user-name">Welcome, Guest!</span>
        <?php endif; ?>
        </div>


        <svg id="hamburger" class="Header__toggle-svg" viewBox="0 0 60 40" width="40" height="40">
            <g stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                <path id="top-line" d="M10,10 L50,10 Z"></path>
                <path id="middle-line" d="M10,20 L50,20 Z"></path>
                <path id="bottom-line" d="M10,30 L50,30 Z"></path>
            </g>
        </svg>

        <nav id="menu-options" class="menu-options">
            <?php
            if (isset($_SESSION['email'])) {
                echo '<a href="account.php">Account</a>';
            } else {
                echo '<a href="login.php">Login</a>';
            }
            ?>

            <?php
            if (isset($_SESSION['email'])) {
                echo '<a href="Research2.php">Home</a>';
            } else {
                echo '<a href="Research1.php">Home</a>';
                
            }
            ?>

            <a href="../System/staff_contact.php">Contact Staff</a>
            <a href="../System/logout.php" class="logout-button">Logout</a>
        </nav>
    </header>
    

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

        <section id="container">
            <div id="menu-items">

                <!-- Menu items here -->

                <div class="menu-item" data-category="meals" data-canteen="Canteen 1">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c9/Adobo_DSCF4391.jpg/1280px-Adobo_DSCF4391.jpg" alt="Chicken Adobo">
                    <h3>Chicken Adobo with Rice</h3>
                    <p>Price: ₱150</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <?php
                        $itemName = "Chicken Adobo";
                        $stock = $stockQuantities[$itemName] ?? 0;
                        if ($stock > 0) {
                            echo '<button class="add-to-cart" 
                                onclick="addToCart(\'' . $itemName . '\', 150, this)">
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
            
                <div class="menu-item" data-category="meals" data-canteen="Canteen 1">
                <img src="https://www.pinoycookingrecipes.com/uploads/7/6/7/8/7678114/watermark-2019-05-06-09-10-22_orig.jpg" alt="Beef Tapa with Rice">
                    <h3>Beef Tapa with Rice</h3>
                    <p>Price: ₱60</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <?php
                        $itemName = "Beef Tapa with Rice";
                        $stock = $stockQuantities[$itemName] ?? 0;
                        if ($stock > 0) {
                            echo '<button class="add-to-cart" 
                                onclick="addToCart(\'' . $itemName . '\', 150, this)">
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

                <div class="menu-item" data-category="snacks" data-canteen="Canteen 1">
                    <img src="https://insanelygoodrecipes.com/wp-content/uploads/2020/05/Burger-with-cheese.jpg" alt="Burger">
                    <h3>Burger</h3>
                    <p>Price: ₱30</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('Burger', 30, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>

                <div class="menu-item" data-category="snacks" data-canteen="Canteen 2">
                    <img src="https://insanelygoodrecipes.com/wp-content/uploads/2020/05/Burger-with-cheese.jpg" alt="Burger">
                    <h3>Burger</h3>
                    <p>Price: ₱30</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('Burger', 30, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>

                <div class="menu-item" data-category="snacks" data-canteen="Canteen 2">
                    <img src="https://luckyme.ph/img/products/webp/2023_0418_LuckyMe_EHC_75g_Mockup_1stPass.webp" alt="Pancit Canton">
                    <h3>Pancit Canton</h3>
                    <p>Price: ₱25</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('Pancit Canton', 25, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>

                <div class="menu-item" data-category="drinks" data-canteen="Canteen 1">
                    <img src="https://drinkoptimum.com/wp-content/uploads/water-bottle-cost-optimum.jpg" alt="Water">
                    <h3>Water</h3>
                    <p>Price: ₱15</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('Water', 15, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>

                <div class="menu-item" data-category="drinks" data-canteen="Canteen 1">
                    <img src="https://libertycokedelivery.com/cdn/shop/products/Sprite_1L_1024x1024@2x.jpg?v=1666116206" alt="Sprite">
                    <h3>Sprite</h3>
                    <p>Price: ₱25</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('Sprite', 25, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>

                <div class="menu-item" data-category="drinks" data-canteen="Canteen 1">
                    <img src="https://primomart.ph/cdn/shop/products/image_02f3116b-d29d-4ef6-9e6c-cf1ac67446fe_1024x1024.jpg?v=1589209450" alt="Sprite">
                    <h3>Coke</h3>
                    <p>Price: ₱25</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('Coke', 25, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>

                <div class="menu-item" data-category="drinks" data-canteen="Canteen 1">
                    <img src="https://shopmetro.ph/itpark-supermarket/wp-content/uploads/2021/03/SM9378636-11.jpg" alt="C2">
                    <h3>C2</h3>
                    <p>Price: ₱25</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('C2', 25, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>

                <div class="menu-item" data-category="drinks" data-canteen="Canteen 2">
                    <img src="https://shopmetro.ph/itpark-supermarket/wp-content/uploads/2021/03/SM9378636-11.jpg" alt="C2">
                    <h3>C2</h3>
                    <p>Price: ₱25</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('C2', 25, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>

                <div class="menu-item" data-category="drinks" data-canteen="Canteen 3">
                    <img src="https://shopmetro.ph/itpark-supermarket/wp-content/uploads/2021/03/SM9378636-11.jpg" alt="C2">
                    <h3>C2</h3>
                    <p>Price: ₱25</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('C2', 25, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>

                <div class="menu-item" data-category="snacks" data-canteen="Canteen 1">
                    <img src="https://www.kawalingpinoy.com/wp-content/uploads/2019/07/kwek-kwek-14-730x973.jpg" alt="kwekkwek">
                    <h3>Kwek-kwek</h3>
                    <p>Price: ₱15</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('Kwek-kwek', 15, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>

                <div class="menu-item" data-category="snacks" data-canteen="Canteen 1">
                    <img src="https://mygroceryph.com/pub/media/catalog/product/cache/942fb7ebd8f124d7d8ad39312cbc983a/p/i/piattos_roadhouse_barbecue_flavor_40g.jpg" alt="piattos">
                    <h3>Piattos BBQ Flavour</h3>
                    <p>Price: ₱20</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('Piattos BBQ Flavour', 20, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>

                <div class="menu-item" data-category="snacks" data-canteen="Canteen 2">
                    <img src="https://mygroceryph.com/pub/media/catalog/product/cache/942fb7ebd8f124d7d8ad39312cbc983a/p/i/piattos_roadhouse_barbecue_flavor_40g.jpg" alt="piattos">
                    <h3>Piattos BBQ Flavour</h3>
                    <p>Price: ₱20</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('Piattos BBQ Flavour', 20, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>

                <div class="menu-item" data-category="snacks" data-canteen="Canteen 3">
                    <img src="https://mygroceryph.com/pub/media/catalog/product/cache/942fb7ebd8f124d7d8ad39312cbc983a/p/i/piattos_roadhouse_barbecue_flavor_40g.jpg" alt="piattos">
                    <h3>Piattos BBQ Flavour</h3>
                    <p>Price: ₱20</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('Piattos BBQ Flavour', 20, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>

                <div class="menu-item" data-category="meals" data-canteen="Canteen 1">
                    <img src="https://images.summitmedia-digital.com/spotph/images/2023/04/03/2-1680515664.jpg" alt="Siomai Rice">
                    <h3>Siomai Rice</h3>
                    <p>Price: ₱40</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('Siomai Rice', 40, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>

                <div class="menu-item" data-category="snacks" data-canteen="Canteen 1">
                    <img src="https://e-saricom.ph/cdn/shop/files/Piatossourcream.jpg?v=1701173327&width=823" alt="piattos">
                    <h3>Piattos Sour Cream Flavour</h3>
                    <p>Price: ₱20</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('Piattos Sour Cream Flavour', 20, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>

                <div class="menu-item" data-category="snacks" data-canteen="Canteen 2">
                    <img src="https://e-saricom.ph/cdn/shop/files/Piatossourcream.jpg?v=1701173327&width=823" alt="piattos">
                    <h3>Piattos Sour Cream Flavour</h3>
                    <p>Price: ₱20</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('Piattos Sour Cream Flavour', 20, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>

                <div class="menu-item" data-category="snacks" data-canteen="Canteen 3">
                    <img src="https://e-saricom.ph/cdn/shop/files/Piatossourcream.jpg?v=1701173327&width=823" alt="piattos">
                    <h3>Piattos Sour Cream Flavour</h3>
                    <p>Price: ₱20</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('Piattos Sour Cream Flavour', 20, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>

                <div class="menu-item" data-category="snacks" data-canteen="Canteen 3">
                    <img src="https://leitesculinaria.com/wp-content/uploads/2021/05/perfect-hot-dog.jpg" alt="hotdog">
                    <h3>Hotdog</h3>
                    <p>Price: ₱25</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('Hotdog', 25, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>

                <div class="menu-item" data-category="snacks" data-canteen="Canteen 1">
                    <img src="https://www.shutterstock.com/image-photo/small-size-french-fries-package-260nw-1557096824.jpg" alt="fries">
                    <h3>French Fries</h3>
                    <p>Price: ₱20</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('French Fries', 20, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>

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
    </main>

    <script src="../JsSystem/script1.js"></script>
    <?php include '../System/include/footer.php'; ?>

        <div id="timer-box" style="display: none;">
            <p>Estimated Time: <span id="timer">00:00</span></p>
        </div>
    </main>
    
</body>

</html>