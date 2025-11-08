<?php

namespace App\Imports;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OrdersImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip empty rows
            if (empty($row['customer_email'])) {
                continue;
            }

            // Find user by email
            $user = User::where('email', $row['customer_email'])->first();

            if (!$user) {
                continue; // Skip if user not found
            }

            // Parse total amount (remove VND and convert to number)
            $totalAmount = isset($row['total_amount'])
                ? (float) preg_replace('/[^0-9.]/', '', $row['total_amount'])
                : 0;

            Order::create([
                'user_id' => $user->id,
                'customer_email' => $row['customer_email'] ?? $user->email,
                'customer_name' => $row['customer_name'] ?? $user->fullname,
                'customer_phone' => $row['customer_phone'] ?? $user->phone,
                'total_amount' => $totalAmount,
                'total_items' => $row['total_items'] ?? 0,
                'status' => $row['status'] ?? 'pending',
                'shipping_address' => $row['shipping_address'] ?? null,
                'note' => $row['note'] ?? null,
            ]);
        }
    }

    /**
     * @return int
     */
    public function headingRow(): int
    {
        return 1;
    }
}
