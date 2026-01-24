<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "config/db.php";

$currentUser = $_SESSION['user_id'] ?? 1;
$receiver_id = $_GET['receiver'] ?? 0;

if ($receiver_id == 0) exit;

// Fetch messages with improved styling
$messages = mysqli_query($conn,
    "SELECT m.*, u.username FROM messages m
     JOIN users u ON m.sender_id = u.id
     WHERE (sender_id=$currentUser AND receiver_id=$receiver_id)
        OR (sender_id=$receiver_id AND receiver_id=$currentUser)
     ORDER BY created_at ASC"
);

$messageCount = 0;
while($msg = mysqli_fetch_assoc($messages)) {
    $messageCount++;
    $isSender = $msg['sender_id'] == $currentUser;
    $formattedTime = isset($msg['created_at']) ? date('g:i A', strtotime($msg['created_at'])) : date('g:i A');
    $class = $isSender ? 'you' : 'other';
    
    echo "<div class='message $class'>";
    echo "<div class='bubble'>";
    echo htmlspecialchars($msg['message']);
    echo "<div class='bubble-time'>$formattedTime</div>";
    echo "</div></div>";
}

if($messageCount == 0) {
    echo '<div class="empty-state">';
    echo '<div class="empty-state-icon">📭</div>';
    echo '<p>No messages yet. Say hi!</p>';
    echo '</div>';
}
?>
