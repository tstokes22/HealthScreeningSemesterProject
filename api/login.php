<?php

header('Content-Type: application/json');
include_once '../includes/db_connect.php';

// Data comes from form from the index.html fetch
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required.']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT id, username, password, is_admin FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Regenerate session ID
        session_regenerate_id(true);

        // Set simple session variables
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];
        
        // Sets last_login to now
        if (isset($_SESSION['user_id'])) {
        $userId = intval($_SESSION['user_id']);
        $conn->query("UPDATE users SET last_login = NOW() WHERE id = $userId");
        }
        
        echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'redirect' => $user['is_admin'] ? 'admin_dashboard.html' : 'dashboard.html'
         ]);
        
//        include 'is_active.php';
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'A database error occurred.']);
}

$stmt->close();
$conn->close();
?> 