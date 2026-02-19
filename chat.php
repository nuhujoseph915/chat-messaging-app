<?php
// -------------------------
// chat.php - Full working
// -------------------------

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "config/db.php";

// TEMP: logged-in user
$currentUser = $_SESSION['user_id'] ?? 1; // replace 1 with your user ID if needed

// -------------------------
// Handle sending message
// -------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message']) && !empty($_POST['receiver_id'])) {
    $sender = $currentUser;
    $receiver = $_POST['receiver_id'];
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
<title>Chat App</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #e5e7eb; display: flex; justify-content: center; padding: 20px; }
.phone { width: 360px; height: 640px; background: #fff; border-radius: 30px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
.header { background: #075e54; color: #fff; padding: 12px; text-align: center; font-weight: bold; }
.select-user { padding: 8px; background: #f0f2f5; }
.chat-body { flex: 1; padding: 10px; background: #efeae2; overflow-y: auto; }
.message { margin-bottom: 8px; display: flex; }
.message.you { justify-content: flex-end; }
.bubble { max-width: 75%; padding: 8px 12px; border-radius: 18px; font-size: 14px; }
.you .bubble { background: #dcf8c6; }
.other .bubble { background: #fff; }
.footer { padding: 10px; background: #f0f2f5; }
.footer form { display: flex; gap: 8px; }
.footer input { border-radius: 20px; }
.footer button { border-radius: 50%; width: 45px; height: 45px; }
</style>
</head>
<script>
function loadMessages() {
    fetch("fetch_messages.php")
        .then(response => response.text())
        .then(data => {
            document.getElementById("chat-box").innerHTML = data;
        });
}

setInterval(loadMessages, 2000);
loadMessages();
</script>

<body>

<div class="phone">

    <div class="header">💬 Chat App</div>

    <div class="select-user">
        <form method="post" id="selectForm">
            <select class="form-select" name="receiver_id" id="userSelect" onchange="document.getElementById('selectForm').submit()">
                <option value="">Select member</option>
                <?php while($u = mysqli_fetch_assoc($users)) { ?>
                    <option value="<?= $u['id'] ?>" <?= ($receiver_id == $u['id'] ? 'selected' : '') ?>>
                        <?= $u['username'] ?>
                    </option>
                <?php } ?>
            </select>
        </form>
    </div>

    <div class="chat-body" id="chatBody">
        <?php while($msg = mysqli_fetch_assoc($messages)) { ?>
            <div class="message <?= $msg['sender_id'] == $currentUser ? 'you' : 'other' ?>">
                <div class="bubble"><?= htmlspecialchars($msg['message']) ?></div>
            </div>
        <?php } ?>
    </div>

    <div class="footer">
        <form method="post">
            <input type="hidden" name="receiver_id" value="<?= $receiver_id ?>">
            <input type="text" name="message" class="form-control" placeholder="Type a message..." required>
            <button type="submit" class="btn btn-success">➤</button>
        </form>
    </div>

</div>

<script>
// Auto-refresh chat every 2 seconds
function loadMessages() {
    const receiver = <?= $receiver_id ?>;
    if(receiver == 0) return;

    fetch("fetch_messages.php?receiver=" + receiver)
        .then(res => res.text())
        .then(data => {
            document.getElementById("chatBody").innerHTML = data;
        });
}

setInterval(loadMessages, 2000);
</script>

</body>
</html>

