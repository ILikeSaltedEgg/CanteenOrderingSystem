document.addEventListener("DOMContentLoaded", () => {
    const hamburgerMenu = document.getElementById("hamburger");
    const menuOptions = document.getElementById("menu-options");
    const loginButton = document.getElementById("login-button");
    const registerButton = document.getElementById("register-button");

    console.log("loginButton:", loginButton); // Debugging

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

    // Check and attach listeners to buttons
    window.placeOrder = function () {
        // Check if the user is logged in
        if (!localStorage.getItem("username")) {
            alert("You need to be logged in to place an order.");
            openBox('register'); // Open the registration/login modal
            return;
        }

        // Check if the cart is empty
        if (cart.length === 0) {
            alert("Your cart is empty.");
            return;
        }

        // Proceed with placing the order
        alert("Order placed successfully! Staff has been notified.");
        clearCart(); // Clear the cart after placing the order
    };

    // Function to display login and register buttons
    function displayLoginAndRegisterButtons() {
        const authContainer = document.getElementById("auth-container"); // Ensure this element exists
        if (authContainer) {
            authContainer.innerHTML = `
                <button class="login-btn" onclick="location.href='login.php'">Login</button>
            `;
        }
    }

    // Function to handle adding items to the cart
    window.handleAddToCart = function () {
        // Check if the user is logged in
        if (!localStorage.getItem("username")) {
            alert("You must have an account to add items to the cart.");
            openBox('register'); // Open the registration/login modal
            return;
        }

        // Add item to the cart
        alert("Item added to cart!");
        // Add your logic to add the item to the cart here
    };

    // Initialize the UI based on login status
    if (localStorage.getItem("username")) {
        // User is logged in
        console.log("User is logged in");
    } else {
        // User is not logged in
        displayLoginAndRegisterButtons();
    }
});