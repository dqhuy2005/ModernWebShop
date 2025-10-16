<footer class="footer bg-dark text-white mt-5">
    <div class="container py-4">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5>ModernWebShop</h5>
                <p>Cửa hàng trực tuyến cung cấp các sản phẩm chất lượng cao với giá cả phải chăng.</p>
                <div class="social-links mt-3">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <h5>Liên kết nhanh</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('home') }}" class="text-white-50">Trang chủ</a></li>
                    <li><a href="{{ route('products.index') }}" class="text-white-50">Sản phẩm</a></li>
                    <li><a href="{{ route('about') }}" class="text-white-50">Về chúng tôi</a></li>
                    <li><a href="{{ route('contact') }}" class="text-white-50">Liên hệ</a></li>
                </ul>
            </div>
            
            <div class="col-md-4 mb-3">
                <h5>Thông tin liên hệ</h5>
                <ul class="list-unstyled text-white-50">
                    <li><i class="fas fa-map-marker-alt me-2"></i> 123 Đường ABC, Quận 1, TP.HCM</li>
                    <li><i class="fas fa-phone me-2"></i> 0123 456 789</li>
                    <li><i class="fas fa-envelope me-2"></i> info@modernwebshop.com</li>
                </ul>
            </div>
        </div>
        
        <hr class="bg-white">
        
        <div class="row">
            <div class="col-md-12 text-center">
                <p class="mb-0">&copy; {{ date('Y') }} ModernWebShop. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
