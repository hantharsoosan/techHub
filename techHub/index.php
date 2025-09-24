<?php
// The shop_header starts the session and includes the database connection.
include 'includes/shop_header.php';

// --- 1. PAGINATION SETUP ---
$products_per_page = 9; // Show 9 products per page (fits a 3-column grid nicely)
$current_page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
$offset = ($current_page - 1) * $products_per_page;

// --- 2. BUILD THE BASE QUERY AND FILTERS ---
// This part is mostly the same, but we will build the query in two parts: one for counting and one for fetching.
$base_sql = "FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             LEFT JOIN brands b ON p.brand_id = b.id
             WHERE 1=1";
$params = [];
$types = '';

// Handle Search Term
$searchTerm = '';
if (!empty($_GET['search'])) {
    $searchTerm = trim($_GET['search']);
    $base_sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $searchWildcard = "%" . $searchTerm . "%";
    array_push($params, $searchWildcard, $searchWildcard);
    $types .= 'ss';
}

// Handle Category Filter
$filter_category = '';
if (!empty($_GET['category_id'])) {
    $filter_category = (int)$_GET['category_id'];
    $base_sql .= " AND p.category_id = ?";
    array_push($params, $filter_category);
    $types .= 'i';
}

// Handle Brand Filter
$filter_brand = '';
if (!empty($_GET['brand_id'])) {
    $filter_brand = (int)$_GET['brand_id'];
    $base_sql .= " AND p.brand_id = ?";
    array_push($params, $filter_brand);
    $types .= 'i';
}

// --- 3. COUNT TOTAL PRODUCTS FOR PAGINATION ---
// Run a query to count the total number of products that match the filters.
$count_sql = "SELECT COUNT(p.id) as total " . $base_sql;
$stmt_count = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$count_result = $stmt_count->get_result()->fetch_assoc();
$total_products = $count_result['total'] ?? 0;
$total_pages = ceil($total_products / $products_per_page);
$stmt_count->close();


// --- 4. FETCH PRODUCTS FOR THE CURRENT PAGE ---
// Now, build the final query to fetch only the products for this page.
$product_sql = "SELECT p.*, c.name as category_name, b.name as brand_name " . $base_sql . " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
$fetch_params = $params; // Copy existing params
array_push($fetch_params, $products_per_page, $offset); // Add limit and offset
$fetch_types = $types . 'ii'; // Add types for limit and offset

$stmt_products = $conn->prepare($product_sql);
if ($stmt_products === false) {
    die("Error preparing the statement: " . $conn->error);
}
if (!empty($fetch_params)) {
    $stmt_products->bind_param($fetch_types, ...$fetch_params);
}
$stmt_products->execute();
$result = $stmt_products->get_result();
?>

<!-- The header with navigation and slideshow is already included via shop_header.php -->

<div class="container my-5">
    <div class="row g-4">
        <!-- Filter Sidebar Column -->
        <div class="col-lg-3">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-light">
                    <h4><i class="fas fa-filter me-2"></i>Filter & Search</h4>
                </div>
                <div class="card-body">
                    <form action="index.php" method="GET">
                        <!-- Search, Category, and Brand filters remain the same -->
                        <div class="mb-4">
                            <label for="search" class="form-label fw-bold">Search Products</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="e.g. iPhone, MacBook" value="<?php echo htmlspecialchars($searchTerm); ?>">
                        </div>
                        <div class="mb-4">
                            <label for="category_id" class="form-label fw-bold">Category</label>
                            <select class="form-select" name="category_id" id="category_id">
                                <option value="">All Categories</option>
                                <?php
                                $categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");
                                while ($cat = $categories->fetch_assoc()) {
                                    $selected = ($filter_category == $cat['id']) ? 'selected' : '';
                                    echo "<option value='{$cat['id']}' $selected>" . htmlspecialchars($cat['name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="brand_id" class="form-label fw-bold">Brand</label>
                            <select class="form-select" name="brand_id" id="brand_id">
                                <option value="">All Brands</option>
                                <?php
                                $brands = $conn->query("SELECT * FROM brands ORDER BY name ASC");
                                while ($brand = $brands->fetch_assoc()) {
                                    $selected = ($filter_brand == $brand['id']) ? 'selected' : '';
                                    echo "<option value='{$brand['id']}' $selected>" . htmlspecialchars($brand['name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="d-grid gap-2">
                             <button type="submit" class="btn btn-primary-nude">Apply Filters</button>
                             <a href="index.php" class="btn btn-outline-secondary">Clear All Filters</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Product Grid Column -->
        <div class="col-lg-9">
            <!-- User Notification Area -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message']['type']; ?> alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['message']['text']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

             <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Showing <?php echo $result->num_rows; ?> of <?php echo $total_products; ?> Products</h4>
            </div>
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($product = $result->fetch_assoc()): ?>
                        <!-- The product card loop -->
                        <div class="col">
                            <div class="card h-100 product-card shadow-sm border-0">
                                <img src="<?php echo !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'https://placehold.co/600x400/f5f1ed/4a3f35?text=No+Image'; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text text-muted small flex-grow-1"><?php echo htmlspecialchars(substr($product['description'], 0, 50)) . '...'; ?></p>
                                    <div class="mt-auto">
                                        <p class="card-text price">
                                            <span class="fs-5 fw-bold text-success">$<?php echo number_format($product['price'], 2); ?></span>
                                            <?php if (!empty($product['original_price']) && $product['original_price'] > $product['price']): ?>
                                                <small class="text-muted text-decoration-line-through ms-2">$<?php echo number_format($product['original_price'], 2); ?></small>
                                            <?php endif; ?>
                                        </p>
                                        <?php if ($product['stock_qty'] > 0): ?>
                                             <p class="text-success small mb-2">In Stock (<?php echo $product['stock_qty']; ?> available)</p>
                                        <?php else: ?>
                                             <p class="text-danger small mb-2">Out of Stock</p>
                                        <?php endif; ?>
                                        
                                        <!-- === ACTION BUTTONS AREA === -->
                                        <div class="d-grid gap-2">
                                            <form action="cart_logic.php" method="POST">
                                                <input type="hidden" name="action" value="add">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" name="quantity" value="1" min="1" max="<?php echo $product['stock_qty']; ?>" <?php echo ($product['stock_qty'] <= 0) ? 'disabled' : ''; ?>>
                                                    <button type="submit" class="btn btn-primary-nude" <?php echo ($product['stock_qty'] <= 0) ? 'disabled' : ''; ?>>
                                                        <i class="fas fa-shopping-cart"></i>
                                                    </button>
                                                </div>
                                            </form>
                                            <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-secondary">
                                                Show More
                                            </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                         <div class="alert alert-info text-center">
                            <h5>No Products Found</h5>
                            <p>Your search or filter criteria did not match any products.</p>
                            <a href="index.php" class="btn btn-primary-nude mt-2">Clear All Filters</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- --- PAGINATION LINKS --- -->
            <?php if ($total_pages > 1): ?>
            <nav class="d-flex justify-content-center mt-5">
                <div class="btn-group" role="group" aria-label="Pagination">
                    <?php
                    $query_params = $_GET;
                    unset($query_params['page']);
                    $query_string = http_build_query($query_params);
                    ?>
                    <a href="?page=<?php echo $current_page - 1; ?>&<?php echo $query_string; ?>" class="btn btn-outline-secondary <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                       <i class="fas fa-arrow-left me-1"></i> Previous
                    </a>
                    <?php
                    $window = 1;
                    for ($i = 1; $i <= $total_pages; $i++):
                        if ($i == 1 || $i == $total_pages || ($i >= $current_page - $window && $i <= $current_page + $window)):
                    ?>
                        <a href="?page=<?php echo $i; ?>&<?php echo $query_string; ?>" class="btn <?php echo ($i == $current_page) ? 'btn-primary-nude' : 'btn-outline-secondary'; ?>">
                           <?php echo $i; ?>
                        </a>
                    <?php
                        elseif ($i == $current_page - $window - 1 || $i == $current_page + $window + 1):
                    ?>
                        <button class="btn btn-outline-secondary" disabled>...</button>
                    <?php
                        endif;
                    endfor;
                    ?>
                    <a href="?page=<?php echo $current_page + 1; ?>&<?php echo $query_string; ?>" class="btn btn-outline-secondary <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                       Next <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </nav>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php 
$stmt_products->close(); // Close the database statement to free up resources.
include 'includes/shop_footer.php'; 
?>

