<?php

session_start();
require 'db_connection.php';

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Online Canteen Ordering</title>
        <link rel="stylesheet" href="../../Styles/styles.css">

    </head>

<body>

    <?php include 'header.php'; ?>

    <div class="greeting">
        <span id="greeting">Arellano University Jose Rizal High School</span>
        <span id="description">An Online Ordering Canteen</span>
        <h2>Made By: Ace Joshua Calimlim</h2>
    </div>

    <div class="background-image">
        <img src="../../Images/arellano-page.png" alt="Arellano Page">
    </div>

    <div id="image-slider"></div>

    <script src="../../Javascript/image_slider.js"></script>

    <div id="menu-container"></div>

    <script src="../../Javascript/menu_loader.js"></script>

    <div id="about-container"></div>

    <script src="../../Javascript/about_loader.js"></script>

    <script src="../../Javascript/script.js"></script>


    <?php include 'footer.php'; ?>

</body>
</html>