<?php
// Securely start the session to check for verification status.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config/db.php';

// --- SECURITY CHECK ---
// This is a critical step. If the user hasn't requested an OTP yet (meaning their
// email isn't in the session), they cannot access this page and are redirected.
if (!isset($_SESSION['otp_email'])) {
    header('Location: forgot_password.php');
    exit();
}

$error = '';

// This block handles the form submission when a user enters the 6-digit code.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $submitted_otp = trim($_POST['otp_code']);
    $email = $_SESSION['otp_email'];

    if (empty($submitted_otp) || !is_numeric($submitted_otp) || strlen($submitted_otp) != 6) {
        $error = "Please enter a valid 6-digit code.";
    } else {
        // Fetch the stored OTP hash and expiry from the database for the user's email.
        $stmt = $conn->prepare("SELECT otp_hash, otp_expires_at FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Check three things: 1. A user was found. 2. The submitted OTP matches the hash. 3. The code has not expired.
        if ($user && password_verify($submitted_otp, $user['otp_hash']) && new DateTime() < new DateTime($user['otp_expires_at'], new DateTimeZone('UTC'))) {
            // --- Verification Successful ---
            
            // Mark the user as verified in the session for the next step.
            $_SESSION['otp_verified_email'] = $email; 
            unset($_SESSION['otp_email']); // Clean up old session variable.
            
            // Clear the OTP from the database so it cannot be used again.
            $conn->query("UPDATE users SET otp_hash = NULL, otp_expires_at = NULL WHERE email = '$email'");

            // Redirect to the final step: resetting the password.
            header('Location: reset_password.php');
            exit();
        } else {
            // If any of the checks fail, show an error.
            $error = "The code is incorrect or has expired. Please request a new one.";
        }
    }
}

$pageTitle = "Verify Code | The Nude Store";
include 'includes/shop_header.php';
?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h2 class="card-title text-center mb-4">Enter Verification Code</h2>
                    <p class="text-center text-muted">A 6-digit code was generated for <strong><?php echo htmlspecialchars($_SESSION['otp_email']); ?></strong>. Please enter it below.</p>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form action="verify_otp.php" method="POST">
                        <div class="mb-3">
                            <label for="otp_code" class="form-label">6-Digit Code</label>
                            <input type="text" class="form-control" id="otp_code" name="otp_code" maxlength="6" required autofocus>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary-nude">Verify Code</button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <a href="forgot_password.php">Request a new code</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/shop_footer.php'; ?>
