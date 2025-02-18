<?php session_start(); 

include 'db_connection.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <a href="<?php echo isset($_SESSION['email']) ? 'research2.php' : 'research1.php'; ?>">
    <img src="https://upload.wikimedia.org/wikipedia/en/thumb/8/8b/Arellano_University_logo.png/200px-Arellano_University_logo.png" alt="Logo" id="logo">
    </a>
    <title>Terms of Service</title>
    <link rel="stylesheet" href="../Styles/styles4.css"> 
</head>
<body>

    <div class="container">
        <h1>Terms of Service for the Online Canteen Ordering System</h1>
        <p><strong>Last Updated: January 29, 2025</strong></p>

        <h2>1. Acceptance of Terms</h2>
        <p>By using the Online Canteen Ordering System, you confirm that you have read, understood, and agree to be bound by these Terms, as well as our Privacy Policy, which is incorporated by reference into these Terms...</p>

        <h2>2. Eligibility</h2>
        <p>You must be at least 18 years old or the age of majority in your jurisdiction to use our Services...</p>

        <h2>3. Account Registration</h2>
        <p>To use our Services, you may need to create an account with us by providing accurate, complete, and up-to-date information...</p>

        <h2>4. Ordering and Payment</h2>
        <p>When you place an order using our online canteen system, you are making an offer to purchase the items specified in your order...</p>

        <h2>5. Delivery and Fulfillment</h2>
        <p>Delivery times are estimates and may vary based on the availability of products, location, and other factors...</p>

        <h2>6. Cancellations and Refunds</h2>
        <p>Once an order has been placed, cancellations are only allowed under certain conditions...</p>

        <h2>7. User Conduct</h2>
        <p>You agree not to use our Services for any unlawful, fraudulent, or malicious purpose...</p>

        <h2>8. Intellectual Property</h2>
        <p>All content on the Online Canteen Ordering System, including but not limited to text, images, logos, software, and other materials, is the property of the Online Canteen Ordering System...</p>

        <h2>9. Limitation of Liability</h2>
        <p>To the fullest extent permitted by law, the Online Canteen Ordering System, its affiliates, employees, and agents will not be liable for any indirect, incidental, special, or consequential damages...</p>

        <h2>10. Indemnification</h2>
        <p>You agree to indemnify, defend, and hold harmless the Online Canteen Ordering System, its affiliates, employees, and agents from any claims, damages, liabilities, and expenses arising from your violation of these Terms...</p>

        <h2>11. Privacy</h2>
        <p>We are committed to protecting your privacy. By using our Services, you agree to our Privacy Policy...</p>

        <h2>12. Termination</h2>
        <p>We reserve the right to suspend or terminate your access to the Services at our discretion if we believe that you have violated these Terms...</p>

        <h2>13. Governing Law</h2>
        <p>These Terms shall be governed by and construed in accordance with the laws of the jurisdiction in which our business operates...</p>

        <h2>14. Miscellaneous</h2>
        <p>These Terms constitute the entire agreement between you and the Online Canteen Ordering System regarding the use of the Services...</p>

        <h2>15. Contact Us</h2>
        <p>If you have any questions or concerns about these Terms or our Services, please contact us at <a href="mailto:acejoshuacalimlim@gmail.com">acejoshuacalimlim@gmail.com</a></p>

    </div>

</body>

<?php include 'footer.php'; ?>

</html>

