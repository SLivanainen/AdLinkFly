<?php
$host = 'sqlXXX.infinityfree.com'; // Replace with your InfinityFree host
$username = 'if0_XXXXXXX'; // Replace with your InfinityFree username
$password = 'XXXXXXXX'; // Replace with your InfinityFree password
$database = 'if0_XXXXXXX'; // Replace with your InfinityFree database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create URLs table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS urls (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    long_url TEXT NOT NULL,
    short_code VARCHAR(10) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    clicks INT(11) DEFAULT 0
)";

if (!$conn->query($sql)) {
    die("Error creating table: " . $conn->error);
}

function generateShortCode($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>
