<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class CsvService
{
    /**
     * Export users to CSV
     *
     * @return string CSV content
     */
    public function exportUsers(): string
    {
        $users = User::with('role')->get();

        $csvData = [];
        $csvData[] = ['ID', 'Full Name', 'Email', 'Phone', 'Address', 'Role', 'Status', 'Created At'];

        foreach ($users as $user) {
            $csvData[] = [
                $user->id,
                $user->fullname,
                $user->email,
                $user->phone ?? 'N/A',
                $user->address ?? 'N/A',
                $user->role->name ?? 'N/A',
                $user->status ? 'Active' : 'Inactive',
                $user->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $this->arrayToCsv($csvData);
    }

    /**
     * Export orders to CSV
     *
     * @return string CSV content
     */
    public function exportOrders(): string
    {
        $orders = Order::with('user')->get();

        $csvData = [];
        $csvData[] = [
            'Order ID',
            'Customer Name',
            'Customer Email',
            'Customer Phone',
            'Total Amount',
            'Status',
            'Payment Method',
            'Shipping Address',
            'Note',
            'Created At'
        ];

        foreach ($orders as $order) {
            $csvData[] = [
                $order->id,
                $order->user->fullname ?? 'N/A',
                $order->user->email ?? 'N/A',
                $order->user->phone ?? 'N/A',
                $order->total_amount,
                $this->getOrderStatusLabel($order->status),
                $this->getPaymentMethodLabel($order->payment_method),
                $order->address ?? 'N/A',
                $order->note ?? 'N/A',
                $order->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $this->arrayToCsv($csvData);
    }

    /**
     * Import users from CSV
     *
     * @param string $filePath
     * @return array ['success' => int, 'failed' => int, 'errors' => array]
     */
    public function importUsers(string $filePath): array
    {
        $csvData = $this->csvToArray($filePath);

        $success = 0;
        $failed = 0;
        $errors = [];

        // Skip header row
        array_shift($csvData);

        foreach ($csvData as $index => $row) {
            $rowNumber = $index + 2; // +2 because we skipped header and arrays are 0-indexed

            try {
                // Map CSV columns to user data
                $userData = [
                    'fullname' => $row[0] ?? null,
                    'email' => $row[1] ?? null,
                    'phone' => $row[2] ?? null,
                    'address' => $row[3] ?? null,
                    'password' => isset($row[4]) ? Hash::make($row[4]) : Hash::make('password123'),
                    'role_id' => $row[5] ?? 2, // Default to customer role
                    'status' => isset($row[6]) ? ($row[6] === 'Active' || $row[6] === '1' ? 1 : 0) : 1,
                ];

                // Validate
                $validator = Validator::make($userData, [
                    'fullname' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email',
                    'phone' => 'nullable|string|max:20',
                    'address' => 'nullable|string|max:500',
                    'role_id' => 'required|exists:roles,id',
                ]);

                if ($validator->fails()) {
                    $failed++;
                    $errors[] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                // Create user
                User::create($userData);
                $success++;

            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Row {$rowNumber}: " . $e->getMessage();
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }

    /**
     * Convert array to CSV string
     *
     * @param array $data
     * @return string
     */
    protected function arrayToCsv(array $data): string
    {
        $output = fopen('php://temp', 'r+');

        // Add BOM for UTF-8
        fputs($output, "\xEF\xBB\xBF");

        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Convert CSV file to array
     *
     * @param string $filePath
     * @return array
     */
    protected function csvToArray(string $filePath): array
    {
        $rows = [];

        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }

        return $rows;
    }

    /**
     * Get order status label
     *
     * @param string $status
     * @return string
     */
    protected function getOrderStatusLabel(string $status): string
    {
        $labels = [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'processing' => 'Processing',
            'shipping' => 'Shipping',
            'delivered' => 'Delivered',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Get payment method label
     *
     * @param string $method
     * @return string
     */
    protected function getPaymentMethodLabel(string $method): string
    {
        $labels = [
            'cod' => 'Cash on Delivery',
            'bank_transfer' => 'Bank Transfer',
            'vnpay' => 'VNPay',
            'momo' => 'MoMo',
        ];

        return $labels[$method] ?? $method;
    }

    /**
     * Get CSV download headers
     *
     * @param string $filename
     * @return array
     */
    public function getDownloadHeaders(string $filename): array
    {
        return [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
    }

    /**
     * Generate sample CSV template for users
     *
     * @return string
     */
    public function generateUserTemplate(): string
    {
        $csvData = [];
        $csvData[] = ['Full Name', 'Email', 'Phone', 'Address', 'Password', 'Role ID (2=Customer, 1=Admin)', 'Status (Active/Inactive)'];
        $csvData[] = ['John Doe', 'john@example.com', '0123456789', '123 Street, City', 'password123', '2', 'Active'];
        $csvData[] = ['Jane Smith', 'jane@example.com', '0987654321', '456 Avenue, Town', 'password123', '2', 'Active'];

        return $this->arrayToCsv($csvData);
    }
}
