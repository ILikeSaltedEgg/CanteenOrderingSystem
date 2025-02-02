<?php
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $conn->real_escape_string($data['username']);
    $cart = $data['cart'];

    if ($username && !empty($cart)) {
        $total_price = 0;
        foreach ($cart as $item) {
            $total_price += $item['price'] * $item['quantity'];
        }

        $sql = "INSERT INTO orders (username, total_price) VALUES ('$username', $total_price)";
        if ($conn->query($sql)) {
            $order_id = $conn->insert_id;

            $itemSuccess = true;
            foreach ($cart as $item) {
                $item_name = $conn->real_escape_string($item['name']);
                $quantity = (int)$item['quantity'];
                $price = (float)$item['price'];

                $itemSql = "INSERT INTO order_items (order_id, item_name, quantity, price) 
                            VALUES ($order_id, '$item_name', $quantity, $price)";
                if (!$conn->query($itemSql)) {
                    $itemSuccess = false;
                    break;
                }
            }

            if ($itemSuccess) {
                echo json_encode(['success' => true, 'message' => 'Order placed successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to insert order items.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create order.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid order data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
