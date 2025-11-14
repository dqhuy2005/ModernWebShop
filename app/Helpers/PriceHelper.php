<?php

namespace App\Helpers;

class PriceHelper
{
    /**
     * Format giá tiền theo chuẩn Việt Nam: 150.000 ₫
     * 
     * @param int|null $price Giá sản phẩm (VNĐ)
     * @param string $currency Đơn vị tiền tệ
     * @return string
     */
    public static function format(?int $price, string $currency = 'VND'): string
    {
        if ($price === null || $price === 0) {
            return 'Liên hệ';
        }

        return number_format($price, 0, ',', '.') . ' ₫';
    }

    /**
     * Format giá với đơn vị tiền tệ đầy đủ
     * 
     * @param int|null $price Giá sản phẩm
     * @param string $currency Đơn vị tiền tệ
     * @return string
     */
    public static function formatWithCurrency(?int $price, string $currency = 'VND'): string
    {
        if ($price === null || $price === 0) {
            return 'Liên hệ';
        }

        return number_format($price, 0, ',', '.') . ' ' . strtoupper($currency);
    }

    /**
     * Validate giá sản phẩm
     * 
     * @param mixed $price Giá cần validate
     * @return array ['valid' => bool, 'error' => string|null, 'value' => int|null]
     */
    public static function validate($price): array
    {
        // Loại bỏ dấu phân cách nếu có
        if (is_string($price)) {
            $price = str_replace(['.', ',', ' ', '₫', 'VND', 'VNĐ'], '', $price);
        }

        // Kiểm tra có phải số không
        if (!is_numeric($price)) {
            return [
                'valid' => false,
                'error' => 'Giá phải là số',
                'value' => null,
            ];
        }

        $priceInt = (int) $price;

        // Kiểm tra số âm
        if ($priceInt < 0) {
            return [
                'valid' => false,
                'error' => 'Giá phải là số dương',
                'value' => null,
            ];
        }

        // Kiểm tra giới hạn tối đa
        if ($priceInt > 999999999) {
            return [
                'valid' => false,
                'error' => 'Giá vượt quá giới hạn cho phép (999.999.999 ₫)',
                'value' => null,
            ];
        }

        return [
            'valid' => true,
            'error' => null,
            'value' => $priceInt,
        ];
    }

    /**
     * Parse giá từ string input (có thể chứa dấu phân cách)
     * 
     * @param string|int $input Input từ form
     * @return int|null
     */
    public static function parse($input): ?int
    {
        if (is_int($input)) {
            return $input;
        }

        if (!is_string($input)) {
            return null;
        }

        // Loại bỏ các ký tự không phải số
        $cleaned = preg_replace('/[^0-9]/', '', $input);

        if (empty($cleaned)) {
            return null;
        }

        return (int) $cleaned;
    }

    /**
     * Tính tổng tiền từ đơn giá và số lượng
     * 
     * @param int $unitPrice Đơn giá
     * @param int $quantity Số lượng
     * @return int
     */
    public static function calculateSubtotal(int $unitPrice, int $quantity): int
    {
        return $unitPrice * $quantity;
    }

    /**
     * Tính tổng đơn hàng
     * 
     * @param array $items Mảng items [['unit_price' => int, 'quantity' => int], ...]
     * @return int
     */
    public static function calculateOrderTotal(array $items): int
    {
        $total = 0;
        
        foreach ($items as $item) {
            if (isset($item['unit_price']) && isset($item['quantity'])) {
                $total += self::calculateSubtotal($item['unit_price'], $item['quantity']);
            }
        }

        return $total;
    }
}
