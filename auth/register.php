<?php
// Show all PHP errors for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include database connection
include("../config/db.php");

// Handle form submission
if (isset($_POST['register'])) {
    // Sanitize inputs
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password

    // Check if email already exists
    $check = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $check);

    if (mysqli_num_rows($result) > 0) {
        $error = "Email already registered!";
    } else {
        // Insert new user
        $sql = "INSERT INTO users (username, email, password) 
                VALUES ('$username', '$email', '$password')";

        if (mysqli_query($conn, $sql)) {
            $success = "Registration successful! You can now login.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Chat Messaging App</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>✨ Join Us Today</h1>
                <p>Create your messaging account</p>
            </div>
            
            <div class="auth-body">
                <?php
                if (isset($error)) {
                    echo "<div class='alert alert-danger'>❌ $error</div>";
                }
                if (isset($success)) {
                    echo "<div class='alert alert-success'>✅ $success</div>";
                }
                ?>

                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">👤 Username</label>
                        <input type="text" name="username" class="form-input" placeholder="Choose a username" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">📧 Email Address</label>
                        <input type="email" name="email" class="form-input" placeholder="Enter your email" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">🔐 Password</label>
                        <input type="password" name="password" class="form-input" placeholder="Create a strong password" required>
                    </div>
                    
                    <button type="submit" name="register" class="btn-submit">Create Account</button>
                </form>
            </div>
            
            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Sign in here →</a></p>
            </div>
        </div>
    </div>
</body>
</html>
