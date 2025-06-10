<?php
include 'db.php';

if (isset($_GET['code'])) {
    $short_code = $_GET['code'];
    
    // Prepare and bind
    $stmt = $conn->prepare("SELECT long_url FROM urls WHERE short_code = ?");
    $stmt->bind_param("s", $short_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $long_url = $row['long_url'];
        
        // Update click count
        $update_stmt = $conn->prepare("UPDATE urls SET clicks = clicks + 1 WHERE short_code = ?");
        $update_stmt->bind_param("s", $short_code);
        $update_stmt->execute();
        
        // Redirect to the long URL
        header("Location: " . $long_url);
        exit();
    } else {
        header("Location: index.php?error=invalid_link");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
