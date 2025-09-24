<?php
// The shop_header starts the session and includes the database connection.
include 'includes/shop_header.php';

// --- SECURITY: AUTHENTICATION CHECK ---
// This is the most important step. If a user is not logged in, they cannot view this page.
if (!isset($_SESSION['user_id'])) {
    // Save the page they were trying to access so we can redirect them back after login.
    $_SESSION['redirect_to'] = 'order_history.php';
    $_SESSION['message'] = ['type' => 'warning', 'text' => 'You must be logged in to view your order history.'];
    header('Location: login.php');
    exit(); // Stop the script from running further.
}

$user_id = $_SESSION['user_id'];

// --- FETCH ORDER DATA ---
// This query securely fetches all orders belonging ONLY to the currently logged-in user.
// It orders them by date to show the most recent orders first.
$stmt = $conn->prepare(
    "SELECT id, created_at, total_amount, order_status, payment_status 
     FROM orders 
     WHERE user_id = ? 
     ORDER BY created_at DESC"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h2 class="mb-0">My Order History</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Order Status</th>
                            <th>Payment Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($order = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>ORD<?php echo $order['id']; ?></strong></td>
                                    <td><?php echo date("F j, Y", strtotime($order['created_at'])); ?></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td><span class="badge bg-info text-dark text-capitalize"><?php echo htmlspecialchars($order['order_status']); ?></span></td>
                                    <td><span class="badge bg-warning text-dark text-capitalize"><?php echo htmlspecialchars($order['payment_status']); ?></span></td>
                                    <td class="text-end">
                                        <a href="order_details.php?order_id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <p class="mb-0">You have not placed any orders yet.</p>
                                    <a href="index.php" class="btn btn-primary-nude mt-3">Start Shopping</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
$stmt->close();
include 'includes/shop_footer.php'; 
?>
