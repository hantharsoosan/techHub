<?php
// --- SECURITY & SETUP ---
// This guard ensures only logged-in admins can view this customer data.
include 'includes/admin_auth_check.php';
include 'config/db.php';
include 'includes/admin_header.php';

// --- FETCH CUSTOMER DATA WITH ORDER STATISTICS ---
// This advanced query retrieves all users and, for each user, calculates:
// 1. COUNT(o.id) AS total_orders: The total number of orders they have placed.
// 2. SUM(o.total_amount) AS total_spent: The total amount of money they have spent.
// A LEFT JOIN is used to ensure that even customers with zero orders are included in the list.
$customers_result = $conn->query(
    "SELECT 
        u.id, 
        u.name, 
        u.email, 
        u.created_at,
        COUNT(o.id) AS total_orders,
        COALESCE(SUM(o.total_amount), 0) AS total_spent
     FROM users u
     LEFT JOIN orders o ON u.id = o.user_id
     GROUP BY u.id, u.name, u.email, u.created_at
     ORDER BY u.created_at DESC"
);
?>

<div class="card shadow-sm">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h3 class="mb-0"><i class="fas fa-users me-2"></i>Customer List</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Customer ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Registration Date</th>
                        <th class="text-center">Total Orders</th>
                        <th class="text-end">Lifetime Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($customers_result && $customers_result->num_rows > 0): ?>
                        <?php while ($customer = $customers_result->fetch_assoc()): ?>
                            <tr>
                                <td>CU<?php echo $customer['id']; ?></td>
                                <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                <td><?php echo date("M j, Y", strtotime($customer['created_at'])); ?></td>
                                <td class="text-center"><?php echo $customer['total_orders']; ?></td>
                                <td class="text-end">$<?php echo number_format($customer['total_spent'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <p class="mb-0">No customers have registered yet.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>
