document.addEventListener("DOMContentLoaded", () => {
    const hamburgerMenu = document.getElementById("hamburger");
    const menuOptions = document.getElementById("menu-options");
    const loginButton = document.getElementById("login-button");
    const registerButton = document.getElementById("register-button");

    console.log("loginButton:", loginButton); // Debugging
    console.log("registerButton:", registerButton); // Debugging

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
        const authContainer = document.getElementById("auth-container"); // Ensure this element exists
        authContainer.innerHTML = `
            <button class="login-btn" onclick="location.href='login.php'">Login</button>
            <button class="register-btn" onclick="location.href='register.php'">Register</button>
        `;
    }    

});
