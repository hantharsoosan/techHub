<?php
// The shop_header starts the session, so it must be included first.
include 'config/db.php';
include 'includes/shop_header.php';

// Initialize variables
$subtotal = 0;

// --- KEY FIX: DIFFERENT LOGIC FOR GUESTS VS. LOGGED-IN USERS ---

// The base SQL query is the same for both.
$sql = "SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.image_url, p.stock_qty 
        FROM cart c 
        JOIN products p ON c.product_id = p.id ";

// First, check if the user is logged in by looking for the 'user_id' in the session.
if (isset($_SESSION['user_id'])) {
    // --- LOGGED-IN USER LOGIC ---
    $user_id = $_SESSION['user_id'];
    $sql .= "WHERE c.user_id = ?"; // Modify the query to search by user_id.
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id); // Bind the integer user_id.
} else {
    // --- GUEST USER LOGIC (FALLBACK) ---
    $session_id = session_id();
    $sql .= "WHERE c.session_id = ?"; // Modify the query to search by session_id.
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_id); // Bind the string session_id.
}

$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container my-5">
    <h1 class="text-center mb-4">Your Shopping Cart</h1>

    <!-- Display Session Messages (e.g., from updating or removing an item) -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message']['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['message']['text']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">Product</th>
                            <th scope="col">Price</th>
                            <th scope="col" style="width: 150px;">Quantity</th>
                            <th scope="col" class="text-end">Total</th>
                            <th scope="col" class="text-center">Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $result->fetch_assoc()): 
                            $item_total = $item['price'] * $item['quantity'];
                            $subtotal += $item_total;
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo htmlspecialchars($item['image_url'] ?: 'https://placehold.co/80x80/f5f1ed/4a3f35?text=N/A'); ?>" class="img-fluid rounded" style="width: 80px;" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                        <div class="ms-3">
                                            <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <!-- This form submits to cart_logic.php to handle the update -->
                                    <form action="cart_logic.php" method="POST" class="d-flex">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <input type="number" name="quantity" class="form-control form-control-sm" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock_qty']; ?>" onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td class="text-end"><strong>$<?php echo number_format($item_total, 2); ?></strong></td>
                                <td class="text-center">
                                    <a href="cart_logic.php?action=remove&cart_id=<?php echo $item['cart_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to remove this item?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer p-4">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <a href="index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Continue Shopping</a>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <h4>Subtotal: <span class="fw-bold text-success">$<?php echo number_format($subtotal, 2); ?></span></h4>
                       <!-- Corrected spelling from 'ckeckout.php' to 'checkout.php' -->
                       <a href="checkout.php" class="btn btn-primary-nude btn-lg mt-2">
                            Proceed to Checkout<i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <h5>Your cart is empty.</h5>
            <p>Looks like you haven't added any items to your cart yet.</p>
            <a href="index.php" class="btn btn-primary-nude mt-2">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php 
$stmt->close();
include 'includes/shop_footer.php'; 
?>

