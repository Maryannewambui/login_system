<?php
session_start();
include 'connection.php';

// Check if the login form data is submitted
if (!isset($_POST['username'], $_POST['password'])) {
    exit('Please fill both the username and password fields!');
}

// Prepare SQL statement to prevent SQL injection
if ($stmt = $conn->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($_POST['password'], $password)) {
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $id;
            header('Location: home.php');
        } else {
            echo 'Incorrect username and/or password!';
        }
    } else {
        echo 'Incorrect username and/or password!';
    }
    $stmt->close();
} else {
    echo 'Could not prepare statement!';
}
?>
