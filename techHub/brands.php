<?php
include 'config/db.php';

$name = '';
$edit_id = null;
$edit_name = '';
$errors = [];

// Handle form submissions (Create and Update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['save_brand'])) { // Create
        $name = trim($_POST['name']);
        if (empty($name)) {
            $errors['name'] = "Brand name is required.";
        } else {
            $stmt = $conn->prepare("INSERT INTO brands (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $stmt->close();
            header("Location: brands.php");
            exit();
        }
    } elseif (isset($_POST['update_brand'])) { // Update
        $id = $_POST['id'];
        $name = trim($_POST['name']);
        if (empty($name)) {
             $errors['name'] = "Brand name cannot be empty.";
        } else {
            $stmt = $conn->prepare("UPDATE brands SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $name, $id);
            $stmt->execute();
            $stmt->close();
            header("Location: brands.php");
            exit();
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM brands WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: brands.php");
    exit();
}

// Handle Edit (fetch data for form)
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT name FROM brands WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $brand = $result->fetch_assoc();
    $edit_name = $brand['name'];
    $stmt->close();
}

include 'includes/admin_header.php';
?>

<div class="row">
    <!-- Form Column -->
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header">
                <h3><?php echo $edit_id ? 'Edit Brand' : 'Add New Brand'; ?></h3>
            </div>
            <div class="card-body">
                <form action="brands.php" method="POST">
                    <?php if ($edit_id): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="name" class="form-label">Brand Name</label>
                        <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo htmlspecialchars($edit_name ?: $name); ?>" required>
                        <?php if (isset($errors['name'])): ?>
                           <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if ($edit_id): ?>
                        <button type="submit" name="update_brand" class="btn btn-success">Update</button>
                        <a href="brands.php" class="btn btn-secondary">Cancel</a>
                    <?php else: ?>
                        <button type="submit" name="save_brand" class="btn btn-primary">Save Brand</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Table Column -->
    <div class="col-md-8">
        <div class="card shadow">
            <style>
                .col-md-8{
                    height: 60vh;
                }
                </style>
            <div class="card-header">
                <h2>Brand List</h2>
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
                            $result = $conn->query("SELECT * FROM brands ORDER BY name ASC");
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row["id"] . "</td>";
                                    echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                                    echo "<td>
                                            <a href='brands.php?edit=" . $row["id"] . "' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i> Edit</a>
                                            <a href='brands.php?delete=" . $row["id"] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i> Delete</a>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' class='text-center'>No brands found</td></tr>";
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
