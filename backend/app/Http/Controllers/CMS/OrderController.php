<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display the specified order (Read-Only).
     * 
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            // Eager load relationships để tránh N+1 query problem
            $order = Order::with([
                'user:id,name,email',
                'orderDetails.product:id,name,image'
            ])->findOrFail($id);

            // Kiểm tra tính toàn vẹn của đơn hàng
            $isIntegrityValid = $order->verifyIntegrity();
            
            // Tính toán lại để hiển thị
            $calculatedTotal = $order->calculateTotalAmount();
            $calculatedItems = $order->calculateTotalItems();

            // Cảnh báo nếu dữ liệu không khớp
            $warnings = [];
            
            if (!$isIntegrityValid) {
                $warnings[] = [
                    'type' => 'danger',
                    'message' => 'CẢNH BÁO: Tổng tiền không khớp! Tổng đã lưu: ' . 
                                number_format($order->total_amount, 0, ',', '.') . ' ₫, ' .
                                'Tổng tính toán: ' . number_format($calculatedTotal, 0, ',', '.') . ' ₫'
                ];
            }

            if ($order->total_items !== $calculatedItems) {
                $warnings[] = [
                    'type' => 'warning',
                    'message' => 'Số lượng sản phẩm không khớp! Đã lưu: ' . $order->total_items . 
                                ', Tính toán: ' . $calculatedItems
                ];
            }

            if ($order->orderDetails->isEmpty()) {
                $warnings[] = [
                    'type' => 'info',
                    'message' => 'Đơn hàng này không có sản phẩm nào.'
                ];
            }

            return view('admin.orders.show', compact('order', 'warnings', 'isIntegrityValid'));
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()
                ->route('admin.orders.index')
                ->with('error', 'Không tìm thấy đơn hàng #' . $id);
                
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.orders.index')
                ->with('error', 'Lỗi khi tải đơn hàng: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of orders.
     * 
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Order::with(['user:id,name,email']);

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Search by order ID or user name
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $orders = $query->paginate($perPage)->withQueryString();

            return view('admin.orders.index', compact('orders'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi tải danh sách đơn hàng: ' . $e->getMessage());
        }
    }
}
