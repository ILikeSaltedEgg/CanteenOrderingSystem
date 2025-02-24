<?php
session_start();

$host = "localhost";
$username = "root";
$password = "";
$dbname = "au_canteen";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['item_name'])) {
    $itemName = $_GET['item_name'];

    $query = "SELECT stock_quantity FROM food_inventory WHERE item_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $itemName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(["stock_quantity" => $row['stock_quantity']]);
    } else {
        echo json_encode(["stock_quantity" => 0]);
    }
} else {
    echo json_encode(["stock_quantity" => 0]);
}
?>