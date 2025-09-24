<?php
// --- SECURITY & SETUP ---
// This acts as a guard to ensure only logged-in admins can access this page.
include 'includes/admin_auth_check.php';
// Include the database connection to perform our calculations.
include 'config/db.php';
// Include the dedicated header for the admin panel layout and navigation.
include 'includes/admin_header.php';

// --- 1. FETCH KEY SALES METRICS ---

// Calculate Total Revenue from all completed orders.
$revenue_result = $conn->query("SELECT SUM(total_amount) as total_revenue FROM orders");
$total_revenue = $revenue_result->fetch_assoc()['total_revenue'] ?? 0;

// Count the Total Number of Orders placed.
$orders_count_result = $conn->query("SELECT COUNT(id) as total_orders FROM orders");
$total_orders = $orders_count_result->fetch_assoc()['total_orders'] ?? 0;

// Calculate the Average Order Value. We check if total_orders is greater than zero to avoid division by zero errors.
$average_order_value = ($total_orders > 0) ? $total_revenue / $total_orders : 0;

// --- 2. FETCH TOP-SELLING PRODUCTS ---
// This query is the heart of the report. It does the following:
// 1. Joins `order_items` with the `products` table to get product names.
// 2. Groups the results by each product.
// 3. Sums the quantity sold for each product.
// 4. Orders the results by the total quantity sold to find the best-sellers.
// 5. Limits the result to the Top 10 products.
$top_products_result = $conn->query(
    "SELECT p.name, SUM(oi.quantity) as total_sold
     FROM order_items oi
     JOIN products p ON oi.product_id = p.id
     GROUP BY p.id, p.name
     ORDER BY total_sold DESC
     LIMIT 10"
);
?>

<div class="card shadow-sm">
    <div class="card-header bg-light">
        <h3><i class="fas fa-chart-line me-2"></i>Sales Report</h3>
    </div>
    <div class="card-body">
        <!-- Summary Metrics Row -->
        <div class="row text-center mb-4">
            <div class="col-md-4 mb-3">
                <div class="card bg-light h-100">
                    <div class="card-body">
                        <h6 class="card-title text-uppercase text-muted">Total Revenue</h6>
                        <p class="card-text fs-4 fw-bold">$<?php echo number_format($total_revenue, 2); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                 <div class="card bg-light h-100">
                    <div class="card-body">
                        <h6 class="card-title text-uppercase text-muted">Total Orders</h6>
                        <p class="card-text fs-4 fw-bold"><?php echo number_format($total_orders); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                 <div class="card bg-light h-100">
                    <div class="card-body">
                        <h6 class="card-title text-uppercase text-muted">Average Order Value</h6>
                        <p class="card-text fs-4 fw-bold">$<?php echo number_format($average_order_value, 2); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <hr>

        <!-- Top-Selling Products Table -->
        <h4 class="mt-4 mb-3">Top-Selling Products</h4>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Rank</th>
                        <th>Product Name</th>
                        <th class="text-end">Total Units Sold</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($top_products_result && $top_products_result->num_rows > 0): ?>
                        <?php $rank = 1; ?>
                        <?php while ($product = $top_products_result->fetch_assoc()): ?>
                            <tr>
                                <td><strong>#<?php echo $rank++; ?></strong></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td class="text-end"><?php echo $product['total_sold']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-4">
                                <p class="mb-0">No sales data is available yet to determine top-selling products.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
// Include the standard footer to close the page structure.
include 'includes/footer.php'; 
?>

