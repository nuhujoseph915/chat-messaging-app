<?php
// Show all PHP errors
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include database connection
include("../config/db.php");

// Start session to track login
session_start();

// Handle form submission
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // plain password from form

    // Check if user exists
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: ../index.php"); // Redirect to home page
            exit();
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

<h2>Login</h2>

<?php
if (isset($error)) {
    echo "<div class='alert alert-danger'>$error</div>";
}
?>

<form method="POST">
    <div class="mb-2">
        <label>Email</label>
        <input type="email" name="email" class="form-control" placeholder="Enter email" required>
    </div>
    <div class="mb-2">
        <label>Password</label>
        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
    </div>
    <button type="submit" name="login" class="btn btn-success">Login</button>
</form>

</body>
</html>
