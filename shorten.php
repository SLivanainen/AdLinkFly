<?php
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['url']) && filter_var($data['url'], FILTER_VALIDATE_URL)) {
        $long_url = $data['url'];
        
        // Check if URL already exists
        $stmt = $conn->prepare("SELECT short_code FROM urls WHERE long_url = ?");
        $stmt->bind_param("s", $long_url);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'short_url' => "http://AdLinkFly.wuaze.com/" . $row['short_code']
            ]);
            exit();
        }
        
        // Generate unique short code
        $short_code = generateShortCode(5);
        $attempts = 0;
        
        while ($attempts < 5) {
            // Check if short code exists
            $check_stmt = $conn->prepare("SELECT id FROM urls WHERE short_code = ?");
            $check_stmt->bind_param("s", $short_code);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows === 0) {
                break;
            }
            
            $short_code = generateShortCode(5 + $attempts);
            $attempts++;
        }
        
        // Insert into database
        $insert_stmt = $conn->prepare("INSERT INTO urls (long_url, short_code) VALUES (?, ?)");
        $insert_stmt->bind_param("ss", $long_url, $short_code);
        
        if ($insert_stmt->execute()) {
            echo json_encode([
                'success' => true,
                'short_url' => "http://AdLinkFly.wuaze.com/" . $short_code
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => "Failed to create short URL"
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'error' => "Invalid URL provided"
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => "Invalid request method"
    ]);
}
?>
