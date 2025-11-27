<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageService;

class ProfileController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem trang cá nhân');
        }

        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    public function update(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }

        $user = Auth::user();

        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birthday' => 'nullable|date|before:today',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            if ($request->hasFile('image')) {
                $imageService = new ImageService();

                if (!$imageService->validateImage($request->file('image'))) {
                    return back()
                        ->withInput()
                        ->with('error', 'Invalid image file. Please check file size (max 2MB) and format (jpg, png, gif, webp).');
                }

                $user->image = $imageService->uploadAvatar($request->file('image'));
            }

            $user->fullname = $request->fullname;
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->birthday = $request->birthday;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin thành công!',
                'image_url' => $user->image_url
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại!'
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }

        try {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ], [
                'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
                'new_password.required' => 'Vui lòng nhập mật khẩu mới',
                'new_password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự',
                'new_password.confirmed' => 'Xác nhận mật khẩu không khớp',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu hiện tại không đúng'
            ], 400);
        }

        try {
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Đổi mật khẩu thành công!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại!'
            ], 500);
        }
    }
}
