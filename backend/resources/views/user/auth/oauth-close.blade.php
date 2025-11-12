<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $success ? 'Đăng nhập thành công' : 'Đăng nhập thất bại' }}</title>
</head>
<body>
    <script>
        // Send message to parent window
        if (window.opener) {
            try {
                window.opener.postMessage({
                    type: '{{ $success ? "oauth-success" : "oauth-failure" }}',
                    message: '{{ $message ?? ($success ? "Đăng nhập thành công!" : "Đăng nhập thất bại. Vui lòng thử lại.") }}'
                }, window.location.origin);
            } catch (e) {
                console.error('Failed to send message to parent:', e);
            }
            
            // Close popup after a short delay to ensure message is sent
            setTimeout(function() {
                window.close();
            }, 100);
        } else {
            // No opener, redirect to home or login page
            @if($success)
                window.location.href = '{{ route('home') }}';
            @else
                window.location.href = '{{ route('login') }}';
            @endif
        }
    </script>
    <p style="text-align: center; margin-top: 50px; font-family: Arial, sans-serif;">
        {{ $success ? '✓ Đăng nhập thành công!' : '✗ Đăng nhập thất bại.' }}
        <br>
        <small>Cửa sổ sẽ tự động đóng...</small>
    </p>
</body>
</html>
