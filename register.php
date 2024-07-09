<?php
session_start();

include 'connection.php';

// Check if the registration form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Escape user inputs to prevent SQL injection
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];

    // Validate form data
    if (empty($username) || empty($password) || empty($confirm_password) || empty($email)) {
        exit('Please complete the registration form');
    }

    if ($password !== $confirm_password) {
        exit('Passwords do not match');
    }

    // Check if the username already exists
    $stmt = $conn->prepare('SELECT id FROM accounts WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo 'Username already exists, please choose another!';
    } else {
        // Username is available, insert the new account
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_stmt = $conn->prepare('INSERT INTO accounts (username, password, email) VALUES (?, ?, ?)');
        $insert_stmt->bind_param('sss', $username, $hashed_password, $email);

        if ($insert_stmt->execute()) {
            echo 'You have successfully registered! You can now <a href="index.html">login</a>.';
            exit;
        } else {
            echo 'Could not register account!';
        }
    }
    $stmt->close();
    $insert_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sign Up</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="register">
        <h1>SIGN UP</h1>
        <form action="register.php" method="post">
            <label for="username">
                <i class="fas fa-user"></i>
            </label>
            <input type="text" name="username" placeholder="Username" id="username" required>
            <label for="email">
                <i class="fas fa-envelope"></i>
            </label>
            <input type="email" name="email" placeholder="Email" id="email" required>
            <label for="password">
                <i class="fas fa-lock"></i>
            </label>
            <input type="password" name="password" placeholder="Password" id="password" required>
            <label for="confirm_password">
                <i class="fas fa-lock"></i>
            </label>
            <input type="password" name="confirm_password" placeholder="Confirm Password" id="confirm_password" required>
            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>
