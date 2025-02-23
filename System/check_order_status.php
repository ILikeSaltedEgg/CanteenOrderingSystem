<?php
session_start();
$host = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "au_canteen";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Validate order_id
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    echo json_encode(["error" => "Invalid order ID"]);
    exit();
}

$order_id = intval($_GET['order_id']);
$sql = "SELECT order_status FROM orders WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["status" => $row['order_status']]);
} else {
    echo json_encode(["error" => "Order not found"]);
}

$stmt->close();
$conn->close();
?>
