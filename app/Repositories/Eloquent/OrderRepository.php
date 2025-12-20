<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private Order $model,
        private Product $product,
        private User $user
    ) {}

    public function paginate(array $filters = [], int $perPage = 15)
    {
        $query = $this->model->query()
            ->select('id', 'user_id', 'customer_name', 'customer_email', 'customer_phone', 'total_amount', 'total_items', 'status', 'address', 'note', 'created_at', 'updated_at')
            ->with('user:id,fullname,email,phone');

        $query = $this->applyFilters($query, $filters);

        return $query->orderBy($filters['sort_by'] ?? 'created_at', $filters['sort_order'] ?? 'desc')
            ->paginate($perPage);
    }

    public function find(int $id)
    {
        return $this->model->query()
            ->select('id', 'user_id', 'customer_name', 'customer_email', 'customer_phone', 'total_amount', 'total_items', 'status', 'address', 'note', 'created_at', 'updated_at')
            ->with([
                'orderDetails' => function ($q) {
                    $q->select('id', 'order_id', 'product_id', 'product_name', 'quantity', 'unit_price', 'total_price', 'product_specifications')
                        ->with('product:id,name,slug,price');
                },
                'user:id,fullname'
            ])
            ->find($id);
    }

    public function findWithTrashed(int $id)
    {
        return $this->model->query()
            ->withTrashed()
            ->with([
                'orderDetails' => function ($q) {
                    $q->select('id', 'order_id', 'product_id', 'product_name', 'quantity', 'unit_price', 'total_price', 'product_specifications')
                        ->with('product:id,name,price');
                },
                'user:id,fullname,email,phone'
            ])
            ->find($id);
    }

    public function getByUser(int $userId)
    {
        return $this->model->query()
            ->select('id', 'user_id', 'customer_name', 'customer_email', 'customer_phone', 'total_amount', 'total_items', 'status', 'address', 'note', 'created_at', 'updated_at')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByStatus(string $status)
    {
        return $this->model->query()
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function delete(int $id)
    {
        return $this->model->where('id', $id)->delete();
    }

    public function restore(int $id)
    {
        return $this->model->withTrashed()->where('id', $id)->restore();
    }

    public function updateStatus(int $id, string $status)
    {
        return $this->model->where('id', $id)->update(['status' => $status]);
    }

    public function getByDateRange(string $dateFrom, string $dateTo)
    {
        return $this->model->query()
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByPriceRange(float $priceMin, float $priceMax)
    {
        return $this->model->query()
            ->where('total_amount', '>=', $priceMin)
            ->where('total_amount', '<=', $priceMax)
            ->orderBy('total_amount', 'desc')
            ->get();
    }

    public function search(string $search)
    {
        return $this->model->query()
            ->where('id', 'like', "%{$search}%")
            ->orWhereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('fullname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAllForExport()
    {
        return $this->model->query()
            ->with([
                'user:id,fullname,email,phone',
                'orderDetails' => function ($q) {
                    $q->select('id', 'order_id', 'product_id', 'product_name', 'quantity', 'unit_price', 'total_price');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function calculateTotalAmount(array $products): float
    {
        $total = 0;

        foreach ($products as $item) {
            $product = $this->product->find($item['product_id']);
            if ($product) {
                $quantity = (int) $item['quantity'];
                $unitPrice = $product->price ?? 0;
                $total += $unitPrice * $quantity;
            }
        }

        return $total;
    }

    public function calculateTotalItems(array $products): int
    {
        $total = 0;

        foreach ($products as $item) {
            $total += (int) $item['quantity'];
        }

        return $total;
    }

    public function searchCustomers(string $search, int $limit = 15)
    {
        return $this->user->query()
            ->select('id', 'fullname', 'email', 'phone', 'address')
            ->where('status', true)
            ->whereDoesntHave('role', fn($query) => $query->where('slug', 'admin'))
            ->where(function ($query) use ($search) {
                $query->where('fullname', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            })
            ->orderBy('fullname')
            ->limit($limit)
            ->get();
    }

    public function getCustomer(int $customerId)
    {
        return $this->user->query()
            ->select('id', 'fullname', 'email', 'phone', 'address')
            ->where('status', true)
            ->find($customerId);
    }

    public function getUserOrders(int $userId, ?string $search = null, ?string $status = null, int $perPage = 10)
    {
        $query = $this->model->query()
            ->select('id', 'user_id', 'customer_name', 'customer_email', 'customer_phone', 'total_amount', 'total_items', 'status', 'address', 'note', 'created_at', 'updated_at')
            ->with([
                'orderDetails' => function ($q) {
                    $q->select('id', 'order_id', 'product_id', 'product_name', 'quantity', 'unit_price', 'total_price', 'product_specifications')
                        ->with([
                            'product:id,name,slug,price',
                            'product.images:id,product_id,path,sort_order'
                        ]);
                }
            ])
            ->where('user_id', $userId);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                    ->orWhereHas('orderDetails', function ($subQuery) use ($search) {
                        $subQuery->where('product_name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getOrderWithDetails(int $orderId)
    {
        return $this->model->query()
            ->select('id', 'user_id', 'customer_name', 'customer_email', 'customer_phone', 'total_amount', 'total_items', 'status', 'address', 'note', 'created_at', 'updated_at')
            ->with([
                'orderDetails' => function ($q) {
                    $q->select('id', 'order_id', 'product_id', 'product_name', 'quantity', 'unit_price', 'total_price', 'product_specifications')
                        ->with([
                            'product:id,name,slug,price',
                            'product.images:id,product_id,path,sort_order'
                        ]);
                }
            ])
            ->find($orderId);
    }

    private function applyFilters($query, array $filters)
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('fullname', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['price_min'])) {
            $query->where('total_amount', '>=', $filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $query->where('total_amount', '<=', $filters['price_max']);
        }

        return $query;
    }
}
