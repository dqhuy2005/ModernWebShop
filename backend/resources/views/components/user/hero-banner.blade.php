{{-- Hero Banner Component - Hot Deal This Week --}}
<section class="hero-banner py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row align-items-center">
            {{-- Left Product Image --}}
            <div class="col-md-4 text-center mb-4 mb-md-0">
                <img src="{{ asset('images/laptop-product.png') }}" alt="Laptop" class="img-fluid" 
                     style="max-height: 300px;">
            </div>

            {{-- Center Content --}}
            <div class="col-md-4 text-center text-white">
                {{-- Countdown Timer --}}
                <div class="countdown-timer mb-4">
                    <div class="d-flex justify-content-center gap-3">
                        <div class="time-box">
                            <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px;">
                                <div>
                                    <h3 class="mb-0 fw-bold">02</h3>
                                    <small>DAYS</small>
                                </div>
                            </div>
                        </div>
                        <div class="time-box">
                            <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px;">
                                <div>
                                    <h3 class="mb-0 fw-bold">10</h3>
                                    <small>HOURS</small>
                                </div>
                            </div>
                        </div>
                        <div class="time-box">
                            <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px;">
                                <div>
                                    <h3 class="mb-0 fw-bold">34</h3>
                                    <small>MINS</small>
                                </div>
                            </div>
                        </div>
                        <div class="time-box">
                            <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px;">
                                <div>
                                    <h3 class="mb-0 fw-bold">60</h3>
                                    <small>SECS</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Title --}}
                <h2 class="fw-bold mb-3">HOT DEAL THIS WEEK</h2>
                <p class="mb-4 text-uppercase">NEW COLLECTION UP TO 50% OFF</p>
                
                {{-- CTA Button --}}
                <a href="{{ route('hot-deals') }}" class="btn btn-danger btn-lg px-5 py-3">
                    SHOP NOW
                </a>
            </div>

            {{-- Right Product Image --}}
            <div class="col-md-4 text-center mb-4 mb-md-0">
                <img src="{{ asset('images/headphone-product.png') }}" alt="Headphone" class="img-fluid" 
                     style="max-height: 300px;">
            </div>
        </div>
    </div>
</section>

<style>
    .hero-banner {
        min-height: 400px;
    }
    
    .countdown-timer .time-box {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
</style>
