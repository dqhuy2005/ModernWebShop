<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Services\User\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(
        private ProfileService $profileService
    ) {
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem trang cá nhân');
        }

        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    public function update(ProfileUpdateRequest $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }

        try {
            $result = $this->profileService->updateProfile(
                Auth::id(),
                $request->validated(),
                $request->file('image')
            );

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
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

            $this->profileService->changePassword(
                Auth::id(),
                $request->current_password,
                $request->new_password
            );

            return response()->json([
                'success' => true,
                'message' => 'Đổi mật khẩu thành công!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
