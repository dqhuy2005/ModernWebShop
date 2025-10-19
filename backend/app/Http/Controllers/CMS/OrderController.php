<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function show($id)
    {
        try {
            $order = Order::with([
                'user:id,fullname,email,phone',
                'orderDetails.product:id,name,image'
            ])->findOrFail($id);

            $calculatedTotal = $order->calculateTotalAmount();
            $calculatedItems = $order->calculateTotalItems();

            $warnings = [];

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

            return view('admin.orders.show', compact('order', 'warnings'));

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

    public function index(Request $request)
    {
        try {
            $query = Order::with(['user:id,fullname,email,phone']);

            if ($request->status === 'deleted') {
                $query->onlyTrashed();
            }

            if ($request->filled('status') && $request->status !== 'deleted') {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('fullname', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }

            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('per_page', 15);
            $orders = $query->paginate($perPage)->withQueryString();

            // Calculate all statistics
            $totalOrders = Order::count();
            $pendingOrders = Order::where('status', 'pending')->count();
            $confirmedOrders = Order::where('status', 'confirmed')->count();
            $processingOrders = Order::where('status', 'processing')->count();
            $shippingOrders = Order::where('status', 'shipping')->count();
            $completedOrders = Order::where('status', 'completed')->count();
            $cancelledOrders = Order::where('status', 'cancelled')->count();

            // Check if AJAX request
            if ($request->ajax()) {
                return view('admin.orders.table', compact(
                    'orders',
                    'totalOrders',
                    'pendingOrders',
                    'confirmedOrders',
                    'processingOrders',
                    'shippingOrders',
                    'completedOrders',
                    'cancelledOrders'
                ));
            }

            return view('admin.orders.index', compact(
                'orders',
                'totalOrders',
                'pendingOrders',
                'confirmedOrders',
                'processingOrders',
                'shippingOrders',
                'completedOrders',
                'cancelledOrders'
            ));

        } catch (\Exception $e) {
            return back()->with('error', 'Error loading orders: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $users = User::where('status', true)->orderBy('fullname')->whereDoesntHave('role', fn($query) => $query->where('name', 'admin'))->get();
            $products = Product::where('status', true)->with('category')->get();

            return view('admin.orders.create', compact('users', 'products'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load create form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,confirmed,processing,shipping,completed,cancelled',
            'address' => 'nullable|string|max:500',
            'note' => 'nullable|string|max:1000',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1|max:9999',
        ], [
            'user_id.required' => 'Please select a customer',
            'products.required' => 'Please add at least one product',
            'products.*.product_id.required' => 'Product is required',
            'products.*.quantity.required' => 'Quantity is required',
            'products.*.quantity.min' => 'Quantity must be at least 1',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $totalItems = 0;
            $orderDetailsData = [];

            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];
                $unitPrice = $product->price ?? 0;
                $subtotal = $unitPrice * $quantity;

                $orderDetailsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $subtotal,
                    'product_specifications' => $product->specifications,
                ];

                $totalAmount += $subtotal;
                $totalItems += $quantity;
            }

            $order = Order::create([
                'user_id' => $request->user_id,
                'total_amount' => $totalAmount,
                'total_items' => $totalItems,
                'status' => $request->status ?? 'pending',
                'address' => $request->address,
                'note' => $request->note,
            ]);

            foreach ($orderDetailsData as $detail) {
                OrderDetail::create(array_merge($detail, ['order_id' => $order->id]));
            }

            DB::commit();

            return redirect()
                ->route('admin.orders.index')
                ->with('success', 'Order created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $order = Order::with(['orderDetails.product', 'user'])->findOrFail($id);
            $users = User::where('status', true)->orderBy('fullname')->whereDoesntHave('role', fn($query) => $query->where('name', 'admin'))->get();
            $products = Product::where('status', true)->with('category')->get();

            return view('admin.orders.edit', compact('order', 'users', 'products'));
        } catch (\Exception $e) {
            return back()->with('error', 'Order not found: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,confirmed,processing,shipping,shipped,completed,cancelled,refunded',
            'address' => 'nullable|string|max:500',
            'note' => 'nullable|string|max:1000',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1|max:9999',
        ], [
            'user_id.required' => 'Please select a customer',
            'products.required' => 'Please add at least one product',
            'products.*.quantity.min' => 'Quantity must be at least 1',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id);

            $order->orderDetails()->delete();

            $totalAmount = 0;
            $totalItems = 0;

            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];
                $unitPrice = $product->price ?? 0;
                $subtotal = $unitPrice * $quantity;

                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $subtotal,
                    'product_specifications' => $product->specifications,
                ]);

                $totalAmount += $subtotal;
                $totalItems += $quantity;
            }

            $order->update([
                'user_id' => $request->user_id,
                'total_amount' => $totalAmount,
                'total_items' => $totalItems,
                'status' => $request->status,
                'address' => $request->address,
                'note' => $request->note,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.orders.index')
                ->with('success', 'Order updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update order: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete();

            // Calculate statistics for AJAX response
            $statistics = [
                'total' => Order::count(),
                'pending' => Order::where('status', 'pending')->count(),
                'confirmed' => Order::where('status', 'confirmed')->count(),
                'processing' => Order::where('status', 'processing')->count(),
                'shipping' => Order::where('status', 'shipping')->count(),
                'completed' => Order::where('status', 'completed')->count(),
                'cancelled' => Order::where('status', 'cancelled')->count(),
            ];

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order deleted successfully!',
                    'counts' => $statistics
                ]);
            }

            return redirect()
                ->route('admin.orders.index')
                ->with('success', 'Order deleted successfully!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete order: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to delete order: ' . $e->getMessage());
        }
    }

    public function restore(Request $request, $id)
    {
        try {
            $order = Order::withTrashed()->findOrFail($id);
            $order->restore();

            // Calculate statistics for AJAX response
            $statistics = [
                'total' => Order::count(),
                'pending' => Order::where('status', 'pending')->count(),
                'confirmed' => Order::where('status', 'confirmed')->count(),
                'processing' => Order::where('status', 'processing')->count(),
                'shipping' => Order::where('status', 'shipping')->count(),
                'completed' => Order::where('status', 'completed')->count(),
                'cancelled' => Order::where('status', 'cancelled')->count(),
            ];

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order restored successfully!',
                    'counts' => $statistics
                ]);
            }

            return back()->with('success', 'Order restored successfully!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to restore order: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to restore order: ' . $e->getMessage());
        }
    }
}
