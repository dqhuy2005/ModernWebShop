<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Services\User\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function index()
    {
        if (!Auth::check())
            return redirect()->route('login');

        $cartItems = $this->cartService->getCartItems(Auth::id());

        return view('user.cart', compact('cartItems'));
    }

    public function add(AddToCartRequest $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to add items to cart'
            ], 401);
        }

        try {
            $result = $this->cartService->addToCart(
                Auth::id(),
                $request->product_id,
                $request->quantity ?? 1
            );

            Session::put('cart_count', $result['cart_count']);

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
                'cart_count' => $result['cart_count']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateCartRequest $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login'
            ], 401);
        }

        try {
            $this->cartService->updateQuantity(
                Auth::id(),
                $request->cart_id,
                $request->quantity
            );

            $total = $this->cartService->calculateTotal(Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật số lượng!',
                'total' => number_format($total)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function remove(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|integer|min:1'
        ]);

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login'
            ], 401);
        }

        try {
            $this->cartService->removeItem(Auth::id(), $request->cart_id);

            $cartCount = $this->cartService->getCartCount(Auth::id());
            $total = $this->cartService->calculateTotal(Auth::id());

            Session::put('cart_count', $cartCount);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
                'cart_count' => $cartCount,
                'total' => number_format($total)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item'
            ], 404);
        }
    }

    public function clear()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->cartService->clearCart(Auth::id());
        Session::put('cart_count', 0);

        return redirect()->route('cart.index')->with('success', 'Đã xóa tất cả sản phẩm trong giỏ hàng!');
    }
}
