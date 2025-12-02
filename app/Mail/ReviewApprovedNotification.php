<?php

namespace App\Mail;

use App\Models\ProductReview;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * ReviewApprovedNotification
 * 
 * Email notification sent when a product review is approved by admin
 */
class ReviewApprovedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public ProductReview $review;

    /**
     * Create a new message instance.
     */
    public function __construct(ProductReview $review)
    {
        $this->review = $review;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Đánh giá của bạn đã được duyệt - ' . $this->review->product->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.review-approved',
            with: [
                'review' => $this->review,
                'product' => $this->review->product,
                'user' => $this->review->user,
                'orderCode' => $this->review->order->code ?? 'N/A',
            ],
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
}
