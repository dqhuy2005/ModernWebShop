<?php

namespace App\Services\User;

use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Models\Order;
use Exception;

class PurchaseService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {
    }

    public function getUserOrders(int $userId, ?string $search = null, ?string $status = null, int $perPage = 10)
    {
        return $this->orderRepository->getUserOrders($userId, $search, $status, $perPage);
    }

    public function getOrderDetail(int $orderId, int $userId): ?Order
    {
        $order = $this->orderRepository->getOrderWithDetails($orderId);

        if (!$order || $order->user_id !== $userId) {
            return null;
        }

        return $order;
    }

    public function cancelOrder(int $orderId, int $userId): array
    {
        $order = Order::select('id', 'user_id', 'status')->find($orderId);

        if (!$order || $order->user_id !== $userId) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng',
                'code' => 404
            ];
        }

        if ($order->status !== 'pending') {
            return [
                'success' => false,
                'message' => 'Chỉ có thể hủy đơn hàng ở trạng thái "Chờ xử lý"',
                'code' => 400
            ];
        }

        try {
            $order->logActivity(
                'order_cancelled',
                'Đơn hàng đã bị hủy bởi khách hàng',
                $order->status,
                Order::STATUS_CANCELLED
            );

            $this->orderRepository->update($orderId, [
                'status' => 'cancelled'
            ]);

            return [
                'success' => true,
                'message' => 'Đã hủy đơn hàng thành công',
                'code' => 200
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại!',
                'code' => 500
            ];
        }
    }
}
