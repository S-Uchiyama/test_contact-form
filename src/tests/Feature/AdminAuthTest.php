<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_requires_login(): void
    {
        $res = $this->get('/admin');
        $res->assertRedirect('/login');
    }

    public function test_admin_can_be_opened_after_login(): void
    {
        $user = User::create([
            'name' => 'テスト太郎',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $res = $this->actingAs($user)->get('/admin');

        $res->assertStatus(200);
        $res->assertSee('Admin');
    }
}
