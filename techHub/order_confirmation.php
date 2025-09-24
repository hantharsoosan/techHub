<?php
// The shop_header starts the session and includes the database connection.
// It must be included first to ensure all dependencies are available.
include 'includes/shop_header.php';

// --- 1. SECURITY: AUTHENTICATION CHECK ---
// Ensure a user is logged in. If not, they cannot view any confirmation page.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// --- 2. VALIDATE THE ORDER ID FROM THE URL ---
// Get the order_id from the URL and make sure it's a valid integer.
$order_id = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);
if (!$order_id) {
    // If no valid order_id is provided, redirect the user away.
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// --- 3. FETCH THE MAIN ORDER DETAILS FROM THE DATABASE ---
// This is a critical security step. The query checks for BOTH the order_id from the URL
// AND the user_id from the session. This prevents one user from viewing another user's order
// by simply changing the ID in the URL.
$stmt_order = $conn->prepare(
    "SELECT o.*, u.name as user_name, u.email as user_email 
     FROM orders o 
     JOIN users u ON o.user_id = u.id 
     WHERE o.id = ? AND o.user_id = ?"
);
$stmt_order->bind_param("ii", $order_id, $user_id);
$stmt_order->execute();
$order = $stmt_order->get_result()->fetch_assoc();
$stmt_order->close();

// If no order is found (or it doesn't belong to this user), display an error message and stop.
if (!$order) {
    echo "<div class='container my-5'><div class='alert alert-danger text-center'><h3>Order Not Found</h3><p>The requested order could not be found or you do not have permission to view it.</p></div></div>";
    include 'includes/shop_footer.php';
    exit();
}

// --- 4. FETCH THE ITEMS ASSOCIATED WITH THIS ORDER ---
// Now that we've confirmed the user owns the order, we can safely fetch its items.
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
    
    <!-- Display the "Order Successful" message from the session -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success text-center" role="alert">
            <h4 class="alert-heading">Thank You for Your Purchase!</h4>
            <p><?php echo $_SESSION['message']['text']; ?></p>
        </div>
        <?php unset($_SESSION['message']); // Clear the message so it doesn't show again ?>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-light text-center py-4">
            <h2>Your Receipt</h2>
            <p class="text-muted mb-0">Order Confirmation #<?php echo $order['id']; ?></p>
        </div>
        <div class="card-body p-4 p-md-5">
            <!-- Order Details Section -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <h5>Order Details:</h5>
                    <ul class="list-unstyled">
                        <li><strong>Order Date:</strong> <?php echo date("F j, Y, g:i a", strtotime($order['created_at'])); ?></li>
                        <li><strong>Payment Status:</strong> <span class="badge bg-warning text-dark text-capitalize"><?php echo htmlspecialchars($order['payment_status']); ?></span></li>
                         <li><strong>Order Status:</strong> <span class="badge bg-info text-dark text-capitalize"><?php echo htmlspecialchars($order['order_status']); ?></span></li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Shipping To:</h5>
                    <address>
                        <strong><?php echo htmlspecialchars($order['user_name']); ?></strong><br>
                        <?php echo nl2br(htmlspecialchars($order['shipping_address'])); // nl2br preserves line breaks ?>
                    </address>
                </div>
            </div>

            <!-- Itemized Product List -->
            <h5 class="mb-3">Items Ordered:</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Product</th>
                            <th scope="col" class="text-center">Quantity</th>
                            <th scope="col" class="text-end">Price</th>
                            <th scope="col" class="text-end">Subtotal</th>
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
            <!--  -->
            <hr>

            <div class="text-center mt-4">
                <p class="text-muted">A confirmation has been sent to your email: <?php echo htmlspecialchars($order['user_email']); ?></p>
                <a href="index.php" class="btn btn-primary-nude">
                    <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>

<?php 
include 'includes/shop_footer.php'; 
?>

