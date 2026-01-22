<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "config/db.php";

$currentUser = $_SESSION['user_id'] ?? 1;
$receiver_id = $_GET['receiver'] ?? 0;

if ($receiver_id == 0) exit;

// Fetch messages
$messages = mysqli_query($conn,
    "SELECT m.*, u.username FROM messages m
     JOIN users u ON m.sender_id = u.id
     WHERE (sender_id=$currentUser AND receiver_id=$receiver_id)
        OR (sender_id=$receiver_id AND receiver_id=$currentUser)
     ORDER BY created_at ASC"
);

while($msg = mysqli_fetch_assoc($messages)) {
    $class = $msg['sender_id'] == $currentUser ? 'you' : 'other';
    echo "<div class='message $class'>";
    echo "<div class='bubble'>";
    echo "<strong>" . htmlspecialchars($msg['username']) . "</strong><br>";
    echo htmlspecialchars($msg['message']) . "<br>";
    echo "<small style='font-size:10px;color:gray'>" . $msg['created_at'] . "</small>";
    echo "</div></div>";
}
?>
