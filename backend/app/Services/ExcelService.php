<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;

class ExcelService
{
    /**
     * Export users to Excel (XML Spreadsheet format)
     */
    public function exportUsers(): string
    {
        $users = User::with('role')->get();

        $headers = ['ID', 'Full Name', 'Email', 'Phone', 'Address', 'Role', 'Status', 'Created At'];
        
        $data = $users->map(function ($user) {
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
        })->toArray();

        return $this->generateExcelXml([
            'title' => 'Users Export',
            'headers' => $headers,
            'data' => $data,
        ]);
    }

    /**
     * Export orders to Excel (XML Spreadsheet format)
     */
    public function exportOrders(): string
    {
        $orders = Order::with('user')->get();

        $headers = [
            'Order ID', 'Customer Name', 'Customer Email', 'Customer Phone',
            'Total Amount', 'Total Items', 'Status', 'Shipping Address', 'Note', 'Created At'
        ];
        
        $data = $orders->map(function ($order) {
            return [
                $order->id,
                $order->user->fullname ?? 'Guest',
                $order->user->email ?? 'N/A',
                $order->user->phone ?? 'N/A',
                number_format($order->total_amount, 0, ',', '.') . ' VND',
                $order->total_items,
                $order->status,
                $order->shipping_address ?? 'N/A',
                $order->note ?? 'N/A',
                $order->created_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();

        return $this->generateExcelXml([
            'title' => 'Orders Export',
            'headers' => $headers,
            'data' => $data,
        ]);
    }

    /**
     * Generate Excel template for users import
     */
    public function generateUserTemplate(): string
    {
        $headers = ['Full Name', 'Email', 'Phone', 'Address', 'Role', 'Status', 'Password'];
        
        $sampleData = [
            ['John Doe', 'john@example.com', '0123456789', '123 Main St', 'User', 'Active', 'password123'],
            ['Jane Smith', 'jane@example.com', '0987654321', '456 Oak Ave', 'User', 'Active', 'password123'],
        ];

        return $this->generateExcelXml([
            'title' => 'Users Import Template',
            'headers' => $headers,
            'data' => $sampleData,
        ]);
    }

    /**
     * Import users from Excel content
     */
    public function importUsers(string $content): array
    {
        $result = ['success' => 0, 'failed' => 0, 'errors' => []];

        try {
            $rows = $this->parseExcelXml($content);
            
            if (empty($rows)) {
                $result['errors'][] = 'No data found in the file';
                return $result;
            }

            // Get headers from first row
            $headers = array_shift($rows);
            
            foreach ($rows as $index => $row) {
                try {
                    $rowData = array_combine($headers, $row);
                    
                    // Skip if email is empty
                    if (empty($rowData['Email'] ?? $rowData['email'])) {
                        continue;
                    }

                    $email = $rowData['Email'] ?? $rowData['email'];
                    
                    // Find role
                    $roleName = $rowData['Role'] ?? $rowData['role'] ?? 'User';
                    $role = Role::where('name', $roleName)->first();
                    $roleId = $role ? $role->id : Role::where('name', 'User')->first()->id;

                    // Convert status
                    $statusText = strtolower($rowData['Status'] ?? $rowData['status'] ?? 'active');
                    $status = in_array($statusText, ['active', '1', 'true']) ? 1 : 0;

                    // Get password
                    $password = $rowData['Password'] ?? $rowData['password'] ?? 'password123';

                    User::updateOrCreate(
                        ['email' => $email],
                        [
                            'fullname' => $rowData['Full Name'] ?? $rowData['full_name'] ?? $rowData['fullname'] ?? null,
                            'phone' => $rowData['Phone'] ?? $rowData['phone'] ?? null,
                            'address' => $rowData['Address'] ?? $rowData['address'] ?? null,
                            'role_id' => $roleId,
                            'status' => $status,
                            'password' => Hash::make($password),
                        ]
                    );

                    $result['success']++;
                } catch (\Exception $e) {
                    $result['failed']++;
                    $result['errors'][] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            $result['errors'][] = "Import failed: " . $e->getMessage();
        }

        return $result;
    }

    /**
     * Generate Excel XML Spreadsheet format
     */
    protected function generateExcelXml(array $config): string
    {
        $title = $config['title'] ?? 'Export';
        $headers = $config['headers'] ?? [];
        $data = $config['data'] ?? [];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        $xml .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
        
        // Styles
        $xml .= '<Styles>' . "\n";
        $xml .= '<Style ss:ID="Header">' . "\n";
        $xml .= '<Font ss:Bold="1" ss:Color="#FFFFFF" ss:Size="12"/>' . "\n";
        $xml .= '<Interior ss:Color="#4472C4" ss:Pattern="Solid"/>' . "\n";
        $xml .= '<Alignment ss:Horizontal="Center" ss:Vertical="Center"/>' . "\n";
        $xml .= '</Style>' . "\n";
        $xml .= '<Style ss:ID="Default">' . "\n";
        $xml .= '<Alignment ss:Vertical="Center"/>' . "\n";
        $xml .= '</Style>' . "\n";
        $xml .= '</Styles>' . "\n";
        
        $xml .= '<Worksheet ss:Name="' . htmlspecialchars($title, ENT_XML1, 'UTF-8') . '">' . "\n";
        $xml .= '<Table>' . "\n";
        
        // Column widths
        foreach ($headers as $header) {
            $width = max(80, strlen($header) * 8);
            $xml .= '<Column ss:Width="' . $width . '"/>' . "\n";
        }
        
        // Header row
        $xml .= '<Row ss:Height="25">' . "\n";
        foreach ($headers as $header) {
            $xml .= '<Cell ss:StyleID="Header">';
            $xml .= '<Data ss:Type="String">' . htmlspecialchars($header, ENT_XML1, 'UTF-8') . '</Data>';
            $xml .= '</Cell>' . "\n";
        }
        $xml .= '</Row>' . "\n";
        
        // Data rows
        foreach ($data as $row) {
            $xml .= '<Row>' . "\n";
            foreach ($row as $cell) {
                $xml .= '<Cell ss:StyleID="Default">';
                
                // Detect if number or string
                if (is_numeric($cell) && !preg_match('/^0/', $cell)) {
                    $xml .= '<Data ss:Type="Number">' . $cell . '</Data>';
                } else {
                    $xml .= '<Data ss:Type="String">' . htmlspecialchars($cell, ENT_XML1, 'UTF-8') . '</Data>';
                }
                
                $xml .= '</Cell>' . "\n";
            }
            $xml .= '</Row>' . "\n";
        }
        
        $xml .= '</Table>' . "\n";
        $xml .= '</Worksheet>' . "\n";
        $xml .= '</Workbook>';
        
        return $xml;
    }

    /**
     * Parse Excel XML format
     */
    protected function parseExcelXml(string $content): array
    {
        $rows = [];
        
        try {
            // Try to load as XML
            $xml = simplexml_load_string($content);
            
            if ($xml !== false) {
                // Register namespace
                $xml->registerXPathNamespace('ss', 'urn:schemas-microsoft-com:office:spreadsheet');
                
                // Get all rows
                $xmlRows = $xml->xpath('//ss:Row');
                
                foreach ($xmlRows as $xmlRow) {
                    $row = [];
                    $cells = $xmlRow->xpath('.//ss:Cell/ss:Data');
                    
                    foreach ($cells as $cell) {
                        $row[] = (string)$cell;
                    }
                    
                    if (!empty($row)) {
                        $rows[] = $row;
                    }
                }
            }
        } catch (\Exception $e) {
            // If XML parsing fails, try simple parsing
            $rows = $this->parseSimpleExcel($content);
        }
        
        return $rows;
    }

    /**
     * Simple Excel parsing fallback
     */
    protected function parseSimpleExcel(string $content): array
    {
        $rows = [];
        $lines = explode("\n", $content);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Try tab-separated
            if (strpos($line, "\t") !== false) {
                $rows[] = explode("\t", $line);
            }
            // Try comma-separated
            elseif (strpos($line, ",") !== false) {
                $rows[] = str_getcsv($line);
            }
        }
        
        return $rows;
    }

    /**
     * Get download headers for Excel file
     */
    public function getDownloadHeaders(string $filename): array
    {
        return [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
    }
}
