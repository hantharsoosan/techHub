<?php
include 'config/db.php';
include 'includes/admin_header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("Invalid product ID.");
}

$sql = "SELECT p.id, p.name, p.description, p.price, p.original_price, p.image_url, p.created_at, c.name as category_name, b.name as brand_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    die("Product not found.");
}
?>

<div class="card shadow">
    <div class="card-header">
        <h2>Product Details: <?php echo htmlspecialchars($product['name']); ?></h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                 <?php 
                    $image = !empty($product['image_url']) ? $product['image_url'] : 'https://placehold.co/600x400/fdfaf6/e0d9d1?text=No+Image';
                 ?>
                 <img src="<?php echo htmlspecialchars($image); ?>" class="img-fluid rounded shadow-sm" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px;">ID</th>
                        <td><?php echo $product['id']; ?></td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td><?php echo nl2br(htmlspecialchars($product['description'])); ?></td>
                    </tr>
                    <tr>
                        <th>Price</th>
                        <td>
                            <?php 
                            echo '<span class="fw-bold fs-5 text-success">$' . number_format($product['price'], 2) . '</span>';
                            if (!empty($product['original_price']) && $product['original_price'] > $product['price']) {
                                echo ' <del class="text-muted small align-middle">$' . number_format($product['original_price'], 2) . '</del>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Category</th>
                        <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Brand</th>
                        <td><?php echo htmlspecialchars($product['brand_name'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Date Added</th>
                        <td><?php echo date("F j, Y, g:i a", strtotime($product['created_at'])); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <hr>
        <a href="products.php" class="btn btn-primary">Back to Products List</a>
        <a href="product_edit.php?id=<?php echo $product['id']; ?>" class="btn btn-warning">Edit</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

