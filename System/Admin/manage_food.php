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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .main-content {
            margin-left: 250px; /* Adjust based on sidebar width */
            padding: 20px;
        }

        .header {
            margin-bottom: 20px;
        }

        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        h2 {
            margin-bottom: 15px;
            color: #333;
        }

        form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="number"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            flex: 1;
        }

        button[type="submit"] {
            padding: 10px 20px;
            background: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f8f9fa;
        }

        tr:hover {
            background: #f1f1f1;
        }

        .btn-update {
            padding: 5px 10px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-update:hover {
            background: #218838;
        }

        .btn-delete {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-delete:hover {
            color: #c82333;
        }

        .stock-warning {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h1>Manage Food Inventory</h1>
        </div>

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
                            <td class="<?= $item['stock_quantity'] == 0 ? 'stock-warning' : '' ?>">
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
                                    <button type="submit" name="delete_food" class="btn-delete" onclick="return confirm('Are you sure you want to delete this item?');">
                                        <i class="fas fa-trash"></i>
                                    </button>
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