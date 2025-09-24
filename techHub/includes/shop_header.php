<?php
// Check if a session is not already active before starting one.
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it hasn't been started yet.
}

// Include the database connection file.
include_once __DIR__ . '/../config/db.php';

// Get the cart item count for both guests and logged-in users.
$cart_item_count = 0;
if (isset($conn)) {
    // First, check if the user is logged in.
    if (isset($_SESSION['user_id'])) {
        // For a logged-in user, count items by their user_id.
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT SUM(quantity) as total_items FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
    } else {
        // For a guest, count items by their session_id.
        $session_id = session_id();
        $stmt = $conn->prepare("SELECT SUM(quantity) as total_items FROM cart WHERE session_id = ?");
        $stmt->bind_param("s", $session_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $cart_item_count = (int)$row['total_items'];
    }
    $stmt->close();
}

// Get the current page's filename to set active navigation links and dynamic titles.
$currentPage = basename($_SERVER['PHP_SELF']);

// Set a dynamic page title based on the current page.
$pageTitle = "The Nude Store"; // Default title
if ($currentPage == 'index.php') {
    $pageTitle = "Home | The Nude Store";
} elseif ($currentPage == 'about.php') {
    $pageTitle = "About Us | The Nude Store";
} elseif ($currentPage == 'contact.php') {
    $pageTitle = "Contact Us | The Nude Store";
} elseif ($currentPage == 'cart_view.php') {
    $pageTitle = "Your Cart | The Nude Store";
} elseif ($currentPage == 'login.php') {
    $pageTitle = "Login | The Nude Store";
} elseif ($currentPage == 'register.php') {
    $pageTitle = "Register | The Nude Store";
} elseif ($currentPage == 'checkout.php') {
    $pageTitle = "Checkout | The Nude Store";
} elseif ($currentPage == 'order_history.php') {
    $pageTitle = "Order History | The Nude Store";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <!-- CSS Links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-nude: #a89078;
            --primary-nude-hover: #8a735d; 
            --bg-light-nude: #f5f1ed;
            --text-dark-nude: #4a3f35;
            --navbar-bg-nude: #3d342e;
        }
        body {
            background-color: var(--bg-light-nude);
            color: var(--text-dark-nude);
            font-family: 'Montserrat', sans-serif;
        }
        .navbar { background-color: var(--navbar-bg-nude) !important; }
        .navbar .nav-link, .navbar .navbar-brand { color: white !important; }
        .navbar .nav-link.active, .navbar .nav-link:hover { color: var(--primary-nude) !important; }
        .btn-primary-nude { background-color: var(--primary-nude); border-color: var(--primary-nude); color: white; }
        .btn-primary-nude:hover { background-color: var(--primary-nude-hover); border-color: var(--primary-nude-hover); }
        
        .carousel-item {
            height: 65vh;
            min-height: 400px;
            background: no-repeat center center scroll;
            background-size: cover;
        }
        .carousel-caption {
            bottom: 20%;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 2rem;
            border-radius: 0.5rem;
        }
        .carousel-caption h5 {
            font-size: 3rem;
            font-weight: 700;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">THE NUDE STORE</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#shopNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="shopNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>" href="index.php">Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage == 'about.php') ? 'active' : ''; ?>" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage == 'contact.php') ? 'active' : ''; ?>" href="contact.php">Contact</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="order_history.php"><i class="fas fa-receipt fa-fw me-2"></i>My Orders</a></li>
                            <li><a class="dropdown-item" href="admin_login.php"><i class="fas fa-user-shield fa-fw me-2"></i>Admin Panel</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-fw me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage == 'user-login.php') ? 'active' : ''; ?>" href="user-login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage == 'user-register.php') ? 'active' : ''; ?>" href="user-register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
            <a href="cart_view.php" class="btn <?php echo ($currentPage == 'cart_view.php') ? 'btn-light' : 'btn-outline-light'; ?> ms-lg-2">
                <i class="fas fa-shopping-cart me-1"></i> Cart
                <span class="badge bg-light text-dark ms-1"><?php echo $cart_item_count; ?></span>
            </a>
        </div>
    </div>
</nav>

<?php if ($currentPage == 'index.php'): // Only show the slideshow on the main shop page (index.php) ?>
<header>
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active" style="background-image: url('https://i.ytimg.com/vi/RRGOEvmKuB8/maxresdefault.jpg')">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Powerful Performance, Sleek Design</h5>
                    <p>Explore our new collection of high-end laptops for work and play.</p>
                </div>
            </div>
            <div class="carousel-item" style="background-image: url('https://img.lovepik.com/photo/40103/0321.jpg_wh860.jpg')">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Stay Connected in Style</h5>
                    <p>Discover the latest smartphones with cutting-edge camera technology.</p>
                </div>
            </div>
            <div class="carousel-item" style="background-image: url('https://img.freepik.com/premium-photo/modern-black-office-workspace-background-with-office-accessories-devices-mockup_67155-21510.jpg')">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Accessorize Your Tech</h5>
                    <p>Find the perfect gear to complement your devices, from cases to headphones.</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</header>
<?php endif; ?>

<div class="container my-4">

