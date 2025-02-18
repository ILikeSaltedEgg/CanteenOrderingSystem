<?php
session_start();
require_once('../tcpdf/tcpdf.php'); // Ensure TCPDF is installed

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
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

// Fetch the latest order for the logged-in user
$email = $_SESSION['email'];
$sql = "SELECT * FROM orders WHERE email = ? ORDER BY order_id DESC LIMIT 1";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No recent orders found.");
}

$order = $result->fetch_assoc();
$stmt->close();

$order_id = $order['order_id'];
$order_name = $order['item_name'];
$order_section = $order['section'];
$order_track = $order['track'];
$order_date = $order['order_date'];
$total_price = number_format($order['total_price'], 2);
$canteen = $order['canteen'];

// Fetch order items
$sql = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

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
$pdf->SetFont('dejavusans', '', 12); // Use a Unicode-supported font
$pdf->SetCreator('Canteen System');
$pdf->SetAuthor('AU Canteen');
$pdf->SetTitle('Canteen Receipt');

// Define the peso sign
$peso = '₱';

// HTML content for the invoice
$html = "
    <h1 style='text-align: center;'>Invoice</h1>
    <hr>
    <p><strong>Order ID:</strong> $order_id</p>
    <p><strong>Section:</strong> $order_section</p>
    <p><strong>Track:</strong> $order_track</p>
    <p><strong>Order Name:</strong> $order_name</p>
    <p><strong>Date:</strong> $order_date</p>
    <p><strong>Canteen:</strong> $canteen</p>
    <p><strong>Customer:</strong> $email</p>
    <br>
    <table border='1' cellpadding='5' style='width: 100%;'>
        <tr style='background-color: #f2f2f2;'>
            <th>Item</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
        </tr>";

foreach ($items as $item) {
    $item_name = $item['item_name'];
    $price = number_format($item['price'], 2);
    $quantity = $item['quantity'];
    $total = number_format($item['price'] * $item['quantity'], 2);

    $html .= "
        <tr>
            <td>$item_name</td>
            <td>{$peso}$price</td>
            <td>$quantity</td>
            <td>{$peso}$total</td>
        </tr>";
}

$html .= "
    </table>
    <br>
    <h3 style='text-align: right;'>Total Amount: {$peso}$total_price</h3>
    <hr>
    <p style='text-align: center;'>Thank you for ordering at AU Canteen!</p>";

// Write HTML content to the PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Output the PDF for download
$pdf->Output("invoice_$order_id.pdf", 'D');
exit();