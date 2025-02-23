<?php
include('../../System/db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['usertype'];

    // Validate input
    if (!in_array($new_role, ['user', 'staff', 'super_admin'])) {
        echo "Invalid role selected.";
        exit;
    }

    // Update user role in the database
    $query_update = "UPDATE users SET usertype = ? WHERE id = ?";
    $stmt = $conn->prepare($query_update);
    $stmt->bind_param("si", $new_role, $user_id);

    if ($stmt->execute()) {
        echo "User role updated successfully!";
    } else {
        echo "Failed to update role.";
    }
}
?>
