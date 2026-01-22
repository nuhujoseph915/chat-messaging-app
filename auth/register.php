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
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

<h2>Register</h2>

<?php
// Display messages
if (isset($error)) {
    echo "<div class='alert alert-danger'>$error</div>";
}
if (isset($success)) {
    echo "<div class='alert alert-success'>$success</div>";
}
?>

<form method="POST">
    <div class="mb-2">
        <label>Username</label>
        <input type="text" name="username" class="form-control" placeholder="Enter username" required>
    </div>
    <div class="mb-2">
        <label>Email</label>
        <input type="email" name="email" class="form-control" placeholder="Enter email" required>
    </div>
    <div class="mb-2">
        <label>Password</label>
        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
    </div>
    <button type="submit" name="register" class="btn btn-primary">Register</button>
</form>

</body>
</html>
