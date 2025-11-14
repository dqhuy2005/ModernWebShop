<?php

namespace App\Http\Requests;

use App\Models\ProductReview;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        $review = $this->route('review');
        return $this->user() && $review && $this->user()->id === $review->user_id;
    }

    public function rules(): array
    {
        return [
            'rating' => 'required|integer|min:' . ProductReview::MIN_RATING . '|max:' . ProductReview::MAX_RATING,
            'title' => 'nullable|string|max:200',
            'comment' => 'required|string|min:10|max:2000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'videos' => 'nullable|array|max:2',
            'videos.*' => 'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/webm|max:10240',
            'keep_existing_images' => 'nullable|boolean',
            'keep_existing_videos' => 'nullable|boolean',
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
