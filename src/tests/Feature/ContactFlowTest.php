<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_confirm_page_can_be_opened_with_valid_input(): void
    {
        $category = Category::create(['content' => 'その他']);

        $res = $this->post('/confirm', [
            'last_name' => '山田',
            'first_name' => '太郎',
            'gender' => '1',
            'email' => 'test@example.com',
            'tel1' => '080',
            'tel2' => '1234',
            'tel3' => '5678',
            'address' => '東京都',
            'building' => '',
            'category_id' => $category->id,
            'detail' => 'テストです',
        ]);

        $res->assertStatus(200);
        $res->assertSee('Confirm');
        $res->assertSee('山田 太郎');
        $res->assertSee('test@example.com');
    }

    public function test_contact_can_be_saved_and_redirect_to_thanks(): void
    {
        $category = Category::create(['content' => 'その他']);

        $res = $this->post('/thanks', [
            'last_name' => '山田',
            'first_name' => '太郎',
            'gender' => '1',
            'email' => 'test@example.com',
            'tel1' => '080',
            'tel2' => '1234',
            'tel3' => '5678',
            'address' => '東京都',
            'building' => '',
            'category_id' => $category->id,
            'detail' => 'テストです',
        ]);

        $res->assertRedirect('/thanks');

        $this->assertDatabaseHas('contacts', [
            'last_name' => '山田',
            'first_name' => '太郎',
            'email' => 'test@example.com',
            'gender' => 1,
        ]);

        $this->assertSame(1, Contact::count());
    }
}
