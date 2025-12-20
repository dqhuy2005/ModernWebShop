<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class ProductData
{
    public function __construct(
        public string $name,
        public int $categoryId,
        public int $price,
        public ?string $slug = null,
        public ?string $description = null,
        public ?array $specifications = null,
        public bool $status = true,
        public bool $isHot = false,
        public ?string $language = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            categoryId: $request->input('category_id'),
            price: $request->input('price'),
            slug: $request->input('slug'),
            description: $request->input('description'),
            specifications: $request->input('specifications'),
            status: $request->boolean('status', true),
            isHot: $request->boolean('is_hot', false),
            language: $request->input('language'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'category_id' => $this->categoryId,
            'price' => $this->price,
            'slug' => $this->slug,
            'description' => $this->description,
            'specifications' => $this->specifications,
            'status' => $this->status,
            'is_hot' => $this->isHot,
            'language' => $this->language,
        ], fn($value) => !is_null($value));
    }
}
