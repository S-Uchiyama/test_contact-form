<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_in_user_can_logout(): void
    {
        // まずユーザー作成してログイン状態にする
        $user = User::create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        // ログイン中に /admin が見えることを確認（前提確認）
        $this->get('/admin')
            ->assertStatus(200)
            ->assertSee('Admin');

        // logout 実行（ヘッダーのフォームは POST /logout なのでそれに合わせる）
        $res = $this->post('/logout');

        // 一旦どこかにリダイレクトされる（Fortifyのデフォルト）
        $res->assertStatus(302);

        // ログアウトしていること
        $this->assertGuest();

        // ログアウト後は /admin にアクセスできず、ログイン画面に飛ばされる想定
        $this->get('/admin')
            ->assertRedirect('/login');
    }

    public function test_guest_cannot_access_admin_and_is_redirected_to_login(): void
    {
        $this->assertGuest();

        $this->get('/admin')
            ->assertRedirect('/login');
    }
}
