<?php
header('Content-Type: application/json');
include_once '../includes/db_connect.php';

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$secret_question = $_POST['secret_question'] ?? '';
$secret_answer = $_POST['secret_answer'] ?? '';

if (empty($username) || empty($email) || empty($password) || empty($secret_question) || empty($secret_answer)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
    exit;
}

if ($password !== $confirm_password) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$hashed_secret_answer = password_hash($secret_answer, PASSWORD_DEFAULT);

try {
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, secret_question, secret_answer) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $email, $hashed_password, $secret_question, $hashed_secret_answer);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Account created!']);
    } else {
        throw new Exception('Cannot create account.');
    }
} catch (Exception $e) {
    if ($conn->errno === 1062) {
        echo json_encode(['success' => false, 'message' => 'Username or email already exists.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'An error occurred during registration.']);
    }
}

$stmt->close();
$conn->close();
?> 