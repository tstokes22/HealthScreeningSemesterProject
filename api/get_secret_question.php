<?php
header('Content-Type: application/json');
include_once '../includes/db_connect.php';

if (isset($_SESSION['reset_secret_question'])) {
    echo json_encode([
        'success' => true,
        'question' => $_SESSION['reset_secret_question']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'question' => null
    ]);
}
?> 