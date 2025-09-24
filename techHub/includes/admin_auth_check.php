<?php
// This script acts as a security guard for all admin pages.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the 'admin_id' session variable is NOT set.
// If it's not set, the user is not logged in as an admin.
if (!isset($_SESSION['admin_id'])) {
    // Set a message to inform them why they are being redirected.
    $_SESSION['message'] = ['type' => 'warning', 'text' => 'Please log in to access the admin panel.'];
    // Redirect them to the admin login page.
    header('Location: admin_login.php');
    // Stop the script from running any further.
    exit();
}
?>
