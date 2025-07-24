<?php
header('Content-Type: application/json');
session_start();
require_once '../includes/db_connect.php';

if (!($_SESSION['loggedin'] ?? false) || ($_SESSION['is_admin'] ?? false) != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$email = $_POST['email'] ?? '';
$question = $_POST['secret_question'] ?? '';
$answer = $_POST['secret_answer'] ?? '';

if (!$username || !$password || !$email || !$question || !$answer) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

$hashedPass = password_hash($password, PASSWORD_DEFAULT);
$hashedAnswer = password_hash($answer, PASSWORD_DEFAULT);

try {
    $stmt = $conn->prepare("INSERT INTO users (username, password, email, secret_question, secret_answer) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $hashedPass, $email, $question, $hashedAnswer);
    $stmt->execute();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Username might already exist or other error.']);
}
?>