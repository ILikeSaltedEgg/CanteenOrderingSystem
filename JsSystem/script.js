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

    const cartItems = [];
    const cartList = document.querySelectorAll('#cart-items li');
    cartList.forEach(item => {
        const name = item.getAttribute('data-name');
        const price = parseFloat(item.getAttribute('data-price'));
        const quantity = parseInt(item.getAttribute('data-quantity'));
        const canteen = item.getAttribute('data-canteen');
        cartItems.push({ name, price, quantity, canteen});
    });

    // Create a hidden form to submit the order
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = formAction; // Set the form action dynamically

    const cartInput = document.createElement('input');
    cartInput.type = 'hidden';
    cartInput.name = 'cart_items';
    cartInput.value = JSON.stringify(cartItems);
    form.appendChild(cartInput);

    const canteenInput = document.createElement('input');
    canteenInput.type = 'hidden';
    canteenInput.name = 'canteen';
    canteenInput.value = currentCanteen; // Include the selected canteen
    form.appendChild(canteenInput);

    const paymentMethodInput = document.createElement('input');
    paymentMethodInput.type = 'hidden';
    paymentMethodInput.name = 'payment_method'; // Ensure this matches the key in PHP
    paymentMethodInput.value = selectedPaymentMethod;
    form.appendChild(paymentMethodInput);

    const totalAmountInput = document.createElement('input');
    totalAmountInput.type = 'hidden';
    totalAmountInput.name = 'total_amount';
    totalAmountInput.value = totalAmount.toFixed(2);
    form.appendChild(totalAmountInput);

    document.body.appendChild(form);
    form.submit();


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

// Function to get the selected payment method
const getSelectedPaymentMethod = () => {
    return Array.from(paymentOptions).find(option => option.checked)?.value || null;
};

// Event listener for removing items from the cart
cartItemsContainer.addEventListener("click", (e) => {
    if (e.target.classList.contains("remove-item")) {
        const itemIndex = e.target.dataset.index;
        totalAmount -= cart[itemIndex].price * cart[itemIndex].quantity;
        cart.splice(itemIndex, 1);
        updateCartDisplay();
        showToast("Item removed from cart.");
    }
});

// Event listener for the hamburger menu
hamburgerMenu.addEventListener("click", () => {
    menuOptions.classList.toggle("active");

    const [topLine, middleLine, bottomLine] = [
        document.getElementById("top-line"),
        document.getElementById("middle-line"),
        document.getElementById("bottom-line")
    ];

    if (menuOptions.classList.contains("active")) {
        topLine.style.transform = "translateY(10px) rotate(45deg)";
        middleLine.style.opacity = "0";
        bottomLine.style.transform = "translateY(-10px) rotate(-45deg)";
    } else {
        topLine.style.transform = "translateY(0) rotate(0)";
        middleLine.style.opacity = "1";
        bottomLine.style.transform = "translateY(0) rotate(0)";
    }
});

// Event listener to close the hamburger menu when clicking outside
document.addEventListener("click", (e) => {
    if (!hamburgerMenu.contains(e.target) && !menuOptions.contains(e.target)) {
        menuOptions.classList.remove("active");

        document.getElementById("top-line").style.transform = "translateY(0) rotate(0)";
        document.getElementById("middle-line").style.opacity = "1";
        document.getElementById("bottom-line").style.transform = "translateY(0) rotate(0)";
    }
});

// Function to show a toast message
const showToast = (message) => {
    const toast = document.createElement("div");
    toast.className = "toast";
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => toast.classList.add("show"), 100);
    setTimeout(() => {
        toast.classList.remove("show");
        toast.remove();
    }, 3000);
};

// Function to create a hidden input field
const createHiddenInput = (name, value) => {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = name;
    input.value = value;
    return input;
};

