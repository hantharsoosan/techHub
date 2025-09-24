</div> <!-- Close main container from header -->

<style>
    /* Footer Style Overrides for a lighter Nude Theme */
    .footer-bg {
        background-color: #e0d9d3; /* A light, warm nude color */
        color: #4a3f35; /* A dark brown for text */
    }
    .footer-bg h6 {
        color: #3d342e; /* A slightly darker brown for headings */
    }
    .footer-bg a, .footer-bg .fas {
        color: #4a3f35;
        text-decoration: none;
        transition: color 0.3s;
    }
    .footer-bg a:hover {
        color: #a89078 !important; /* The primary nude color for hover */
        text-decoration: underline;
    }
    .footer-bottom-bg {
        background-color: #d1c8c0; /* A slightly darker nude for the bottom bar */
        color: #4a3f35;
    }
     .map-responsive {
        overflow:hidden;
        padding-bottom:75%; /* Aspect Ratio */
        position:relative;
        height:0;
        border-radius: 0.5rem;
    }
    .map-responsive iframe {
        left:0;
        top:0;
        height:100%;
        width:100%;
        position:absolute;
    }
</style>

<footer class="footer-bg pt-5 pb-4">
    <div class="container text-center text-md-start">
        <div class="row">

            <!-- About Section -->
            <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mt-3">
                <h6 class="text-uppercase mb-4 fw-bold">The Nude Store</h6>
                <p>
                    Bringing you the finest selection of modern technology with a minimalist and elegant aesthetic. Quality and design in every product.
                </p>
            </div>

            <!-- Quick Links -->
            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                <h6 class="text-uppercase mb-4 fw-bold">Quick Links</h6>
                <p><a href="index.php">Shop</a></p>
                <p><a href="#">About Us</a></p>
                <p><a href="#">Contact</a></p>
            </div>

            <!-- Contact Info -->
            <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mt-3">
                <h6 class="text-uppercase mb-4 fw-bold">Contact</h6>
                <p><i class="fas fa-home me-3"></i> Yangon, Myanmar</p>
                <p><i class="fas fa-envelope me-3"></i> info@thenudestore.com</p>
                <p><i class="fas fa-phone me-3"></i> + 95 9 123 456 789</p>
            </div>
            
            <!-- Google Map -->
            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
                 <h6 class="text-uppercase mb-4 fw-bold">Our Location</h6>
                 <!-- Embedded Google Map -->
                 <div class="map-responsive">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d244434.9377187425!2d96.0924971485311!3d16.83907584109724!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30c1949e223e196b%3A0x56fbd271f8080bb4!2sYangon%2C%20Myanmar%20(Burma)!5e0!3m2!1sen!2smm!4v1695052219750!5m2!1sen!2smm" width="100%" height="150" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                 </div>
            </div>

        </div>
    </div>
</footer>

<!-- Copyright Section -->
<div class="footer-bottom-bg p-3">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                &copy; <?php echo date('Y'); ?> The Nude Store. All Rights Reserved.
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

