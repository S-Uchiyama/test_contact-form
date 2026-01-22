<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_can_be_opened(): void
    {
        $res = $this->get('/register');

        $res->assertStatus(200);
        $res->assertSee('Register');
    }

    public function test_register_validation_messages_required(): void
    {
        $res = $this->from('/register')->post('/register', [
            'name' => '',
            'email' => '',
            'password' => '',
        ]);

        $res->assertRedirect('/register');
        $res->assertSessionHasErrors(['name', 'email', 'password']);

        $this->assertSame('お名前を入力してください', session('errors')->first('name'));
        $this->assertSame('メールアドレスを入力してください', session('errors')->first('email'));
        $this->assertSame('パスワードを入力してください', session('errors')->first('password'));
    }

    public function test_register_validation_messages_email_format(): void
    {
        $res = $this->from('/register')->post('/register', [
            'name' => '山田太郎',
            'email' => 'aaa',
            'password' => 'password',
        ]);

        $res->assertRedirect('/register');
        $res->assertSessionHasErrors(['email']);
        $this->assertSame('メールアドレスはメール形式で入力してください', session('errors')->first('email'));
    }

    public function test_user_can_register_and_is_redirected_to_admin_and_password_is_hashed(): void
    {
        $plainPassword = 'password';

        $res = $this->post('/register', [
            'name' => '山田太郎',
            'email' => 'test@example.com',
            'password' => $plainPassword,
        ]);

        // Fortify の home は config/fortify.php で '/admin'
        $res->assertRedirect('/admin');

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name' => '山田太郎',
            'email' => 'test@example.com',
        ]);

        $user = User::where('email', 'test@example.com')->firstOrFail();

        // パスワードがハッシュ化されていること
        $this->assertNotSame($plainPassword, $user->password);
        $this->assertTrue(Hash::check($plainPassword, $user->password));

        // 登録後に管理画面が開けること（ログイン済み）
        $this->actingAs($user)->get('/admin')->assertStatus(200)->assertSee('Admin');
    }
}
