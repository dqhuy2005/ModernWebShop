<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Services\impl\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    protected ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function index(Product $product)
    {
        $reviews = $product->approvedReviews()
            ->select('id', 'product_id', 'user_id', 'rating', 'title', 'comment', 'images', 'status', 'created_at', 'updated_at')
            ->with('user:id,fullname,email,image')
            ->latest()
            ->paginate(10);

        $stats = $this->reviewService->getProductReviewStats($product);

        return response()->json([
            'success' => true,
            'reviews' => $reviews,
            'stats' => $stats,
        ]);
    }

    public function create(Order $order, Product $product)
    {
        $user = Auth::user();

        $eligibility = $this->reviewService->canUserReviewProduct($user, $product, $order);

        if (!$eligibility['can_review']) {
            if (isset($eligibility['existing_review'])) {
                return redirect()->route('products.show', $product->slug)
                    ->with('info', 'Bạn đã hoàn thành đánh giá cho sản phẩm này. Cảm ơn bạn đã chia sẻ!');
            }

            return back()->with('error', $eligibility['reason']);
        }

        if (isset($eligibility['order_detail']) && $eligibility['order_detail']->hasBeenReviewed()) {
            return redirect()->route('products.show', $product->slug)
                ->with('info', 'Bạn đã hoàn thành đánh giá cho sản phẩm này. Cảm ơn bạn đã chia sẻ!');
        }

        return view('reviews.create', [
            'order' => $order,
            'product' => $product,
            'orderDetail' => $eligibility['order_detail'],
        ]);
    }

    public function store(StoreReviewRequest $request)
    {
        try {
            $user = Auth::user();
            $product = Product::findOrFail($request->product_id);
            $order = Order::findOrFail($request->order_id);

            $eligibility = $this->reviewService->canUserReviewProduct($user, $product, $order);

            if (!$eligibility['can_review']) {
                return back()
                    ->withInput()
                    ->with('error', $eligibility['reason']);
            }

            $reviewData = [
                'product_id' => $request->product_id,
                'user_id' => $user->id,
                'order_id' => $request->order_id,
                'order_detail_id' => $eligibility['order_detail']->id ?? null,
                'rating' => $request->rating,
                'title' => $request->title,
                'comment' => $request->comment,
            ];

            $review = $this->reviewService->createReview(
                $reviewData,
                $request->file('images'),
                null
            );

            if (isset($eligibility['order_detail'])) {
                $eligibility['order_detail']->update([
                    'reviewed_at' => now(),
                ]);
            }

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

    public function edit(Order $order, Product $product)
    {
        return redirect()->route('products.show', $product->slug)
            ->with('info', 'Đánh giá đã được ghi nhận và không thể chỉnh sửa. Cảm ơn bạn đã chia sẻ!');
    }

    public function update(UpdateReviewRequest $request, ProductReview $review)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Đánh giá đã được ghi nhận và không thể chỉnh sửa.',
            ], 403);
        }

        return redirect()->route('products.show', $review->product->slug)
            ->with('info', 'Đánh giá đã được ghi nhận và không thể chỉnh sửa.');
    }

    public function destroy(ProductReview $review)
    {
        try {
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
