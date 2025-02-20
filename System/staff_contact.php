<?php session_start();

include 'db_connection.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Contact</title>
    <script src="main.js" defer></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: rgb(50,122,163);
            background: linear-gradient(90deg, rgba(50,122,163,1) 21%, rgba(97,132,168,1) 80%);
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        header {
            text-align: center;
            margin-bottom: 20px;
        }

        #logo {
            height: 120px; /* Adjusted for better proportion */
            width: auto;
            margin: 0 auto 20px;
            display: block;
            filter: drop-shadow(0 0 8px rgba(0, 0, 0, 0.2));
        }

        .contact-container {
            padding: 20px;
            background: #fff;
            border-radius: 8px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        textarea {
            resize: none;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 16px;
        }
        button {
            background: #007BFF;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
        <a href="<?php echo isset($_SESSION['email']) ? 'research2.php' : 'research1.php'; ?>">
            <img src="https://upload.wikimedia.org/wikipedia/en/thumb/8/8b/Arellano_University_logo.png/200px-Arellano_University_logo.png" alt="Logo" id="logo">
        </a>
            <h1>Staff Contact</h1>
            <p>Have any questions or concerns? Reach out to us below.</p>
        </header>

        <section class="contact-section">
            <div class="contact-container">
                <h2>Contact Us</h2>
                <form method="POST" action="process_contact.php">
                    <label for="contact">Your Message:</label>
                    <textarea id="contact" name="contact" rows="5" required></textarea>
                    <button type="submit">Submit</button>
                </form>
            </div>
        </section>
    </div>
</body>
</html>
