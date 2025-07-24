<?php
include_once '../includes/db_connect.php';
header('Content-Type: application/json');

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    echo json_encode([
        'logged_in' => true,
        'username' => $_SESSION['username'] ?? 'User'
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
?> 