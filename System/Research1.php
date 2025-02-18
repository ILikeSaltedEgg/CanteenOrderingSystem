<?php
session_start();
require 'db_connection.php';

$is_valid_to_order = false;
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $query = "SELECT school_valid FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $user_data = mysqli_fetch_assoc($result);
        $is_valid_to_order = (bool) $user_data['school_valid'];
    } else {
        die("Error fetching user data: " . mysqli_error($conn));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_items'], $_POST['total_amount'], $_POST['order_note'])) {
    $cart_items = json_decode($_POST['cart_items'], true);
    $total_amount = floatval($_POST['total_amount']);
    $order_note = $conn->real_escape_string($_POST['order_note']);
    $username = $_SESSION['username']; 

    $insertOrderQuery = "INSERT INTO orders (username, total_price, order_status) VALUES (?, ?, 'pending')";
    $stmt = $conn->prepare($insertOrderQuery);
    $stmt->bind_param("sd", $username, $total_amount);
    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;

        $insertOrderItemsQuery = "INSERT INTO order_items (order_id, item_name, price, quantity, canteen) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertOrderItemsQuery);
        foreach ($cart_items as $item) {
            $stmt->bind_param("isdis", $order_id, $item['name'], $item['price'], $item['quantity'], $item['canteen']);
            $stmt->execute();
        }

        if (!empty($order_note)) {
            $insertNoteQuery = "INSERT INTO order_notes (order_id, note) VALUES (?, ?)";
            $stmt = $conn->prepare($insertNoteQuery);
            $stmt->bind_param("is", $order_id, $order_note);
            if (!$stmt->execute()) {
                $_SESSION['message'] = "Failed to add note.";
            }
        }

        $_SESSION['message'] = "Order placed successfully.";
        $stmt->close();
    } else {
        $_SESSION['message'] = "Failed to place order.";
    }

    header("Location: ./system/staff.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafeteria</title>
    <link rel="stylesheet" type="text/css" href="../Styles/styles.css">
    <script>
        let currentCanteen = "Canteen 1"; // Default to Canteen 1
        let currentCategory = ""; // Track the currently selected category

        // Function to filter items by canteen and category
        function filterItems(canteen, category = "") {
            currentCanteen = canteen; // Update the current canteen
            currentCategory = category; // Update the current category

            const menuItems = document.querySelectorAll(".menu-item");
            menuItems.forEach(item => {
                const itemCanteen = item.getAttribute("data-canteen");
                const itemCategory = item.getAttribute("data-category");

                const matchesCanteen = itemCanteen === currentCanteen;
                const matchesCategory = !currentCategory || itemCategory === currentCategory;

                if (matchesCanteen && matchesCategory) {
                    item.style.display = "block"; // Show items that match both filters
                } else {
                    item.style.display = "none"; // Hide items that don't match
                }
            });
        }

        // Function to add items to the cart
        window.addToCart = (itemName, itemPrice, button) => {
            const quantityInput = button.parentElement.querySelector(".quantity-input");
            const quantity = parseInt(quantityInput.value);
            const canteen = button.closest(".menu-item").getAttribute("data-canteen"); // Get the canteen

            if (!itemName || isNaN(itemPrice) || isNaN(quantity) || quantity <= 0) {
                showToast("Invalid item or quantity. Please try again.");
                return;
            }

            const existingItem = cart.find(item => item.name === itemName && item.canteen === canteen);

            if (existingItem) {
                existingItem.quantity += quantity;
            } else {
                cart.push({ name: itemName, price: itemPrice, quantity, canteen });
            }

            totalAmount += itemPrice * quantity;
            updateCartDisplay();
            showToast(`${itemName} added to cart.`);
        };

        // Function to update the cart display
        const updateCartDisplay = () => {
            const cartItemsContainer = document.getElementById("cart-items");
            const cartTotalDisplay = document.getElementById("cart-total");

            if (cart.length === 0) {
                cartItemsContainer.innerHTML = "<p>Your cart is empty.</p>";
                cartTotalDisplay.textContent = "Total: ₱0.00";
                return;
            }

            cartItemsContainer.innerHTML = cart.map((item, index) => `
                <li data-name="${item.name}" data-price="${item.price}" data-quantity="${item.quantity}" data-canteen="${item.canteen}">
                    ${item.name} (${item.canteen}) ${item.quantity}x - ₱${(item.price * item.quantity).toFixed(2)}
                    <button class="remove-item" data-index="${index}">Remove</button>
                </li>
            `).join("");
            cartTotalDisplay.textContent = `Total: ₱${totalAmount.toFixed(2)}`;
        };

        // Function to clear the cart
        window.clearCart = () => {
            cart = [];
            totalAmount = 0;
            updateCartDisplay();
            showToast("Cart cleared.");
        };

        // Function to open the payment modal
        window.openModal = () => {
            if (cart.length === 0) {
                showToast("Your cart is empty. Add items before proceeding to payment.");
                return;
            }

            const cartItems = [];
            const cartList = document.querySelectorAll('#cart-items li');
            cartList.forEach(item => {
                const name = item.getAttribute('data-name');
                const price = parseFloat(item.getAttribute('data-price'));
                const quantity = parseInt(item.getAttribute('data-quantity'));
                const canteen = item.getAttribute('data-canteen');
                cartItems.push({ name, price, quantity, canteen });
            });

            redirectToStaff(cartItems);
        };

        // Function to redirect to staff.php
        function redirectToStaff(cartItems) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'staff.php';

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'cart_items';
            input.value = JSON.stringify(cartItems);

            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }

        // Show Canteen 1 items by default on page load
        document.addEventListener("DOMContentLoaded", () => {
            filterItems("Canteen 1");
        });
    </script>
</head>

<body>
    <?php include("header.php"); ?>

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

            <div class="menu-item" data-category="meals" data-canteen="Canteen 1">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c9/Adobo_DSCF4391.jpg/1280px-Adobo_DSCF4391.jpg" alt="Chicken Adobo">
                    <h3>Chicken Adobo</h3>
                    <p>Price: ₱150</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('Chicken Adobo', 150, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>

                <div class="menu-item" data-category="meals" data-canteen="Canteen 1">
                    <img src="https://www.pinoycookingrecipes.com/uploads/7/6/7/8/7678114/watermark-2019-05-06-09-10-22_orig.jpg" alt="Beef Tapa with Rice">
                    <h3>Beef Tapa with Rice</h3>
                    <p>Price: ₱100</p>
                    <div class="cart-controls">
                        <input type="number" class="quantity-input" value="1" min="1">
                        <button class="add-to-cart" 
                            <?php if (!$is_valid_to_order) echo 'disabled'; ?> 
                            onclick="addToCart('Beef Tapa with Rice', 100, this)">
                            Add to Cart
                        </button>
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

                <div class="menu-item" data-category="snacks" data-canteen="Canteen 1">
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
                <!-- Menu items here -->
            </div>
        </section>

        <section id="cart-container">
            <h3>Your Order</h3>
            <ul id="cart-items"></ul>
            <p id="cart-total">Total: ₱0</p>
            <button id="clear-cart-button" onclick="clearCart()">Clear Cart</button>
            <button id="order-button" onclick="openModal()">Order Now</button>
        </section>

        <div id="payment-modal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close-button" onclick="closeModal()">&times;</span>
                <h2>Complete Your Payment</h2>
                <form id="payment-form" action="process_payment.php" method="POST">
                    <!-- Payment form content -->
                </form>
            </div>
        </div>
    </main>

    <script src="../JsSystem/script.js"></script>
    <?php include 'footer.php'; ?>
</body>

</html>