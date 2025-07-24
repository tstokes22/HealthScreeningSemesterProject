<?php
session_start();
header('Content-Type: application/json');
include_once '../includes/db_connect.php';

// Queries for each timeframe
$queryW = "SELECT YEARWEEK(created_at, 1) AS period, COUNT(*) AS count
           FROM users
           GROUP BY period
           ORDER BY period ASC";

$queryM = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS period, COUNT(*) AS count
           FROM users
           GROUP BY period
           ORDER BY period ASC";

$queryD = "SELECT DATE(created_at) AS period, COUNT(*) AS count
           FROM users
           GROUP BY period
           ORDER BY period ASC";

// Function to execute query and fetch results
function fetchData($conn, $query) {
    $result = $conn->query($query);
    $periods = [];
    $counts = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $periods[] = $row['period'];
            $counts[] = (int)$row['count'];
        }
    }
    return ['periods' => $periods, 'counts' => $counts];
}

// Get data for each timeframe
$dataDay = fetchData($conn, $queryD);
$dataWeek = fetchData($conn, $queryW);
$dataMonth = fetchData($conn, $queryM);

// Get total users count
$totalUsersResult = $conn->query("SELECT COUNT(*) AS total FROM users");
$totalUsers = 0;
if ($totalUsersResult) {
    $row = $totalUsersResult->fetch_assoc();
    $totalUsers = (int)$row['total'];
}

$totalEntriesResult = $conn->query("SELECT COUNT(*) AS total FROM health_entries");
$totalEntries = 0;
if ($totalEntriesResult) {
$row = $totalEntriesResult->fetch_assoc();
$totalEntries = (int)$row['total'];
}

// Count active users (logged in within last 7 days)
$activeQuery = "SELECT COUNT(*) AS active_count FROM users WHERE last_login >= NOW() - INTERVAL 1 DAY";

$activeCount = 0;
$res = $conn->query($activeQuery);
if ($res) {
    $row = $res->fetch_assoc();
    $activeCount = (int)$row['active_count'];
}

$inactiveCount = $totalUsers - $activeCount;

// Output JSON
echo json_encode([
    'active_users' => $activeCount,
    'inactive_users' => $inactiveCount,
    'total_entries' => $totalEntries,
    'total_users' => $totalUsers,
    'user_registrations' => [
        'day' => [
            'dates' => $dataDay['periods'],
            'counts' => $dataDay['counts']
        ],
        'week' => [
            'dates' => $dataWeek['periods'],
            'counts' => $dataWeek['counts']
        ],
        'month' => [
            'dates' => $dataMonth['periods'],
            'counts' => $dataMonth['counts']
        ]
    ]
]);