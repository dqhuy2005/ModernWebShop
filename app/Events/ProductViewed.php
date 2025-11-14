<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductViewed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Product $product;
    public string $ipAddress;
    public ?string $userAgent;
    public ?int $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(
        Product $product,
        string $ipAddress,
        ?string $userAgent = null,
        ?int $userId = null
    ) {
        $this->product = $product;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->userId = $userId;
    }
}
