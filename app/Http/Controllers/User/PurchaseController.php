<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\User\PurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function __construct(
        private PurchaseService $purchaseService
    ) {}

    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem đơn hàng');
        }

        $userId = Auth::id();
        $search = $request->input('search');
        $status = $request->input('status');

        $orders = $this->purchaseService->getUserOrders($userId, $search, $status);

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

        $order = $this->purchaseService->getOrderDetail($orderId, Auth::id());

        if (!$order) {
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

        $result = $this->purchaseService->cancelOrder($orderId, Auth::id());

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message']
        ], $result['code']);
    }
}
