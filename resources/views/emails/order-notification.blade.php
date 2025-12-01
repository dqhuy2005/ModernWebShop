<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $subject }}</title>
    <style>
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            height: 100% !important;
        }

        body {
            background-color: #f4f4f4;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333333;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 20px;
            text-align: center;
            color: #ffffff;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .logo {
            margin-bottom: 15px;
        }

        .logo img {
            max-width: 150px;
            height: auto;
        }

        .email-content {
            padding: 30px 20px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #333333;
            margin-bottom: 15px;
        }

        .message {
            color: #666666;
            margin-bottom: 25px;
            line-height: 1.8;
        }

        .order-info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }

        .order-info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .order-info-row:last-child {
            border-bottom: none;
        }

        .order-info-label {
            font-weight: 600;
            color: #555555;
        }

        .order-info-value {
            color: #333333;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            background-color: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            overflow: hidden;
        }

        .products-table th {
            background-color: #667eea;
            color: #ffffff;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
        }

        .products-table td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
            color: #555555;
        }

        .products-table tr:last-child td {
            border-bottom: none;
        }

        .products-table .text-right {
            text-align: right;
        }

        .products-table .text-center {
            text-align: center;
        }

        .total-section {
            background-color: #f8f9fa;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
            text-align: right;
        }

        .total-row {
            display: flex;
            justify-content: flex-end;
            padding: 8px 0;
            font-size: 16px;
        }

        .total-label {
            margin-right: 20px;
            color: #555555;
        }

        .total-value {
            font-weight: 600;
            color: #333333;
            min-width: 120px;
        }

        .grand-total {
            border-top: 2px solid #667eea;
            margin-top: 10px;
            padding-top: 15px;
        }

        .grand-total .total-value {
            color: #667eea;
            font-size: 20px;
            font-weight: 700;
        }

        .shipping-info {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 20px;
            margin: 25px 0;
        }

        .shipping-info h3 {
            color: #667eea;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 16px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .shipping-info p {
            margin: 8px 0;
            color: #555555;
        }

        .shipping-info strong {
            color: #333333;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            margin: 20px 0;
            transition: all 0.3s ease;
        }

        .btn:hover {
            cursor: pointer;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-container {
            text-align: center;
            margin: 30px 0;
        }

        .email-footer {
            background-color: #f8f9fa;
            padding: 30px 20px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }

        .email-footer p {
            margin: 5px 0;
            color: #666666;
            font-size: 13px;
        }

        .email-footer a {
            color: #667eea;
            text-decoration: none;
        }

        .social-links {
            margin: 20px 0;
        }

        .social-links a {
            display: inline-block;
            margin: 0 8px;
            color: #667eea;
            font-size: 20px;
        }

        .divider {
            height: 1px;
            background-color: #e0e0e0;
            margin: 20px 0;
        }

        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                margin: 0 !important;
                border-radius: 0 !important;
            }

            .email-header h1 {
                font-size: 20px;
            }

            .products-table {
                font-size: 12px;
            }

            .products-table th,
            .products-table td {
                padding: 8px;
            }

            .order-info-row {
                flex-direction: column;
            }

            .order-info-value {
                margin-top: 5px;
            }

            .total-row {
                flex-direction: column;
                text-align: left;
            }

            .total-value {
                margin-top: 5px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <div class="logo">
                @if (isset($companyLogo))
                    <img src="{{ $companyLogo }}" alt="{{ $companyName ?? 'Company Logo' }}">
                @else
                    <h2 style="margin: 0; color: #ffffff;">{{ $companyName ?? 'Modern Web Shop' }}</h2>
                @endif
            </div>
            <h1>{{ $emailTitle ?? 'Thông báo đơn hàng' }}</h1>
        </div>

        <div class="email-content">
            <div class="greeting">
                Xin chào {{ $customerName ?? 'Quý khách' }},
            </div>

            <div class="message">
                {!! $emailMessage ?? 'Cảm ơn bạn đã đặt hàng tại cửa hàng chúng tôi!' !!}
            </div>

            <div class="order-info-box">
                <div class="order-info-row">
                    <span class="order-info-label">Mã đơn hàng:</span>
                    <span class="order-info-value"><strong>
                            #{{ $orderId }}</strong></span>
                </div>
                <div class="order-info-row">
                    <span class="order-info-label">Trạng thái:</span>
                    <span class="order-info-value">
                        <span class="status-{{ $orderStatus }}">
                            {{ $orderStatusLabel }}
                        </span>
                    </span>
                </div>
                <div class="order-info-row">
                    <span class="order-info-label">Ngày đặt hàng:</span>
                    <span class="order-info-value">{{ $orderDate }}</span>
                </div>
            </div>

            <h3 style="color: #667eea; margin-top: 30px;">Chi tiết sản phẩm</h3>
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-right">Đơn giá</th>
                        <th class="text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orderItems as $item)
                        <tr>
                            <td>{{ $item['product_name'] }}</td>
                            <td class="text-center">{{ $item['quantity'] }}</td>
                            <td class="text-right">{{ number_format($item['unit_price'], 0, ',', '.') }} ₫</td>
                            <td class="text-right"><strong>{{ number_format($item['total_price'], 0, ',', '.') }}
                                    ₫</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total-section">
                <div class="total-row">
                    <span class="total-label">Tạm tính:</span>
                    <span class="total-value">{{ number_format($subtotal ?? $totalAmount, 0, ',', '.') }} ₫</span>
                </div>
                @if (isset($shippingFee) && $shippingFee > 0)
                    <div class="total-row">
                        <span class="total-label">Phí vận chuyển:</span>
                        <span class="total-value">{{ number_format($shippingFee, 0, ',', '.') }} ₫</span>
                    </div>
                @endif
                @if (isset($discount) && $discount > 0)
                    <div class="total-row">
                        <span class="total-label">Giảm giá:</span>
                        <span class="total-value">-{{ number_format($discount, 0, ',', '.') }} ₫</span>
                    </div>
                @endif
                <div class="total-row grand-total">
                    <span class="total-label">Tổng cộng:</span>
                    <span class="total-value">{{ number_format($totalAmount, 0, ',', '.') }} ₫</span>
                </div>
            </div>

            <div class="shipping-info">
                <h3>Thông tin giao hàng</h3>
                <p><strong>Người nhận:</strong> {{ $recipientName ?? $customerName }}</p>
                <p><strong>Số điện thoại:</strong> {{ $recipientPhone }}</p>
                <p><strong>Địa chỉ:</strong> {{ $shippingAddress }}</p>
                @if (isset($orderNote) && $orderNote)
                    <p><strong>Ghi chú:</strong> {{ $orderNote }}</p>
                @endif
            </div>

            @if ($orderStatus === 'completed')
                <div
                    style="background-color: #f0f8ff; border: 1px solid #cce7ff; border-radius: 8px; padding: 20px; margin: 25px 0;">
                    <h3 style="color: #667eea; margin-top: 0; margin-bottom: 15px; text-align: center;">
                        ⭐ Đánh giá sản phẩm
                    </h3>
                    <p style="text-align: center; color: #666; margin-bottom: 20px;">
                        Hãy chia sẻ trải nghiệm của bạn về những sản phẩm đã mua!
                    </p>

                    @foreach ($orderItems as $item)
                        <div
                            style="background-color: #ffffff; border-radius: 6px; padding: 15px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between;">
                            <span style="color: #333; font-weight: 500;">{{ $item['product_name'] }}</span>
                            <a href="{{ route('reviews.create', ['order' => $orderId, 'product' => $item['product_id']]) }}"
                                style="display: inline-block; padding: 10px 20px; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); color: #ffffff !important; text-decoration: none; border-radius: 20px; font-weight: 600; font-size: 13px;">
                                Đánh giá ngay
                            </a>
                        </div>
                    @endforeach
                </div>
            @elseif (isset($trackingUrl))
                <div class="btn-container">
                    <a href="{{ $trackingUrl }}" class="btn">Theo dõi đơn hàng</a>
                </div>
            @endif

            @if (isset($additionalMessage))
                <div class="message"
                    style="margin-top: 30px; padding: 15px; background-color: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                    {!! $additionalMessage !!}
                </div>
            @endif
        </div>

        <div class="email-footer">
            <p><strong>{{ $companyName ?? 'Modern Web Shop' }}</strong></p>
            <p>{{ $companyAddress ?? 'Địa chỉ: 123 Đường ABC, Quận XYZ, TP.HCM' }}</p>
            <p>Hotline: {{ $companyPhone ?? '1900-xxxx' }} | Email: {{ $companyEmail ?? 'support@example.com' }}</p>

            <div class="divider"></div>

            <p style="font-size: 12px; color: #999999;">
                Email này được gửi tự động, vui lòng không trả lời email này.<br>
                Nếu bạn cần hỗ trợ, vui lòng liên hệ với chúng tôi qua hotline hoặc email hỗ trợ.
            </p>

            @if (isset($unsubscribeUrl))
                <p style="font-size: 11px; color: #999999; margin-top: 15px;">
                    <a href="{{ $unsubscribeUrl }}" style="color: #999999;">Hủy đăng ký nhận email</a>
                </p>
            @endif

            <p style="font-size: 11px; color: #cccccc; margin-top: 10px;">
                © {{ date('Y') }} {{ $companyName ?? 'Modern Web Shop' }}. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>
