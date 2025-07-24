<?php
header('Content-Type: application/json');
include_once '../includes/db_connect.php';

try {
    $username = $_SESSION['reset_username'] ?? null;
    $submitted_answer = $_POST['secret_answer'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    if (empty($username) || empty($submitted_answer) || empty($new_password)) {
        throw new Exception('Invalid session or missing fields. Please start over.');
    }

    // Get hashed secret answer from the database
    $sql = "SELECT secret_answer FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Database error.');
    }
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        throw new Exception('Could not verify user. Please start over.');
    }

    // If answer is correct, update the password
    if (password_verify($submitted_answer, $user['secret_answer'])) {

    
    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Database error.');
    }
    $stmt->bind_param('ss', $new_hashed_password, $username);
    $stmt->execute();
    $stmt->close();

    unset($_SESSION['reset_username']);
    unset($_SESSION['reset_secret_question']);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Password has been reset successfully. Redirecting to login...'
    ]);
    
} catch (Exception $e) {
    unset($_SESSION['reset_username']);
    unset($_SESSION['reset_secret_question']);
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}