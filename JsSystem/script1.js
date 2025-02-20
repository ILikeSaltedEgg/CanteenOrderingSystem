let totalAmount = 0;
let cart = [];
let currentCanteen = "Canteen 1"; // Default to Canteen 1
let currentCategory = ""; // Track the currently selected category

document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("payment-modal");
    const modalContent = document.querySelector(".modal-content");
    const paymentForm = document.getElementById("payment-form");
    const paymentOptions = document.getElementsByName("payment-method");
    const totalAmountInput = document.getElementById("total-amount");
    const cartItemsContainer = document.getElementById("cart-items");
    const cartTotalDisplay = document.getElementById("cart-total");
    const hamburgerMenu = document.getElementById("hamburger");
    const menuOptions = document.getElementById("menu-options");

    // Function to update the cart display
    const updateCartDisplay = () => {
        if (cart.length === 0) {
            cartItemsContainer.innerHTML = "<p>Your cart is empty.</p>";
            cartTotalDisplay.textContent = "Total: ₱0.00";
            return;
        }

        cartItemsContainer.innerHTML = cart.map((item, index) => `
            <li data-name="${item.name}" data-price="${item.price}" data-quantity="${item.quantity}" data-canteen="${item.canteen}">
                ${item.name} (${item.canteen}) (₱${item.price}) ${item.quantity}x - ₱${(item.price * item.quantity).toFixed(2)}
                <button class="remove-item" data-index="${index}">Remove</button>
            </li>
        `).join("");
        cartTotalDisplay.textContent = `Total: ₱${totalAmount.toFixed(2)}`;
    };

    // Clear the cart
    window.clearCart = function () {
        cart = [];
        updateCartDisplay();
    };

    // Function to add items to the cart
    window.addToCart = (itemName, itemPrice, button) => {
        const quantityInput = button.parentElement.querySelector(".quantity-input");
        const quantity = parseInt(quantityInput.value);
        const canteen = button.closest(".menu-item").getAttribute("data-canteen");

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

    // Function to filter menu items by canteen and category
    window.filterItems = (canteen, category = "") => {
        currentCanteen = canteen;
        currentCategory = category;

        document.querySelectorAll(".menu-item").forEach(item => {
            const matchesCanteen = item.getAttribute("data-canteen") === currentCanteen;
            const matchesCategory = !currentCategory || item.getAttribute("data-category") === currentCategory;
            item.style.display = matchesCanteen && matchesCategory ? "block" : "none";
        });
    };

    // Function to open the payment modal
    window.openModal = () => {
        if (cart.length === 0) {
            showToast("Your cart is empty. Add items before proceeding to payment.");
            return;
        }

        totalAmountInput.value = totalAmount.toFixed(2);
        modal.style.display = "flex";
        setTimeout(() => modalContent.classList.add("show"), 10);
    };

    // Function to close the payment modal
    window.closeModal = () => {
        modalContent.classList.remove("show");
        setTimeout(() => { modal.style.display = "none"; }, 300);
    };

    // Function to get the selected payment method
    const getSelectedPaymentMethod = () => {
        return Array.from(paymentOptions).find(option => option.checked)?.value || null;
    };

    // Handle payment form submission
    paymentForm.addEventListener("submit", (e) => {
        e.preventDefault();
        const selectedPaymentMethod = getSelectedPaymentMethod();

        if (!selectedPaymentMethod) {
            showToast("Please select a payment method.");
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = selectedPaymentMethod === "gcash" || selectedPaymentMethod === "paymaya" || selectedPaymentMethod === "cash"
            ? "process_payment.php" : "staff.php";

        form.append(createHiddenInput("cart_items", JSON.stringify(cart)));
        form.append(createHiddenInput("canteen", currentCanteen));
        form.append(createHiddenInput("payment_method", selectedPaymentMethod));
        form.append(createHiddenInput("total_amount", totalAmount.toFixed(2)));

        document.body.appendChild(form);
        form.submit();
    });

    // Handle item removal
    cartItemsContainer.addEventListener("click", (e) => {
        if (e.target.classList.contains("remove-item")) {
            const itemIndex = parseInt(e.target.dataset.index);
            totalAmount -= cart[itemIndex].price * cart[itemIndex].quantity;
            cart.splice(itemIndex, 1);
            updateCartDisplay();
            showToast("Item removed from cart.");
        }
    });

    // Hamburger menu toggle
    hamburgerMenu.addEventListener("click", () => {
        menuOptions.classList.toggle("active");
        const lines = ["top-line", "middle-line", "bottom-line"].map(id => document.getElementById(id));
        if (menuOptions.classList.contains("active")) {
            lines[0].style.transform = "translateY(10px) rotate(45deg)";
            lines[1].style.opacity = "0";
            lines[2].style.transform = "translateY(-10px) rotate(-45deg)";
        } else {
            lines.forEach(line => { line.style.transform = ""; line.style.opacity = ""; });
        }
    });

    // Close menu when clicking outside
    document.addEventListener("click", (e) => {
        if (!hamburgerMenu.contains(e.target) && !menuOptions.contains(e.target)) {
            menuOptions.classList.remove("active");
            ["top-line", "middle-line", "bottom-line"].forEach(id => {
                const line = document.getElementById(id);
                line.style.transform = "";
                line.style.opacity = "";
            });
        }
    });

    // Function to show toast message
    const showToast = (message) => {
        const toast = document.createElement("div");
        toast.className = "toast";
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add("show"), 100);
        setTimeout(() => { toast.classList.remove("show"); toast.remove(); }, 3000);
    };

    // Function to create a hidden input field
    const createHiddenInput = (name, value) => {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = name;
        input.value = value;
        return input;
    };

    // Show Canteen 1 items by default
    filterItems("Canteen 1");
});
