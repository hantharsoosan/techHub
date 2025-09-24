<?php
// 1. Start the existing session to access its data.
session_start();

// 2. Unset all of the session variables.
// $_SESSION = array(); is a common way to do this.
$_SESSION = [];

// 3. Destroy the session itself.
// This invalidates the user's session cookie.
session_destroy();

// 4. Start a new, clean session just to pass the logout message.
session_start();
$_SESSION['message'] = ['type' => 'info', 'text' => 'You have been successfully logged out.'];

// 5. Redirect the user to the login page.
header("Location: user-login.php");
exit(); // Ensure no further code is executed after the redirect.
?>

