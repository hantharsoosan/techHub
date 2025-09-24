<?php
// This security check MUST be at the top. It ensures only logged-in admins can perform this action.
include 'includes/admin_auth_check.php';
include 'config/db.php';

// Check if the request is a POST request, which is what our form uses.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- 1. Sanitize and Validate Input ---
    $order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
    $new_status = trim($_POST['new_status']);
    $status_type = trim($_POST['status_type']);

    // Define the columns and statuses that are allowed to be updated to prevent malicious input.
    $allowed_status_types = ['order_status', 'payment_status'];
    $allowed_statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled', 'Paid', 'Refunded'];

    // --- 2. Check if all data is valid ---
    if ($order_id && in_array($new_status, $allowed_statuses) && in_array($status_type, $allowed_status_types)) {
        
        // --- 3. Prepare and Execute the Database Update ---
        // Using a prepared statement is crucial for security to prevent SQL injection.
        // The column name is validated from our allowed list, not from user input directly.
        $sql = "UPDATE orders SET `$status_type` = ? WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("si", $new_status, $order_id);
            if ($stmt->execute()) {
                // Success: Set a success message in the session.
                $_SESSION['message'] = ['type' => 'success', 'text' => "Order #$order_id status has been updated to '$new_status'."];
            } else {
                // Failure: Set an error message.
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error: Could not update the order status.'];
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error: Could not prepare the database statement.'];
        }
    } else {
        // If the input data was invalid.
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error: Invalid data provided for status update.'];
    }
} else {
    // If someone tries to access this page directly without submitting the form.
    $_SESSION['message'] = ['type' => 'warning', 'text' => 'Invalid request method.'];
}

// --- 4. Redirect Back to the Orders Page ---
// No matter the outcome, always send the admin back to the order list to see the result.
header("Location: admin_orders.php");
exit();
?>
