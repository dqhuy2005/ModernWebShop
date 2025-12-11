<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\Cart;
use App\Models\Product;
use App\Repository\impl\CartRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    protected $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function index()
    {
        // User is not login -> redirect to login page
        if (!Auth::check())
            return redirect()->route('login');

        if (Auth::check()) {
            $cartItems = $this->cartRepository->findByUser(Auth::id());
            $total = $this->cartRepository->calculateUserCartTotal(Auth::id());
        } else {
            $cartItems = collect(Session::get('cart', []));
            $total = $cartItems->sum(function ($item) {
                return $item['quantity'] * $item['price'];
            });
        }

        return view('user.cart', compact('cartItems', 'total'));
    }

    public function add(AddToCartRequest $request)
    {
        // Get product and ensure it's active
        $product = Product::select('id', 'name', 'slug', 'price', 'status')
            ->where('status', true) // Only allow active products
            ->findOrFail($request->product_id);

        $quantity = $request->quantity ?? 1;

        if (Auth::check()) {
            try {
                $cartItem = $this->cartRepository->findByUserAndProduct(Auth::id(), $product->id);

                if ($cartItem) {
                    if ($cartItem->trashed()) {
                        $cartItem->restore();
                        $this->cartRepository->updateQuantity($cartItem->id, $quantity);
                    } else {
                        $newQuantity = min($cartItem->quantity + $quantity, 999);
                        $this->cartRepository->updateQuantity($cartItem->id, $newQuantity);
                    }
                } else {
                    $this->cartRepository->create([
                        'user_id' => Auth::id(),
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $product->price,
                    ]);
                }

                $cartCount = $this->cartRepository->findByUser(Auth::id())->count();
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                $cartItem = $this->cartRepository->findByUserAndProduct(Auth::id(), $product->id);
                if ($cartItem) {
                    if ($cartItem->trashed()) {
                        $cartItem->restore();
                        $this->cartRepository->updateQuantity($cartItem->id, $quantity);
                    } else {
                        $newQuantity = min($cartItem->quantity + $quantity, 999);
                        $this->cartRepository->updateQuantity($cartItem->id, $newQuantity);
                    }
                }
                $cartCount = $this->cartRepository->findByUser(Auth::id())->count();
            }
        } else {
            $cart = Session::get('cart', []);

            if (isset($cart[$product->id])) {
                $cart[$product->id]['quantity'] = min($cart[$product->id]['quantity'] + $quantity, 999);
            } else {
                $cart[$product->id] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->price,
                    'quantity' => $quantity,
                ];
            }

            Session::put('cart', $cart);
            $cartCount = count($cart);
        }

        Session::put('cart_count', $cartCount);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
            'cart_count' => $cartCount
        ]);
    }

    public function update(UpdateCartRequest $request)
    {
        if (Auth::check()) {
            $cartItem = $this->cartRepository->find($request->cart_id);

            if (!$cartItem || $cartItem->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm trong giỏ hàng!'
                ], 404);
            }

            $this->cartRepository->updateQuantity($request->cart_id, $request->quantity);
            $total = $this->cartRepository->calculateUserCartTotal(Auth::id());
        } else {
            $cart = Session::get('cart', []);

            if (!isset($cart[$request->cart_id])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm trong giỏ hàng!'
                ], 404);
            }

            $cart[$request->cart_id]['quantity'] = $request->quantity;
            Session::put('cart', $cart);

            $total = collect($cart)->sum(function ($item) {
                return $item['quantity'] * $item['price'];
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật số lượng!',
            'total' => number_format($total)
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|integer|min:1'
        ]);

        if (Auth::check()) {
            $cartItem = $this->cartRepository->find($request->cart_id);

            if (!$cartItem || $cartItem->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm trong giỏ hàng!'
                ], 404);
            }

            $this->cartRepository->delete($request->cart_id);
            $cartCount = $this->cartRepository->findByUser(Auth::id())->count();
            $total = $this->cartRepository->calculateUserCartTotal(Auth::id());
        } else {
            $cart = Session::get('cart', []);

            if (!isset($cart[$request->cart_id])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm trong giỏ hàng!'
                ], 404);
            }

            unset($cart[$request->cart_id]);
            Session::put('cart', $cart);

            $cartCount = count($cart);
            $total = collect($cart)->sum(function ($item) {
                return $item['quantity'] * $item['price'];
            });
        }

        Session::put('cart_count', $cartCount);

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
            'cart_count' => $cartCount,
            'total' => number_format($total)
        ]);
    }

    public function clear()
    {
        if (Auth::check()) {
            $this->cartRepository->clearUserCart(Auth::id());
        } else {
            Session::forget('cart');
        }

        Session::put('cart_count', 0);

        return redirect()->route('cart.index')->with('success', 'Đã xóa tất cả sản phẩm trong giỏ hàng!');
    }
}
