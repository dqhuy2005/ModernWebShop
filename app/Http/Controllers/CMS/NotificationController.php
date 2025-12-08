<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\NotificationLog;
use App\Models\NotificationType;
use App\Models\Order;
use App\Services\impl\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display notification logs
     */
    public function index(Request $request)
    {
        $query = NotificationLog::select('id', 'notification_type_id', 'user_id', 'email_template_id', 'recipient_email', 'status', 'sent_at', 'error_message', 'created_at', 'updated_at')
            ->with([
                'notificationType:id,name,code',
                'user:id,fullname,email',
                'emailTemplate:id,name,subject'
            ])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->whereHas('notificationType', function ($q) use ($request) {
                $q->where('code', $request->type);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20);
        $types = NotificationType::select('id', 'name', 'code')->active()->get();
        $stats = $this->notificationService->getStatistics();

        return view('admin.notifications.index', compact('logs', 'types', 'stats'));
    }

    /**
     * Preview email template
     */
    public function preview($templateId)
    {
        $template = EmailTemplate::select('id', 'name', 'subject', 'body', 'preview_data')->findOrFail($templateId);
        $previewData = $template->preview_data ? json_decode($template->preview_data, true) : [];

        if (empty($previewData)) {
            $order = Order::select('id', 'user_id', 'customer_name', 'total_amount', 'status', 'created_at')
                ->with([
                    'user:id,fullname,email',
                    'orderDetails:id,order_id,product_name,quantity,unit_price'
                ])
                ->first();
            if ($order) {
                $previewData = [
                    'orderId' => $order->id,
                    'customerName' => $order->user->fullname ?? 'Sample Customer',
                    'orderStatus' => $order->status,
                    'orderStatusLabel' => $order->status_label,
                    'orderDate' => $order->created_at->format('d/m/Y H:i'),
                    'totalAmount' => number_format($order->total_amount, 0, ',', '.'),
                ];
            }
        }

        $html = $template->getPreviewHtml($previewData);

        return view('admin.notifications.preview', compact('template', 'html'));
    }

    /**
     * Send test email
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'notification_type' => 'required|exists:notification_types,code',
            'test_email' => 'nullable|email',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($request->filled('test_email')) {
            $order->user->email = $request->test_email;
        }

        $result = $this->notificationService->sendOrderNotification(
            $order,
            $request->notification_type
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully!',
                'log_id' => $result['log_id'],
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $result['error'],
            ], 500);
        }
    }

    /**
     * Retry failed notification
     */
    public function retry($logId)
    {
        $log = NotificationLog::findOrFail($logId);

        if (!$log->canRetry()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot retry this notification (max retries reached)',
            ], 400);
        }

        $order = Order::find($log->related_id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Related order not found',
            ], 404);
        }

        $result = $this->notificationService->sendOrderNotification(
            $order,
            $log->notificationType->code
        );

        return response()->json($result);
    }

    /**
     * Get statistics
     */
    public function statistics(Request $request)
    {
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $stats = $this->notificationService->getStatistics($dateFrom, $dateTo);

        return response()->json($stats);
    }

    /**
     * Show notification log detail
     */
    public function show($id)
    {
        $log = NotificationLog::with([
            'notificationType',
            'emailTemplate',
            'user',
        ])->findOrFail($id);

        return view('admin.notifications.show', compact('log'));
    }
}
