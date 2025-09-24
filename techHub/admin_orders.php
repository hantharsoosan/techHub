<?php
// --- SECURITY & SETUP ---
// This line acts as a guard, ensuring only logged-in admins can access this page.
include 'includes/admin_auth_check.php';
// Include the database connection to fetch order data.
include 'config/db.php';
// Include the dedicated header for the admin panel.
include 'includes/admin_header.php';

/*
 * FEATURES FOR THIS PAGE:
 * Centralized Order Hub: A complete list of all customer orders, featuring robust filtering and sorting options by status, date range, or customer.
 * Payment Reconciliation: Tools to track, verify, and log Cash-on-Delivery payments to maintain accurate transaction records.
 */

// --- FETCH ORDER DATA ---
// This query retrieves all orders and joins with the 'users' table to get the customer's name.
// The results are ordered by the creation date to show the most recent orders first.
$orders_result = $conn->query(
    "SELECT o.id, u.name as customer_name, o.total_amount, o.order_status, o.payment_status, o.created_at 
     FROM orders o
     JOIN users u ON o.user_id = u.id
     ORDER BY o.created_at DESC"
);

// Define the possible statuses for the dropdowns
$order_statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
$payment_statuses = ['Pending', 'Paid', 'Refunded'];
?>

<div class="card shadow-sm">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h3 class="mb-0"><i class="fas fa-receipt me-2"></i>Customer Orders</h3>
    </div>
    <div class="card-body">
        <!-- Display session messages for status updates -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message']['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']['text']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Total Amount</th>
                        <th>Order Status</th>
                        <th>Payment Status</th>
                        <th>Order Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orders_result && $orders_result->num_rows > 0): ?>
                        <?php while ($order = $orders_result->fetch_assoc()): ?>
                            <tr>
                                <td><strong>#<?php echo $order['id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <!-- Form to update order status -->
                                    <form action="admin_update_order_status.php" method="POST" class="status-form">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <input type="hidden" name="status_type" value="order_status">
                                        <select name="new_status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <?php foreach ($order_statuses as $status): ?>
                                                <option value="<?php echo $status; ?>" <?php echo ($order['order_status'] == $status) ? 'selected' : ''; ?>>
                                                    <?php echo $status; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                     <!-- Form to update payment status -->
                                    <form action="admin_update_order_status.php" method="POST" class="status-form">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <input type="hidden" name="status_type" value="payment_status">
                                        <select name="new_status" class="form-select form-select-sm" onchange="this.form.submit()">
                                             <?php foreach ($payment_statuses as $status): ?>
                                                <option value="<?php echo $status; ?>" <?php echo ($order['payment_status'] == $status) ? 'selected' : ''; ?>>
                                                    <?php echo $status; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </td>
                                <td><?php echo date("M j, Y, g:i a", strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <p class="mb-0">No orders have been placed yet.</p>
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
