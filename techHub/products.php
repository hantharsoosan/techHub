<?php
include 'config/db.php';
include 'includes/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Our Products</h2>
    <a href="product_create.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Product</a>
</div>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php
    // Query to get products with category and brand names
    $sql = "SELECT p.id, p.name, p.price, p.original_price, p.image_url, c.name as category_name, b.name as brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN brands b ON p.brand_id = b.id
            ORDER BY p.id DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            $image = !empty($row['image_url']) ? $row['image_url'] : 'https://placehold.co/600x400/fdfaf6/e0d9d1?text=No+Image';
    ?>
    <div class="col">
        <div class="card h-100 shadow-sm text-center">
            <a href="product_view.php?id=<?php echo $row['id']; ?>">
                <img src="<?php echo htmlspecialchars($image); ?>" class="card-img-top" style="aspect-ratio: 4 / 3; object-fit: cover;" alt="<?php echo htmlspecialchars($row['name']); ?>">
            </a>
            <div class="card-body d-flex flex-column">
                <div class="mb-2">
                    <small class="text-muted"><?php echo htmlspecialchars($row["category_name"] ?? 'N/A'); ?></small>
                </div>
                <h5 class="card-title fs-6 fw-bold mb-3">
                    <a href="product_view.php?id=<?php echo $row['id']; ?>" class="text-decoration-none" style="color: var(--text-dark-nude);">
                        <?php echo htmlspecialchars($row['name']); ?>
                    </a>
                </h5>
                <p class="card-text mt-auto">
                    <span class="fw-bold fs-5" style="color: var(--primary-nude);">$<?php echo number_format($row["price"], 2); ?></span>
                    <?php if (!empty($row['original_price']) && $row['original_price'] > $row['price']) : ?>
                        <del class="text-muted small">$<?php echo number_format($row['original_price'], 2); ?></del>
                    <?php endif; ?>
                </p>
            </div>
            <div class="card-footer bg-transparent border-top-0 pb-3">
                <a href='product_view.php?id=<?php echo $row["id"]; ?>' class='btn btn-secondary btn-sm' title='View'><i class='fas fa-eye'></i></a>
                <a href='product_edit.php?id=<?php echo $row["id"]; ?>' class='btn btn-warning btn-sm' title='Edit'><i class='fas fa-edit'></i></a>
                <a href='product_delete.php?id=<?php echo $row["id"]; ?>' class='btn btn-danger btn-sm' onclick='return confirm("Are you sure you want to delete this product?")' title='Delete'><i class='fas fa-trash'></i></a>
            </div>
        </div>
    </div>
    <?php
        }
    } else {
        echo '<div class="col-12"><div class="alert alert-info">No products found. <a href="product_create.php">Click here to add one.</a></div></div>';
    }
    ?>
</div>

<?php include 'includes/footer.php'; ?>