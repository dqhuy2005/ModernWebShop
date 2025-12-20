<?php

namespace App\Http\Controllers\CMS;

use App\DTOs\OrderData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Product;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\CMS\OrderService;
use App\Services\impl\ExcelService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private OrderRepositoryInterface $orderRepository
    ) {
    }

    public function show($id)
    {
        try {
            $order = $this->orderRepository->find($id);

            if (!$order) {
                return back()->with('error', 'Order not found');
            }

            return view('admin.orders.show', compact('order'));

        } catch (\Exception $e) {
            return back()->with('error', 'Order not found: ' . $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $filters = [
            'status' => $request->status,
            'search' => $request->search,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'price_min' => $request->price_min,
            'price_max' => $request->price_max,
            'sort_by' => $request->sort_by ?? 'created_at',
            'sort_order' => $request->sort_order ?? 'desc',
        ];

        $orders = $this->orderRepository->paginate($filters, 15);

        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::select('id', 'name', 'price', 'category_id', 'status')
            ->where('status', true)
            ->with('category:id,name')
            ->get();

        return view('admin.orders.create', compact('products'));
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            $orderData = OrderData::fromRequest($request);
            $this->orderService->create($orderData);

            return redirect()
                ->route('admin.orders.index')
                ->with('success', 'Order created successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $order = $this->orderRepository->find($id);

            if (!$order) {
                return back()->with('error', 'Order not found');
            }

            $products = Product::select('id', 'name', 'price', 'category_id', 'status')
                ->where('status', true)
                ->with('category:id,name')
                ->get();

            return view('admin.orders.edit', compact('order', 'products'));

        } catch (\Exception $e) {
            return back()->with('error', 'Order not found: ' . $e->getMessage());
        }
    }

    public function update(UpdateOrderRequest $request, $id)
    {
        try {
            $orderData = OrderData::fromRequest($request);
            $this->orderService->update($id, $orderData);

            return redirect()
                ->route('admin.orders.index')
                ->with('success', 'Order updated successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update order: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $this->orderService->cancel($id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order cancelled successfully!'
                ]);
            }

            return redirect()
                ->route('admin.orders.index')
                ->with('success', 'Order cancelled successfully!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to cancel order: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to cancel order: ' . $e->getMessage());
        }
    }

    public function restore(Request $request, $id)
    {
        try {
            $this->orderService->restore($id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order restored successfully!'
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

    public function export(ExcelService $excelService)
    {
        $excel = $excelService->exportOrders();
        $filename = 'orders_export_' . date('Y-m-d_His') . '.xls';

        return response($excel, 200, $excelService->getDownloadHeaders($filename));
    }

    public function searchCustomers(Request $request)
    {
        $search = $request->get('q', '');
        $limit = $request->get('limit', 15);

        if ($request->filled('id') && is_numeric($request->get('id'))) {
            $user = $this->orderRepository->getCustomer((int) $request->get('id'));

            if (!$user) {
                return response()->json(['users' => []]);
            }

            return response()->json([
                'users' => [
                    [
                        'id' => $user->id,
                        'fullname' => $user->fullname,
                        'email' => $user->email,
                        'phone' => $user->phone ?? 'N/A',
                        'display' => $user->fullname . ' (' . $user->email . ')',
                        'address' => $user->address ?? '',
                        'highlighted_name' => $this->highlightTerm($user->fullname, ''),
                        'highlighted_email' => $this->highlightTerm($user->email, ''),
                        'can_receive_email' => !empty($user->email),
                    ]
                ]
            ]);
        }

        if (strlen($search) < 2) {
            return response()->json(['users' => []]);
        }

        $customers = $this->orderRepository->searchCustomers($search, $limit)
            ->map(function ($user) use ($search) {
                return [
                    'id' => $user->id,
                    'fullname' => $user->fullname,
                    'email' => $user->email,
                    'phone' => $user->phone ?? 'N/A',
                    'address' => $user->address ?? '',
                    'display' => $user->fullname . ' (' . $user->email . ')',
                    'highlighted_name' => $this->highlightTerm($user->fullname, $search),
                    'highlighted_email' => $this->highlightTerm($user->email, $search),
                ];
            });

        return response()->json(['users' => $customers]);
    }

    private function highlightTerm($text, $term)
    {
        if (empty($term)) {
            return $text;
        }

        return preg_replace(
            '/(' . preg_quote($term, '/') . ')/i',
            '<mark>$1</mark>',
            $text
        );
    }
}
