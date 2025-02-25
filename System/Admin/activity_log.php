<?php
session_start();
include '../../System/db_connection.php';

// Pagination settings
$logs_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $logs_per_page;

// Filters
$search = $_GET['search'] ?? '';
$filter_date = $_GET['filter_date'] ?? '';

// Build query with filters
$query = "SELECT * FROM activity_log WHERE 1";

if (!empty($search)) {
    $query .= " AND (username LIKE ? OR action LIKE ?)";
}
if (!empty($filter_date)) {
    $query .= " AND DATE(timestamp) = ?";
}

$query .= " ORDER BY timestamp DESC LIMIT ?, ?";

// Prepare statement
$stmt = $conn->prepare($query);

if (!empty($search) && !empty($filter_date)) {
    $like_search = "%$search%";
    $stmt->bind_param("sssii", $like_search, $like_search, $filter_date, $offset, $logs_per_page);
} elseif (!empty($search)) {
    $like_search = "%$search%";
    $stmt->bind_param("ssii", $like_search, $like_search, $offset, $logs_per_page);
} elseif (!empty($filter_date)) {
    $stmt->bind_param("sii", $filter_date, $offset, $logs_per_page);
} else {
    $stmt->bind_param("ii", $offset, $logs_per_page);
}

$stmt->execute();
$log_result = $stmt->get_result();

// Count total logs for pagination
$count_query = "SELECT COUNT(*) AS total FROM activity_log WHERE 1";
if (!empty($search)) {
    $count_query .= " AND (username LIKE '%$search%' OR action LIKE '%$search%')";
}
if (!empty($filter_date)) {
    $count_query .= " AND DATE(timestamp) = '$filter_date'";
}

$count_result = $conn->query($count_query);
$total_logs = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_logs / $logs_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log</title>
    <link rel="stylesheet" href="../../Styles/styles_admin.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h1>Activity Log</h1>

        <div class="search-filter-container">
            <form method="GET">
                <input type="text" name="search" placeholder="Search username or action" value="<?= htmlspecialchars($search) ?>">
                <input type="date" name="filter_date" value="<?= htmlspecialchars($filter_date) ?>">
                <button type="submit">Filter</button>
                <a href="activity_log.php">Reset</a>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Action</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($log = $log_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($log['log_id']) ?></td>
                        <td><?= htmlspecialchars($log['username']) ?></td>
                        <td><?= htmlspecialchars($log['action']) ?></td>
                        <td><?= htmlspecialchars($log['timestamp']) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php if ($page > 1) : ?>
                <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&filter_date=<?= urlencode($filter_date) ?>">Previous</a>
            <?php endif; ?>

            <span>Page <?= $page ?> of <?= $total_pages ?></span>

            <?php if ($page < $total_pages) : ?>
                <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&filter_date=<?= urlencode($filter_date) ?>">Next</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
