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
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <div class="top-header">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/8/8b/Arellano_University_logo.png/200px-Arellano_University_logo.png" alt="Logo" id="logo">
        <h1>Arellano University Jose Rizal Campus</h1>
        <div id="auth-container">
            <button class="register-btn" onclick="window.location.href='register.php'" id="register-btn">Register</button>
            <button class="login-btn" onclick="window.location.href='Login.php'" id="login-btn">Login</button>
            <span id="user-name" style="display: none;">Welcome, <span id="user-display-name"></span>!</span>
        </div>
            <span>Welcome</span>
    </div>
    
    <div class="menu-header">
        <h1 style="color: blue;">Canteen Menu</h1>
    
    </div>    

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
    
    <script src="script.js"></script>
</body>
</html>
