<?php

namespace Database\Seeders;

use App\Models\NotificationType;
use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class NotificationSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Notification Types for Orders
        $orderTypes = [
            [
                'code' => 'order_created',
                'name' => 'Đơn hàng mới được tạo',
                'category' => 'order',
                'description' => 'Gửi khi khách hàng tạo đơn hàng mới',
            ],
            [
                'code' => 'order_confirmed',
                'name' => 'Đơn hàng được xác nhận',
                'category' => 'order',
                'description' => 'Gửi khi admin xác nhận đơn hàng',
            ],
            [
                'code' => 'order_processing',
                'name' => 'Đơn hàng đang xử lý',
                'category' => 'order',
                'description' => 'Gửi khi đơn hàng đang được đóng gói',
            ],
            [
                'code' => 'order_shipping',
                'name' => 'Đơn hàng đang giao',
                'category' => 'order',
                'description' => 'Gửi khi đơn hàng được giao cho shipper',
            ],
            [
                'code' => 'order_completed',
                'name' => 'Đơn hàng hoàn thành',
                'category' => 'order',
                'description' => 'Gửi khi đơn hàng giao thành công',
            ],
            [
                'code' => 'order_cancelled',
                'name' => 'Đơn hàng bị hủy',
                'category' => 'order',
                'description' => 'Gửi khi đơn hàng bị hủy',
            ],
        ];

        foreach ($orderTypes as $type) {
            $notificationType = NotificationType::create(array_merge($type, [
                'is_active' => true,
                'email_enabled' => true,
                'sms_enabled' => false,
                'push_enabled' => false,
                'default_config' => [
                    'retry_count' => 3,
                    'delay_seconds' => 60,
                ],
            ]));

            // Create default email template for each type
            $this->createDefaultTemplate($notificationType);
        }

        // Create other notification types (for future expansion)
        $otherTypes = [
            [
                'code' => 'user_registered',
                'name' => 'Đăng ký tài khoản mới',
                'category' => 'user',
                'description' => 'Gửi email chào mừng khi user đăng ký',
            ],
            [
                'code' => 'user_verify_email',
                'name' => 'Xác thực email',
                'category' => 'user',
                'description' => 'Gửi link xác thực email',
            ],
            [
                'code' => 'user_reset_password',
                'name' => 'Đặt lại mật khẩu',
                'category' => 'user',
                'description' => 'Gửi link đặt lại mật khẩu',
            ],
            [
                'code' => 'promotion_new',
                'name' => 'Khuyến mãi mới',
                'category' => 'promotion',
                'description' => 'Thông báo về chương trình khuyến mãi',
            ],
        ];

        foreach ($otherTypes as $type) {
            NotificationType::create(array_merge($type, [
                'is_active' => false, // Disabled by default, will be enabled later
                'email_enabled' => true,
                'sms_enabled' => false,
                'push_enabled' => false,
            ]));
        }
    }

    private function createDefaultTemplate(NotificationType $notificationType)
    {
        $templates = [
            'order_created' => [
                'subject' => 'Đơn hàng #{{order_id}} đã được tạo thành công',
                'message' => 'Cảm ơn bạn đã đặt hàng! Đơn hàng của bạn đang chờ xác nhận.',
            ],
            'order_confirmed' => [
                'subject' => 'Đơn hàng #{{order_id}} đã được xác nhận',
                'message' => 'Đơn hàng của bạn đã được xác nhận. Chúng tôi đang chuẩn bị hàng cho bạn.',
            ],
            'order_processing' => [
                'subject' => 'Đơn hàng #{{order_id}} đang được xử lý',
                'message' => 'Đơn hàng của bạn đang được đóng gói.',
            ],
            'order_shipping' => [
                'subject' => 'Đơn hàng #{{order_id}} đang được giao',
                'message' => 'Đơn hàng của bạn đã được giao cho đơn vị vận chuyển.',
            ],
            'order_completed' => [
                'subject' => 'Đơn hàng #{{order_id}} đã hoàn thành',
                'message' => 'Đơn hàng của bạn đã được giao thành công!',
            ],
            'order_cancelled' => [
                'subject' => 'Đơn hàng #{{order_id}} đã bị hủy',
                'message' => 'Đơn hàng của bạn đã bị hủy.',
            ],
        ];

        $template = $templates[$notificationType->code] ?? null;

        if ($template) {
            EmailTemplate::create([
                'notification_type_id' => $notificationType->id,
                'name' => $notificationType->name . ' - Default Template',
                'subject' => $template['subject'],
                'body_html' => $this->getDefaultHtmlBody($template['message']),
                'body_text' => $this->getDefaultTextBody($template['message']),
                'available_variables' => [
                    'order_id',
                    'customer_name',
                    'order_status',
                    'order_date',
                    'total_amount',
                ],
                'locale' => 'vi',
                'is_active' => true,
                'is_default' => true,
                'version' => 1,
                'preview_data' => json_encode([
                    'order_id' => '000123',
                    'customer_name' => 'Nguyễn Văn A',
                    'order_status' => 'pending',
                    'order_date' => now()->format('d/m/Y H:i'),
                    'total_amount' => '1,500,000',
                ]),
            ]);
        }
    }

    private function getDefaultHtmlBody($message)
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #667eea; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thông báo đơn hàng</h1>
        </div>
        <div class="content">
            <p>Xin chào {{customer_name}},</p>
            <p>{$message}</p>
            <p><strong>Mã đơn hàng:</strong> #{{order_id}}</p>
            <p><strong>Ngày đặt:</strong> {{order_date}}</p>
            <p><strong>Tổng tiền:</strong> {{total_amount}} ₫</p>
        </div>
        <div class="footer">
            <p>© 2025 Modern Web Shop. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getDefaultTextBody($message)
    {
        return <<<TEXT
Xin chào {{customer_name}},

{$message}

Mã đơn hàng: #{{order_id}}
Ngày đặt: {{order_date}}
Tổng tiền: {{total_amount}} ₫

---
© 2025 Modern Web Shop. All rights reserved.
TEXT;
    }
}

