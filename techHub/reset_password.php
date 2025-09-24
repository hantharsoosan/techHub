<?php
// Securely start the session to check for verification status.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config/db.php';

// --- SECURITY CHECK ---
// This is the most important step on this page. It checks if the user has successfully
// passed the OTP verification step. If they have not, they are immediately redirected
// away from this page. This prevents anyone from accessing this page directly.
if (!isset($_SESSION['otp_verified_email'])) {
    // Set a message to inform the user why they were redirected.
    $_SESSION['message'] = ['type' => 'warning', 'text' => 'For your security, please verify your one-time code first.'];
    // Send them back to the beginning of the process.
    header('Location: forgot_password.php');
    exit();
}

// Get the verified email from the session.
$email = $_SESSION['otp_verified_email'];
$error = '';

// This block of code handles the form submission when the user enters their new password.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // --- Validation ---
    if (strlen($password) < 6) {
        $error = "For security, your password must be at least 6 characters long.";
    } elseif ($password !== $password_confirm) {
        $error = "The new passwords you entered do not match.";
    } else {
        // If validation passes, securely hash the new password.
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare and execute the query to update the user's password in the database.
        $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt_update->bind_param("ss", $hashed_password, $email);
        $stmt_update->execute();
        
        // Clean up the session variable so it can't be used again.
        unset($_SESSION['otp_verified_email']);

        // Set a success message and redirect the user to the login page.
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Your password has been successfully reset. Please log in with your new password.'];
        header('Location: user-login.php');
        exit();
    }
}

$pageTitle = "Set New Password | The Nude Store";
include 'includes/shop_header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h2 class="card-title text-center mb-4">Set Your New Password</h2>
                    <p class="text-center text-muted">You have successfully verified your identity. Please enter a new password for your account below.</p>
                    
                    <!-- Display any validation errors here -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <!-- This is the form where the user submits their new password -->
                    <form action="reset_password.php" method="POST">
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-4">
                            <label for="password_confirm" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary-nude">Reset Password and Log In</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/shop_footer.php'; ?>

