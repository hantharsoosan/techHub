<?php
session_start();
include 'config/db.php';

// Security check: must be logged in and submitting the form
if (!isset($_SESSION['user_id']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: user-login.php');
    exit();
}

// Basic validation
if (empty(trim($_POST['shipping_address']))) {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Shipping address is required.'];
    header('Location: checkout.php');
    exit();
}

$conn->begin_transaction();

try {
    $user_id = $_SESSION['user_id'];
    $shipping_address = trim($_POST['shipping_address']);

    // 1. Get cart items and lock the products row to prevent race conditions on stock
    $sql_cart = "SELECT p.id as product_id, p.price, c.quantity, p.stock_qty 
                 FROM cart c 
                 JOIN products p ON c.product_id = p.id 
                 WHERE c.user_id = ? FOR UPDATE"; // Lock rows
    $stmt_cart = $conn->prepare($sql_cart);
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $result_cart = $stmt_cart->get_result();

    $cart_items = [];
    $total_amount = 0;
    while ($row = $result_cart->fetch_assoc()) {
        // Check if there is enough stock
        if ($row['quantity'] > $row['stock_qty']) {
            throw new Exception("Not enough stock for product ID: " . $row['product_id']);
        }
        $cart_items[] = $row;
        $total_amount += $row['price'] * $row['quantity'];
    }
    $stmt_cart->close();

    if (empty($cart_items)) {
        throw new Exception("Cannot place order with an empty cart.");
    }

    // 2. Create the order in the 'orders' table
    $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address) VALUES (?, ?, ?)");
    $stmt_order->bind_param("ids", $user_id, $total_amount, $shipping_address);
    $stmt_order->execute();
    $order_id = $stmt_order->insert_id;
    $stmt_order->close();

    // 3. Insert items into 'order_items' and update product stock
    $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt_stock = $conn->prepare("UPDATE products SET stock_qty = stock_qty - ? WHERE id = ?");

    foreach ($cart_items as $item) {
        $stmt_items->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $stmt_items->execute();

        $stmt_stock->bind_param("ii", $item['quantity'], $item['product_id']);
        $stmt_stock->execute();
    }
    $stmt_items->close();
    $stmt_stock->close();

    // 4. Clear the user's cart
    $stmt_clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt_clear->bind_param("i", $user_id);
    $stmt_clear->execute();
    $stmt_clear->close();

    // If everything is successful, commit the transaction
    $conn->commit();

    header("Location: order_success.php?order_id=" . $order_id);
    exit();

} catch (Exception $e) {
    $conn->rollback();
    error_log($e->getMessage()); // Log error for debugging
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'There was a problem placing your order (e.g., an item is out of stock). Please review your cart and try again.'];
    header('Location: checkout.php');
    exit();
}
?>
