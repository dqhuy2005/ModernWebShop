<?php

namespace App\Services\User;

use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository
    ) {
    }

    public function addToCart(int $userId, int $productId, int $quantity = 1): array
    {
        DB::beginTransaction();
        try {
            $product = $this->productRepository->find($productId);

            if (!$product || !$product->status) {
                throw new \Exception("Product not available");
            }

            // Use DB::table for atomic upsert with proper locking
            $existingCart = DB::table('carts')
                ->where('user_id', $userId)
                ->where('product_id', $productId)
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->first();

            if ($existingCart) {
                $newQuantity = min($existingCart->quantity + $quantity, 999);
                DB::table('carts')
                    ->where('id', $existingCart->id)
                    ->update([
                        'quantity' => $newQuantity,
                        'price' => $product->price,
                        'updated_at' => now(),
                    ]);
            } else {
                // Check for soft-deleted cart items
                $trashedCart = DB::table('carts')
                    ->where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->whereNotNull('deleted_at')
                    ->first();

                if ($trashedCart) {
                    DB::table('carts')
                        ->where('id', $trashedCart->id)
                        ->update([
                            'quantity' => $quantity,
                            'price' => $product->price,
                            'deleted_at' => null,
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('carts')->insert([
                        'user_id' => $userId,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $product->price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $cartCount = $this->cartRepository->getCount($userId);

            DB::commit();

            return [
                'success' => true,
                'cart_count' => $cartCount,
                'message' => 'Product added to cart successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateQuantity(int $userId, int $cartId, int $quantity): bool
    {
        if ($quantity < 1 || $quantity > 999) {
            throw new \Exception("Invalid quantity");
        }

        $cartItem = $this->cartRepository->findByUserAndCart($userId, $cartId);

        if (!$cartItem || $cartItem->user_id !== $userId) {
            throw new \Exception("Cart item not found");
        }

        return $this->cartRepository->updateQuantity($cartId, $quantity);
    }

    public function removeItem(int $userId, int $cartId): bool
    {
        return $this->cartRepository->delete($cartId);
    }

    public function removeSelectedItems(int $userId, array $cartIds): bool
    {
        return $this->cartRepository->deleteSelected($userId, $cartIds);
    }

    public function clearCart(int $userId): bool
    {
        return $this->cartRepository->deleteByUser($userId);
    }

    public function getCartItems(int $userId)
    {
        return $this->cartRepository->getByUser($userId);
    }

    public function getCartCount(int $userId): int
    {
        return $this->cartRepository->getCount($userId);
    }

    public function calculateTotal(int $userId): float
    {
        return $this->cartRepository->calculateTotal($userId);
    }

    public function invalidateCartCountCache(int $userId): void
    {
        Session::forget('cart_count_' . Auth::id());
        Session::put('cart_count', 0);
    }
}
