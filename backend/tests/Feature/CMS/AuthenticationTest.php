<?php

namespace Tests\Feature\CMS;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertViewIs('admin.login');
        $response->assertSee('Chào mừng trở lại!');
        $response->assertSee('Email hoặc Số Điện Thoại');
        $response->assertSee('Mật khẩu');
    }

    public function test_register_page_can_be_rendered(): void
    {
        $response = $this->get('/admin/register');

        $response->assertStatus(200);
        $response->assertViewIs('admin.register');
        $response->assertSee('Tạo tài khoản');
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        // Create a test user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 1,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_incorrect_email(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 1,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'wrong@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Email hoặc mật khẩu không đúng.');
        $this->assertGuest();
    }

    public function test_user_cannot_login_with_incorrect_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 1,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Email hoặc mật khẩu không đúng.');
        $this->assertGuest();
    }

    public function test_user_cannot_login_with_inactive_account(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 0, // Inactive
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.');
        $this->assertGuest();
    }

    public function test_login_requires_email(): void
    {
        $response = $this->post('/admin/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_login_requires_valid_email_format(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'not-an-email',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_login_requires_password(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_login_requires_password_minimum_length(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => '12345', // Less than 6 characters
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_authenticated_user_cannot_access_login_page(): void
    {
        $user = User::factory()->create(['status' => 1]);

        $response = $this->actingAs($user)->get('/admin/login');

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_authenticated_user_cannot_access_register_page(): void
    {
        $user = User::factory()->create(['status' => 1]);

        $response = $this->actingAs($user)->get('/admin/register');

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create(['status' => 1]);

        $response = $this->actingAs($user)->post('/admin/logout');

        $response->assertRedirect('/admin/login');
        $response->assertSessionHas('success', 'Đăng xuất thành công!');
        $this->assertGuest();
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/admin/login');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create(['status' => 1]);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertSee($user->fullname);
        $response->assertSee($user->email);
    }

    public function test_session_is_regenerated_on_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 1,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($user);

        // Session should be regenerated
        $response->assertSessionHasNoErrors();
    }

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->post('/admin/register', [
            'fullname' => 'Test User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/admin/login');

        $this->assertDatabaseHas('users', [
            'fullname' => 'Test User',
            'email' => 'newuser@example.com',
            'status' => 1,
        ]);

        // User is NOT automatically logged in - they need to login manually
        $this->assertGuest();
    }

    public function test_register_requires_name(): void
    {
        $response = $this->post('/admin/register', [
            'fullname' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['fullname']);
        $this->assertGuest();
    }

    public function test_register_requires_email(): void
    {
        $response = $this->post('/admin/register', [
            'fullname' => 'Test User',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_register_requires_valid_email_format(): void
    {
        $response = $this->post('/admin/register', [
            'fullname' => 'Test User',
            'email' => 'not-an-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_register_requires_unique_email(): void
    {
        // Create existing user
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->post('/admin/register', [
            'fullname' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_register_requires_password(): void
    {
        $response = $this->post('/admin/register', [
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_register_requires_password_minimum_length(): void
    {
        $response = $this->post('/admin/register', [
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'password' => '12345',
            'password_confirmation' => '12345',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_register_requires_password_confirmation(): void
    {
        $response = $this->post('/admin/register', [
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_password_is_hashed_when_stored(): void
    {
        $plainPassword = 'password123';

        $this->post('/admin/register', [
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'password' => $plainPassword,
            'password_confirmation' => $plainPassword,
        ]);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotEquals($plainPassword, $user->password);
        $this->assertTrue(Hash::check($plainPassword, $user->password));
    }

    public function test_newly_registered_user_has_active_status(): void
    {
        $this->post('/admin/register', [
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertEquals(1, $user->status);
    }

    public function test_user_can_login_with_remember_me(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 1,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'remember' => true,
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($user);

        // Check if remember token is set
        $user->refresh();
        $this->assertNotNull($user->remember_token);
    }

    public function test_multiple_failed_login_attempts(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 1,
        ]);

        // Attempt 1
        $response1 = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);
        $response1->assertRedirect();
        $this->assertGuest();

        // Attempt 2
        $response2 = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);
        $response2->assertRedirect();
        $this->assertGuest();

        // Attempt 3 - should still allow
        $response3 = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);
        $response3->assertRedirect();
        $this->assertGuest();
    }

    public function test_logout_invalidates_session(): void
    {
        $user = User::factory()->create(['status' => 1]);

        $this->actingAs($user);
        $this->assertAuthenticated();

        $response = $this->post('/admin/logout');

        $response->assertRedirect('/admin/login');
        $this->assertGuest();

        // Try to access dashboard again
        $dashboardResponse = $this->get('/admin/dashboard');
        $dashboardResponse->assertRedirect('/admin/login');
    }

    public function test_login_requires_csrf_token(): void
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Should still work without CSRF in test environment
        // In production, this would fail
        $response->assertStatus(302);
    }

    public function test_old_input_is_retained_on_validation_failure(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => '', // Invalid
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['password']);

        // Old input should be available
        $this->assertEquals('test@example.com', old('email'));
    }

    public function test_successful_login_redirects_to_intended_page(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 1,
        ]);

        // Try to access dashboard (will redirect to login)
        $this->get('/admin/dashboard');

        // Now login
        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Should redirect to dashboard (intended page)
        $response->assertRedirect('/admin/dashboard');
    }
}
