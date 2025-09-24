<?php
// The shop_header starts the session and includes the database connection.
include 'includes/shop_header.php';

// --- 1. GET AND VALIDATE PRODUCT ID ---
// Get the product ID from the URL and ensure it is a valid integer.
$product_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$product_id) {
    // If no valid ID is provided, redirect to the main shop page.
    header('Location: index.php');
    exit();
}

// --- 2. FETCH PRODUCT DATA FROM THE DATABASE ---
// This query fetches all details for the specific product, including its category and brand name.
$sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

// If no product was found with that ID, display an error message and stop.
if (!$product) {
    echo "<div class='container my-5'><div class='alert alert-danger'>Product not found.</div></div>";
    include 'includes/shop_footer.php';
    exit();
}
?>

<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-body p-5">
            <div class="row g-5">
                <!-- Product Image Column -->
                <div class="col-lg-6">
                    <img src="<?php echo !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'https://placehold.co/600x600/f5f1ed/4a3f35?text=No+Image'; ?>" class="img-fluid rounded shadow" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>

                <!-- Product Details Column -->
                <div class="col-lg-6">
                    <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="text-muted">
                        <a href="index.php?brand_id=<?php echo $product['brand_id']; ?>" class="text-muted text-decoration-none"><?php echo htmlspecialchars($product['brand_name'] ?? 'N/A'); ?></a> / 
                        <a href="index.php?category_id=<?php echo $product['category_id']; ?>" class="text-muted text-decoration-none"><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></a>
                    </p>
                    
                    <p class="price fs-3 my-4">
                        <span class="fw-bold text-success">$<?php echo number_format($product['price'], 2); ?></span>
                        <?php if (!empty($product['original_price']) && $product['original_price'] > $product['price']): ?>
                            <small class="text-muted text-decoration-line-through ms-2">$<?php echo number_format($product['original_price'], 2); ?></small>
                        <?php endif; ?>
                    </p>

                    <h5 class="mt-4">Description</h5>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); // nl2br preserves line breaks ?></p>
                    
                    <hr class="my-4">
                    
                    <?php if ($product['stock_qty'] > 0): ?>
                        <p class="text-success mb-3"><i class="fas fa-check-circle me-2"></i>In Stock (<?php echo $product['stock_qty']; ?> available)</p>
                    <?php else: ?>
                        <p class="text-danger mb-3"><i class="fas fa-times-circle me-2"></i>Out of Stock</p>
                    <?php endif; ?>
                    
                    <!-- Add to Cart Form -->
                    <form action="cart_logic.php" method="POST">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <div class="input-group mb-3" style="max-width: 300px;">
                             <label class="input-group-text" for="quantity-<?php echo $product['id']; ?>">Quantity</label>
                            <input type="number" id="quantity-<?php echo $product['id']; ?>" class="form-control" name="quantity" value="1" min="1" max="<?php echo $product['stock_qty']; ?>" <?php echo ($product['stock_qty'] <= 0) ? 'disabled' : ''; ?>>
                        </div>
                        <button type="submit" class="btn btn-primary-nude btn-lg" <?php echo ($product['stock_qty'] <= 0) ? 'disabled' : ''; ?>>
                            <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                        </button>
                    </form>
                    
                    <a href="index.php" class="btn btn-link mt-3"><i class="fas fa-arrow-left me-2"></i>Back to Shop</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/shop_footer.php'; 
?>
