<?php
session_start();
require 'config/db.php';

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="profile-box">
    <h2>Edit Profile</h2>

    <form action="update_profile.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="full_name" placeholder="Full Name"
               value="<?= htmlspecialchars($user['full_name']) ?>">

        <textarea name="bio" placeholder="Bio"><?= htmlspecialchars($user['bio']) ?></textarea>

        <input type="file" name="profile_pic">

        <?php if ($user['profile_pic']): ?>
            <img src="<?= $user['profile_pic'] ?>" class="avatar">
        <?php endif; ?>

        <button type="submit">Update Profile</button>
    </form>
</div>

</body>
</html>
