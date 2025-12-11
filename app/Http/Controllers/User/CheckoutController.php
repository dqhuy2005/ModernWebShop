<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Repository\impl\CartRepository;
use App\Repository\impl\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    protected $cartRepository;
    protected $orderRepository;

    public function __construct(
        CartRepository $cartRepository,
        OrderRepository $orderRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->orderRepository = $orderRepository;
    }

    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục thanh toán');
        }

        $allCartItems = $this->cartRepository->findByUser(Auth::id());

        if ($allCartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống');
        }

        $selectedIdsJson = $request->input('selected_items');
        $selectedIds = null;

        if ($selectedIdsJson) {
            if (is_string($selectedIdsJson)) {
                $selectedIds = json_decode($selectedIdsJson, true);
            } else {
                $selectedIds = $selectedIdsJson;
            }
        }

        if ($selectedIds && is_array($selectedIds) && count($selectedIds) > 0) {
            $cartItems = $allCartItems->whereIn('id', $selectedIds);

            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Vui lòng chọn sản phẩm để thanh toán');
            }
        } else {
            $cartItems = $allCartItems;
        }

        $total = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $shippingFee = 0; // Free shipping
        $grandTotal = $total; // Grand total equals items total

        $user = Auth::user();

        return view('user.checkout', compact('cartItems', 'total', 'shippingFee', 'grandTotal', 'user'));
    }

    public function process(CheckoutRequest $request)
    {
        $userId = Auth::id();
        $allCartItems = $this->cartRepository->findByUser($userId);

        if ($allCartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng của bạn đang trống'
            ], 400);
        }

        $selectedIds = $request->input('selected_items');

        if ($selectedIds && is_array($selectedIds) && count($selectedIds) > 0) {
            $cartItems = $allCartItems->whereIn('id', $selectedIds);
        } else {
            $cartItems = $allCartItems;
        }

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn sản phẩm để thanh toán'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $totalItems = 0;

            foreach ($cartItems as $cartItem) {
                if (!$cartItem->product || !$cartItem->product->status) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Một số sản phẩm trong giỏ hàng không còn khả dụng'
                    ], 400);
                }

                $currentPrice = $cartItem->product->price;
                if (abs($cartItem->price - $currentPrice) > 0.01) {
                    $cartItem->price = $currentPrice;
                    $cartItem->save();
                }

                $totalAmount += $currentPrice * $cartItem->quantity;
                $totalItems += $cartItem->quantity;
            }

            $user = Auth::user();

            $order = $this->orderRepository->create([
                'user_id' => $userId,
                'customer_name' => $request->name,
                'customer_phone' => $request->phone,
                'customer_email' => $user->email,
                'total_amount' => $totalAmount,
                'total_items' => $totalItems,
                'status' => Order::STATUS_PENDING,
                'address' => $request->address,
                'note' => $request->note,
            ]);

            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;

                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->price,
                    'total_price' => $cartItem->price * $cartItem->quantity,
                    'product_specifications' => [
                        'category' => $product->category ? $product->category->name : null,
                        'image' => $product->main_image ?? 'default.png',
                        'description' => $product->description,
                    ]
                ]);
            }

            foreach ($cartItems as $cartItem) {
                $cartItem->delete();
            }

            $remainingCount = $this->cartRepository->findByUser($userId)->count();
            Session::put('cart_count', $remainingCount);

            if (method_exists($order, 'logActivity')) {
                $order->logActivity(
                    'order_created',
                    'Đơn hàng được tạo thành công',
                    null,
                    Order::STATUS_PENDING
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công!',
                'order_id' => $order->id,
                'cart_count' => $remainingCount,
                'redirect_url' => route('checkout.success', $order->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

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

        $order = $this->orderRepository
            ->select('id', 'user_id', 'customer_name', 'customer_email', 'customer_phone', 'total_amount', 'total_items', 'status', 'address', 'note', 'created_at')
            ->with([
                'orderDetails' => function ($q) {
                    $q->select('id', 'order_id', 'product_id', 'product_name', 'quantity', 'unit_price', 'total_price')
                        ->with([
                            'product:id,name,slug,price',
                            'product.images:id,product_id,path,sort_order'
                        ]);
                }
            ])
            ->find($orderId);

        if (!$order || $order->user_id !== Auth::id()) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng');
        }

        return view('user.checkout-success', compact('order'));
    }
}
