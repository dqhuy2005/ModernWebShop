<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ComprehensiveOrderSeeder extends Seeder
{
    public function run(): void
    {
        echo "Starting Order Seeder...\n";

        $userCount = DB::table('users')->count();
        $productCount = DB::table('products')->count();

        if ($userCount < 2 || $productCount < 1) {
            echo "Not enough users or products to create orders.\n";
            return;
        }

        $totalOrders = 1500;
        $batchSize = 100;

        $statusDistribution = [
            'pending' => 0.10,      // 10%
            'confirmed' => 0.05,    // 5%
            'processing' => 0.15,   // 15%
            'shipping' => 0.10,     // 10%
            'shipped' => 0.10,      // 10%
            'completed' => 0.45,    // 45%
            'cancelled' => 0.05,    // 5%
        ];

        for ($batch = 0; $batch < ceil($totalOrders / $batchSize); $batch++) {
            $ordersInBatch = min($batchSize, $totalOrders - ($batch * $batchSize));

            DB::transaction(function () use ($ordersInBatch, $userCount, $productCount, $statusDistribution) {
                for ($i = 0; $i < $ordersInBatch; $i++) {
                    // Random user (bỏ qua admin - id=1)
                    $userId = rand(2, $userCount);

                    // Get user address
                    $user = DB::table('users')->where('id', $userId)->first();
                    $address = $user->address ?? 'Chưa có địa chỉ';

                    // Random status theo phân bổ
                    $status = $this->getWeightedRandomStatus($statusDistribution);

                    // Timestamps trong 6 tháng gần đây
                    $createdAt = Carbon::now()->subDays(rand(1, 180))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

                    // Số items trong order (1-5 sản phẩm)
                    $itemCount = rand(1, 5);

                    // Tạo order trước
                    $orderId = DB::table('orders')->insertGetId([
                        'user_id' => $userId,
                        'status' => $status,
                        'address' => $address,
                        'note' => (rand(1, 100) <= 30) ? $this->getRandomNote() : null,
                        'total_amount' => 0, // Sẽ update sau
                        'total_items' => $itemCount,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    // Tạo order details
                    $usedProducts = [];
                    $totalAmount = 0;

                    for ($j = 0; $j < $itemCount; $j++) {
                        // Chọn sản phẩm random không trùng lặp trong cùng order
                        do {
                            $productId = rand(1, $productCount);
                        } while (in_array($productId, $usedProducts));
                        $usedProducts[] = $productId;

                        // Get product info
                        $product = DB::table('products')->where('id', $productId)->first();
                        if (!$product) continue;

                        $quantity = rand(1, 3);
                        $unitPrice = $product->price;
                        $totalPrice = $quantity * $unitPrice;
                        $totalAmount += $totalPrice;

                        DB::table('order_details')->insert([
                            'order_id' => $orderId,
                            'product_id' => $productId,
                            'product_name' => $product->name,
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'total_price' => $totalPrice,
                            'product_specifications' => $product->specifications,
                            'created_at' => $createdAt,
                            'updated_at' => $createdAt,
                        ]);
                    }

                    // Update total amount
                    DB::table('orders')
                        ->where('id', $orderId)
                        ->update(['total_amount' => $totalAmount]);
                }
            });
        }

        echo "Order Seeder completed successfully!\n";
    }

    private function getWeightedRandomStatus(array $distribution): string
    {
        $rand = mt_rand(1, 100) / 100;
        $cumulative = 0;

        foreach ($distribution as $status => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $status;
            }
        }

        return 'completed';
    }

    private function getRandomNote(): string
    {
        $notes = [
            'Giao hàng giờ hành chính',
            'Gọi điện trước khi giao',
            'Giao hàng buổi sáng',
            'Giao hàng buổi chiều',
            'Kiểm tra hàng trước khi nhận',
            'Cần hóa đơn đỏ',
            'Giao hàng ngoài giờ',
            'Để hàng ở bảo vệ',
            'Giao tận tay',
            'Chuyển khoản trước',
        ];

        return $notes[array_rand($notes)];
    }
}
