<?php
session_start();

// If user is already logged in, redirect them away from the register page
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

include 'config/db.php';
$name = $email = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // --- Validation Checks ---
    if (empty($name)) {
        $errors['name'] = "Name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "A valid email is required.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors['email'] = "Email address already in use.";
        }
        $stmt->close();
    }
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    }
    if (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters long.";
    }
    if ($password !== $password_confirm) {
        $errors['password_confirm'] = "Passwords do not match.";
    }

    // --- If Validation Passes, Insert User ---
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            // --- SUCCESSFUL REGISTRATION LOGIC ---

            // REMOVED: Automatic login after registration
            // $_SESSION['user_id'] = $stmt->insert_id;
            // $_SESSION['user_name'] = $name;

            // REMOVED: Cart merge logic, as the user is not logged in yet.
            // This will now be handled when they log in for the first time.

            // ADDED: Set a success message to be displayed on the login page
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Registration successful! You can now log in.'];

            // CHANGED: Redirect to the login page instead of the shop
            header("Location: user-login.php");
            exit();

        } else {
            $errors['db'] = "Error during registration. Please try again.";
        }
        $stmt->close();
    }
}

// Set the page title for the header
$pageTitle = "Register | The Nude Store";
include 'includes/shop_header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card shadow">
                 <div class="card-body p-5">
                    <h2 class="card-title text-center mb-4">Create an Account</h2>
                    
                    <?php if (!empty($errors['db'])): ?>
                        <div class="alert alert-danger"><?php echo $errors['db']; ?></div>
                    <?php endif; ?>

                    <form action="user-register.php" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                            <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?php echo $errors['name']; ?></div><?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                             <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?php echo $errors['email']; ?></div><?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password" required>
                             <?php if (isset($errors['password'])): ?><div class="invalid-feedback"><?php echo $errors['password']; ?></div><?php endif; ?>
                        </div>
                        <div class="mb-4">
                            <label for="password_confirm" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control <?php echo isset($errors['password_confirm']) ? 'is-invalid' : ''; ?>" id="password_confirm" name="password_confirm" required>
                             <?php if (isset($errors['password_confirm'])): ?><div class="invalid-feedback"><?php echo $errors['password_confirm']; ?></div><?php endif; ?>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary-nude">Register</button>
                        </div>
                    </form>
                    <hr class="my-4">
                    <div class="text-center">
                        <p>Already have an account? <a href="user-login.php">Login here</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/shop_footer.php'; ?>

