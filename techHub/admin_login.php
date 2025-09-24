<?php
// Securely start the session only if it's not already active.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config/db.php';

$errors = [];
$email = '';

// If an admin is already logged in, redirect them to the dashboard.
if (isset($_SESSION['admin_id'])) {
    header('Location: admin_dashboard.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors['login'] = "Invalid email or password."; }
    if (empty($password)) { $errors['login'] = "Invalid email or password."; }

    if (empty($errors)) {
        // Fetch the admin by email. We also fetch the 'role' here.
        $stmt = $conn->prepare("SELECT id, name, password, role FROM admins WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($admin = $result->fetch_assoc()) {
            // Verify the submitted password against the hashed password from the database.
            if (password_verify($password, $admin['password'])) {
                // --- Successful Login ---
                session_regenerate_id(true); // Regenerate session ID for security.
                
                // Store admin info in the session.
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_role'] = $admin['role']; // Store the admin's role.
                
                // Redirect to the admin dashboard.
                header("Location: admin_dashboard.php");
                exit();
            }
        }
        // If the email was not found or the password was incorrect, set a generic error.
        $errors['login'] = "Invalid email or password.";
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
        /* --- Nude Color Palette --- */
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

        /* --- General Styles --- */
        body {
            background-color: var(--bg-light-nude);
            font-family: 'Montserrat', sans-serif;
            color: var(--text-dark-nude);
        }

        .card {
            border: 1px solid var(--border-nude);
            box-shadow: 0 4px 12px var(--shadow-color);
            border-radius: 0.75rem;
            background-color: var(--bg-card-nude);
        }
        
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid var(--border-nude);
            padding: 1.5rem;
        }

        .card-header h2, .card-header h3 {
             font-weight: 700;
        }

        .shadow {
             box-shadow: 0 0.5rem 1rem var(--shadow-color) !important;
        }

        /* --- Navbar --- */
        .navbar.bg-dark {
            background-color: var(--navbar-bg-nude) !important;
        }
        .navbar .navbar-brand, .navbar .nav-link {
            color: var(--text-light-nude);
            font-weight: 500;
        }
        .navbar .nav-link:hover, .navbar .navbar-brand:hover {
            color: var(--bg-light-nude);
        }
        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.25);
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }


        /* --- Buttons --- */
        .btn {
            border-radius: 0.5rem;
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            border: none;
        }
        .btn-primary-nude {
            background-color: var(--primary-nude);
            color: white;
        }
        .btn-primary-nude:hover {
            background-color: var(--primary-nude-hover);
            color: white;
        }
        
        /* --- Forms --- */
        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 1px solid var(--border-nude);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-nude);
            box-shadow: 0 0 0 0.25rem rgba(168, 144, 120, 0.25);
        }

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
                    <h2 class="card-title text-center mb-4">Admin Portal Login</h2>
                    
                    <!-- Display success message from registration -->
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['message']['type']; ?> alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['message']['text']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['message']); ?>
                    <?php endif; ?>

                    <!-- Display login error message -->
                    <?php if (!empty($errors['login'])): ?>
                        <div class="alert alert-danger"><?php echo $errors['login']; ?></div>
                    <?php endif; ?>

                    <form action="admin_login.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary-nude">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

