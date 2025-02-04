let totalAmount = 0;
let cart = [];
let currentCanteen = "all"; // Track the currently selected canteen
let currentCategory = "all"; // Track the currently selected category

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
            <li data-name="${item.name}" data-price="${item.price}" data-quantity="${item.quantity}">
                ${item.name} ${item.quantity}x - ₱${(item.price * item.quantity).toFixed(2)}
                <button class="remove-item" data-index="${index}">Remove</button>
            </li>
        `).join("");
        cartTotalDisplay.textContent = `Total: ₱${totalAmount.toFixed(2)}`;
    };

    // Function to add items to the cart
    window.addToCart = (itemName, itemPrice, button) => {
        const quantityInput = button.parentElement.querySelector(".quantity-input");
        const quantity = parseInt(quantityInput.value);

        if (!itemName || isNaN(itemPrice) || isNaN(quantity) || quantity <= 0) {
            showToast("Invalid item or quantity. Please try again.");
            return;
        }

        const existingItem = cart.find(item => item.name === itemName);

        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.push({ name: itemName, price: itemPrice, quantity });
        }

        totalAmount += itemPrice * quantity;
        updateCartDisplay();
        showToast(`${itemName} added to cart.`);
    };

    // Function to filter menu items by canteen and category
    window.filterItems = (canteen, category = "all") => {
        currentCanteen = canteen; // Update the current canteen
        currentCategory = category; // Update the current category

        const menuItems = document.querySelectorAll(".menu-item");
        menuItems.forEach(item => {
            const itemCanteen = item.getAttribute("data-canteen");
            const itemCategory = item.getAttribute("data-category");

            const matchesCanteen = currentCanteen === "all" || itemCanteen === currentCanteen;
            const matchesCategory = currentCategory === "all" || itemCategory === currentCategory;

            if (matchesCanteen && matchesCategory) {
                item.style.display = "block"; // Show items that match both filters
            } else {
                item.style.display = "none"; // Hide items that don't match
            }
        });
    };

    window.placeOrder = () => {
        if (cart.length === 0) {
            showToast("Your cart is empty. Add items before placing an order.");
            return;
        }

        const selectedPaymentMethod = getSelectedPaymentMethod();

        if (!selectedPaymentMethod) {
            showToast("Please select a payment method.");
            return;
        }

        openModal();
        
        const cartDataInput = createHiddenInput("cart", JSON.stringify(cart));
        const totalAmountInput = createHiddenInput("total-amount", totalAmount.toFixed(2));
        const paymentMethodInput = createHiddenInput("payment-method", selectedPaymentMethod);

        paymentForm.append(cartDataInput, totalAmountInput, paymentMethodInput);
        paymentForm.submit();
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

    const selectedPaymentMethod = getSelectedPaymentMethod();

    if (!selectedPaymentMethod) {
        showToast("Please select a payment method.");
        return;
    }

    // Function to open the payment modal and submit the order
    window.openModal = () => {
        if (cart.length === 0) {
            showToast("Your cart is empty. Add items before proceeding to payment.");
            return;
        }

        const cartDataInput = createHiddenInput("cart", JSON.stringify(cart));
        const totalAmountInput = createHiddenInput("total-amount", totalAmount.toFixed(2));
        const paymentMethodInput = createHiddenInput("payment-method", selectedPaymentMethod);

        paymentForm.append(cartDataInput, totalAmountInput, paymentMethodInput);
        paymentForm.submit();

        const canteenInput = document.createElement('input');
        canteenInput.type = 'hidden';
        canteenInput.name = 'canteen';
        canteenInput.value = currentCanteen; // Include the selected canteen
        form.appendChild(canteenInput);

        document.body.appendChild(form);
        form.submit();
    };

    // Function to close the payment modal
    window.closeModal = () => {
        modalContent.classList.remove("show");
        setTimeout(() => modal.style.display = "none", 300);
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
});
