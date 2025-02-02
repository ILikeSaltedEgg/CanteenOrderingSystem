<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate the input (simple example)
    if (!empty($username) && !empty($email) && !empty($password)) {
        // Here you would add code to insert the data into the database
        echo "<div class='box-content'>
                <h2>Registration Successful</h2>
                <p>Welcome, " . htmlspecialchars($username) . "!</p>
              </div>";
    } else {
        echo "<div class='box-content'>
                <h2>Error</h2>
                <p>All fields are required.</p>
              </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="styles.css"> <!-- Your provided CSS -->
</head>
<body>

    <div class="top-header">
        <h1>Register</h1>
    </div>

    <div id="container">
        <div class="box-content">
            <h2>Sign Up</h2>
            <form action="" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit" class="submit-button">Register</button>
            </form>
        </div>
    </div>

</body>
</html>
