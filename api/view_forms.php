<?php
require_once __DIR__ . '/../includes/db_connect.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT date, symptoms, temperature, blood_pressure, heart_rate, notes, created_at FROM health_forms WHERE user_id = ? ORDER BY date DESC, id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$forms = [];
while ($row = $result->fetch_assoc()) {
    $forms[] = $row;
}
$stmt->close();
$conn->close();
echo json_encode(['success' => true, 'forms' => $forms]); 