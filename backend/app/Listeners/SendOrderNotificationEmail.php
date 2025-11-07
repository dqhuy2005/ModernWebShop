<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class SendOrderNotificationEmail
{

    protected $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order;
        $newStatus = $event->newStatus;

        // Map order status to notification type code
        $notificationTypeMap = [
            'pending' => 'order_created',
            'confirmed' => 'order_confirmed',
            'processing' => 'order_processing',
            'shipping' => 'order_shipping',
            'completed' => 'order_completed',
            'cancelled' => 'order_cancelled',
        ];

        $notificationTypeCode = $notificationTypeMap[$newStatus] ?? 'order_created';

        // Send notification
        try {
            $result = $this->notificationService->sendOrderNotification($order, $notificationTypeCode);

            if ($result['success']) {
                Log::info("Order notification sent successfully", [
                    'order_id' => $order->id,
                    'status' => $newStatus,
                    'log_id' => $result['log_id'],
                ]);
            } else {
                Log::warning("Order notification failed", [
                    'order_id' => $order->id,
                    'status' => $newStatus,
                    'error' => $result['error'],
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Exception in order notification listener", [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(OrderStatusChanged $event, \Throwable $exception): void
    {
        Log::error("Order notification listener failed", [
            'order_id' => $event->order->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
