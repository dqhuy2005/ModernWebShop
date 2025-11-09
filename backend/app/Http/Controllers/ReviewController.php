<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ReviewController
 *
 * Handles product review operations for customers
 * - Display review form
 * - Submit new reviews
 * - Update existing reviews
 * - View reviews on product pages
 */
class ReviewController extends Controller
{
    protected ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * Display reviews for a product (public)
     *
     * GET /products/{product}/reviews
     */
    public function index(Product $product)
    {
        $reviews = $product->approvedReviews()
            ->with('user')
            ->latest()
            ->paginate(10);

        $stats = $this->reviewService->getProductReviewStats($product);

        return response()->json([
            'success' => true,
            'reviews' => $reviews,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the review form for a specific order and product
     *
     * GET /orders/{order}/products/{product}/review
     */
    public function create(Order $order, Product $product)
    {
        $user = Auth::user();

        // Check if user can review
        $eligibility = $this->reviewService->canUserReviewProduct($user, $product, $order);

        if (!$eligibility['can_review']) {
            if (isset($eligibility['existing_review'])) {
                // Redirect to edit if already reviewed
                return redirect()->route('reviews.edit', [
                    'order' => $order->id,
                    'product' => $product->id,
                ])
                ->with('info', 'Bạn đã đánh giá sản phẩm này. Bạn có thể chỉnh sửa đánh giá.');
            }

            return back()->with('error', $eligibility['reason']);
        }

        return view('reviews.create', [
            'order' => $order,
            'product' => $product,
            'orderDetail' => $eligibility['order_detail'],
        ]);
    }

    /**
     * Store a newly created review
     *
     * POST /reviews
     */
    public function store(StoreReviewRequest $request)
    {
        try {
            $user = Auth::user();
            $product = Product::findOrFail($request->product_id);
            $order = Order::findOrFail($request->order_id);

            // Verify eligibility
            $eligibility = $this->reviewService->canUserReviewProduct($user, $product, $order);

            if (!$eligibility['can_review']) {
                return back()
                    ->withInput()
                    ->with('error', $eligibility['reason']);
            }

            // Prepare review data
            $reviewData = [
                'product_id' => $request->product_id,
                'user_id' => $user->id,
                'order_id' => $request->order_id,
                'order_detail_id' => $eligibility['order_detail']->id ?? null,
                'rating' => $request->rating,
                'title' => $request->title,
                'comment' => $request->comment,
            ];

            // Upload media and create review
            $review = $this->reviewService->createReview(
                $reviewData,
                $request->file('images'),
                $request->file('videos')
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cảm ơn bạn đã đánh giá sản phẩm!',
                    'review' => $review,
                ], 201);
            }

            return redirect()
                ->route('products.show', $product->slug)
                ->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm! Đánh giá của bạn đã được ghi nhận.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi gửi đánh giá: ' . $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi gửi đánh giá. Vui lòng thử lại.');
        }
    }

    /**
     * Show the form for editing the review
     *
     * GET /orders/{order}/products/{product}/review/edit
     */
    public function edit(Order $order, Product $product)
    {
        $user = Auth::user();

        $review = ProductReview::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('order_id', $order->id)
            ->firstOrFail();

        return view('reviews.edit', [
            'review' => $review,
            'order' => $order,
            'product' => $product,
        ]);
    }

    /**
     * Update the specified review
     *
     * PUT /reviews/{review}
     */
    public function update(UpdateReviewRequest $request, ProductReview $review)
    {
        try {
            // Prepare update data
            $updateData = [
                'rating' => $request->rating,
                'title' => $request->title,
                'comment' => $request->comment,
            ];

            // Handle media updates
            $newImages = $request->hasFile('images') && !$request->keep_existing_images
                ? $request->file('images')
                : null;

            $newVideos = $request->hasFile('videos') && !$request->keep_existing_videos
                ? $request->file('videos')
                : null;

            $updatedReview = $this->reviewService->updateReview(
                $review,
                $updateData,
                $newImages,
                $newVideos
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đánh giá đã được cập nhật!',
                    'review' => $updatedReview,
                ]);
            }

            return redirect()
                ->route('products.show', $review->product->slug)
                ->with('success', 'Đánh giá của bạn đã được cập nhật thành công!');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật đánh giá.');
        }
    }

    /**
     * Delete the specified review
     *
     * DELETE /reviews/{review}
     */
    public function destroy(ProductReview $review)
    {
        try {
            // Check authorization
            if (Auth::id() !== $review->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa đánh giá này',
                ], 403);
            }

            $this->reviewService->deleteReview($review);

            return response()->json([
                'success' => true,
                'message' => 'Đánh giá đã được xóa',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa đánh giá',
            ], 500);
        }
    }
}
