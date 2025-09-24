<?php
// Include the shared header.
include 'includes/shop_header.php';
?>

<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-body p-5">
            <div class="text-center mb-5">
                <h1 class="display-4">Get in Touch</h1>
                <p class="lead text-muted">We'd love to hear from you! Whether you have a question about our products, a feature request, or feedback for us, our team is ready to answer all your questions.</p>
            </div>

             <!-- Display any success or error messages from the form submission -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message']['type']; ?> alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['message']['text']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <div class="row g-5">
                <!-- Contact Form -->
                <div class="col-lg-7">
                    <h3>Send us a Message</h3>
                    <form action="process_contact.php" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary-nude">Submit Feedback</button>
                    </form>
                </div>

                <!-- Contact Information -->
                <div class="col-lg-5">
                    <h3>Contact Information</h3>
                    <p>Feel free to reach out to us through any of the following methods:</p>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="fas fa-map-marker-alt fa-fw me-2"></i>123 Tech Avenue, Silicon Valley, CA 94000</li>
                        <li class="mb-3"><i class="fas fa-phone fa-fw me-2"></i>(123) 456-7890</li>
                        <li class="mb-3"><i class="fas fa-envelope fa-fw me-2"></i>contact@nude-store.com</li>
                    </ul>
                    <!-- Embedded Google Map -->
                    <div class="ratio ratio-4x3">
                         <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3172.3325332999333!2d-122.086278384695!3d37.42206567982542!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fba024255f5f3%3A0x66f8c7e0f21a1d1!2sGoogleplex!5e0!3m2!1sen!2sus!4v1663793616584!5m2!1sen!2sus" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include the shared footer.
include 'includes/shop_footer.php';
?>
