<?php
$host = "localhost";
$user = "root";       // default XAMPP user
$password = "";       // default XAMPP password is empty
$dbname = "chat_messaging_app"; // must match your DB name exactly

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>


