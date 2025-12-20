<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class CheckoutData
{
    public function __construct(
        public readonly string $name,
        public readonly string $phone,
        public readonly string $address,
        public readonly ?string $note,
        public readonly ?array $selectedItems,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            phone: $request->input('phone'),
            address: $request->input('address'),
            note: $request->input('note'),
            selectedItems: $request->input('selected_items'),
        );
    }

    public function toArray(): array
    {
        return [
            'customer_name' => $this->name,
            'customer_phone' => $this->phone,
            'address' => $this->address,
            'note' => $this->note,
        ];
    }
}
