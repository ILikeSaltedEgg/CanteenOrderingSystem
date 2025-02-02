let cart = [];

document.addEventListener('DOMContentLoaded', function () {
    const headerContainer = document.querySelector('.top-header');
    const authContainer = document.getElementById("auth-container");

    if (localStorage.getItem("username")) {
        displayLoggedInState();
    } else {
        displayLoginAndRegisterButtons();
    }

    window.addToCart = function (itemName, itemPrice) {
        if (!localStorage.getItem("username")) {
            alert("You need an account to add items to the cart.");
            openBox('register');
            return;
        }

        let existingItem = cart.find(item => item.name === itemName);

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({ name: itemName, price: itemPrice, quantity: 1 });
        }

        updateCartDisplay();
    };

    function updateCartDisplay() {
        const cartItemsContainer = document.getElementById("cart-items");
        const cartTotal = document.getElementById("cart-total");

        if (!cartItemsContainer || !cartTotal) {
            console.error("Cart items container or cart total element is missing in the HTML.");
            return;
        }

        cartItemsContainer.innerHTML = "";
        let total = 0;

        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;

            const listItem = document.createElement("li");
            listItem.textContent = `${item.name} (x${item.quantity}) - ₱${itemTotal.toFixed(2)}`;
            cartItemsContainer.appendChild(listItem);
        });

        cartTotal.textContent = `₱${total.toFixed(2)}`;
    }

    window.clearCart = function () {
        cart = [];
        updateCartDisplay();
    };

    window.placeOrder = function () {
        if (!localStorage.getItem("username")) {
            alert("You need to be logged in to place an order.");
            openBox('register');
            return;
        }

        if (cart.length === 0) {
            alert("Your cart is empty.");
            return;
        }

        alert("Order placed successfully! Staff has been notified.");
        clearCart();
    };

    function displayLoginAndRegisterButtons() {
        authContainer.innerHTML = `
            <button class="login-btn" onclick="location.href='login.php'">Login</button>
            <button class="register-btn" onclick="location.href='register.php'">Register</button>
        `;
    }

    function displayLoggedInState() {
        const username = localStorage.getItem("username");

        authContainer.innerHTML = `
            <span>Welcome, ${username}</span>
            <div id="hamburger-menu">
                <button class="menu-btn">☰</button>
                <div class="menu-dropdown" id="menu-dropdown">
                    <a href="account.html">Account</a>
                    <a href="settings.html">Settings</a>
                    <button onclick="logout()">Logout</button>
                </div>
            </div>
        `;

        const menuBtn = document.querySelector('.menu-btn');
        const menuDropdown = document.getElementById('menu-dropdown');
        
        menuBtn.addEventListener('click', () => {
            menuDropdown.style.display = 
                menuDropdown.style.display === 'block' ? 'none' : 'block';
        });
    }

    window.logout = function () {
        localStorage.removeItem("username");
        alert("You have been logged out.");
        location.reload();
    };
});

function filterItems(category) {
    const menuItems = document.querySelectorAll('.menu-item');

    menuItems.forEach((item) => {
        if (category === 'all' || item.dataset.category === category) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

const filterCategory = (category) => {
    const allItems = document.querySelectorAll('.menu-item');
    allItems.forEach((item) => {
        if (category === 'All' || item.dataset.category === category) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });

    const container = document.getElementById('menu-items');
    if (container) {
        container.style.minHeight = `${container.offsetHeight}px`;
    }
};
