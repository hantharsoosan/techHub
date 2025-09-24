<?php
// Start the session to set a feedback message.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Include the database connection.
include 'config/db.php';

// Only process POST requests.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and validate input data.
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
    $subject = trim(filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING));
    $message = trim(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING));

    // Check if data is valid.
    if ($name && $email && $subject && $message) {
        // Prepare and execute the SQL statement to insert the feedback.
        $stmt = $conn->prepare("INSERT INTO feedback (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);

        if ($stmt->execute()) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Thank you for your feedback! We have received your message.'];
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Sorry, there was an error sending your message. Please try again.'];
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Please fill out all fields with valid information.'];
    }
}

// Redirect the user back to the contact page.
header("Location: contact.php");
exit();
?>
