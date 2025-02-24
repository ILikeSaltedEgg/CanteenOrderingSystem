<?php
session_start();
require_once('../tcpdf/tcpdf.php'); 

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

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
$sql = "
    SELECT o.order_id, o.order_date, o.total_price, o.canteen, o.note,
           u.username, u.section, u.track
    FROM orders o
    JOIN users u ON o.email = u.email
    WHERE o.email = ?
    ORDER BY o.order_id DESC
    LIMIT 1
";
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
$username = $order['username'];
$section = $order['section'];
$track = $order['track'];
$order_date = $order['order_date'];
$total_price = number_format($order['total_price'], 2);
$canteen = $order['canteen'];
$note = $order['note'];

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

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 12); 
$pdf->SetCreator('Canteen System');
$pdf->SetAuthor('AU Canteen');
$pdf->SetTitle('Canteen Receipt');

$peso = '₱';

$html = "
    <h1 style='text-align: center;'>Reciept</h1>
    <hr>
    <p><strong>Order ID:</strong> $order_id</p>
    <p><strong>Customer Name:</strong> $username</p>
    <p><strong>Section:</strong> $section</p>
    <p><strong>Track:</strong> $track</p>
    <p><strong>Order Date:</strong> $order_date</p>
    <p><strong>Canteen:</strong> $canteen</p>
    <p><strong>Note:</strong> $note</p>
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

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output("invoice_$order_id.pdf", 'D');
exit();