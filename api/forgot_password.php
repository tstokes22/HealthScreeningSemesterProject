<?php
header('Content-Type: application/json');
include_once '../includes/db_connect.php';

try {
    $username = $_POST['username'] ?? '';

    if (empty($username)) {
        throw new Exception('Please enter your username.');
    }

    $sql = "SELECT id, secret_question FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Database error.');
    }
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user || empty($user['secret_question'])) {
        echo json_encode(['success' => false, 'message' => 'User does not exist.']);
        exit;
    }

    $_SESSION['reset_username'] = $username;
    $_SESSION['reset_secret_question'] = $user['secret_question'];

    echo json_encode([
        'success' => true,
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 