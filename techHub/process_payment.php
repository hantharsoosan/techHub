<?php
// --- DEBUGGING: These two lines will change a 500 error into a useful message ---
ini_set('display_errors', 1);
error_reporting(E_ALL);

// This script is for backend processing ONLY. It should not produce any HTML output.

// Step 1: Start the session manually.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Step 2: Include the database configuration directly.
include 'config/db.php';


// --- 1. SECURITY CHECKS ---
// Ensure this script is accessed via a POST request and that a user is logged in.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    // If not, redirect them away to prevent direct access.
    header('Location: index.php');
    exit();
}

// --- 2. GATHER AND VALIDATE INCOMING DATA ---
$user_id = $_SESSION['user_id'];
$shipping_address = trim($_POST['shipping_address']);

// Basic validation: A shipping address is always required.
if (empty($shipping_address)) {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Shipping address is required.'];
    header('Location: checkout.php');
    exit();
}

// --- 3. RECALCULATE TOTAL ON THE SERVER ---
// This is a CRITICAL security step. Never trust the total amount sent from the browser.
$total_amount = 0;
$stmt_cart = $conn->prepare("SELECT SUM(c.quantity * p.price) as total FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt_cart->bind_param("i", $user_id);
$stmt_cart->execute();
$cart_total_result = $stmt_cart->get_result()->fetch_assoc();
$stmt_cart->close();

// Ensure the cart is not empty and has a valid total.
if (!$cart_total_result || $cart_total_result['total'] <= 0) {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Your cart is empty. Cannot place order.'];
    header('Location: checkout.php');
    exit();
}
$total_amount = $cart_total_result['total'];


// --- 4. ORDER CREATION FOR CASH ON DELIVERY ---
// A database transaction ensures that all steps either succeed or fail together.
$conn->begin_transaction();
try {
    // Since COD is the only method, we no longer need to check the payment type.
    
    $transaction_id = 'COD-' . time() . '-' . $user_id;
    $payment_status = 'pending'; // For COD, payment is always pending until delivery.

    // Step A: Insert the main order record into the `orders` table.
    $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, payment_status) VALUES (?, ?, ?, ?)");
    $stmt_order->bind_param("idss", $user_id, $total_amount, $shipping_address, $payment_status);
    $stmt_order->execute();
    $order_id = $conn->insert_id; // Get the ID of the new order we just created.
    $stmt_order->close();

    // Step B: Move the items from the user's cart to the `order_items` table.
    $stmt_move = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) SELECT ?, product_id, quantity, price FROM cart WHERE user_id = ?");
    $stmt_move->bind_param("ii", $order_id, $user_id);
    $stmt_move->execute();
    $stmt_move->close();

    // Step C: Clear the user's now-empty shopping cart.
    $stmt_clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt_clear->bind_param("i", $user_id);
    $stmt_clear->execute();
    $stmt_clear->close();

    // If all database operations were successful, commit the transaction.
    $conn->commit();

    // --- 5. REDIRECT TO CONFIRMATION PAGE ---
    header("Location: order_confirmation.php?order_id=" . $order_id);
    exit();

} catch (Exception $e) {
    // If any step failed, roll back the transaction to undo all changes.
    $conn->rollback();
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'An unexpected error occurred: ' . $e->getMessage()];
    header('Location: checkout.php');
    exit();
}
?>


