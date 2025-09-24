<?php
// The shop_header starts the session and includes the database connection.
include 'includes/shop_header.php';

// --- Security Check: Redirect if user is not logged in ---
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = 'checkout.php';
    $_SESSION['message'] = ['type' => 'warning', 'text' => 'You must be logged in to check out.'];
    header('Location: user-login.php');
    exit();
}

// Fetch cart data to display in the order summary.
$user_id = $_SESSION['user_id'];
$subtotal = 0;
$cart_items = [];
$sql = "SELECT c.quantity, p.name, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $subtotal += $row['price'] * $row['quantity'];
    }
} else {
    // If the cart is empty, redirect away from the checkout page.
    $_SESSION['message'] = ['type' => 'info', 'text' => 'Your cart is empty.'];
    header('Location: index.php');
    exit();
}
$stmt->close();
?>

<!-- Include Stripe.js library for secure credit card handling -->
<script src="https://js.stripe.com/v3/"></script>

<div class="container my-5">
    <h1 class="text-center mb-5">Secure Checkout</h1>

    <!-- Display any session messages (e.g., payment failed) -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message']['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['message']['text']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    
    <div class="row g-5">
        <!-- Left Side: Shipping and Payment Form -->
        <div class="col-lg-7">
            <!-- This form will now submit to our new payment processing script -->
            <form action="process_payment.php" method="POST" id="payment-form">
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h4 class="mb-0">1. Shipping Information</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Shipping Address</label>
                            <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" placeholder="Enter your full address, city, and phone number" required></textarea>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-light py-3">
                        <h4 class="mb-0">2. Payment Method</h4>
                    </div>
                    <div class="card-body p-4">
                        <!-- Payment method selection tabs -->
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <!-- <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pills-card-tab" data-bs-toggle="pill" data-bs-target="#pills-card" type="button" role="tab" aria-selected="true"><i class="fas fa-credit-card me-2"></i>Credit Card</button>
                            </li> -->
                             <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-cod-tab" data-bs-toggle="pill" data-bs-target="#pills-cod" type="button" role="tab" aria-selected="false"><i class="fas fa-money-bill-wave me-2"></i>Cash on Delivery</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <!-- Credit Card Tab Content -->
                            <div class="tab-pane fade show active" id="pills-card" role="tabpanel">
                                <p>Pay securely with your credit card. We partner with Stripe for safe payment processing.</p>
                                <!--  -->
                                <!-- This div is a container for the secure Stripe form elements -->
                                <!-- <div id="card-element" class="form-control p-3"></div> -->
                                <!-- Used to display form errors from Stripe -->
                                <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                            </div>
                             <!-- Cash on Delivery Tab Content -->
                            <div class="tab-pane fade" id="pills-cod" role="tabpanel">
                                <p class="text-muted">Pay with cash when your order is delivered. Please have the exact amount ready.</p>
                            </div>
                        </div>
                        <!-- Hidden inputs to manage payment data -->
                        <input type="hidden" name="payment_method_id" id="payment_method_id">
                        <input type="hidden" name="payment_method_type" id="payment_method_type" value="card">
                    </div>
                </div>
                 <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary-nude btn-lg" id="submit-button">
                        <span id="button-text">Pay $<?php echo number_format($subtotal, 2); ?></span>
                        <span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Side: Order Summary -->
        <div class="col-lg-5">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-light py-3">
                    <h4 class="d-flex justify-content-between align-items-center mb-0">
                        <span>Order Summary</span>
                        <span class="badge bg-primary-nude rounded-pill"><?php echo count($cart_items); ?></span>
                    </h4>
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($cart_items as $item): ?>
                            <li class="list-group-item d-flex justify-content-between lh-sm">
                                <div>
                                    <h6 class="my-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <small class="text-muted">Quantity: <?php echo $item['quantity']; ?></small>
                                </div>
                                <span class="text-muted">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                            </li>
                        <?php endforeach; ?>
                        <li class="list-group-item d-flex justify-content-between fs-5 fw-bold bg-light mt-3">
                            <span>Total (USD)</span>
                            <strong>$<?php echo number_format($subtotal, 2); ?></strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // --- JavaScript for Secure Stripe Integration ---
    // IMPORTANT: Replace with your actual Stripe Publishable Key
    const stripe = Stripe('pk_test_YOUR_STRIPE_PUBLISHABLE_KEY'); 
    const elements = stripe.elements();
    const cardElement = elements.create('card');
    cardElement.mount('#card-element');

    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const buttonText = document.getElementById('button-text');
    const spinner = document.getElementById('spinner');

    // Update the hidden input and button text when the payment method changes
    document.querySelectorAll('#pills-tab button').forEach(tab => {
        tab.addEventListener('shown.bs.tab', event => {
            const paymentType = event.target.id.split('-')[1]; // 'card' or 'cod'
            document.getElementById('payment_method_type').value = paymentType;
            if(paymentType === 'cod') {
                buttonText.textContent = 'Place Order';
            } else {
                buttonText.textContent = 'Pay $<?php echo number_format($subtotal, 2); ?>';
            }
        });
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const paymentType = document.getElementById('payment_method_type').value;

        // Show spinner and disable button
        submitButton.disabled = true;
        buttonText.style.display = 'none';
        spinner.style.display = 'inline-block';

        if (paymentType === 'card') {
            // If paying by card, create a secure PaymentMethod token with Stripe
            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardElement,
            });

            if (error) {
                document.getElementById('card-errors').textContent = error.message;
                // Re-enable button if there was an error
                submitButton.disabled = false;
                buttonText.style.display = 'inline-block';
                spinner.style.display = 'none';
            } else {
                // If successful, add the token to the form and submit it to your server
                document.getElementById('payment_method_id').value = paymentMethod.id;
                form.submit();
            }
        } else {
            // For Cash on Delivery, just submit the form directly
            form.submit();
        }
    });
</script>

<?php include 'includes/shop_footer.php'; ?>

