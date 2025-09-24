<?php
// The shop_header starts the session and includes the database connection.
include 'includes/shop_header.php';

// --- SECURITY: AUTHENTICATION CHECK ---
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get and validate the Order ID from the URL.
$order_id = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);
if (!$order_id) {
    header('Location: order_history.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// --- FETCH THE MAIN ORDER DETAILS ---
// CRITICAL SECURITY: The query checks for BOTH the order_id from the URL AND the user_id from the session.
// This prevents one user from seeing another user's order by changing the URL.
$stmt_order = $conn->prepare(
    "SELECT o.*, u.name as user_name FROM orders o 
     JOIN users u ON o.user_id = u.id 
     WHERE o.id = ? AND o.user_id = ?"
);
$stmt_order->bind_param("ii", $order_id, $user_id);
$stmt_order->execute();
$order = $stmt_order->get_result()->fetch_assoc();
$stmt_order->close();

// If no order is found, it means it doesn't exist or doesn't belong to this user.
if (!$order) {
    echo "<div class='container my-5'><div class='alert alert-danger'>Order not found.</div></div>";
    include 'includes/shop_footer.php';
    exit();
}

// --- FETCH THE ITEMS FOR THIS ORDER ---
$order_items = [];
$stmt_items = $conn->prepare(
    "SELECT oi.quantity, oi.price, p.name as product_name 
     FROM order_items oi 
     JOIN products p ON oi.product_id = p.id 
     WHERE oi.order_id = ?"
);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();
while ($row = $result_items->fetch_assoc()) {
    $order_items[] = $row;
}
$stmt_items->close();
?>

<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Order Details</h2>
            <a href="order_history.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Order History
            </a>
        </div>
        <div class="card-body p-4 p-md-5">
            <!-- Order summary details -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Order Details:</h5>
                    <ul class="list-unstyled">
                       
                        <li><strong>Order Date:</strong> <?php echo date("F j, Y, g:i a", strtotime($order['created_at'])); ?></li>
                        <li><strong>Order Status:</strong> <span class="badge bg-info text-dark text-capitalize"><?php echo htmlspecialchars($order['order_status']); ?></span></li>
                        <li><strong>Payment Status:</strong> <span class="badge bg-warning text-dark text-capitalize"><?php echo htmlspecialchars($order['payment_status']); ?></span></li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Shipping Address:</h5>
                    <address>
                        <strong><?php echo htmlspecialchars($order['user_name']); ?></strong><br>
                        <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                    </address>
                </div>
            </div>

            <!-- Table of items in the order -->
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td class="text-center"><?php echo $item['quantity']; ?></td>
                                <td class="text-end">$<?php echo number_format($item['price'], 2); ?></td>
                                <td class="text-end">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="3" class="text-end fs-5">Total Amount</td>
                            <td class="text-end fs-5 text-success">$<?php echo number_format($order['total_amount'], 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/shop_footer.php'; ?>
