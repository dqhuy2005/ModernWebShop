<?php

namespace App\Http\Controllers\User;

use App\DTOs\CheckoutData;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Services\User\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function __construct(
        private CheckoutService $checkoutService
    ) {
    }

    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục thanh toán');
        }

        try {
            $selectedIdsJson = $request->input('selected_items');
            $selectedIds = null;

            if ($selectedIdsJson) {
                $selectedIds = is_string($selectedIdsJson)
                    ? json_decode($selectedIdsJson, true)
                    : $selectedIdsJson;
            }

            $checkoutData = $this->checkoutService->getCheckoutItems(Auth::id(), $selectedIds);
            $user = Auth::user();

            return view('user.checkout', array_merge($checkoutData, ['user' => $user]));

        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }
    }

    public function process(CheckoutRequest $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login'
            ], 401);
        }

        try {
            $checkoutData = CheckoutData::fromRequest($request);
            $result = $this->checkoutService->processCheckout(Auth::id(), $checkoutData);

            Session::put('cart_count', $result['cart_count']);

            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công!',
                'order_id' => $result['order_id'],
                'cart_count' => $result['cart_count'],
                'redirect_url' => route('checkout.success', $result['order_id'])
            ]);

        } catch (\Exception $e) {
            Log::error('Checkout Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại!'
            ], 500);
        }
    }

    public function success($orderId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $order = $this->checkoutService->getOrder($orderId);

        if (!$order || $order->user_id !== Auth::id()) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng');
        }

        return view('user.checkout-success', compact('order'));
    }
}
