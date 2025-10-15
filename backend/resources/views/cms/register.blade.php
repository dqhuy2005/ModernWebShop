<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng ký - CMS ModernWebShop</title>
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

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

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

        .alert {
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-error {
            background: #ed4245;
            color: #ffffff;
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
            transition: border-color 0.2s ease;
        }

        .cms-form-input:focus {
            outline: none;
            border-color: #5865f2;
            background: #18191c;
        }

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
            transition: background-color 0.2s ease;
            margin-top: 20px;
        }

        .cms-login-button:hover {
            background: #4752c4;
        }

        .cms-register-prompt {
            margin-top: 8px;
            font-size: 14px;
            color: #72767d;
            text-align: left;
        }

        .cms-register-link {
            color: #00aff4;
            text-decoration: none;
        }

        .cms-register-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="cms-login-container">
        <div class="cms-login-background"></div>

        <div class="cms-login-card">
            <div class="cms-login-content">
                <h1 class="cms-login-title">Tạo tài khoản</h1>
                <p class="cms-login-subtitle">Chào mừng bạn đến với ModernWebShop!</p>

                @if($errors->any())
                    <div class="alert alert-error">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('cms.register.post') }}" method="POST" class="cms-login-form">
                    @csrf

                    <div class="cms-form-group">
                        <label for="name" class="cms-form-label">
                            Tên <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="fullname"
                            value="{{ old('fullname') }}"
                            class="cms-form-input"
                            required
                        />
                    </div>

                    <div class="cms-form-group">
                        <label for="email" class="cms-form-label">
                            Email <span class="required">*</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="cms-form-input"
                            required
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
                            class="cms-form-input"
                            required
                        />
                    </div>

                    <div class="cms-form-group">
                        <label for="password_confirmation" class="cms-form-label">
                            Xác nhận mật khẩu <span class="required">*</span>
                        </label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="cms-form-input"
                            required
                        />
                    </div>

                    <button type="submit" class="cms-login-button">
                        Đăng ký
                    </button>

                    <div class="cms-register-prompt">
                        Đã có tài khoản? <a href="{{ route('cms.login') }}" class="cms-register-link">Đăng nhập</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
