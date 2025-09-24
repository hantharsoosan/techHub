<?php
include 'config/db.php';

$id = $_GET['id'];
$name = $description = $image_url = '';
$price = $original_price = $category_id = $brand_id = '';
$stock_qty = 0;
$errors = [];

// Fetch current product data
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $name = $product['name'];
        $description = $product['description'];
        $price = $product['price'];
        $original_price = $product['original_price'];
        $stock_qty = $product['stock_qty'];
        $image_url = $product['image_url'];
        $category_id = $product['category_id'];
        $brand_id = $product['brand_id'];
    } else {
        die("Product not found.");
    }
    $stmt->close();
} else {
    die("No product ID specified.");
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $original_price = trim($_POST['original_price']);
    $stock_qty = filter_input(INPUT_POST, 'stock_qty', FILTER_VALIDATE_INT);
    $image_url = trim($_POST['image_url']);
    $category_id = $_POST['category_id'];
    $brand_id = $_POST['brand_id'];
    $id = $_POST['id'];

    if (empty($name)) {
        $errors['name'] = "Product name is required.";
    }
    if (empty($price) || !is_numeric($price) || $price < 0) {
        $errors['price'] = "A valid price is required.";
    }
    if (!empty($original_price) && (!is_numeric($original_price) || $original_price < 0)) {
        $errors['original_price'] = "If provided, the original price must be a valid number.";
    }
    if ($stock_qty === false || $stock_qty < 0) {
        $errors['stock_qty'] = "A valid stock quantity is required.";
    }
    if (empty($category_id)) {
        $errors['category'] = "Please select a category.";
    }
    if (empty($brand_id)) {
        $errors['brand'] = "Please select a brand.";
    }

    if (empty($errors)) {
        $original_price_db = !empty($original_price) ? $original_price : NULL;
        
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, original_price = ?, stock_qty = ?, category_id = ?, brand_id = ?, image_url = ? WHERE id = ?");
        $stmt->bind_param("ssddiissi", $name, $description, $price, $original_price_db, $stock_qty, $category_id, $brand_id, $image_url, $id);

        if ($stmt->execute()) {
            header("Location: products.php");
            exit();
        } else {
             $errors['db'] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

include 'includes/admin_header.php';
?>

<div class="card shadow">
    <div class="card-header">
        <h2>Edit Product</h2>
    </div>
    <div class="card-body">
        <?php if (!empty($errors['db'])): ?>
            <div class="alert alert-danger"><?php echo $errors['db']; ?></div>
        <?php endif; ?>
        <form action="product_edit.php?id=<?php echo $id; ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                 <?php if (isset($errors['name'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="price" class="form-label">Sale Price</label>
                    <input type="number" step="0.01" class="form-control <?php echo isset($errors['price']) ? 'is-invalid' : ''; ?>" id="price" name="price" value="<?php echo htmlspecialchars($price); ?>" required>
                    <?php if (isset($errors['price'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['price']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="original_price" class="form-label">Original Price (Optional)</label>
                    <input type="number" step="0.01" class="form-control <?php echo isset($errors['original_price']) ? 'is-invalid' : ''; ?>" id="original_price" name="original_price" value="<?php echo htmlspecialchars($original_price); ?>">
                    <?php if (isset($errors['original_price'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['original_price']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="stock_qty" class="form-label">Stock Quantity</label>
                    <input type="number" class="form-control <?php echo isset($errors['stock_qty']) ? 'is-invalid' : ''; ?>" id="stock_qty" name="stock_qty" value="<?php echo htmlspecialchars($stock_qty); ?>" required>
                     <?php if (isset($errors['stock_qty'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['stock_qty']; ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mb-3">
                <label for="image_url" class="form-label">Image URL</label>
                <input type="text" class="form-control" id="image_url" name="image_url" value="<?php echo htmlspecialchars($image_url); ?>">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-select <?php echo isset($errors['category']) ? 'is-invalid' : ''; ?>" id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php
                        $result = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
                        while($row = $result->fetch_assoc()) {
                            $selected = ($row['id'] == $category_id) ? 'selected' : '';
                            echo "<option value='" . $row['id'] . "' $selected>" . htmlspecialchars($row['name']) . "</option>";
                        }
                        ?>
                    </select>
                    <?php if (isset($errors['category'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['category']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="brand_id" class="form-label">Brand</label>
                    <select class="form-select <?php echo isset($errors['brand']) ? 'is-invalid' : ''; ?>" id="brand_id" name="brand_id" required>
                        <option value="">Select Brand</option>
                         <?php
                        $result = $conn->query("SELECT id, name FROM brands ORDER BY name ASC");
                        while($row = $result->fetch_assoc()) {
                            $selected = ($row['id'] == $brand_id) ? 'selected' : '';
                            echo "<option value='" . $row['id'] . "' $selected>" . htmlspecialchars($row['name']) . "</option>";
                        }
                        ?>
                    </select>
                    <?php if (isset($errors['brand'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['brand']; ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <button type="submit" class="btn btn-success">Update Product</button>
            <a href="products.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
