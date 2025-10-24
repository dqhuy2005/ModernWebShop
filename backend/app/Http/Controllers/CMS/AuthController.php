<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Repository\CartRepository;

class AuthController extends Controller
{
    protected $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard.index');
        }

        return view('login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không đúng định dạng',
            'password.required' => 'Mật khẩu không được để trống',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if ($user->status == 0) {
                Auth::logout();
                return redirect()->back()
                    ->with('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.')
                    ->withInput();
            }

            $request->session()->regenerate();

            $this->mergeSessionCartToDatabase($user->id);

            return redirect()->intended(route('home'));
        }

        return redirect()->back()
            ->with('error', 'Email hoặc mật khẩu không đúng.')
            ->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Session::forget('cart');
        Session::forget('cart_count');

        return redirect()->route('home');
    }

    protected function mergeSessionCartToDatabase($userId)
    {
        $sessionCart = Session::get('cart', []);

        if (empty($sessionCart)) {
            $cartCount = $this->cartRepository->findByUser($userId)->count();
            Session::put('cart_count', $cartCount);
            return;
        }

        foreach ($sessionCart as $item) {
            $existingCart = $this->cartRepository->findByUserAndProduct($userId, $item['product_id']);

            if ($existingCart) {
                $newQuantity = $existingCart->quantity + $item['quantity'];
                $this->cartRepository->updateQuantity($existingCart->id, $newQuantity);
            } else {
                $this->cartRepository->create([
                    'user_id' => $userId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }
        }

        Session::forget('cart');

        $cartCount = $this->cartRepository->findByUser($userId)->count();
        Session::put('cart_count', $cartCount);
    }

    public function showRegisterForm()
    {
        return view('cms.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'fullname.required' => 'Tên không được để trống',
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã được sử dụng',
            'password.required' => 'Mật khẩu không được để trống',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        User::create([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'phone' => $request->phone ?? null,
            'birthdate' => $request->birthdate ?? null,
            'role_id' => 2, // Role user
            'password' => Hash::make($request->password),
            'status' => 1,
        ]);

        return redirect()->route('login')
            ->with('success', 'Đăng ký thành công!');
    }
}
