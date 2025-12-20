<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class OrderData
{
    public function __construct(
        public readonly int $userId,
        public readonly ?string $customerEmail,
        public readonly ?string $customerName,
        public readonly ?string $customerPhone,
        public readonly string $status,
        public readonly ?string $address,
        public readonly ?string $note,
        public readonly array $products,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            userId: (int) $request->input('user_id'),
            customerEmail: $request->input('customer_email'),
            customerName: $request->input('customer_name'),
            customerPhone: $request->input('customer_phone'),
            status: $request->input('status', 'pending'),
            address: $request->input('address'),
            note: $request->input('note'),
            products: $request->input('products', []),
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'customer_email' => $this->customerEmail,
            'customer_name' => $this->customerName,
            'customer_phone' => $this->customerPhone,
            'status' => $this->status,
            'address' => $this->address,
            'note' => $this->note,
        ];
    }
}
