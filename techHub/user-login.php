<?php
// The top PHP block for handling login remains the same.
// It securely processes the login attempt and redirects on success.
session_start();
include 'config/db.php';

$email = $password = '';
$errors = [];
$login_error_message = '';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email)) {
        $errors['email'] = "Email is required.";
    }
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];

                $old_session_id = session_id();
                $update_cart_stmt = $conn->prepare("UPDATE cart SET user_id = ?, session_id = NULL WHERE session_id = ? AND user_id IS NULL");
                $update_cart_stmt->bind_param("is", $user['id'], $old_session_id);
                $update_cart_stmt->execute();
                $update_cart_stmt->close();

                $redirect_to = $_SESSION['redirect_to'] ?? 'index.php';
                unset($_SESSION['redirect_to']);

                $_SESSION['message'] = ['type' => 'success', 'text' => 'Welcome back, ' . htmlspecialchars($user['name']) . '!'];
                header("Location: " . $redirect_to);
                exit();
            } else {
                $login_error_message = "The email or password you entered is incorrect.";
            }
        } else {
            $login_error_message = "The email or password you entered is incorrect.";
        }
        $stmt->close();
    }
}

$pageTitle = "Login | The Nude Store";
include 'includes/shop_header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="card-title text-center mb-4">Login</h2>

                    <!-- (Message display logic remains the same) -->
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['message']['type']; ?> alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['message']['text']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['message']); ?>
                    <?php endif; ?>
                    <?php if (!empty($login_error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $login_error_message; ?>
                        </div>
                    <?php endif; ?>

                    <!-- CORRECTED: Form action now points to the correct file name. -->
                    <form action="user-login.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <div class="mb-4">
                            <!-- This container aligns the label and the new link -->
                            <div class="d-flex justify-content-between align-items-baseline">
                                <label for="password" class="form-label">Password</label>
                                <!-- NEW "FORGOT PASSWORD?" LINK ADDED HERE -->
                                <a href="forgot_password.php" class="form-text text-muted text-decoration-none">Forgot Password?</a>
                            </div>
                            <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary-nude">Login</button>
                        </div>
                    </form>
                    <hr class="my-4">
                    <div class="text-center">
                        <!-- CORRECTED: Link now points to a more standard file name. -->
                        <p>Don't have an account? <a href="user-register.php">Sign Up</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/shop_footer.php'; ?>