<?php
require_once __DIR__ . '/../includes/db_connect.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$date = $_POST['date'] ?? null;
$symptoms = $_POST['symptoms'] ?? '';
$temperature = $_POST['temperature'] ?? null;
$blood_pressure = $_POST['blood_pressure'] ?? '';
$heart_rate = $_POST['heart_rate'] ?? null;
$notes = $_POST['notes'] ?? '';

if (!$date) {
    echo json_encode(['success' => false, 'message' => 'Date is required.']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO health_forms (user_id, date, symptoms, temperature, blood_pressure, heart_rate, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param('issdsis', $user_id, $date, $symptoms, $temperature, $blood_pressure, $heart_rate, $notes);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}
$stmt->close();
$conn->close(); 