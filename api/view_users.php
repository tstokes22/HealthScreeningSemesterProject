<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']) || $_SESSION['is_admin'] === '0') {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}

require_once '../includes/db_connect.php';

try {
  $stmt = $conn->prepare("SELECT id, username, email, created_at FROM users WHERE is_admin = 0");
  $stmt->execute();
  $result = $stmt->get_result();
  $users = [];

  while ($row = $result->fetch_assoc()) {
    $users[] = $row;
  }

  echo json_encode(['success' => true, 'users' => $users]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Database error']);
}