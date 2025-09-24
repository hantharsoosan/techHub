<?php
// --- STEP 1: ENABLE FULL ERROR REPORTING ---
// These two lines are the most important tools for debugging.
// They will force PHP to show you the exact error instead of a generic server error.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Always start the session at the very top to access session variables.
session_start();

// Include the database connection file.
include 'config/db.php';

// --- STEP 2: VERIFY DATABASE CONNECTION ---
// Immediately check if the connection was successful.
if ($conn->connect_error) {
    die("FATAL ERROR: Database connection failed: " . $conn->connect_error);
}

/*
// --- DEBUGGING HELPER ---
// Uncomment the following lines to see exactly what data this script is receiving.
echo "<pre>POST Data:\n";
var_dump($_POST);
echo "\nSession Data:\n";
var_dump($_SESSION);
echo "</pre>";
// exit(); // Uncomment this to stop the script here and only see the data.
*/


// --- 1. IDENTIFY THE USER ---
// This logic is crucial. It determines if the current user is logged in or a guest.
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
$session_id = session_id(); // The session_id is always available for guests.

// --- ACTION: ADD A NEW ITEM TO THE CART (from index.php) ---
if (isset($_POST['action']) && $_POST['action'] == 'add') {
    // Sanitize input
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    if ($product_id && $quantity > 0) {
        // Get product price and stock from the database for security and validation.
        $stmt_product = $conn->prepare("SELECT price, stock_qty FROM products WHERE id = ?");
        // --- STEP 3: CHECK IF SQL PREPARATION SUCCEEDED ---
        if ($stmt_product === false) {
            die("SQL PREPARE ERROR (Product Select): " . $conn->error);
        }
        $stmt_product->bind_param("i", $product_id);
        $stmt_product->execute();
        $product = $stmt_product->get_result()->fetch_assoc();
        $stmt_product->close();

        if ($product) {
            // Check if the item already exists in the cart for this specific user or guest.
            if ($user_id) {
                $stmt_check = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
            } else {
                $stmt_check = $conn->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ?");
            }
            if ($stmt_check === false) {
                die("SQL PREPARE ERROR (Cart Check): " . $conn->error);
            }

            if ($user_id) {
                $stmt_check->bind_param("ii", $user_id, $product_id);
            } else {
                $stmt_check->bind_param("si", $session_id, $product_id);
            }
            $stmt_check->execute();
            $cart_item = $stmt_check->get_result()->fetch_assoc();
            $stmt_check->close();
            
            $new_quantity = $cart_item ? $cart_item['quantity'] + $quantity : $quantity;

            if ($new_quantity > $product['stock_qty']) {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error: Cannot add more than the available stock (' . $product['stock_qty'] . ').'];
            } else {
                if ($cart_item) {
                    $stmt_update = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
                    if ($stmt_update === false) {
                        die("SQL PREPARE ERROR (Cart Update): " . $conn->error);
                    }
                    $stmt_update->bind_param("ii", $new_quantity, $cart_item['id']);
                    $stmt_update->execute();
                    $stmt_update->close();
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Product quantity updated in your cart.'];
                } else {
                    $session_id_for_db = $user_id ? null : $session_id;
                    $stmt_insert = $conn->prepare("INSERT INTO cart (user_id, session_id, product_id, quantity, price) VALUES (?, ?, ?, ?, ?)");
                    if ($stmt_insert === false) {
                        // THIS IS THE MOST LIKELY PLACE FOR AN INSERT ERROR
                        die("SQL PREPARE ERROR (Cart Insert): " . $conn->error);
                    }
                    $stmt_insert->bind_param("isiid", $user_id, $session_id_for_db, $product_id, $quantity, $product['price']);
                    if (!$stmt_insert->execute()) {
                        die("SQL EXECUTE ERROR (Cart Insert): " . $stmt_insert->error);
                    }
                    $stmt_insert->close();
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Product added to cart successfully!'];
                }
            }
        } else {
             $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error: Product not found.'];
        }
    } else {
         $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error: Invalid product data.'];
    }
    header("Location: index.php");
    exit();
}

// --- ACTION: UPDATE ITEM QUANTITY (from cart_view.php) ---
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    // ... (rest of the code is assumed to be correct but would need similar error checking)
    $cart_id = filter_input(INPUT_POST, 'cart_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    if ($cart_id && $quantity > 0) {
        if ($user_id) {
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
            if($stmt) $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND session_id = ?");
            if($stmt) $stmt->bind_param("iis", $quantity, $cart_id, $session_id);
        }
        if($stmt){
            $stmt->execute();
            $stmt->close();
        }
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Cart updated successfully.'];
    }
    header("Location: cart_view.php");
    exit();
}

// --- ACTION: REMOVE ITEM FROM CART (from cart_view.php) ---
if (isset($_GET['action']) && $_GET['action'] == 'remove') {
    // ... (rest of the code is assumed to be correct but would need similar error checking)
    $cart_id = filter_input(INPUT_GET, 'cart_id', FILTER_VALIDATE_INT);
    if ($cart_id) {
        if ($user_id) {
            $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            if($stmt) $stmt->bind_param("ii", $cart_id, $user_id);
        } else {
            $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND session_id = ?");
            if($stmt) $stmt->bind_param("is", $cart_id, $session_id);
        }
        if($stmt){
            $stmt->execute();
            $stmt->close();
        }
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Item removed from cart.'];
    }
    header("Location: cart_view.php");
    exit();
}

// Fallback redirect if no valid action is provided.
header("Location: index.php");
exit();
?>

