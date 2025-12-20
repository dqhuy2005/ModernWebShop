<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class CategoryData
{
    public function __construct(
        public string $name,
        public ?string $slug = null,
        public ?string $image = null,
        public ?int $parentId = null,
        public ?string $language = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            slug: $request->input('slug'),
            image: $request->input('image'),
            parentId: $request->input('parent_id'),
            language: $request->input('language'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'slug' => $this->slug,
            'image' => $this->image,
            'parent_id' => $this->parentId,
            'language' => $this->language,
        ], fn($value) => !is_null($value));
    }
}
