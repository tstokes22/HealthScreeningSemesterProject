<?php
// Database connection
$host = 'localhost';
$dbname = 'health_tracker'; // Replace with your actual database name
$username = 'root';
$password = '';

// Connect to the existing database
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    secret_question VARCHAR(255),
    secret_answer VARCHAR(255),
    is_admin BOOLEAN DEFAULT FALSE,
    last_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($sql)) {
    die("Error creating users table: " . $conn->error);
}

// Create health_entries table
$sql = "CREATE TABLE IF NOT EXISTS health_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    date DATE NOT NULL,
    symptoms TEXT,
    temperature DECIMAL(4,1),
    blood_pressure VARCHAR(20),
    heart_rate INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
if (!$conn->query($sql)) {
    die("Error creating health_entries table: " . $conn->error);
}

// Insert default admin user
$admin_password = password_hash('admin123', PASSWORD_DEFAULT);
$admin_secret_answer = password_hash('admin', PASSWORD_DEFAULT);
$sql = "INSERT IGNORE INTO users (username, password, email, is_admin, secret_question, secret_answer) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssssss', $admin_user, $admin_password, $admin_email, $is_admin, $admin_secret_question, $admin_secret_answer);
$admin_user = 'admin';
$admin_email = 'admin@healthtracker.com';
$is_admin = 1;
$admin_secret_question = 'What is your favorite color?';
$stmt->execute();
$stmt->close();

// Insert sample user
$user_password = password_hash('password123', PASSWORD_DEFAULT);
$user_secret_answer = password_hash('buddy', PASSWORD_DEFAULT);
$sql = "INSERT IGNORE INTO users (username, password, email, secret_question, secret_answer) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sssss', $user1, $user_password, $user1_email, $user1_secret_question, $user_secret_answer);
$user1 = 'user1';
$user1_email = 'user1@example.com';
$user1_secret_question = 'What was the name of your first pet?';
$stmt->execute();
$stmt->close();

// Get user_id for user1
$sql = "SELECT id FROM users WHERE username = 'user1'";
$result = $conn->query($sql);
$user_id = null;
if ($result && $row = $result->fetch_assoc()) {
    $user_id = $row['id'];
}

// Output success message
echo "Database setup completed successfully!<br>";
echo "Default admin credentials: username: admin, password: admin123<br>";
echo "Sample user credentials: username: user1, password: password123";

$conn->close();
?>