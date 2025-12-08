<?php

namespace App\Services\impl;

use App\Mail\OrderNotification;
use App\Models\EmailTemplate;
use App\Models\NotificationLog;
use App\Models\NotificationType;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Services\INotificationService;

class NotificationService implements INotificationService
{
    public function sendOrderNotification(Order $order, $notificationTypeCode = 'order_status_changed')
    {
        try {
            $notificationType = NotificationType::where('code', $notificationTypeCode)
                ->where('is_active', true)
                ->where('email_enabled', true)
                ->first();

            if (!$notificationType) {
                throw new Exception("Notification type '{$notificationTypeCode}' not found or disabled");
            }

            $template = $notificationType->activeEmailTemplate('vi')->first();

            if (!$template) {
                throw new Exception("No active email template found for notification type '{$notificationTypeCode}'");
            }

            $recipientEmail = $order->customer_email ?? $order->user?->email;
            $recipientName = $order->customer_name ?? $order->user?->fullname ?? 'Quý khách';
            $recipientPhone = $order->customer_phone ?? $order->user?->phone ?? null;

            if (!$recipientEmail) {
                throw new Exception("Order #{$order->id} has no valid recipient email");
            }

            $emailData = is_array($template->preview_data) ? $template->preview_data : [];

            $subject = $this->replaceSubjectVariables($template->subject, $order);

            $log = $this->createNotificationLog(
                $notificationType,
                $template,
                $order,
                $recipientEmail,
                $recipientName,
                $recipientPhone,
                $subject
            );

            Mail::to($recipientEmail, $recipientName)
                ->send(new OrderNotification($order, $emailData, $subject));

            $log->markAsSent();

            return [
                'success' => true,
                'log_id' => $log->id,
                'message' => 'Notification sent successfully',
            ];

        } catch (Exception $e) {
            Log::error('Failed to send order notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if (isset($log)) {
                $log->markAsFailed($e->getMessage(), [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function createNotificationLog(
        NotificationType $notificationType,
        EmailTemplate $template,
        Order $order,
        string $recipientEmail,
        ?string $recipientName = null,
        ?string $recipientPhone = null,
        $subject = null
    ) {
        return NotificationLog::create([
            'notification_type_id' => $notificationType->id,
            'email_template_id' => $template->id,
            'user_id' => $order->user_id,
            'recipient_email' => $recipientEmail,
            'recipient_name' => $recipientName,
            'recipient_phone' => $recipientPhone,
            'related_type' => 'Order',
            'related_id' => $order->id,
            'channel' => 'email',
            'status' => 'pending',
            'subject' => $subject ?? $template->subject,
            'template_data' => [
                'order_id' => $order->id,
                'order_status' => $order->status,
                'customer_name' => $recipientName,
            ],
            'max_retry' => $notificationType->getDefaultRetryCount(),
            'scheduled_at' => now(),
        ]);
    }

    public function getStatistics($dateFrom = null, $dateTo = null)
    {
        $query = NotificationLog::query();

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return [
            'total' => $query->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'sent' => (clone $query)->where('status', 'sent')->count(),
            'failed' => (clone $query)->where('status', 'failed')->count(),
            'read' => (clone $query)->where('status', 'read')->count(),
            'clicked' => (clone $query)->where('status', 'clicked')->count(),
        ];
    }

    private function replaceSubjectVariables($subject, Order $order)
    {
        $orderId = str_pad($order->id, 6, '0', STR_PAD_LEFT);

        $replacements = [
            '{{order_id}}' => $orderId,
            '{{orderId}}' => $orderId,
            '{order_id}' => $orderId,
            '{orderId}' => $orderId,
            '{{customer_name}}' => $order->user->fullname ?? 'Quý khách',
            '{{order_status}}' => $order->status,
            '{{total_amount}}' => number_format($order->total_amount, 0, ',', '.') . ' ₫',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $subject);
    }
}
