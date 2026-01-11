<?php

namespace App\Services\User;

use App\DTOs\CheckoutData;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckoutService
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private OrderRepositoryInterface $orderRepository
    ) {
    }

    public function processCheckout(int $userId, CheckoutData $checkoutData): array
    {
        DB::beginTransaction();
        try {
            $allCartItems = $this->cartRepository->getByUser($userId);

            if ($allCartItems->isEmpty()) {
                throw new \Exception("Cart is empty");
            }

            $cartItems = $this->filterSelectedItems($allCartItems, $checkoutData->selectedItems);

            if ($cartItems->isEmpty()) {
                throw new \Exception("Please select items to checkout");
            }

            $totals = $this->calculateOrderTotals($cartItems);

            $orderData = array_merge($checkoutData->toArray(), [
                'user_id' => $userId,
                'customer_email' => Auth::user()->email,
                'total_amount' => $totals['totalAmount'],
                'total_items' => $totals['totalItems'],
                'status' => Order::STATUS_PENDING,
            ]);

            $order = $this->orderRepository->create($orderData);

            $this->createOrderDetails($order->id, $cartItems);

            $cartIds = $cartItems->pluck('id')->toArray();
            $this->cartRepository->deleteSelected($userId, $cartIds);

            if (method_exists($order, 'logActivity')) {
                $order->logActivity(
                    'order_created',
                    'Đơn hàng được tạo thành công',
                    null,
                    Order::STATUS_PENDING
                );
            }

            DB::commit();

            return [
                'success' => true,
                'order_id' => $order->id,
                'cart_count' => $this->cartRepository->getCount($userId),
                'message' => 'Order placed successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function filterSelectedItems($allItems, ?array $selectedIds)
    {
        if ($selectedIds && is_array($selectedIds) && count($selectedIds) > 0) {
            return $allItems->whereIn('id', $selectedIds);
        }

        return $allItems;
    }

    private function calculateOrderTotals($cartItems): array
    {
        $totalAmount = 0;
        $totalItems = 0;
        $productIds = $cartItems->pluck('product_id')->toArray();

        // Lock all products at once to prevent race conditions
        $products = DB::table('products')
            ->whereIn('id', $productIds)
            ->where('status', true)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($cartItems as $cartItem) {
            $product = $products->get($cartItem->product_id);

            if (!$product) {
                throw new \Exception("Product '{$cartItem->product->name}' is no longer available");
            }

            // If you have stock management (add this field to products table)
            // if (isset($product->stock) && $product->stock < $cartItem->quantity) {
            //     throw new \Exception("Insufficient stock for '{$product->name}'. Only {$product->stock} left.");
            // }

            $currentPrice = $product->price;

            // Update cart price if product price changed
            if (abs($cartItem->price - $currentPrice) > 0.01) {
                DB::table('carts')
                    ->where('id', $cartItem->id)
                    ->update(['price' => $currentPrice, 'updated_at' => now()]);
                $cartItem->price = $currentPrice;
            }

            $totalAmount += $currentPrice * $cartItem->quantity;
            $totalItems += $cartItem->quantity;

            // Decrement stock if you have stock management
            // DB::table('products')
            //     ->where('id', $product->id)
            //     ->decrement('stock', $cartItem->quantity);
        }

        return [
            'totalAmount' => $totalAmount,
            'totalItems' => $totalItems
        ];
    }

    private function createOrderDetails(int $orderId, $cartItems): void
    {
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->product;

            OrderDetail::create([
                'order_id' => $orderId,
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
    }

    public function getCheckoutItems(int $userId, ?array $selectedIds): array
    {
        $allCartItems = $this->cartRepository->getByUser($userId);

        if ($allCartItems->isEmpty()) {
            throw new \Exception("Cart is empty");
        }

        $cartItems = $this->filterSelectedItems($allCartItems, $selectedIds);

        if ($cartItems->isEmpty()) {
            throw new \Exception("Please select items to checkout");
        }

        $total = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return [
            'cartItems' => $cartItems,
            'total' => $total,
            'shippingFee' => 0,
            'grandTotal' => $total
        ];
    }

    /**
     * Get order by ID
     *
     * @param int $orderId
     * @return Order|null
     */
    public function getOrder(int $orderId): ?Order
    {
        return $this->orderRepository->find($orderId);
    }
}
