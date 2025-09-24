<?php
// Securely start the session to store information between steps.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config/db.php';

$otp_generated = false;
$otp_code_for_display = '';
$error = '';

// This block handles the form submission when a user requests a code.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($user = $result->fetch_assoc()) {
            // --- User's email was found, so we generate a secure OTP ---

            // 1. Create a random 6-digit code.
            $otp_code_for_display = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // 2. Securely hash the code before storing it in the database.
            $otp_hash = password_hash($otp_code_for_display, PASSWORD_DEFAULT);

            // 3. Set the code to expire in 10 minutes for security.
            $expires = new DateTime('now', new DateTimeZone('UTC'));
            $expires->add(new DateInterval('PT10M')); // 10 minute expiry
            $expires_at = $expires->format('Y-m-d H:i:s');

            // 4. Store the HASH and expiry date in the database for this user.
            $stmt_update = $conn->prepare("UPDATE users SET otp_hash = ?, otp_expires_at = ? WHERE id = ?");
            $stmt_update->bind_param("ssi", $otp_hash, $expires_at, $user['id']);
            $stmt_update->execute();

            // 5. Store the user's email in the session to carry over to the verification step.
            $_SESSION['otp_email'] = $email;

            $otp_generated = true;
        } else {
            // IMPORTANT: For security, show a generic message even if the email doesn't exist.
            // This prevents attackers from guessing which emails are registered.
            $error = "If an account with that email exists, a verification code has been generated.";
        }
        $stmt_check->close();
    }
}

$pageTitle = "Forgot Password | The Nude Store";
include 'includes/shop_header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h2 class="card-title text-center mb-4">Forgot Password</h2>

                    <?php if ($otp_generated): ?>
                        <div class="alert alert-success">
                            <p>A 6-digit verification code has been generated. In a real application, this would be sent to your email.</p>
                            <p><strong>For demonstration purposes, your code is:</strong></p>
                            <h3 class="text-center my-3 user-select-all"><?php echo $otp_code_for_display; ?></h3>
                            <div class="d-grid">
                                <a href="verify_otp.php" class="btn btn-primary-nude">Proceed to Verification</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center mb-4">Enter your email address to receive a 6-digit verification code.</p>
                        <?php if ($error): ?><div class="alert alert-info"><?php echo $error; ?></div><?php endif; ?>
                        <form action="forgot_password.php" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary-nude">Get Verification Code</button>
                            </div>
                        </form>
                    <?php endif; ?>
                    <div class="text-center mt-3">
                        <a href="user-login.php">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/shop_footer.php'; ?>