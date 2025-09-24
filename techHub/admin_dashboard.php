<?php
// --- SECURITY & SETUP ---
// This ensures only logged-in admins can see this page.
include 'includes/admin_auth_check.php';
// Include the database connection for our queries.
include 'config/db.php';
// Include the dedicated header for the admin panel.
include 'includes/admin_header.php';

// --- 1. FETCH DASHBOARD STATISTICS ---

// Get Total Revenue from all 'paid' or 'pending' (for COD) orders
$total_revenue_result = $conn->query("SELECT SUM(total_amount) as total_revenue FROM orders");
$total_revenue = $total_revenue_result->fetch_assoc()['total_revenue'] ?? 0;

// Get Total Number of Orders
$total_orders_result = $conn->query("SELECT COUNT(id) as total_orders FROM orders");
$total_orders = $total_orders_result->fetch_assoc()['total_orders'] ?? 0;

// Get Total Number of Products
$total_products_result = $conn->query("SELECT COUNT(id) as total_products FROM products");
$total_products = $total_products_result->fetch_assoc()['total_products'] ?? 0;

// Get Total Number of Customers
$total_users_result = $conn->query("SELECT COUNT(id) as total_users FROM users");
$total_users = $total_users_result->fetch_assoc()['total_users'] ?? 0;
?>

<!-- Personalized Welcome Message -->
<div class="alert alert-success shadow-sm">
    <h4 class="alert-heading">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</h4>
    <p>This is your central command center. Below is a summary of your store's performance.</p>
</div>

<!-- Summary Statistics Row -->
<div class="row mt-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-uppercase mb-1">Total Revenue</div>
                        <div class="h5 mb-0 fw-bold">$<?php echo number_format($total_revenue, 2); ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-primary-nude"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                 <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-uppercase mb-1">Total Orders</div>
                        <div class="h5 mb-0 fw-bold"><?php echo number_format($total_orders); ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-receipt fa-2x text-primary-nude"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                 <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-uppercase mb-1">Products</div>
                        <div class="h5 mb-0 fw-bold"><?php echo number_format($total_products); ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-box-open fa-2x text-primary-nude"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                 <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-uppercase mb-1">Customers</div>
                        <div class="h5 mb-0 fw-bold"><?php echo number_format($total_users); ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-users fa-2x text-primary-nude"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Dashboard Navigation Cards -->
<div class="row mt-4">
    <div class="col-md-6 mb-4">
        <div class="card text-center h-100 shadow-sm">
            <div class="card-body p-4"><i class="fas fa-receipt fa-3x text-primary-nude mb-3"></i>
                <h5 class="card-title">View All Orders</h5>
                <p class="card-text">Browse and manage all customer orders, view details, and update statuses.</p>
                <a href="admin_orders.php" class="btn btn-primary-nude stretched-link">Go to Orders</a>
            </div>
        </div>
    </div>
     <div class="col-md-6 mb-4">
        <div class="card text-center h-100 shadow-sm">
            <div class="card-body p-4"><i class="fas fa-chart-line fa-3x text-primary-nude mb-3"></i>
                <h5 class="card-title">Sales Report</h5>
                <p class="card-text">View your store's performance, including total sales and top-selling products.</p>
                <a href="admin_sales_report.php" class="btn btn-primary-nude stretched-link">View Report</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card text-center h-100 shadow-sm">
            <div class="card-body p-4"><i class="fas fa-box-open fa-3x text-primary-nude mb-3"></i>
                <h5 class="card-title">Manage Products</h5>
                <p class="card-text">Add, edit, view, and delete products in your store inventory.</p>
                <a href="products.php" class="btn btn-primary-nude stretched-link">Go to Products</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card text-center h-100 shadow-sm">
            <div class="card-body p-4"><i class="fas fa-tags fa-3x text-primary-nude mb-3"></i>
                <h5 class="card-title">Manage Categories</h5>
                <p class="card-text">Organize your products by creating, editing, or removing categories.</p>
                <a href="categories.php" class="btn btn-primary-nude stretched-link">Go to Categories</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card text-center h-100 shadow-sm">
            <div class="card-body p-4"><i class="fas fa-copyright fa-3x text-primary-nude mb-3"></i>
                <h5 class="card-title">Manage Brands</h5>
                <p class="card-text">Add or edit the product brands that you carry in your store.</p>
                <a href="brands.php" class="btn btn-primary-nude stretched-link">Go to Brands</a>
            </div>
        </div>
    </div>
</div>

<?php 
// Include the standard footer
include 'includes/footer.php'; 
?>

