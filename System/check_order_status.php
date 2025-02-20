<?php
session_start();
require 'db_connection.php';

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    $query = "SELECT order_status FROM orders WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();

    echo json_encode(['status' => $order['order_status']]);
}
?>