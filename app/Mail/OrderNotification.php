<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $emailData;
    public $emailSubject;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, array $emailData = [], $subject = null)
    {
        $this->order = $order;
        $this->emailData = $emailData;
        $this->emailSubject = $subject ?? $this->getDefaultSubject();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-notification',
            with: $this->prepareEmailData(),
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Prepare data for email template
     */
    private function prepareEmailData()
    {
        $order = $this->order->load(['user', 'orderDetails']);

        return array_merge([
            // Company info
            'companyName' => config('app.name', 'Modern Web Shop'),
            'companyAddress' => config('mail.from.address', ''),
            'companyPhone' => '1900-xxxx',
            'companyEmail' => config('mail.from.address', 'support@example.com'),
            'companyLogo' => asset('images/logo.png'),

            // Email metadata
            'subject' => $this->emailSubject,
            'emailTitle' => $this->getEmailTitle(),
            'emailMessage' => $this->getMessage(),

            // Order info
            'orderId' => $order->id,
            'orderStatus' => $order->status,
            'orderStatusLabel' => $order->status_label,
            'orderDate' => $order->created_at->format('d/m/Y H:i'),

            // Customer info
            'customerName' => $order->user->fullname ?? 'Quý khách',
            'recipientName' => $order->user->fullname ?? 'Quý khách',
            'recipientPhone' => $order->user->phone ?? 'N/A',
            'shippingAddress' => $order->address ?? 'Chưa cung cấp',
            'orderNote' => $order->note,

            // Order items
            'orderItems' => $order->orderDetails->map(function ($detail) {
                return [
                    'product_id' => $detail->product_id,
                    'product_name' => $detail->product_name,
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->unit_price,
                    'total_price' => $detail->total_price,
                ];
            })->toArray(),

            // Totals
            'subtotal' => $order->total_amount,
            'shippingFee' => 0,
            'discount' => 0,
            'totalAmount' => $order->total_amount,

            // Action links
            'trackingUrl' => $this->getTrackingUrl($order->id),

        ], $this->emailData);
    }

    /**
     * Get tracking URL safely
     */
    private function getTrackingUrl($orderId)
    {
        return route('purchase.show', $orderId);
    }

    /**
     * Get default subject based on order status
     */
    private function getDefaultSubject()
    {
        $statusSubjects = [
            'pending' => 'Đơn hàng #{orderId} đã được tạo thành công',
            'confirmed' => 'Đơn hàng #{orderId} đã được xác nhận',
            'processing' => 'Đơn hàng #{orderId} đang được xử lý',
            'shipping' => 'Đơn hàng #{orderId} đang được giao',
            'completed' => 'Đơn hàng #{orderId} đã hoàn thành',
            'cancelled' => 'Đơn hàng #{orderId} đã bị hủy',
        ];

        $template = $statusSubjects[$this->order->status] ?? 'Thông báo về đơn hàng #{orderId}';
        return str_replace('{orderId}', str_pad($this->order->id, 6, '0', STR_PAD_LEFT), $template);
    }

    /**
     * Get email title based on order status
     */
    private function getEmailTitle()
    {
        $statusTitles = [
            'pending' => '🎉 Đơn hàng đã được tạo',
            'confirmed' => '✅ Đơn hàng đã xác nhận',
            'processing' => '⚙️ Đơn hàng đang xử lý',
            'shipping' => '🚚 Đơn hàng đang giao',
            'completed' => '🎁 Đơn hàng hoàn thành',
            'cancelled' => '❌ Đơn hàng đã hủy',
        ];

        return $statusTitles[$this->order->status] ?? 'Thông báo đơn hàng';
    }

    /**
     * Get message based on order status
     */
    private function getMessage()
    {
        $statusMessages = [
            'pending' => 'Cảm ơn bạn đã đặt hàng! Đơn hàng của bạn đang chờ xác nhận. Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất.',
            'confirmed' => 'Đơn hàng của bạn đã được xác nhận thành công. Chúng tôi đang chuẩn bị hàng cho bạn.',
            'processing' => 'Đơn hàng của bạn đang được xử lý và đóng gói. Sản phẩm sẽ sớm được giao đến bạn.',
            'shipping' => 'Đơn hàng của bạn đã được giao cho đơn vị vận chuyển. Vui lòng chú ý điện thoại để nhận hàng.',
            'completed' => 'Đơn hàng của bạn đã được giao thành công! Cảm ơn bạn đã mua sắm tại cửa hàng chúng tôi.',
            'cancelled' => 'Đơn hàng của bạn đã bị hủy. Nếu bạn có thắc mắc, vui lòng liên hệ với chúng tôi.',
        ];

        return $statusMessages[$this->order->status] ?? 'Chúng tôi xin thông báo về trạng thái đơn hàng của bạn.';
    }
}
