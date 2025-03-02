<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['cart']) && isset($data['totalAmount'])) {
        $_SESSION['cart'] = $data['cart'];
        $_SESSION['totalAmount'] = $data['totalAmount'];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid data']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>