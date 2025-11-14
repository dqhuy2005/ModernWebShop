<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereDoesntHave('role', fn($query) => $query->where('name', 'admin'))->get();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {

            return;
        }



        $statuses = ['pending', 'confirmed', 'processing', 'shipping', 'completed', 'cancelled'];
        $orderCount = 0;

        foreach ($users->take(10) as $user) {
            $numOrders = rand(1, 3);

            for ($i = 0; $i < $numOrders; $i++) {
                $numItems = rand(1, 5);
                $selectedProducts = $products->random(min($numItems, $products->count()));

                $orderItems = [];
                $totalAmount = 0;
                $totalItems = 0;

                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 3);
                    $unitPrice = $product->price ?? rand(100000, 5000000);
                    $subtotal = $unitPrice * $quantity;

                    $orderItems[] = [
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
                    'user_id' => $user->id,
                    'total_amount' => $totalAmount,
                    'total_items' => $totalItems,
                    'status' => $statuses[array_rand($statuses)],
                    'address' => $this->generateRandomAddress(),
                    'note' => rand(0, 1) ? $this->generateRandomNote() : null,
                    'created_at' => Carbon::now()->subDays(rand(1, 60)),
                ]);

                foreach ($orderItems as $item) {
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'product_name' => $item['product_name'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['total_price'],
                        'product_specifications' => $item['product_specifications'],
                    ]);
                }

                $orderCount++;
            }
        }

        $this->createSpecialOrders($users, $products);


    }

    /**
     * Tạo các đơn hàng đặc biệt để test
     */
    private function createSpecialOrders($users, $products)
    {
        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        $testUser = $users->first();


        $testProduct = $products->first();

        $order1 = Order::create([
            'user_id' => $testUser->id,
            'total_amount' => 0,
            'total_items' => 1,
            'status' => 'pending',
            'address' => 'Địa chỉ test - Sản phẩm liên hệ',
            'note' => 'Test: Sản phẩm có giá = 0 (Liên hệ)',
            'created_at' => Carbon::now(),
        ]);

        OrderDetail::create([
            'order_id' => $order1->id,
            'product_id' => $testProduct->id,
            'product_name' => 'Test Product - Liên hệ giá',
            'quantity' => 1,
            'unit_price' => 0,
            'total_price' => 0,
            'product_specifications' => null,
        ]);



        $largeTotal = 0;
        $largeItemCount = 0;
        $largeOrderDetails = [];

        foreach ($products->take(8) as $product) {
            $qty = rand(5, 10);
            $price = rand(1000000, 9000000);
            $subtotal = $price * $qty;

            $largeOrderDetails[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $qty,
                'unit_price' => $price,
                'total_price' => $subtotal,
                'product_specifications' => $product->specifications,
            ];

            $largeTotal += $subtotal;
            $largeItemCount += $qty;
        }

        $order2 = Order::create([
            'user_id' => $testUser->id,
            'total_amount' => $largeTotal,
            'total_items' => $largeItemCount,
            'status' => 'confirmed',
            'address' => 'Địa chỉ test - Đơn hàng lớn',
            'note' => 'Test: Đơn hàng với nhiều sản phẩm và giá trị cao',
            'created_at' => Carbon::now()->subDays(5),
        ]);

        foreach ($largeOrderDetails as $detail) {
            OrderDetail::create(array_merge($detail, ['order_id' => $order2->id]));
        }



        $completedTotal = 0;
        $completedItems = 0;

        $order3 = Order::create([
            'user_id' => $testUser->id,
            'total_amount' => 0,
            'total_items' => 0,
            'status' => 'completed',
            'address' => '123 Đường ABC, Quận 1, TP.HCM',
            'note' => 'Giao hàng trong giờ hành chính',
            'created_at' => Carbon::now()->subDays(30),
        ]);

        foreach ($products->random(3) as $product) {
            $qty = rand(1, 2);
            $price = $product->price ?? rand(500000, 2000000);
            $subtotal = $price * $qty;

            OrderDetail::create([
                'order_id' => $order3->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $qty,
                'unit_price' => $price,
                'total_price' => $subtotal,
                'product_specifications' => $product->specifications,
            ]);

            $completedTotal += $subtotal;
            $completedItems += $qty;
        }

        $order3->update([
            'total_amount' => $completedTotal,
            'total_items' => $completedItems,
        ]);


    }

    /**
     * Generate random Vietnamese address
     */
    private function generateRandomAddress(): string
    {
        $streets = [
            'Nguyễn Trãi', 'Lê Lợi', 'Trần Hưng Đạo', 'Hai Bà Trưng',
            'Võ Văn Tần', 'Điện Biên Phủ', 'Cách Mạng Tháng 8', 'Lý Thường Kiệt'
        ];

        $districts = [
            'Quận 1', 'Quận 3', 'Quận 5', 'Quận 10', 'Quận Bình Thạnh',
            'Quận Tân Bình', 'Quận Phú Nhuận', 'Quận Gò Vấp'
        ];

        $cities = ['TP. Hồ Chí Minh', 'Hà Nội', 'Đà Nẵng', 'Cần Thơ'];

        $number = rand(1, 999);
        $street = $streets[array_rand($streets)];
        $district = $districts[array_rand($districts)];
        $city = $cities[array_rand($cities)];

        return "{$number} {$street}, {$district}, {$city}";
    }

    /**
     * Generate random order note
     */
    private function generateRandomNote(): string
    {
        $notes = [
            'Giao hàng trong giờ hành chính (8h-17h)',
            'Gọi điện trước khi giao',
            'Giao tận tay, không gửi qua bảo vệ',
            'Thanh toán khi nhận hàng',
            'Kiểm tra kỹ hàng trước khi nhận',
            'Giao hàng vào cuối tuần',
            'Liên hệ số điện thoại: 0901234567',
            'Đóng gói cẩn thận',
        ];

        return $notes[array_rand($notes)];
    }
}
