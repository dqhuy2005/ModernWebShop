<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đánh giá đã được duyệt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
        }
        .review-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .rating {
            color: #ffc107;
            font-size: 18px;
            margin: 10px 0;
        }
        .product-info {
            background: #fff;
            border: 1px solid #e0e0e0;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .product-name {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .order-code {
            color: #666;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background: #5568d3;
        }
        .footer {
            background: #f8f9fa;
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e0e0e0;
        }
        .note {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Đánh giá của bạn đã được duyệt!</h1>
        </div>

        <div class="content">
            <p>Xin chào <strong>{{ $user->fullname }}</strong>,</p>

            <p>Cảm ơn bạn đã dành thời gian đánh giá sản phẩm của chúng tôi! Đánh giá của bạn đã được phê duyệt và hiện đang hiển thị trên trang sản phẩm.</p>

            <div class="product-info">
                <div class="product-name">{{ $product->name }}</div>
                <div class="order-code">Mã đơn hàng: #{{ $orderCode }}</div>
            </div>

            <div class="review-box">
                <div class="rating">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= $review->rating)
                            ★
                        @else
                            ☆
                        @endif
                    @endfor
                    ({{ $review->rating }}/5)
                </div>

                @if($review->title)
                    <p><strong>{{ $review->title }}</strong></p>
                @endif

                @if($review->comment)
                    <p>{{ $review->comment }}</p>
                @endif
            </div>

            <div class="note">
                <strong>⚠️ Lưu ý quan trọng:</strong><br>
                Đánh giá của bạn đã được ghi nhận và không thể chỉnh sửa. Nếu bạn nhấp vào liên kết đánh giá trong email lần nữa, hệ thống sẽ thông báo rằng bạn đã hoàn thành đánh giá cho sản phẩm này.
            </div>

            <p style="text-align: center;">
                <a href="{{ route('products.show', $product->slug) }}" class="button">
                    Xem đánh giá trên trang sản phẩm
                </a>
            </p>

            <p>Đánh giá của bạn sẽ giúp những khách hàng khác có thêm thông tin để đưa ra quyết định mua hàng tốt hơn.</p>

            <p>Một lần nữa, xin cảm ơn bạn đã tin tưởng và ủng hộ ModernWebShop!</p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} ModernWebShop. All rights reserved.</p>
            <p>Email này được gửi tự động, vui lòng không trả lời email này.</p>
        </div>
    </div>
</body>
</html>
