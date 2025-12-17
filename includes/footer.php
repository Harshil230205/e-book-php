<footer class="bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row g-4">
            <!-- Brand Section -->
            <div class="col-lg-4 col-md-6">
                <h5 class="fw-bold mb-3"><?php echo $site_name; ?></h5>
                <p class="text-light mb-3">Your digital library for reading and sharing knowledge. Discover, learn, and grow with our extensive collection of books and resources.</p>
                <div class="social-links">
                    <a href="#" class="text-white me-3 fs-5" title="Facebook">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="#" class="text-white me-3 fs-5" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-white me-3 fs-5" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="text-white me-3 fs-5" title="LinkedIn">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <a href="#" class="text-white fs-5" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold mb-3">Quick Links</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Browse Books</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Categories</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">New Arrivals</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Popular Books</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Authors</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold mb-3">Services</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Digital Reading</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Book Recommendations</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Reading Lists</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Book Reviews</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Community</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold mb-3">Support</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Help Center</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Contact Us</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">FAQ</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Report Issue</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Feedback</a></li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div class="col-lg-2 col-md-12">
                <h6 class="fw-bold mb-3">Stay Updated</h6>
                <p class="text-light small mb-3">Subscribe to get updates on new books and features.</p>
                <form class="newsletter-form">
                    <div class="input-group mb-2">
                        <input type="email" class="form-control form-control-sm" placeholder="Your email" required>
                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
                <div class="mt-3">
                    <small class="text-light">
                        <i class="fas fa-mobile-alt me-2"></i>
                        Download our mobile app
                    </small>
                    <div class="mt-2">
                        <a href="#" class="text-white me-2" title="Download on App Store">
                            <i class="fab fa-apple fs-5"></i>
                        </a>
                        <a href="#" class="text-white" title="Get it on Google Play">
                            <i class="fab fa-google-play fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <hr class="my-4 border-secondary">
        <div class="row align-items-center">
            <div class="col-md-8">
                <p class="mb-0 small text-light">
                    &copy; 2025 <?php echo $site_name; ?>. All rights reserved. 
                    <span class="mx-2">|</span>
                    <a href="#" class="text-light text-decoration-none">Privacy Policy</a>
                    <span class="mx-2">|</span>
                    <a href="#" class="text-light text-decoration-none">Terms of Service</a>
                    <span class="mx-2">|</span>
                    <a href="#" class="text-light text-decoration-none">Cookie Policy</a>
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-2 mt-md-0">
                <small class="text-light">
                    <i class="fas fa-heart text-danger"></i>
                    Made with love for book lovers
                </small>
            </div>
        </div>
    </div>
</footer>

<!-- Add some custom CSS for better styling -->
<style>
.social-links a:hover {
    color: #007bff !important;
    transition: color 0.3s ease;
}

.newsletter-form .btn {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

.newsletter-form .form-control:focus {
    box-shadow: none;
    border-color: #007bff;
}

footer a:hover {
    color: #007bff !important;
    transition: color 0.3s ease;
}

footer ul li a:hover {
    padding-left: 5px;
    transition: padding-left 0.3s ease;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Newsletter subscription handler
document.querySelector('.newsletter-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const email = this.querySelector('input[type="email"]').value;
    if (email) {
        // Here you would typically send the email to your backend
        alert('Thank you for subscribing! You will receive updates about new books and features.');
        this.reset();
    }
});
</script>
</body>
</html>