<?php
session_start();
require_once('../tcpdf/tcpdf.php'); // Ensure you have TCPDF installed in your project

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$host = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "au_canteen";
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch latest order for the logged-in user
$username = $_SESSION['username'];
$sql = "SELECT * FROM orders WHERE username = ? ORDER BY order_id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    die("No recent orders found.");
}

$order_id = $order['order_id'];
$order_name = $order['item_name'];
$order_date = $order['order_date'];
$total_price = $order['total_price'];
$canteen = $order['canteen'];

// Fetch order items
$sql = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

// Create PDF invoice
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 12); // Use Unicode-supported font
$pdf->SetCreator('Canteen System');
$pdf->SetAuthor('AU Canteen');
$pdf->SetTitle('Canteen Receipt');

$peso = '₱'; // Explicitly define peso sign

$html = "<h1>Invoice</h1>
         <p><strong>Order ID:</strong> $order_id</p>
         <p><strong>Order Name:</strong> $order_name</p>
         <p><strong>Date:</strong> $order_date</p>
         <p><strong>Canteen:</strong> $canteen</p>
         <p><strong>Customer:</strong> $username</p>
         <table border='1' cellpadding='5'>
         <tr style='background-color: #f2f2f2;'><th>Item</th><th>Price</th><th>Quantity</th><th>Total</th></tr>";

foreach ($items as $item) {
    $item_name = $item['item_name'];
    $price = number_format($item['price'], 2);
    $quantity = $item['quantity'];
    $total = number_format($price * $quantity, 2);
    
    $html .= "<tr>
                <td>$item_name</td>
                <td>{$peso}$price</td>
                <td>$quantity</td>
                <td>{$peso}$total</td>
              </tr>";
}

$html .= "</table>
          <h3>Total Amount: {$peso}$total_price</h3>
          <p>Thank you for ordering!</p>";

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output("invoice_$order_id.pdf", 'D'); // Download the PDF

