<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <a href="research2.php">
    <img src="https://upload.wikimedia.org/wikipedia/en/thumb/8/8b/Arellano_University_logo.png/200px-Arellano_University_logo.png" alt="Logo" id="logo">
    </a>
    <title>Contact Us</title>
    <link rel="stylesheet" href="styles4.css"> 
</head>
<body>

    <div class="contact-container">
        <h1>Contact Us</h1>
        <p>If you have any questions or feedback, feel free to reach out to us. We're here to help!</p>

        <h2>Contact Form</h2>
        <form action="process_contact.php" method="POST" class="contact-form">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required placeholder="Your full name">
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="Your email address">
            </div>
            <div class="form-group">
                <label for="message">Your Message</label>
                <textarea id="message" name="message" required placeholder="Write your message here..." rows="4"></textarea>
            </div>
            <button type="submit" class="submit-button">Send Message</button>
        </form>

        <h2>Other Contact Information</h2>
        <p>If you prefer to reach us directly, you can use the following methods:</p>
        <ul>
            <li><strong>Email:</strong> <a href="mailto:acejoshuacalimlim@gmail.com">acejoshuacalimlim@gmail.com</a></li>
            <li><strong>Phone:</strong> +1 (123) 456-7890</li>
            <li><strong>Address:</strong> 123 Canteen Universe, Planet Nemek, XYZ 12345</li>
        </ul>

        <h2>Follow Us</h2>
        <p>Stay connected with us on social media:</p>
        <ul>
            <li><a href="https://www.facebook.com/ace.joshua.calimlim/" target="_blank">Facebook</a></li>
            <li><a href="https://x.com/omphukos" target="_blank">Twitter</a></li>
            <li><a href="https://www.instagram.com/ace_je_taime/" target="_blank">Instagram</a></li>
        </ul>
    </div>

</body>

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

</html>
