@extends('layouts.user.app')

@section('title', 'Đăng nhập - ModernWebShop')

@section('content')
<div class="login-section py-5" style="background-color: #F8F9FA; min-height: calc(100vh - 200px);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card shadow-sm border-0" style="border-radius: 12px;">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold" style="color: #202732;">Đăng nhập</h2>
                            <p class="text-muted">Chào mừng bạn quay trở lại!</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('login.post') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">
                                        <i class="fas fa-envelope text-muted"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}"
                                           placeholder="example@email.com"
                                           required 
                                           autofocus>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">Mật khẩu</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="••••••••"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="far fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Ghi nhớ đăng nhập
                                </label>
                            </div>

                            <button type="submit" class="btn btn-danger w-100 py-2 fw-semibold mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                            </button>

                            <div class="text-center">
                                <a href="{{ route('password.request') }}" class="text-muted text-decoration-none small">
                                    Quên mật khẩu?
                                </a>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="text-muted mb-0">
                                Chưa có tài khoản? 
                                <a href="{{ route('register') }}" class="text-danger text-decoration-none fw-semibold">
                                    Đăng ký ngay
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const icon = this.querySelector('i');
        
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>
@endpush
