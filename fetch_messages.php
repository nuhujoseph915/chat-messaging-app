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
<?php
session_start();
require 'config/db.php';

$user_id = $_SESSION['user_id'];

$sql = "
SELECT 
    m.id,
    m.message,
    m.created_at,
    m.sender_id,
    u.username,
    u.full_name,
    u.profile_pic
FROM messages m
JOIN users u ON u.id = m.sender_id
WHERE m.sender_id = ? OR m.receiver_id = ?
ORDER BY m.created_at ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    // Check if message is from logged-in user
    $isMe = ($row['sender_id'] == $user_id) ? 'you' : 'other';

    echo "<div class='message $isMe'>";

    // Show avatar only for other users
    if ($isMe === 'other') {
        $avatar = $row['profile_pic'] ? $row['profile_pic'] : 'assets/default.png';
        echo "<img src='$avatar' class='avatar'>";
    }

    echo "<div class='bubble'>";
    echo "<strong>" . htmlspecialchars($row['full_name'] ?: $row['username']) . "</strong><br>";
    echo htmlspecialchars($row['message']) . "<br>";
    echo "<small>" . $row['created_at'] . "</small>";
    echo "</div>";

    echo "</div>";
}
