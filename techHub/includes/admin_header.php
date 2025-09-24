<?php
// Securely start the session only if it's not already active.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get the current page name for highlighting the active navigation link.
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Using the same nude color palette but with some variations for the admin area */
        :root {
            --primary-nude: #a89078;
            --primary-nude-hover: #8a735d; 
            --bg-light-nude: #f5f1ed;
            --text-dark-nude: #4a3f35;
            --navbar-bg-admin: #3d342e; /* Same dark background for navbar */
        }
        body {
            background-color: #f8f9fa; /* A slightly cooler background for the admin area */
            font-family: 'Montserrat', sans-serif;
        }
        .navbar {
            background-color: var(--navbar-bg-admin) !important;
        }
        .navbar .navbar-brand, .navbar .nav-link { color: white !important; }
        .navbar .nav-link.active, .navbar .nav-link:hover { color: var(--primary-nude) !important; }
        .btn-primary-nude { background-color: var(--primary-nude); border-color: var(--primary-nude); color: white; }
        .btn-primary-nude:hover { background-color: var(--primary-nude-hover); border-color: var(--primary-nude-hover); }
        .card { border: none; border-radius: 0.75rem; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="admin_dashboard.php"><i class="fas fa-user-shield me-2"></i>Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <?php if (isset($_SESSION['admin_id'])): // Only show nav links if admin is logged in ?>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage == 'admin_dashboard.php') ? 'active' : ''; ?>" href="admin_dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage == 'products.php') ? 'active' : ''; ?>" href="products.php">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage == 'categories.php') ? 'active' : ''; ?>" href="categories.php">Categories</a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage == 'brands.php') ? 'active' : ''; ?>" href="brands.php">Brands</a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage == 'admin_customer_list.php') ? 'active' : ''; ?>" href="admin_customer_list.php">Customer List</a>
                </li>
                
            </ul>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="admin_logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mt-4">
