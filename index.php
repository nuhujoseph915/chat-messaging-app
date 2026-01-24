<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Include database connection
include "config/db.php";

// Get current user info
$currentUser = $_SESSION['user_id'];
$userResult = mysqli_query($conn, "SELECT username FROM users WHERE id=$currentUser");
$currentUserData = mysqli_fetch_assoc($userResult);

// TEMP: logged-in user
// $currentUser = $_SESSION['user_id'] ?? 1; // replace 1 with your user ID if needed

// -------------------------
// Handle sending message
// -------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message']) && !empty($_POST['receiver_id'])) {
    $sender = $currentUser;
    $receiver = mysqli_real_escape_string($conn, $_POST['receiver_id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    mysqli_query($conn, "INSERT INTO messages (sender_id, receiver_id, message) 
        VALUES ('$sender','$receiver','$message')");
}

// -------------------------
// Fetch users (exclude current)
// -------------------------
$users = mysqli_query($conn, "SELECT id, username FROM users WHERE id != $currentUser");

// -------------------------
// Determine selected receiver
// -------------------------
$receiver_id = $_POST['receiver_id'] ?? 0;

// -------------------------
// Fetch messages between users
// -------------------------
$messages = mysqli_query($conn,
    "SELECT * FROM messages 
     WHERE (sender_id=$currentUser AND receiver_id=$receiver_id) 
        OR (sender_id=$receiver_id AND receiver_id=$currentUser) 
     ORDER BY created_at ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - Messaging App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="chat-container">
        <div class="chat-wrapper">
            <!-- Chat Header -->
            <div class="chat-header">
                <div class="chat-header-title">
                    <div class="chat-header-avatar">💬</div>
                    <h2>
                        <?php 
                            if($receiver_id > 0) {
                                $receiver = mysqli_query($conn, "SELECT username FROM users WHERE id=$receiver_id");
                                $r = mysqli_fetch_assoc($receiver);
                                echo htmlspecialchars($r['username'] ?? 'Chat');
                            } else {
                                echo 'Select a contact';
                            }
                        ?>
                    </h2>
                </div>
                <div class="chat-header-actions">
                    <button class="chat-header-btn" title="Call">☎️</button>
                    <button class="chat-header-btn" title="Video">📹</button>
                    <a href="auth/logout.php" class="chat-header-btn" title="Logout" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">🚪</a>
                </div>
            </div>

            <!-- User Selection -->
            <div class="user-selector">
                <form method="post" id="selectForm">
                    <select name="receiver_id" id="userSelect" onchange="document.getElementById('selectForm').submit()">
                        <option value="">👥 Select a contact...</option>
                        <?php while($u = mysqli_fetch_assoc($users)) { ?>
                            <option value="<?= $u['id'] ?>" <?= ($receiver_id == $u['id'] ? 'selected' : '') ?>>
                                👤 <?= htmlspecialchars($u['username']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </form>
            </div>

            <!-- Chat Messages -->
            <div class="chat-body" id="chatBody">
                <?php 
                    if($receiver_id == 0) {
                        echo '<div class="empty-state">';
                        echo '<div class="empty-state-icon">💬</div>';
                        echo '<p>Select a contact to start chatting</p>';
                        echo '</div>';
                    } else {
                        $messageCount = 0;
                        while($msg = mysqli_fetch_assoc($messages)) { 
                            $messageCount++;
                            $isSender = $msg['sender_id'] == $currentUser;
                            $formattedTime = isset($msg['created_at']) ? date('g:i A', strtotime($msg['created_at'])) : date('g:i A');
                ?>
                    <div class="message <?= $isSender ? 'you' : 'other' ?>">
                        <div class="bubble">
                            <?= htmlspecialchars($msg['message']) ?>
                            <div class="bubble-time"><?= $formattedTime ?></div>
                        </div>
                    </div>
                <?php 
                        }
                        if($messageCount == 0) {
                            echo '<div class="empty-state">';
                            echo '<div class="empty-state-icon">📭</div>';
                            echo '<p>No messages yet. Say hi!</p>';
                            echo '</div>';
                        }
                    }
                ?>
            </div>

            <!-- Chat Footer -->
            <div class="chat-footer">
                <?php if($receiver_id > 0) { ?>
                    <form method="post">
                        <input type="hidden" name="receiver_id" value="<?= $receiver_id ?>">
                        <input type="text" name="message" class="form-control" placeholder="Type a message..." required>
                        <button type="submit" class="btn-send" title="Send">➤</button>
                    </form>
                <?php } else { ?>
                    <div style="width: 100%; text-align: center; color: var(--gray-500); padding: 1rem;">
                        <p>Select a contact to start messaging</p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom
        function scrollToBottom() {
            const chatBody = document.getElementById('chatBody');
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        // Auto-refresh chat every 2 seconds
        function loadMessages() {
            const receiver = <?= $receiver_id ?>;
            if(receiver == 0) return;

            fetch("fetch_messages.php?receiver=" + receiver)
                .then(res => res.text())
                .then(data => {
                    document.getElementById("chatBody").innerHTML = data;
                    scrollToBottom();
                });
        }

        // Load messages on page load and every 2 seconds
        window.addEventListener('load', scrollToBottom);
        setInterval(loadMessages, 2000);

        // Auto-scroll when new messages arrive
        const observer = new MutationObserver(scrollToBottom);
        const chatBody = document.getElementById('chatBody');
        observer.observe(chatBody, { childList: true });
    </script>
</body>
</html>
