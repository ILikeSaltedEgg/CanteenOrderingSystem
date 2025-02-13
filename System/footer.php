<link   rel="stylesheet" href="../Styles/footer.css">

<footer>
    <div class="footer-container">
        <div class="footer-content">
            <div class="footer-logo">
                <img src="https://upload.wikimedia.org/wikipedia/en/thumb/8/8b/Arellano_University_logo.png/200px-Arellano_University_logo.png" alt="Arellano Logo">
                <h2>Arellano University</h2>
                <p>Online Canteen Ordering</p>
            </div>

            <div class="footer-links">
                <h3>Quick Links</h3>
                <ul>
                    <?php if (isset($_SESSION["username"])): ?>
                    <li><a href="research2.php">Home</a></li>
                    <?php else: ?>
                        <li><a href="research1.php">Home</a></li>
                    <?php endif; ?>
                    <li><a href="policy.php">Policy</a></li>
                    <li><a href="terms.php">Terms</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>

            <div class="footer-contact">
                <h3>Contact Us</h3>
                <p>Email: <a href="mailto:acejoshuacalimlim@gmail.com">support@arellanocanteen.com</a></p>
                <p>Phone: +63 994 345 6789</p>
            </div>

            <div class="footer-social">
                <h3>Follow Us</h3>
                <a href="#" target="https://www.facebook.com/ace.joshua.calimlim"><img src="https://static.vecteezy.com/system/resources/previews/042/127/201/non_2x/round-square-blue-and-white-facebook-logo-with-thick-white-border-and-shadow-on-a-transparent-background-free-png.png" alt="Facebook"></a>
                <a href="#" target="https://x.com/omphukos"><img src="https://cdn.prod.website-files.com/5d66bdc65e51a0d114d15891/64cebdd90aef8ef8c749e848_X-EverythingApp-Logo-Twitter.jpg" alt="Twitter"></a>
                <a href="#" target="https://www.instagram.com/ace_je_taime/"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/95/Instagram_logo_2022.svg/1024px-Instagram_logo_2022.svg.png" alt="Instagram"></a>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> Arellano High School Online Canteen Ordering. All Rights Reserved.</p>
        </div>
    </div>
</footer>
