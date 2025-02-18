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

// Fetch orders with item names from order_items
$orders_query = "
    SELECT o.order_id, 
           u.username, 
           o.email,
           o.total_price, 
           o.order_date, 
           o.canteen, 
           GROUP_CONCAT(oi.item_name SEPARATOR ', ') AS item_names
    FROM orders o
    JOIN users u ON o.username = u.username
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
";
$orders_result = mysqli_query($conn, $orders_query);

if (!$orders_result) {
    die("Query failed: " . mysqli_error($conn));
}

$users_query = "SELECT * FROM users";
$users_result = mysqli_query($conn, $users_query);

if (!$users_result) {
    die("Query failed: " . mysqli_error($conn));
}

// Fetch dashboard statistics
$total_users_query = "SELECT COUNT(*) AS total_users FROM users";
$total_users_result = mysqli_query($conn, $total_users_query);
$total_users = mysqli_fetch_assoc($total_users_result)['total_users'];

$online_users_query = "SELECT COUNT(*) AS online_users FROM users WHERE last_activity > NOW() - INTERVAL 5 MINUTE";
$online_users_result = mysqli_query($conn, $online_users_query);
$online_users = mysqli_fetch_assoc($online_users_result)['online_users'];

$total_orders_query = "SELECT COUNT(*) AS total_orders FROM orders";
$total_orders_result = mysqli_query($conn, $total_orders_query);
$total_orders = mysqli_fetch_assoc($total_orders_result)['total_orders'];

if (isset($_POST['validate_users'])) {
    $validate_query = "UPDATE users SET school_valid = CASE
                       WHEN username LIKE '%@arellano.edu%' THEN 1
                       ELSE 0 END";
    if (!mysqli_query($conn, $validate_query)) {
        die("Validation query failed: " . mysqli_error($conn));
    }
    header("Location: super_admin.php");
    exit();
}

if (isset($_POST['validate_user'])) {
    $user_id = (int) $_POST['user_id'];
    $validate_user_query = "UPDATE users SET school_valid = 1 WHERE id = '$user_id'";
    if (!mysqli_query($conn, $validate_user_query)) {
        die("Validation query failed: " . mysqli_error($conn));
    }
    header("Location: super_admin.php");
    exit();
}

if (isset($_POST['add_order'])) {
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);

    $check_valid_query = "SELECT school_valid FROM users WHERE id = '$user_id'";
    $check_valid_result = mysqli_query($conn, $check_valid_query);
    $valid_user = mysqli_fetch_assoc($check_valid_result);

    if ($valid_user['school_valid'] == 0) {
        echo "You must be validated to place an order.";
        exit();
    }

    $add_order_query = "INSERT INTO orders (user_id, item_name) VALUES ('$user_id', '$item_name')";
    if (!mysqli_query($conn, $add_order_query)) {
        die("Add order query failed: " . mysqli_error($conn));
    }
    header("Location: super_admin.php");
    exit();
}

if (isset($_GET['delete_order_id'])) {
    $order_id = (int) $_GET['delete_order_id'];
    $delete_order_query = "DELETE FROM orders WHERE order_id = '$order_id'";
    if (!mysqli_query($conn, $delete_order_query)) {
        die("Delete order query failed: " . mysqli_error($conn));
    }
    header("Location: super_admin.php");
    exit();
}

if (isset($_GET['delete_user_id'])) {
    $user_id = (int) $_GET['delete_user_id'];
    $delete_user_query = "DELETE FROM users WHERE id = '$user_id'";
    if (!mysqli_query($conn, $delete_user_query)) {
        die("Delete user query failed: " . mysqli_error($conn));
    }
    header("Location: super_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Panel</title>
    <link rel="stylesheet" href="../../Styles/styles_admin.css">
    <style>
        .dashboard {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .dashboard-item {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            flex: 1;
            margin: 0 10px;
        }
        .dashboard-item h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .dashboard-item p {
            margin: 10px 0 0;
            font-size: 24px;
            font-weight: bold;
            color: #555;
        }
        .btn-validate-user {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-validate-user:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Super Admin Panel</h1>
        <a href="../../System/logout.php" class="logout">Logout</a>
    </div>

    <!-- Dashboard -->
    <div class="dashboard">
        <div class="dashboard-item">
            <h3>Total Users</h3>
            <p><?php echo $total_users; ?></p>
        </div>
        <div class="dashboard-item">
            <h3>Online Users</h3>
            <p><?php echo $online_users; ?></p>
        </div>
        <div class="dashboard-item">
            <h3>Total Orders</h3>
            <p><?php echo $total_orders; ?></p>
        </div>
    </div>

    <div class="container">
        <section class="user-management">
            <h2>Manage Users</h2>
            <form method="POST" action="">
                <button type="submit" name="validate_users" class="btn-validate">Validate Users</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>User Type</th>
                        <th>Section</th>
                        <th>Track</th>
                        <th>Valid</th>
                        <th>Validate</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($users_result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['usertype']); ?></td>
                            <td><?php echo htmlspecialchars($user['section']); ?></td>
                            <td><?php echo htmlspecialchars($user['track']); ?></td>
                            <td><?php echo $user['school_valid'] ? 'Yes' : 'No'; ?></td>
                            <td>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="validate_user" class="btn-validate-user">Validate</button>
                                </form>
                            </td>
                            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                            <td>
                                <a href="super_admin.php?delete_user_id=<?php echo $user['id']; ?>" class="btn-delete">Remove</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>

        <section class="order-management">
            <h2>Manage Orders</h2>
            <form method="POST" action="">
                <label for="user_id">User ID:</label>
                <input type="number" name="user_id" id="user_id" required>
                <label for="order_details">Order Details:</label>
                <textarea name="order_details" id="order_details" rows="4" required></textarea>
                <button type="submit" name="add_order" class="btn-add">Add Order</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>User</th>
                        <th>Section</th>
                        <th>Track</th>
                        <th>Item Names</th>
                        <th>Canteen</th>
                        <th>Total Price</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = mysqli_fetch_assoc($orders_result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($order['username']); ?></td>
                            <td><?php echo htmlspecialchars($order['item_names']); ?></td>
                            <td><?php echo htmlspecialchars($order['section']); ?></td>
                            <td><?php echo htmlspecialchars($order['track']); ?></td>
                            <td><?php echo htmlspecialchars($order['canteen']); ?></td>
                            <td>₱<?php echo htmlspecialchars($order['total_price']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td>
                                <a href="super_admin.php?delete_order_id=<?php echo $order['order_id']; ?>" class="btn-delete">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>