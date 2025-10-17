<section class="newsletter-section py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <i class="fas fa-envelope fa-4x text-muted mb-3"></i>
                <h3 class="mb-0">Sign Up for the <strong>NEWSLETTER</strong></h3>
            </div>
            <div class="col-md-6">
                <form action="#" method="POST" class="d-flex">
                    @csrf
                    <input type="email" name="email" class="form-control form-control-lg me-2"
                        placeholder="Enter Your Email" required>
                    <button type="submit" class="btn btn-danger btn-lg">
                        <i class="fas fa-envelope me-2"></i>Subscribe
                    </button>
                </form>
                <div class="social-links mt-3 text-center text-md-start">
                    <a href="#" class="text-dark me-3"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-dark me-3"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-dark me-3"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-dark"><i class="fab fa-pinterest fa-lg"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="main-footer bg-dark text-white py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-3 mb-4">
                <h5 class="text-uppercase mb-3">ABOUT US</h5>
                <p class="text-white-50 small">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                    sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                </p>
                <ul class="list-unstyled text-white-50 small">
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                        1734 Stonecoal Road
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone me-2 text-danger"></i>
                        +021-95-51-84
                    </li>
                    <li>
                        <i class="fas fa-envelope me-2 text-danger"></i>
                        email@email.com
                    </li>
                </ul>
            </div>

            <div class="col-md-3 mb-4">
                <h5 class="text-uppercase mb-3">CATEGORIES</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="" class="text-white-50 text-decoration-none">Hot deals</a></li>
                    <li class="mb-2"><a href="" class="text-white-50 text-decoration-none">Laptops</a></li>
                    <li class="mb-2"><a href="" class="text-white-50 text-decoration-none">Smartphones</a></li>
                    <li class="mb-2"><a href="" class="text-white-50 text-decoration-none">Cameras</a></li>
                    <li class="mb-2"><a href="" class="text-white-50 text-decoration-none">Accessories</a></li>
                </ul>
            </div>

            <div class="col-md-3 mb-4">
                <h5 class="text-uppercase mb-3">INFORMATION</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="" class="text-white-50 text-decoration-none">About Us</a></li>
                    <li class="mb-2"><a href=""class="text-white-50 text-decoration-none">Contact Us</a></li>
                    <li class="mb-2"><a href="" class="text-white-50 text-decoration-none">Privacy Policy</a>
                    </li>
                    <li class="mb-2"><a href="" class="text-white-50 text-decoration-none">Orders and
                            Returns</a></li>
                    <li class="mb-2"><a href="" class="text-white-50 text-decoration-none">Terms &
                            Conditions</a></li>
                </ul>
            </div>

            <div class="col-md-3 mb-4">
                <h5 class="text-uppercase mb-3">SERVICE</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="" class="text-white-50 text-decoration-none">My Account</a></li>
                    <li class="mb-2"><a href="" class="text-white-50 text-decoration-none">View Cart</a></li>
                    <li class="mb-2"><a href="" class="text-white-50 text-decoration-none">Wishlist</a></li>
                    <li class="mb-2"><a href="" class="text-white-50 text-decoration-none">Track My Order</a>
                    </li>
                    <li class="mb-2"><a href="" class="text-white-50 text-decoration-none">Help</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<div class="copyright-bar bg-black text-white-50 py-3">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                <small>
                    Copyright ©{{ date('Y') }} All rights reserved | This template is made with
                    <i class="fas fa-heart text-danger"></i> by <strong class="text-white">Electro</strong>
                </small>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <img src="{{ asset('images/payment-methods.png') }}" alt="Payment Methods" class="payment-icons"
                    style="max-height: 30px;">
            </div>
        </div>
    </div>
</div>

<style>
    .newsletter-section {
        border-top: 1px solid #dee2e6;
    }

    .main-footer a:hover {
        color: #dc3545 !important;
    }

    .copyright-bar {
        font-size: 0.875rem;
    }
</style>
