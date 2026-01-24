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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Chat Messaging App</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>💬 Welcome Back</h1>
                <p>Sign in to your messaging account</p>
            </div>
            
            <div class="auth-body">
                <?php
                if (isset($error)) {
                    echo "<div class='alert alert-danger'>❌ $error</div>";
                }
                ?>

                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">📧 Email Address</label>
                        <input type="email" name="email" class="form-input" placeholder="Enter your email" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">🔐 Password</label>
                        <input type="password" name="password" class="form-input" placeholder="Enter your password" required>
                    </div>
                    
                    <button type="submit" name="login" class="btn-submit">Sign In</button>
                </form>
            </div>
            
            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php">Create one now →</a></p>
            </div>
        </div>
    </div>
</body>
</html>
