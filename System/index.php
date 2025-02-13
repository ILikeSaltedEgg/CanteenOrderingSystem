<?php if (isset($_GET['message']) && $_GET['message'] === 'logged_out'): ?>
    <div class="logout-message">
        You have been logged out successfully. Please log in again.
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canteen Menu</title>
    <link rel="stylesheet" type="text/css" href="../Styles/styles.css">
</head>
<body>

    <div class="top-header">
        <a href="research1.php">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/8/8b/Arellano_University_logo.png/200px-Arellano_University_logo.png" alt="Logo" id="logo">
        </a>
        <h1>Arellano University Jose Rizal Campus</h1>
        <h2>Online Canteen</h2>
        <div id="auth-container">
        <?php if (isset($_SESSION["usename"])): ?>

            <?php
            if (isset($_SESSION['username'])) {
                echo '<span id="user-name"><span id="user-display-name">' . htmlspecialchars($_SESSION['username']) . '</span>!</span>';
            } else {
                echo '<span id="user-name">Welcome, Guest!</span>';
            }
            ?>
                <!-- Show Logout Button -->
                <a class="Header_toggle-svg" id="hamburger">
                <svg id="hamburger" class="Header__toggle-svg" viewBox="0 0 60 40" width="40" height="40">
                <g stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                    <path id="top-line" d="M10,10 L50,10 Z"></path>
                    <path id="middle-line" d="M10,20 L50,20 Z"></path>
                    <path id="bottom-line" d="M10,30 L50,30 Z"></path>
                </g>
                </svg>
                </a>
            <?php else: ?>
                <!-- Show Login Button -->
                <a href="../Homepage/login.php" id="login-button">
                    <button type="button">Login</button>
                </a>
            <?php endif; ?>
        </div>
    </div> 

    <section id="Canteen-buttons">
        <button class="Canteen-button" onclick="filterItems('Canteen 1')">Canteen 1</button>
        <button class="Canteen-button" onclick="filterItems('Canteen 2')">Canteen 2</button>
        <button class="Canteen-button" onclick="filterItems('Canteen 3')">Canteen 3</button>
        <button class="Canteen-button" onclick="filterItems('all')">All</button>
        </section>

    <div id="category-buttons">
        <button class="category-button" onclick="filterItems('all')">All</button>
        <button class="category-button" onclick="filterItems('meals')">Meals</button>
        <button class="category-button" onclick="filterItems('snacks')">Snacks</button>
        <button class="category-button" onclick="filterItems('drinks')">Drinks</button>
    </div>
    
    <div id="container">
        <div id="menu-items">

            <div class="menu-item" data-category="meals">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c9/Adobo_DSCF4391.jpg/1280px-Adobo_DSCF4391.jpg" alt="Chicken Adobo">
                <h3>Chicken Adobo</h3>
                <p>Price: ₱150</p>
                <button class="add-to-cart" onclick="addToCart('Chicken Adobo', 150)">Add to Cart</button>
            </div>
            <div class="menu-item" data-category="meals">
                <img src="https://www.pinoycookingrecipes.com/uploads/7/6/7/8/7678114/watermark-2019-05-06-09-10-22_orig.jpg" alt="Beef Tapa with Rice">
                <h3>Beef Tapa with Rice</h3>
                <p>Price: ₱100</p>
                <button class="add-to-cart" onclick="addToCart('Beef Tapa with Rice', 100)">Add to Cart</button>
            </div>

            <div class="menu-item" data-category="snacks">
                <img src="https://insanelygoodrecipes.com/wp-content/uploads/2020/05/Burger-with-cheese.jpg" alt="Burger">
                <h3>Burger</h3>
                <p>Price: ₱30</p>
                <button class="add-to-cart" onclick="addToCart('Burger', 30)">Add to Cart</button>
            </div>
            <div class="menu-item" data-category="snacks">
                <img src="https://luckyme.ph/img/products/webp/2023_0418_LuckyMe_EHC_75g_Mockup_1stPass.webp" alt="Pancit Canton">
                <h3>Pancit Canton</h3>
                <p>Price: ₱25</p>
                <button class="add-to-cart" onclick="addToCart('Pancit Canton', 120)">Add to Cart</button>
            </div>

            <div class="menu-item" data-category="drinks">
                <img src="https://drinkoptimum.com/wp-content/uploads/water-bottle-cost-optimum.jpg" alt="Water">
                <h3>Water</h3>
                <p>Price: ₱15</p>
                <button class="add-to-cart" onclick="addToCart('Water', 10)">Add to Cart</button>
            </div>

            <div class="menu-item" data-category="drinks">
                <img src="https://libertycokedelivery.com/cdn/shop/products/Sprite_1L_1024x1024@2x.jpg?v=1666116206" alt="Sprite">
                <h3>Sprite</h3>
                <p>Price: ₱25</p>
                <button class="add-to-cart" onclick="addToCart('Sprite', 25)">Add to Cart</button>
            </div>

            <div class="menu-item" data-category="drinks">
                <img src="https://primomart.ph/cdn/shop/products/image_02f3116b-d29d-4ef6-9e6c-cf1ac67446fe_1024x1024.jpg?v=1589209450" alt="Sprite">
                <h3>Coke</h3>
                <p>Price: ₱25</p>
                <button class="add-to-cart" onclick="addToCart('Coke', 25)">Add to Cart</button>
            </div>

            <div class="menu-item" data-category="snacks">
                <img src="https://www.kawalingpinoy.com/wp-content/uploads/2019/07/kwek-kwek-14-730x973.jpg" alt="kwekkwek">
                <h3>Kwek-kwek</h3>
                <p>Price: ₱20</p>
                <button class="add-to-cart" onclick="addToCart('kwekkwek', 20)">Add to Cart</button>
            </div>

            <div class="menu-item" data-category="snacks">
                <img src="https://mygroceryph.com/pub/media/catalog/product/cache/942fb7ebd8f124d7d8ad39312cbc983a/p/i/piattos_roadhouse_barbecue_flavor_40g.jpg" alt="piattos">
                <h3>Piattos BBQ Flavour</h3>
                <p>Price: ₱20</p>
                <button class="add-to-cart" onclick="addToCart('piattos', 20)">Add to Cart</button>
            </div>

            <div class="menu-item" data-category="snacks">
                <img src="https://e-saricom.ph/cdn/shop/files/Piatossourcream.jpg?v=1701173327&width=823" alt="piattos">
                <h3>Piattos Sour Cream Flavour</h3>
                <p>Price: ₱20</p>
                <button class="add-to-cart" onclick="addToCart('piattos', 20)">Add to Cart</button>
            </div>

            <div class="menu-item" data-category="snacks">
                <img src="https://leitesculinaria.com/wp-content/uploads/2021/05/perfect-hot-dog.jpg" alt="hotdog">
                <h3>Hotdog</h3>
                <p>Price: ₱25</p>
                <button class="add-to-cart" onclick="addToCart('hotdog', 25)">Add to Cart</button>
            </div>

            <div class="menu-item" data-category="snacks">
                <img src="https://www.shutterstock.com/image-photo/small-size-french-fries-package-260nw-1557096824.jpg" alt="fries">
                <h3>French Fries</h3>
                <p>Price: ₱20</p>
                <button class="add-to-cart" onclick="addToCart('fries', 20)">Add to Cart</button>
            </div>
            
        </div>

        <script>
            
            function checkLoginStatus() {
                const userName = localStorage.getItem('userName');
    
                if (userName) {
                
                    document.getElementById('register-btn').style.display = 'none';
                    document.getElementById('login-btn').style.display = 'none';
                    document.getElementById('user-name').style.display = 'inline';
                    document.getElementById('user-display-name').textContent = userName;
                }
            }

        </script>

        <script>
            let isLoggedIn = false;
        
            function handleAddToCart() {
                if (!isLoggedIn) {
                    
                    alert("You cannot order, you must have an account to place an order.");
                } else {
                    
                    alert("Item added to cart!"); 
                }
            }
        </script>
        

        <div id="cart-container">
            <h3>Your Order</h3>
            <ul id="cart-items"></ul>
            <p id="cart-total">Total:</p>
        
            <button id="clear-cart-button" onclick="clearCart()">Clear Cart</button>
            <button id="order-button" onclick="placeOrder()">Order</button>
        </div>

    </div>

    <footer>
    <div class="footer-container">
        <p>&copy; 2025 Arellano University. All Rights Reserved.</p>
        <div class="footer-links">
            <a href="policy.php">Privacy Policy</a> |
            <a href="terms.php">Terms of Service</a> |
            <a href="contact.php">Contact Us</a>
        </div>
        <div class="social-icons">
            <a href="https://www.facebook.com/ace.joshua.calimlim/" target="_blank"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b8/2021_Facebook_icon.svg/1200px-2021_Facebook_icon.svg.png" alt="Facebook"></a>
            <a href="https://x.com/omphukos" target="_blank"><img src="https://static.vecteezy.com/system/resources/previews/031/737/215/non_2x/twitter-new-logo-twitter-icons-new-twitter-logo-x-2023-x-social-media-icon-free-png.png" alt="Twitter"></a>
            <a href="https://www.instagram.com/ace_je_taime/" target="_blank"><img src="https://static.vecteezy.com/system/resources/previews/018/930/415/non_2x/instagram-logo-instagram-icon-transparent-free-png.png" alt="Instagram"></a>
        </div>
        </div>
    </footer>
    
    <script src="../JsSystem/script.js"></script>
</body>
</html>

