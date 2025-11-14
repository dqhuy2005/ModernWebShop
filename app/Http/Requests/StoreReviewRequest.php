<?php

namespace App\Http\Requests;

use App\Models\ProductReview;
use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:' . ProductReview::MIN_RATING . '|max:' . ProductReview::MAX_RATING,
            'title' => 'nullable|string|max:200',
            'comment' => 'required|string|min:10|max:2000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'videos' => 'nullable|array|max:2',
            'videos.*' => 'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/webm|max:10240',
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
            'product_id.required' => 'Vui lòng chọn sản phẩm',
            'product_id.exists' => 'Sản phẩm không tồn tại',
            'order_id.required' => 'Đơn hàng không hợp lệ',
            'order_id.exists' => 'Đơn hàng không tồn tại',
            'rating.required' => 'Vui lòng chọn số sao đánh giá',
            'rating.integer' => 'Đánh giá phải là số nguyên',
            'rating.min' => 'Đánh giá tối thiểu là ' . ProductReview::MIN_RATING . ' sao',
            'rating.max' => 'Đánh giá tối đa là ' . ProductReview::MAX_RATING . ' sao',
            'title.max' => 'Tiêu đề không được vượt quá 200 ký tự',
            'comment.required' => 'Vui lòng nhập nội dung đánh giá',
            'comment.min' => 'Nội dung đánh giá phải có ít nhất 10 ký tự',
            'comment.max' => 'Nội dung đánh giá không được vượt quá 2000 ký tự',
            'images.max' => 'Bạn chỉ có thể tải lên tối đa 5 hình ảnh',
            'images.*.image' => 'File phải là hình ảnh',
            'images.*.mimes' => 'Hình ảnh phải có định dạng: jpeg, jpg, png, gif, webp',
            'images.*.max' => 'Kích thước hình ảnh không được vượt quá 2MB',
            'videos.max' => 'Bạn chỉ có thể tải lên tối đa 2 video',
            'videos.*.mimetypes' => 'Video phải có định dạng: mp4, mov, avi, webm',
            'videos.*.max' => 'Kích thước video không được vượt quá 10MB',
        ];
    }
}
