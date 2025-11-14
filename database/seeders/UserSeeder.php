<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;

class UserSeeder extends Seeder
{
    private $lastNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Phan', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương'];
    private $middleNames = ['Văn', 'Thị', 'Minh', 'Anh', 'Đức', 'Hữu', 'Quang', 'Thanh', 'Thiên', 'Công', 'Bảo'];
    private $firstNames = ['Tuấn', 'Hùng', 'Nam', 'Linh', 'Thu', 'Hương', 'Phương', 'Long', 'Dũng', 'Khoa', 'An', 'Bình', 'Cường', 'Hải', 'Khánh', 'Phúc', 'Quân', 'Sơn', 'Tâm', 'Tùng', 'Vy', 'Ngọc', 'Mai', 'Lan', 'Hoa', 'Trang', 'Thảo', 'Nhung', 'Chi', 'Dung'];

    private $phonePrefix = ['016', '090', '091', '093', '094', '096', '097', '098', '032', '033', '034', '035', '036', '037', '038', '039', '052', '056', '058', '059', '070', '076', '077', '078', '079', '081', '082', '083', '084', '085', '086', '088', '089'];

    private $emailDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com'];

    private $hcmDistricts = [
        'Quận 1' => ['Phường Bến Nghé', 'Phường Bến Thành', 'Phường Nguyễn Thái Bình', 'Phường Phạm Ngũ Lão', 'Phường Cầu Ông Lãnh', 'Phường Đa Kao', 'Phường Tân Định'],
        'Quận 3' => ['Phường 1', 'Phường 2', 'Phường 3', 'Phường 4', 'Phường 5', 'Phường 6', 'Phường 7', 'Phường 8'],
        'Quận 5' => ['Phường 1', 'Phường 2', 'Phường 3', 'Phường 4', 'Phường 5', 'Phường 10', 'Phường 11'],
        'Quận 7' => ['Phường Tân Thuận Đông', 'Phường Tân Thuận Tây', 'Phường Tân Phú', 'Phường Tân Quy', 'Phường Bình Thuận'],
        'Quận 10' => ['Phường 1', 'Phường 2', 'Phường 4', 'Phường 5', 'Phường 8', 'Phường 12', 'Phường 13'],
        'Bình Thạnh' => ['Phường 1', 'Phường 2', 'Phường 3', 'Phường 5', 'Phường 11', 'Phường 13', 'Phường 15'],
        'Tân Bình' => ['Phường 1', 'Phường 2', 'Phường 3', 'Phường 4', 'Phường 5', 'Phường 7', 'Phường 10'],
        'Phú Nhuận' => ['Phường 1', 'Phường 2', 'Phường 3', 'Phường 4', 'Phường 5', 'Phường 9', 'Phường 10'],
    ];

    private $hnDistricts = [
        'Quận Ba Đình' => ['Phường Phúc Xá', 'Phường Trúc Bạch', 'Phường Vĩnh Phúc', 'Phường Cống Vị', 'Phường Liễu Giai', 'Phường Nguyễn Trung Trực'],
        'Quận Hoàn Kiếm' => ['Phường Hàng Bạc', 'Phường Hàng Đào', 'Phường Hàng Bồ', 'Phường Lý Thái Tổ', 'Phường Tràng Tiền'],
        'Quận Đống Đa' => ['Phường Cát Linh', 'Phường Văn Miếu', 'Phường Quốc Tử Giám', 'Phường Láng Thượng', 'Phường Ô Chợ Dừa'],
        'Quận Hai Bà Trưng' => ['Phường Nguyễn Du', 'Phường Bạch Đằng', 'Phường Phạm Đình Hổ', 'Phường Lê Đại Hành', 'Phường Đồng Nhân'],
        'Quận Cầu Giấy' => ['Phường Nghĩa Đô', 'Phường Nghĩa Tân', 'Phường Mai Dịch', 'Phường Dịch Vọng', 'Phường Quan Hoa'],
        'Quận Thanh Xuân' => ['Phường Nhân Chính', 'Phường Thượng Đình', 'Phường Khương Trung', 'Phường Khương Mai'],
    ];

    private $dnDistricts = [
        'Quận Hải Châu' => ['Phường Thạch Thang', 'Phường Hải Châu 1', 'Phường Hải Châu 2', 'Phường Phước Ninh', 'Phường Bình Hiên'],
        'Quận Thanh Khê' => ['Phường Tam Thuận', 'Phường Thanh Khê Tây', 'Phường Thanh Khê Đông', 'Phường An Khê'],
        'Quận Sơn Trà' => ['Phường Thọ Quang', 'Phường Nại Hiên Đông', 'Phường Mân Thái', 'Phường Phước Mỹ'],
        'Quận Ngũ Hành Sơn' => ['Phường Mỹ An', 'Phường Khuê Mỹ', 'Phường Hòa Quý', 'Phường Hòa Hải'],
    ];

    private $streets = ['Lê Lợi', 'Trần Hưng Đạo', 'Nguyễn Huệ', 'Hai Bà Trưng', 'Điện Biên Phủ', 'Nguyễn Văn Linh', 'Võ Văn Kiệt', 'Lý Thường Kiệt', 'Hoàng Văn Thụ', 'Phan Đình Phùng', 'Quang Trung', 'Lê Văn Sỹ', 'Cách Mạng Tháng Tám', 'Nguyễn Thị Minh Khai', 'Pasteur', 'Nam Kỳ Khởi Nghĩa', 'Đồng Khởi', 'Tôn Đức Thắng'];

    public function run(): void
    {
        $adminRole = Role::where('slug', Role::ADMIN)->first();

        if ($adminRole) {
            User::updateOrCreate(
                ['email' => 'admin@gmail.com'],
                [
                    'fullname' => 'Administrator',
                    'phone' => '0909999999',
                    'address' => '123 Admin Street, TP. Hồ Chí Minh',
                    'password' => Hash::make('12345@54321'),
                    'role_id' => $adminRole->id,
                    'status' => 1,
                    'language' => 'vi',
                    'birthday' => Carbon::now()->subYears(30)->format('Y-m-d'),
                ]
            );
        }

        $users = [];
        $password = Hash::make('12345@54321');
        $usedEmails = ['admin@gmail.com']; // Reserve admin email
        $usedPhones = ['0909999999']; // Reserve admin phone

        for ($i = 1; $i <= 1000; $i++) {
            $lastName = $this->lastNames[array_rand($this->lastNames)];
            $middleName = $this->middleNames[array_rand($this->middleNames)];
            $firstName = $this->firstNames[array_rand($this->firstNames)];
            $fullname = "{$lastName} {$middleName} {$firstName}";

            $emailName = $this->removeVietnameseTones(strtolower($firstName));
            $domain = $this->emailDomains[array_rand($this->emailDomains)];
            do {
                $emailNumber = rand(1, 9999);
                $email = "{$emailName}{$emailNumber}@{$domain}";
            } while (in_array($email, $usedEmails));
            $usedEmails[] = $email;

            do {
                $prefix = $this->phonePrefix[array_rand($this->phonePrefix)];
                $phone = $prefix . rand(1000000, 9999999);
            } while (in_array($phone, $usedPhones));
            $usedPhones[] = $phone;

            $address = $this->generateAddress();

            $createdAt = Carbon::now()->subDays(rand(1, 730));

            // Assign role_id: 90% users (2), 10% admins (1) for variety
            $roleId = (rand(1, 100) <= 90) ? 2 : 1;

            $users[] = [
                'fullname' => $fullname,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'password' => $password,
                'role_id' => $roleId,
                'status' => rand(1, 100) <= 90 ? 1 : 0,
                'language' => 'vi',
                'birthday' => Carbon::now()->subYears(rand(18, 60))->format('Y-m-d'),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            if (count($users) >= 100) {
                DB::table('users')->insert($users);
                $users = [];
            }
        }

        if (!empty($users)) {
            DB::table('users')->insert($users);
        }
    }

    private function generateAddress(): string
    {
        $cityType = rand(1, 3);

        if ($cityType === 1) {
            $district = array_rand($this->hcmDistricts);
            $ward = $this->hcmDistricts[$district][array_rand($this->hcmDistricts[$district])];
            $city = 'TP. Hồ Chí Minh';
        } elseif ($cityType === 2) {
            $district = array_rand($this->hnDistricts);
            $ward = $this->hnDistricts[$district][array_rand($this->hnDistricts[$district])];
            $city = 'Hà Nội';
        } else {
            $district = array_rand($this->dnDistricts);
            $ward = $this->dnDistricts[$district][array_rand($this->dnDistricts[$district])];
            $city = 'Đà Nẵng';
        }

        $houseNumber = rand(1, 999);
        $street = $this->streets[array_rand($this->streets)];

        return "{$houseNumber} {$street}, {$ward}, {$district}, {$city}";
    }

    private function removeVietnameseTones(string $str): string
    {
        $vietnamese = [
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ'
        ];

        $latin = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd'
        ];

        return str_replace($vietnamese, $latin, $str);
    }
}
