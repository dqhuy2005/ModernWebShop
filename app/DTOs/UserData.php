<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class UserData
{
    public function __construct(
        public string $fullname,
        public string $email,
        public ?string $phone = null,
        public ?string $password = null,
        public ?string $image = null,
        public ?string $address = null,
        public ?string $birthday = null,
        public ?int $roleId = null,
        public bool $status = true,
        public ?string $language = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            fullname: $request->input('fullname'),
            email: $request->input('email'),
            phone: $request->input('phone'),
            password: $request->input('password'),
            image: $request->input('image'),
            address: $request->input('address'),
            birthday: $request->input('birthday'),
            roleId: $request->input('role_id'),
            status: $request->boolean('status', true),
            language: $request->input('language'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'fullname' => $this->fullname,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $this->password,
            'image' => $this->image,
            'address' => $this->address,
            'birthday' => $this->birthday,
            'role_id' => $this->roleId,
            'status' => $this->status,
            'language' => $this->language,
        ], fn($value) => !is_null($value));
    }
}
