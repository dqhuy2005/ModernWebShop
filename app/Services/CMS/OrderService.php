<?php

namespace App\Services\CMS;

use App\DTOs\OrderData;
use App\Events\OrderStatusChanged;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {
    }

    public function create(OrderData $data): Order
    {
        DB::beginTransaction();
        try {
            $totalAmount = $this->calculateTotalAmount($data->products);
            $totalItems = $this->calculateTotalItems($data->products);

            $customer = $this->orderRepository->getCustomer($data->userId);

            $orderData = array_merge($data->toArray(), [
                'customer_email' => $data->customerEmail ?? $customer?->email,
                'customer_name' => $data->customerName ?? $customer?->fullname,
                'customer_phone' => $data->customerPhone ?? $customer?->phone,
                'total_amount' => $totalAmount,
                'total_items' => $totalItems,
            ]);

            $order = $this->orderRepository->create($orderData);

            $this->createOrderDetails($order->id, $data->products);

            DB::commit();

            return $order->fresh(['orderDetails', 'user']);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, OrderData $data): Order
    {
        DB::beginTransaction();
        try {
            $order = $this->orderRepository->find($id);

            if (!$order) {
                throw new \Exception("Order not found");
            }

            $oldStatus = $order->status;
            $oldAddress = $order->address;
            $oldNote = $order->note;

            OrderDetail::where('order_id', $order->id)->delete();

            $totalAmount = $this->calculateTotalAmount($data->products);
            $totalItems = $this->calculateTotalItems($data->products);

            $updateData = array_merge($data->toArray(), [
                'total_amount' => $totalAmount,
                'total_items' => $totalItems,
            ]);

            $this->orderRepository->update($id, $updateData);

            $this->createOrderDetails($order->id, $data->products);

            $order = $order->fresh(['orderDetails', 'user']);

            if ($oldStatus !== $data->status) {
                $order->logActivity(
                    'status_changed',
                    "Status changed from {$oldStatus} to {$data->status}",
                    $oldStatus,
                    $data->status
                );

                event(new OrderStatusChanged($order, $oldStatus, $data->status));
            }

            if ($oldAddress !== $data->address) {
                $order->logActivity('address_updated', 'Shipping address was updated');
            }

            if ($oldNote !== $data->note && $data->note) {
                $order->logActivity('note_added', 'Order note was updated');
            }

            DB::commit();

            return $order;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function cancel(int $id): bool
    {
        DB::beginTransaction();
        try {
            $order = $this->orderRepository->find($id);

            if (!$order) {
                throw new \Exception("Order not found");
            }

            $this->orderRepository->updateStatus($id, Order::STATUS_CANCELLED);

            $order = $order->fresh();
            $order->logActivity('order_cancelled', 'Order was cancelled by admin');

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function restore(int $id): bool
    {
        DB::beginTransaction();
        try {
            $result = $this->orderRepository->restore($id);

            if ($result) {
                $order = $this->orderRepository->findWithTrashed($id);
                $order?->logActivity('order_restored', 'Order was restored from trash');
            }

            DB::commit();

            return $result;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function createOrderDetails(int $orderId, array $products): void
    {
        foreach ($products as $item) {
            $product = Product::find($item['product_id']);

            if (!$product) {
                continue;
            }

            $quantity = (int) $item['quantity'];
            $unitPrice = $product->price ?? 0;
            $subtotal = $unitPrice * $quantity;

            OrderDetail::create([
                'order_id' => $orderId,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $subtotal,
                'product_specifications' => $product->specifications,
            ]);
        }
    }

    private function calculateTotalAmount(array $products): float
    {
        return $this->orderRepository->calculateTotalAmount($products);
    }

    private function calculateTotalItems(array $products): int
    {
        return $this->orderRepository->calculateTotalItems($products);
    }
}
