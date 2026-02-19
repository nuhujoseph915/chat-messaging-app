<?php
session_start();
require 'config/db.php';

$user_id = $_SESSION['user_id'];
$full_name = $_POST['full_name'];
$bio = $_POST['bio'];

$profile_pic_path = null;

if (!empty($_FILES['profile_pic']['name'])) {
    $folder = "uploads/profiles/";
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $filename = time() . "_" . $_FILES['profile_pic']['name'];
    $target = $folder . $filename;

    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)) {
        $profile_pic_path = $target;
    }
}

if ($profile_pic_path) {
    $sql = "UPDATE users SET full_name=?, bio=?, profile_pic=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $full_name, $bio, $profile_pic_path, $user_id);
} else {
    $sql = "UPDATE users SET full_name=?, bio=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $full_name, $bio, $user_id);
}

$stmt->execute();

header("Location: profile.php");
exit;
