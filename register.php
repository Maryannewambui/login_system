<?php
session_start();

include 'connection.php';
// Check if the registration form data is submitted
if (isset($_POST['username'], $_POST['password'], $_POST['confirm_password'], $_POST['email'])) {

    // Validate form data
    if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['confirm_password']) || empty($_POST['email'])) {
        exit('Please complete the registration form');
    }

    if ($_POST['password'] !== $_POST['confirm_password']) {
        exit('Passwords do not match');
    }

    // Check if the username already exists
    if ($stmt = $con->prepare('SELECT id FROM accounts WHERE username = ?')) {
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo 'Username already exists, please choose another!';
        } else {
            // Username is available, insert the new account
            if ($stmt = $con->prepare('INSERT INTO accounts (username, password, email) VALUES (?, ?, ?)')) {
                // Hash the password
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt->bind_param('sss', $_POST['username'], $password, $_POST['email']);
                $stmt->execute();
                echo 'You have successfully registered! You can now login.';
                // Redirect to login page
                header('Location: index.html');
                exit;
            } else {
                echo 'Could not prepare statement!';
            }
        }
        $stmt->close();
    } else {
        echo 'Could not prepare statement!';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>sign up</title>
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


