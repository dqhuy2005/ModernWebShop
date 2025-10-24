<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repository\OrderRepository;
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

        $query = $this->orderRepository->with(['orderDetails.product'])
            ->scopeQuery(function($q) use ($userId, $search, $status) {
                $q = $q->where('user_id', $userId);

                if ($search) {
                    $q = $q->where(function($query) use ($search) {
                        $query->where('id', 'like', '%' . $search . '%')
                              ->orWhereHas('orderDetails', function($q) use ($search) {
                                  $q->where('product_name', 'like', '%' . $search . '%');
                              });
                    });
                }

                if ($status) {
                    $q = $q->where('status', $status);
                }

                return $q->orderBy('created_at', 'desc');
            });

        $orders = $query->paginate(10);

        return view('user.purchase', compact('orders', 'search', 'status'));
    }

    public function show($orderId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $order = $this->orderRepository->with(['orderDetails.product'])
            ->find($orderId);

        if (!$order || $order->user_id !== Auth::id()) {
            return redirect()->route('purchase.index')->with('error', 'Không tìm thấy đơn hàng');
        }

        return view('user.purchase-detail', compact('order'));
    }

    /**
     * Cancel order
     */
    public function cancel($orderId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }

        $order = $this->orderRepository->find($orderId);

        if (!$order || $order->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng'
            ], 404);
        }

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy đơn hàng ở trạng thái này'
            ], 400);
        }

        try {
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
