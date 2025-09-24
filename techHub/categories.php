<?php
include 'config/db.php';

$name = '';
$edit_id = null;
$edit_name = '';
$errors = [];

// Handle form submissions (Create and Update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['save_category'])) { // Create
        $name = trim($_POST['name']);
        if (empty($name)) {
            $errors['name'] = "Category name is required.";
        } else {
            $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $stmt->close();
            header("Location: categories.php");
            exit();
        }
    } elseif (isset($_POST['update_category'])) { // Update
        $id = $_POST['id'];
        $name = trim($_POST['name']);
        if (empty($name)) {
             $errors['name'] = "Category name cannot be empty.";
        } else {
            $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $name, $id);
            $stmt->execute();
            $stmt->close();
            header("Location: categories.php");
            exit();
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: categories.php");
    exit();
}

// Handle Edit (fetch data for form)
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $edit_name = $category['name'];
    $stmt->close();
}

include 'includes/admin_header.php';
?>

<div class="row">
    <!-- Form Column -->
    <div class="col-md-4">
        
        <div class="card shadow">
            <div class="card-header">
                <h3><?php echo $edit_id ? 'Edit Category' : 'Add New Category'; ?></h3>
            </div>
            <div class="card-body">
            

                <form action="categories.php" method="POST">
                    <?php if ($edit_id): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo htmlspecialchars($edit_name ?: $name); ?>" required>
                        <?php if (isset($errors['name'])): ?>
                           <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if ($edit_id): ?>
                        <button type="submit" name="update_category" class="btn btn-success">Update</button>
                        <a href="categories.php" class="btn btn-secondary">Cancel</a>
                    <?php else: ?>
                        <button type="submit" name="save_category" class="btn btn-primary">Save Category</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Table Column -->
    <div class="col-md-8">
            <style>
                .col-md-8{
                    height: 60vh;
                }
                </style>
        <div class="card shadow">
            <div class="card-header">
                <h2>Category List</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row["id"] . "</td>";
                                    echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                                    echo "<td>
                                            <a href='categories.php?edit=" . $row["id"] . "' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i> Edit</a>
                                            <a href='categories.php?delete=" . $row["id"] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i> Delete</a>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' class='text-center'>No categories found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
