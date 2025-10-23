@extends('layouts.user.app')

@section('title', 'Đăng ký - ModernWebShop')

@section('content')
<div class="register-section py-5" style="background-color: #F8F9FA; min-height: calc(100vh - 200px);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-sm border-0" style="border-radius: 12px;">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold" style="color: #202732;">Đăng ký tài khoản</h2>
                            <p class="text-muted">Tạo tài khoản mới để mua sắm</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('register.post') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label fw-semibold">Họ và tên</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white">
                                            <i class="fas fa-user text-muted"></i>
                                        </span>
                                        <input type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               id="name"
                                               name="name"
                                               value="{{ old('name') }}"
                                               placeholder="Nguyễn Văn A"
                                               required
                                               autofocus>
                                    </div>
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
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
                                               required>
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label fw-semibold">Số điện thoại</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">
                                        <i class="fas fa-phone text-muted"></i>
                                    </span>
                                    <input type="tel"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           id="phone"
                                           name="phone"
                                           value="{{ old('phone') }}"
                                           placeholder="0123456789">
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
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

                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label fw-semibold">Xác nhận mật khẩu</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white">
                                            <i class="fas fa-lock text-muted"></i>
                                        </span>
                                        <input type="password"
                                               class="form-control"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               placeholder="••••••••"
                                               required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                            <i class="far fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    Tôi đồng ý với <a href="#" class="text-danger">Điều khoản dịch vụ</a> và
                                    <a href="#" class="text-danger">Chính sách bảo mật</a>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-danger w-100 py-2 fw-semibold mb-3">
                                <i class="fas fa-user-plus me-2"></i>Đăng ký
                            </button>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="text-muted mb-0">
                                Đã có tài khoản?
                                <a href="{{ route('login') }}" class="text-danger text-decoration-none fw-semibold">
                                    Đăng nhập ngay
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

    document.getElementById('togglePasswordConfirm').addEventListener('click', function() {
        const password = document.getElementById('password_confirmation');
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

    // Password strength indicator
    const passwordInput = document.getElementById('password');
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = getPasswordStrength(password);

        // You can add visual feedback here
    });

    function getPasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]+/)) strength++;
        if (password.match(/[A-Z]+/)) strength++;
        if (password.match(/[0-9]+/)) strength++;
        if (password.match(/[$@#&!]+/)) strength++;

        return strength;
    }
</script>
@endpush
