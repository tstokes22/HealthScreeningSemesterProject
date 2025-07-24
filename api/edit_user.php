<?php
header('Content-Type: application/json');
session_start();
require_once '../includes/db_connect.php';

// Security check: must be logged in and admin
if (!isset($_SESSION['loggedin']) || $_SESSION['is_admin'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Collect and validate POST data
$user_id = $_POST['user_id'] ?? '';
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';

if (!is_numeric($user_id) || empty($username) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $username, $email, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made or user not found']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}