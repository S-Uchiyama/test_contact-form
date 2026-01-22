<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_opened(): void
    {
        $res = $this->get('/login');

        $res->assertStatus(200);
        $res->assertSee('Login');
    }

    public function test_login_validation_messages_required(): void
    {
        $res = $this->from('/login')->post('/login', [
            'email' => '',
            'password' => '',
        ]);

        $res->assertRedirect('/login');
        $res->assertSessionHasErrors(['email', 'password']);

        $this->assertSame('メールアドレスを入力してください', session('errors')->first('email'));
        $this->assertSame('パスワードを入力してください', session('errors')->first('password'));
    }

    public function test_login_validation_messages_email_format(): void
    {
        $res = $this->from('/login')->post('/login', [
            'email' => 'aaa',
            'password' => 'password',
        ]);

        $res->assertRedirect('/login');
        $res->assertSessionHasErrors(['email']);
        $this->assertSame('メールアドレスはメール形式で入力してください', session('errors')->first('email'));
    }

    public function test_login_fails_with_wrong_credentials_and_shows_expected_message(): void
    {
        User::create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $res = $this->from('/login')->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $res->assertRedirect('/login');

        // resources/lang/ja/auth.php の failed が表示される想定
        $res->assertSessionHasErrors(['email']);
        $this->assertSame('ログイン情報が登録されていません', session('errors')->first('email'));

        $this->assertGuest();
    }

    public function test_user_can_login_and_is_redirected_to_admin(): void
    {
        User::create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $res = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $res->assertRedirect('/admin');
        $this->assertAuthenticated();

        $this->get('/admin')->assertStatus(200)->assertSee('Admin');
    }
}
