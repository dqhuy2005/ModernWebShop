<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng nhập CMS - ModernWebShop</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }

        .cms-login-container {
            position: relative;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Animated Background */
        .cms-login-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #5865f2 0%, #3949ab 50%, #1e1b4b 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            z-index: 0;
        }

        .cms-login-background::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background-image:
                radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(88, 101, 242, 0.1) 0%, transparent 50%);
            animation: floatBubbles 20s ease-in-out infinite;
        }

        @keyframes gradientShift {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }

        @keyframes floatBubbles {
            0%, 100% {
                transform: translate(0, 0);
            }
            50% {
                transform: translate(50px, 50px);
            }
        }

        /* Login Card */
        .cms-login-card {
            position: relative;
            background: #36393f;
            border-radius: 8px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 480px;
            padding: 32px;
            z-index: 1;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Title and Subtitle */
        .cms-login-title {
            font-size: 24px;
            font-weight: 600;
            color: #ffffff;
            text-align: center;
            margin-bottom: 8px;
        }

        .cms-login-subtitle {
            font-size: 16px;
            color: #b9bbbe;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Alert Messages */
        .alert {
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
            animation: shake 0.3s ease;
        }

        .alert-error {
            background: #ed4245;
            color: #ffffff;
        }

        .alert-success {
            background: #3ba55d;
            color: #ffffff;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        /* Form Styles */
        .cms-login-form {
            width: 100%;
        }

        .cms-form-group {
            margin-bottom: 20px;
        }

        .cms-form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #b9bbbe;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .required {
            color: #ed4245;
        }

        .cms-form-input {
            width: 100%;
            padding: 10px 12px;
            background: #202225;
            border: 1px solid #202225;
            border-radius: 4px;
            color: #dcddde;
            font-size: 16px;
            transition: border-color 0.2s ease, background-color 0.2s ease;
        }

        .cms-form-input:focus {
            outline: none;
            border-color: #5865f2;
            background: #18191c;
        }

        .cms-form-input.error {
            border-color: #ed4245;
        }

        .cms-forgot-link {
            display: inline-block;
            margin-top: 4px;
            font-size: 14px;
            color: #00aff4;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .cms-forgot-link:hover {
            text-decoration: underline;
            color: #00c8ff;
        }

        /* Login Button */
        .cms-login-button {
            width: 100%;
            padding: 12px 16px;
            background: #5865f2;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
            margin-top: 20px;
        }

        .cms-login-button:hover:not(:disabled) {
            background: #4752c4;
        }

        .cms-login-button:active:not(:disabled) {
            transform: scale(0.98);
        }

        .cms-login-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Register Prompt */
        .cms-register-prompt {
            margin-top: 8px;
            font-size: 14px;
            color: #72767d;
            text-align: left;
        }

        .cms-register-link {
            color: #00aff4;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .cms-register-link:hover {
            text-decoration: underline;
            color: #00c8ff;
        }

        /* Loading Spinner */
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #ffffff;
            animation: spin 0.6s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .cms-login-card {
                max-width: 90%;
                padding: 24px;
            }

            .cms-login-title {
                font-size: 20px;
            }

            .cms-login-subtitle {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .cms-login-card {
                border-radius: 0;
                height: 100vh;
                max-width: 100%;
                display: flex;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="cms-login-container">
        <div class="cms-login-background"></div>

        <div class="cms-login-card">
            <div class="cms-login-content">
                <h1 class="cms-login-title">Chào mừng trở lại!</h1>
                <p class="cms-login-subtitle">Rất vui mừng khi được gặp lại bạn!</p>

                @if(session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('cms.login.post') }}" method="POST" class="cms-login-form" id="loginForm">
                    @csrf

                    <div class="cms-form-group">
                        <label for="email" class="cms-form-label">
                            Email hoặc Số Điện Thoại <span class="required">*</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="cms-form-input @error('email') error @enderror"
                            required
                            autocomplete="email"
                            autofocus
                        />
                    </div>

                    <div class="cms-form-group">
                        <label for="password" class="cms-form-label">
                            Mật khẩu <span class="required">*</span>
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="cms-form-input @error('password') error @enderror"
                            required
                            autocomplete="current-password"
                        />
                        <a href="{{ route('password.request') }}" class="cms-forgot-link">
                            Quên mật khẩu?
                        </a>
                    </div>

                    <button type="submit" class="cms-login-button" id="loginBtn">
                        Đăng nhập
                    </button>

                    <div class="cms-register-prompt">
                        Cần một tài khoản? <a href="{{ route('cms.register') }}" class="cms-register-link">Đăng ký</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Handle form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner"></span>Đang đăng nhập...';
        });

        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);
    </script>
</body>
</html>
