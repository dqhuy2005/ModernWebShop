@extends('layouts.user.app')

@section('title', 'Đăng ký')

@section('content')
    <div class="register-section py-5" style="background-color: #F8F9FA; min-height: calc(100vh - 200px);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="card shadow-sm border-0" style="border-radius: 12px;">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <h2 class="fw-bold" style="color: #202732;">Đăng ký tài khoản</h2>
                            </div>

                            <form action="{{ route('register.post') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="fullname" class="form-label fw-semibold">Họ và tên</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('fullname') is-invalid @enderror"
                                            id="fullname" name="fullname" value="{{ old('fullname') }}" required autofocus>
                                    </div>
                                    @error('fullname')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Email</label>
                                    <div class="input-group">
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email') }}" placeholder=""
                                            required>
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label fw-semibold">Số điện thoại</label>
                                    <div class="input-group">
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                            id="phone" name="phone" value="{{ old('phone') }}" placeholder="">
                                    </div>
                                    @error('phone')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">Mật khẩu</label>
                                    <div class="input-group password-input-wrapper">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            id="password" name="password" placeholder="" required>
                                        <button class="btn btn-outline-secondary password-toggle-btn" type="button"
                                            id="togglePassword">
                                            <i class="far fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block password-error-msg">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label fw-semibold">Xác nhận mật
                                        khẩu</label>
                                    <div class="input-group password-input-wrapper">
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" placeholder="" required>
                                        <button class="btn btn-outline-secondary password-toggle-btn" type="button"
                                            id="togglePasswordConfirm">
                                            <i class="far fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="password-confirm-feedback"></div>
                                </div>

                                <button type="submit" class="btn btn-danger w-100 py-2 mb-3">
                                    TẠO TÀI KHOẢN
                                </button>
                            </form>

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

@push('styles')
    <style>
        .password-input-wrapper {
            position: relative;
        }

        .password-toggle-btn {
            z-index: 5;
            position: relative;
        }

        .password-error-msg {
            display: block !important;
            margin-top: 0.25rem;
            position: relative;
            z-index: 1;
        }

        .invalid-feedback {
            display: block !important;
        }

        #password_confirmation.is-invalid {
            border-color: #dc3545;
        }

        #password_confirmation.is-valid {
            border-color: #198754;
        }

        .valid-feedback {
            display: block;
            color: #198754;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#togglePassword').on('click', function() {
                const $password = $('#password');
                const $icon = $(this).find('i');

                if ($password.attr('type') === 'password') {
                    $password.attr('type', 'text');
                    $icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    $password.attr('type', 'password');
                    $icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            $('#togglePasswordConfirm').on('click', function() {
                const $password = $('#password_confirmation');
                const $icon = $(this).find('i');

                if ($password.attr('type') === 'password') {
                    $password.attr('type', 'text');
                    $icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    $password.attr('type', 'password');
                    $icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            function validatePasswordConfirmation() {
                const password = $('#password').val();
                const passwordConfirm = $('#password_confirmation').val();
                const $confirmField = $('#password_confirmation');
                const $feedback = $('#password-confirm-feedback');

                if (passwordConfirm.length === 0) {
                    $confirmField.removeClass('is-invalid is-valid');
                    $feedback.removeClass('invalid-feedback valid-feedback').text('');
                    return;
                }

                if (password === passwordConfirm) {
                    $confirmField.removeClass('is-invalid').addClass('is-valid');
                    $feedback.removeClass('invalid-feedback').addClass('valid-feedback');
                    $feedback.text('Mật khẩu khớp');
                } else {
                    $confirmField.removeClass('is-valid').addClass('is-invalid');
                    $feedback.removeClass('valid-feedback').addClass('invalid-feedback');
                    $feedback.text('Mật khẩu không khớp');
                }
            }

            $('#password, #password_confirmation').on('input', function() {
                validatePasswordConfirmation();
            });

            $('#password').on('input', function() {
                const password = $(this).val();
                const strength = getPasswordStrength(password);

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
        });
    </script>
@endpush
