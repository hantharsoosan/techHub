<?php
// Securely start the session only if it's not already active.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config/db.php';

$errors = [];
$name = '';
$email = '';

// If an admin is already logged in, redirect them away from the registration page.
if (isset($_SESSION['admin_id'])) {
    header('Location: admin_dashboard.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // --- Validation ---
    if (empty($name)) { $errors['name'] = "Name is required."; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors['email'] = "A valid email is required."; }
    if (strlen($password) < 6) { $errors['password'] = "Password must be at least 6 characters long."; }
    if ($password !== $password_confirm) { $errors['password_confirm'] = "Passwords do not match."; }

    // Check if the email is already in use in the admins table.
    $stmt_check = $conn->prepare("SELECT id FROM admins WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        $errors['email'] = "This email address is already registered for an admin account.";
    }
    $stmt_check->close();

    // If there are no errors, proceed with creating the new admin.
    if (empty($errors)) {
        // Hash the password for secure storage.
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // The 'role' column will automatically use its default value ('admin') from the database schema.
        $stmt_insert = $conn->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("sss", $name, $email, $hashed_password);
        
        if ($stmt_insert->execute()) {
            // Set a success message and redirect to the login page.
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Admin registration successful! You may now log in.'];
            header("Location: admin_login.php");
            exit();
        } else {
            $errors['db'] = "A database error occurred. Could not register the admin.";
        }
        $stmt_insert->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        /* --- Nude Color Palette (Copied from admin_login.php for consistency) --- */
        :root {
            --bg-light-nude: #fdfaf6;
            --bg-card-nude: #ffffff;
            --navbar-bg-nude: #d3c4b3;
            --primary-nude: #a89078;
            --primary-nude-hover: #8f7962;
            --secondary-nude: #c3b8ad;
            --secondary-nude-hover: #a99d92;
            --text-dark-nude: #50463f;
            --text-light-nude: #ffffff;
            --border-nude: #e0d9d1;
            --shadow-color: rgba(0, 0, 0, 0.05);
        }
        body { background-color: var(--bg-light-nude); font-family: 'Montserrat', sans-serif; color: var(--text-dark-nude); }
        .card { border: 1px solid var(--border-nude); box-shadow: 0 4px 12px var(--shadow-color); border-radius: 0.75rem; background-color: var(--bg-card-nude); }
        .navbar.bg-dark { background-color: var(--navbar-bg-nude) !important; }
        .navbar .navbar-brand, .navbar .nav-link { color: var(--text-light-nude); font-weight: 500; }
        .btn { border-radius: 0.5rem; padding: 0.6rem 1.2rem; font-weight: 500; border: none; }
        .btn-primary-nude { background-color: var(--primary-nude); color: white; }
        .btn-primary-nude:hover { background-color: var(--primary-nude-hover); color: white; }
        .form-control, .form-select { border-radius: 0.5rem; border: 1px solid var(--border-nude); }
        .form-control:focus, .form-select:focus { border-color: var(--primary-nude); box-shadow: 0 0 0 0.25rem rgba(168, 144, 120, 0.25); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="#">
        <i class="fas fa-store"></i> Store Admin
    </a>
  </div>
</nav>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h2 class="card-title text-center mb-4">Admin Registration</h2>
                    
                    <!-- Display any validation or database errors -->
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php foreach ($errors as $error): ?>
                                <p class="mb-0"><?php echo $error; ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form action="admin_register.php" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-4">
                            <label for="password_confirm" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary-nude">Register</button>
                        </div>
                    </form>
                    <hr class="my-4">
                    <div class="text-center">
                        <p>Already have an account? <a href="admin_login.php">Login here</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
$stmt->close();
include 'includes/shop_footer.php'; 
?>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>