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

if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'super_admin') {
    echo "Redirecting to login...";
    header("Location: login.php");
    exit();
}

// Handle adding a new food item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_food'])) {
    $item_name = trim($_POST['item_name']);
    $stock_quantity = intval($_POST['stock_quantity']);

    $insertQuery = "INSERT INTO food_inventory (item_name, stock_quantity) VALUES (?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("si", $item_name, $stock_quantity);
    $stmt->execute();
    header("Location: manage_food.php");
    exit();
}

// Handle stock updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $item_id = intval($_POST['item_id']);
    $new_stock = intval($_POST['stock_quantity']);
    
    $updateQuery = "UPDATE food_inventory SET stock_quantity = ? WHERE item_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ii", $new_stock, $item_id);
    $stmt->execute();
    header("Location: manage_food.php");
    exit();
}

// Handle deleting a food item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_food'])) {
    $delete_item_id = intval($_POST['delete_item_id']);
    
    $deleteQuery = "DELETE FROM food_inventory WHERE item_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $delete_item_id);
    $stmt->execute();
    header("Location: manage_food.php");
    exit();
}

// Fetch inventory items sorted alphabetically
$inventoryQuery = "SELECT * FROM food_inventory ORDER BY item_name ASC";
$inventoryResult = $conn->query($inventoryQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Food Inventory</title>
    <link rel="stylesheet" href="../../Styles/styles_admin.css">
</head>
<body>
    <!-- Sidebar Navigation -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Manage Food Inventory</h1>
        </div>

        <!-- Add New Food Item Form -->
        <section class="container">
            <h2>Add New Food Item</h2>
            <form method="post">
                <input type="text" name="item_name" placeholder="Food Name" required>
                <input type="number" name="stock_quantity" placeholder="Stock Quantity" min="0" required>
                <button type="submit" name="add_food">Add Food</button>
            </form>
        </section>

        <!-- Food Inventory Table -->
        <section class="container">
            <h2>Food Inventory</h2>
            <table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Stock Quantity</th>
                        <th>Update Stock</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $inventoryResult->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                            <td <?= $item['stock_quantity'] == 0 ? 'style="color: red; font-weight: bold;"' : '' ?>>
                                <?= htmlspecialchars($item['stock_quantity']) ?>
                            </td>
                            <td>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                                    <input type="number" name="stock_quantity" value="<?= $item['stock_quantity'] ?>" min="0" required>
                                    <button type="submit" name="update_stock" class="btn-update">Update</button>
                                </form>
                            </td>
                            <td>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="delete_item_id" value="<?= $item['item_id'] ?>">
                                    <button type="submit" name="delete_food" class="btn-delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
<?php $conn->close(); ?>