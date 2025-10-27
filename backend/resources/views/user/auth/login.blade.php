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
                            </div>

                            <form action="{{ route('login.post') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}" required autofocus>
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">Mật khẩu</label>
                                    <div class="position-relative password-input-wrapper">
                                        <input type="password"
                                            class="form-control @error('password') is-invalid @enderror" id="password"
                                            name="password" required>
                                        <button
                                            class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted password-toggle-btn d-none"
                                            type="button" id="togglePassword" style="text-decoration: none;">
                                            <i class="far fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 text-start">
                                    <a href="{{ route('password.request') }}" class="text-muted text-decoration-none small">
                                        Quên mật khẩu?
                                    </a>
                                </div>

                                <button type="submit" class="btn btn-danger w-100 py-2 fw-normal mb-3">
                                    ĐĂNG NHẬP
                                </button>
                            </form>

                            <div class="text-center mb-3 position-relative">
                                <span class="text-muted small inner-text">HOẶC</span>
                                <div class="divided"></div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="button"
                                    class="btn btn-outline-primary d-flex align-items-center justify-content-center py-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        fill="currentColor" class="bi bi-facebook me-2" viewBox="0 0 16 16">
                                        <path
                                            d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z" />
                                    </svg>
                                    Facebook
                                </button>

                                <button type="button"
                                    class="btn btn-outline-danger d-flex align-items-center justify-content-center py-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 48 48" class="me-2">
                                        <path fill="#EA4335"
                                            d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z" />
                                        <path fill="#4285F4"
                                            d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z" />
                                        <path fill="#FBBC05"
                                            d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z" />
                                        <path fill="#34A853"
                                            d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z" />
                                        <path fill="none" d="M0 0h48v48H0z" />
                                    </svg>
                                    Google
                                </button>
                            </div>

                            <div class="text-center mt-4">
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

@push('styles')
    <style>
        .password-input-wrapper {
            position: relative;
        }

        .password-input-wrapper input.form-control {
            padding-right: 1rem;
            transition: padding-right 0.2s ease;
        }

        .password-input-wrapper.has-content input.form-control {
            padding-right: 3rem;
        }

        .password-toggle-btn {
            border: none;
            background: transparent;
            padding: 0.375rem 0.75rem;
            z-index: 10;
            transition: opacity 0.2s ease-in-out;
        }

        .password-toggle-btn:hover {
            color: #202732 !important;
        }

        .password-toggle-btn:focus {
            box-shadow: none;
        }

        .divided {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #757575;
            transform: translateY(-50%);
        }

        .inner-text {
            position: relative;
            background-color: #F8F9FA;
            padding: 0 10px;
            z-index: 1;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            const $passwordInput = $('#password');
            const $togglePasswordBtn = $('#togglePassword');
            const $passwordWrapper = $('.password-input-wrapper');

            // Function to toggle password visibility
            function togglePasswordVisibility() {
                const $icon = $togglePasswordBtn.find('i');

                if ($passwordInput.attr('type') === 'password') {
                    $passwordInput.attr('type', 'text');
                    $icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    $passwordInput.attr('type', 'password');
                    $icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            }

            // Function to toggle button visibility based on input content
            function toggleButtonVisibility() {
                if ($passwordInput.val().length > 0) {
                    $togglePasswordBtn.removeClass('d-none');
                    $passwordWrapper.addClass('has-content');
                } else {
                    $togglePasswordBtn.addClass('d-none');
                    $passwordWrapper.removeClass('has-content');
                    // Reset password type to password when empty
                    $passwordInput.attr('type', 'password');
                    $togglePasswordBtn.find('i').removeClass('fa-eye-slash').addClass('fa-eye');
                }
            }

            // Show/hide toggle button on input
            $passwordInput.on('input', function() {
                toggleButtonVisibility();
            });

            // Toggle password visibility on button click
            $togglePasswordBtn.on('click', function() {
                togglePasswordVisibility();
            });

            // Check on page load (in case of validation errors or autofill)
            toggleButtonVisibility();
        });
    </script>
@endpush
