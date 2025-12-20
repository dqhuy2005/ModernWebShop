<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'customer_email' => 'nullable|email|max:255',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'status' => 'nullable|in:pending,confirmed,processing,shipping,shipped,completed,delivered,cancelled,refunded',
            'address' => 'nullable|string|max:500',
            'note' => 'nullable|string|max:1000',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1|max:9999',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Please select a customer',
            'user_id.exists' => 'Selected customer does not exist',
            'customer_email.email' => 'Invalid email format',
            'customer_email.max' => 'Email cannot exceed 255 characters',
            'customer_name.max' => 'Name cannot exceed 255 characters',
            'customer_phone.max' => 'Phone cannot exceed 20 characters',
            'status.in' => 'Invalid order status',
            'address.max' => 'Address cannot exceed 500 characters',
            'note.max' => 'Note cannot exceed 1000 characters',
            'products.required' => 'Please add at least one product',
            'products.array' => 'Products must be an array',
            'products.min' => 'Please add at least one product',
            'products.*.product_id.required' => 'Product ID is required',
            'products.*.product_id.exists' => 'Selected product does not exist',
            'products.*.quantity.required' => 'Product quantity is required',
            'products.*.quantity.integer' => 'Quantity must be a number',
            'products.*.quantity.min' => 'Quantity must be at least 1',
            'products.*.quantity.max' => 'Quantity cannot exceed 9999',
        ];
    }
}
