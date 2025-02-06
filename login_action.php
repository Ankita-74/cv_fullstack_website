<?php
session_start();

// Hardcoded credentials (you should replace this with database logic)
$validEmail = 'ankita@gmail.com';
$validPassword = 'Ankitaaa@7411';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the email and password from the form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate credentials
    if ($email === $validEmail && $password === $validPassword) {
        // If the credentials are correct, create a session
        $_SESSION['loggedin'] = true;
        $_SESSION['email'] = $email;
        
        // Redirect to the protected page (e.g., dashboard)
        header('Location: dashboard.php');
        exit();
    } else {
        // If the credentials are invalid, redirect back to login with an error message
        header('Location: login.php?error=Invalid email or password');
        exit();
    }
}
?>
