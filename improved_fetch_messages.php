<?php
// improved_fetch_messages.php

// Database connection setup using PDO
$dsn = 'mysql:host=localhost;dbname=your_database_name';
$username = 'your_username';
$password = 'your_password';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log('Connection failed: ' . $e->getMessage());
    exit('Database connection error.');
}

// CSRF token validation
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        exit('CSRF token validation failed.');
    }
}

// Rate limiting setup
$ip = $_SERVER['REMOTE_ADDR'];
$rateLimitKey = 'rate_limit_' . $ip;
$rateLimit = 10; // allow 10 requests per minute
$timeFrame = 60;

if (!isset($_SESSION[$rateLimitKey])) {
    $_SESSION[$rateLimitKey] = 0;
}

if ($_SESSION[$rateLimitKey] < $rateLimit) {
    $_SESSION[$rateLimitKey]++;
} else {
    exit('Rate limit exceeded. Please wait before trying again.');
}

// Input sanitization
$user_id = intval($_POST['user_id']); // example input

// Prepared statement for secure message fetching
try {
    $stmt = $pdo->prepare('SELECT * FROM messages WHERE user_id = :user_id');
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($messages);
} catch (PDOException $e) {
    error_log('Error fetching messages: ' . $e->getMessage());
    exit('Error fetching messages.');
}
?>