<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repository\impl\OrderRepository;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem đơn hàng');
        }

        $userId = Auth::id();
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Order::select('id', 'user_id', 'customer_name', 'customer_email', 'customer_phone', 'total_amount', 'total_items', 'status', 'address', 'note', 'created_at', 'updated_at')
            ->with([
                'orderDetails' => function ($q) {
                    $q->select('id', 'order_id', 'product_id', 'product_name', 'quantity', 'unit_price', 'total_price', 'product_specifications')
                        ->with([
                            'product:id,name,slug,price',
                            'product.images:id,product_id,path,sort_order'
                        ]);
                }
            ])
            ->where('user_id', $userId);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                    ->orWhereHas('orderDetails', function ($subQuery) use ($search) {
                        $subQuery->where('product_name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('user.partials.orders-list', compact('orders', 'status'))->render(),
                'status' => $status,
                'search' => $search
            ]);
        }

        return view('user.purchase', compact('orders', 'search', 'status'));
    }

    public function show($orderId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $order = Order::select('id', 'user_id', 'customer_name', 'customer_email', 'customer_phone', 'total_amount', 'total_items', 'status', 'address', 'note', 'created_at', 'updated_at')
            ->with([
                'orderDetails' => function ($q) {
                    $q->select('id', 'order_id', 'product_id', 'product_name', 'quantity', 'unit_price', 'total_price', 'product_specifications')
                        ->with([
                            'product:id,name,slug,price',
                            'product.images:id,product_id,path,sort_order'
                        ]);
                }
            ])
            ->find($orderId);

        if (!$order || $order->user_id !== Auth::id()) {
            return redirect()->route('purchase.index')->with('error', 'Không tìm thấy đơn hàng');
        }

        return view('user.purchase-detail', compact('order'));
    }

    public function cancel($orderId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }

        $order = Order::select('id', 'user_id', 'status')->find($orderId);

        if (!$order || $order->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng'
            ], 404);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể hủy đơn hàng ở trạng thái "Chờ xử lý"'
            ], 400);
        }

        try {
            $order->logActivity(
                'order_cancelled',
                'Đơn hàng đã bị hủy bởi khách hàng',
                $order->status,
                Order::STATUS_CANCELLED
            );

            $this->orderRepository->update([
                'status' => 'cancelled'
            ], $orderId);

            return response()->json([
                'success' => true,
                'message' => 'Đã hủy đơn hàng thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại!'
            ], 500);
        }
    }
}
