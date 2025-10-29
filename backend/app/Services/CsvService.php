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
                $user->fullname ?? 'N/A',
                $user->email,
                $user->phone ?? 'N/A',
                $user->address ?? 'N/A',
                optional($user->role)->name ?? 'No Role',
                $user->status ? 'Active' : 'Inactive',
                $user->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $this->arrayToCsv($csvData);
    }

    /**
     * Export users to Excel (XLSX format)
     *
     * @return string Excel HTML content
     */
    public function exportUsersExcel(): string
    {
        $users = User::with('role')->get();

        return $this->generateExcelHtml([
            'title' => 'Users Export',
            'headers' => ['ID', 'Full Name', 'Email', 'Phone', 'Address', 'Role', 'Status', 'Created At'],
            'data' => $users->map(function ($user) {
                return [
                    $user->id,
                    $user->fullname ?? 'N/A',
                    $user->email,
                    $user->phone ?? 'N/A',
                    $user->address ?? 'N/A',
                    optional($user->role)->name ?? 'No Role',
                    $user->status ? 'Active' : 'Inactive',
                    $user->created_at->format('Y-m-d H:i:s'),
                ];
            })->toArray()
        ]);
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
            'Total Items',
            'Status',
            'Shipping Address',
            'Note',
            'Created At'
        ];

        foreach ($orders as $order) {
            $csvData[] = [
                $order->id,
                $order->user->fullname ?? 'Guest',
                $order->user->email ?? 'N/A',
                $order->user->phone ?? 'N/A',
                number_format($order->total_amount, 0, ',', '.') . ' VND',
                $order->total_items,
                $this->getOrderStatusLabel($order->status),
                $order->address ?? 'N/A',
                $order->note ?? 'N/A',
                $order->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $this->arrayToCsv($csvData);
    }

    /**
     * Export orders to Excel (XLSX format)
     *
     * @return string Excel HTML content
     */
    public function exportOrdersExcel(): string
    {
        $orders = Order::with('user')->get();

        return $this->generateExcelHtml([
            'title' => 'Orders Export',
            'headers' => [
                'Order ID',
                'Customer Name',
                'Customer Email',
                'Customer Phone',
                'Total Amount',
                'Total Items',
                'Status',
                'Shipping Address',
                'Note',
                'Created At'
            ],
            'data' => $orders->map(function ($order) {
                return [
                    $order->id,
                    $order->user->fullname ?? 'Guest',
                    $order->user->email ?? 'N/A',
                    $order->user->phone ?? 'N/A',
                    number_format($order->total_amount, 0, ',', '.') . ' VND',
                    $order->total_items,
                    $this->getOrderStatusLabel($order->status),
                    $order->address ?? 'N/A',
                    $order->note ?? 'N/A',
                    $order->created_at->format('Y-m-d H:i:s'),
                ];
            })->toArray()
        ]);
    }

    public function importUsers(string $filePath): array
    {
        $csvData = $this->csvToArray($filePath);

        $success = 0;
        $failed = 0;
        $errors = [];

        array_shift($csvData);

        foreach ($csvData as $index => $row) {
            $rowNumber = $index + 2;

            try {
                $userData = [
                    'fullname' => $row[0] ?? null,
                    'email' => $row[1] ?? null,
                    'phone' => $row[2] ?? null,
                    'address' => $row[3] ?? null,
                    'password' => isset($row[4]) ? Hash::make($row[4]) : Hash::make('password123'),
                    'role_id' => $row[5] ?? 2,
                    'status' => isset($row[6]) ? ($row[6] === 'Active' || $row[6] === '1' ? 1 : 0) : 1,
                ];

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

    protected function generateExcelHtml(array $config): string
    {
        $title = $config['title'] ?? 'Export';
        $headers = $config['headers'] ?? [];
        $data = $config['data'] ?? [];

        $html = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $html .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $html .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        $html .= ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
        $html .= ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
        $html .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        $html .= ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";

        $html .= '<Styles>' . "\n";
        $html .= '<Style ss:ID="Header">' . "\n";
        $html .= '<Font ss:Bold="1" ss:Color="#FFFFFF"/>' . "\n";
        $html .= '<Interior ss:Color="#4472C4" ss:Pattern="Solid"/>' . "\n";
        $html .= '<Alignment ss:Horizontal="Center" ss:Vertical="Center"/>' . "\n";
        $html .= '</Style>' . "\n";
        $html .= '<Style ss:ID="Default">' . "\n";
        $html .= '<Alignment ss:Vertical="Center"/>' . "\n";
        $html .= '</Style>' . "\n";
        $html .= '</Styles>' . "\n";

        $html .= '<Worksheet ss:Name="' . htmlspecialchars($title) . '">' . "\n";
        $html .= '<Table>' . "\n";

        foreach ($headers as $header) {
            $width = strlen($header) * 10;
            $html .= '<Column ss:Width="' . max($width, 60) . '"/>' . "\n";
        }

        $html .= '<Row>' . "\n";
        foreach ($headers as $header) {
            $html .= '<Cell ss:StyleID="Header"><Data ss:Type="String">' . htmlspecialchars($header) . '</Data></Cell>' . "\n";
        }
        $html .= '</Row>' . "\n";

        foreach ($data as $row) {
            $html .= '<Row>' . "\n";
            foreach ($row as $cell) {
                $type = is_numeric($cell) ? 'Number' : 'String';
                $html .= '<Cell ss:StyleID="Default"><Data ss:Type="' . $type . '">' . htmlspecialchars($cell) . '</Data></Cell>' . "\n";
            }
            $html .= '</Row>' . "\n";
        }

        $html .= '</Table>' . "\n";
        $html .= '</Worksheet>' . "\n";
        $html .= '</Workbook>';

        return $html;
    }

    public function generateUserTemplate(): string
    {
        $csvData = [];
        $csvData[] = ['Full Name', 'Email', 'Phone', 'Address', 'Password', 'Role ID (2=Customer, 1=Admin)', 'Status (Active/Inactive)'];
        $csvData[] = ['John Doe', 'john@example.com', '0123456789', '123 Street, City', 'password123', '2', 'Active'];
        $csvData[] = ['Jane Smith', 'jane@example.com', '0987654321', '456 Avenue, Town', 'password123', '2', 'Active'];

        return $this->arrayToCsv($csvData);
    }
}
