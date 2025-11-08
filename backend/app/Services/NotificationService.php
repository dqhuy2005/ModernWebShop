<?php

namespace App\Services;

use App\Mail\OrderNotification;
use App\Models\EmailTemplate;
use App\Models\NotificationLog;
use App\Models\NotificationType;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class NotificationService
{
    /**
     * Send order notification email
     */
    public function sendOrderNotification(Order $order, $notificationTypeCode = 'order_status_changed')
    {
        try {
            // Get notification type
            $notificationType = NotificationType::where('code', $notificationTypeCode)
                ->where('is_active', true)
                ->where('email_enabled', true)
                ->first();

            if (!$notificationType) {
                throw new Exception("Notification type '{$notificationTypeCode}' not found or disabled");
            }

            // Get email template
            $template = $notificationType->activeEmailTemplate('vi')->first();

            if (!$template) {
                throw new Exception("No active email template found for notification type '{$notificationTypeCode}'");
            }

            // Prepare recipient - prioritize order's customer email over user email
            $recipientEmail = $order->customer_email ?? $order->user?->email;
            $recipientName = $order->customer_name ?? $order->user?->fullname ?? 'Quý khách';
            $recipientPhone = $order->customer_phone ?? $order->user?->phone ?? null;

            if (!$recipientEmail) {
                throw new Exception("Order #{$order->id} has no valid recipient email");
            }

            // Prepare email data from template
            $emailData = is_array($template->preview_data) ? $template->preview_data : [];

            // Replace variables in subject
            $subject = $this->replaceSubjectVariables($template->subject, $order);

            // Create notification log
            $log = $this->createNotificationLog(
                $notificationType,
                $template,
                $order,
                $recipientEmail,
                $recipientName,
                $recipientPhone,
                $subject
            );

            // Send email
            Mail::to($recipientEmail, $recipientName)
                ->send(new OrderNotification($order, $emailData, $subject));

            // Mark as sent
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

    /**
     * Create notification log entry
     */
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

    /**
     * Retry failed notifications
     */
    public function retryFailedNotifications($limit = 10)
    {
        $failedLogs = NotificationLog::retryable()
            ->orderBy('failed_at', 'asc')
            ->limit($limit)
            ->get();

        $results = [
            'total' => $failedLogs->count(),
            'success' => 0,
            'failed' => 0,
        ];

        foreach ($failedLogs as $log) {
            try {
                // Reload related order
                $order = Order::find($log->related_id);

                if (!$order) {
                    $log->markAsFailed('Related order not found');
                    $results['failed']++;
                    continue;
                }

                // Resend
                Mail::to($log->recipient_email)
                    ->send(new OrderNotification($order));

                $log->markAsSent();
                $results['success']++;

            } catch (Exception $e) {
                $log->markAsFailed($e->getMessage());
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Track email open (implement tracking pixel later)
     */
    public function trackEmailOpen($logId)
    {
        $log = NotificationLog::find($logId);

        if ($log && !$log->read_at) {
            $log->markAsRead();
        }

        return $log;
    }

    /**
     * Track email click (implement link tracking later)
     */
    public function trackEmailClick($logId)
    {
        $log = NotificationLog::find($logId);

        if ($log && !$log->clicked_at) {
            $log->markAsClicked();
        }

        return $log;
    }

    /**
     * Get notification statistics
     */
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

    /**
     * Replace variables in email subject
     */
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
