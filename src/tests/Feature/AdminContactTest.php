<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminContactTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $cat1;
    private Category $cat2;

    protected function setUp(): void
    {
        parent::setUp();

        // 管理ユーザー作成
        $this->admin = User::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // カテゴリ作成
        $this->cat1 = Category::create(['content' => '商品のお届けについて']);
        $this->cat2 = Category::create(['content' => '商品の交換について']);
    }

    private function createContact(array $override = []): Contact
    {
        $base = [
            'category_id' => $this->cat1->id,
            'first_name'  => '太郎',
            'last_name'   => '山田',
            'gender'      => 1,
            'email'       => 'taro@example.com',
            'tel'         => '08012345678',
            'address'     => '東京都渋谷区',
            'building'    => 'テストビル101',
            'detail'      => 'お問い合わせ内容テスト',
        ];

        $data = array_merge($base, $override);

        $c = Contact::create($data);

        if (isset($override['created_at'])) {
            $c->created_at = $override['created_at'];
            $c->save();
        }

        return $c;
    }

    public function test_admin_index_shows_list_and_pagination_links(): void
    {
        // 8件作成 → 7件/ページ + ページ2リンクが出る想定
        for ($i = 1; $i <= 8; $i++) {
            $this->createContact([
                'last_name' => '山田' . $i,
                'email'     => "user{$i}@example.com",
            ]);
        }

        $this->actingAs($this->admin);

        $res = $this->get('/admin');

        $res->assertStatus(200);
        $res->assertSee('Admin');

        // 必要な列が表示されているか
        $res->assertSee('お名前');
        $res->assertSee('性別');
        $res->assertSee('メールアドレス');
        $res->assertSee('お問い合わせの種類');
        $res->assertSee('お問い合わせ内容');

        // 1ページ目にページネーション（page=2 のリンク）があること
        $res->assertSee('?page=2', false);
    }

    public function test_admin_search_by_name_and_email_partial_and_exact(): void
    {
        // 山田太郎 / 田中花子 の2件を用意
        $c1 = $this->createContact([
            'last_name'  => '山田',
            'first_name' => '太郎',
            'email'      => 'taro@example.com',
        ]);

        $c2 = $this->createContact([
            'last_name'  => '田中',
            'first_name' => '花子',
            'email'      => 'hanako@example.com',
        ]);

        $this->actingAs($this->admin);

        // 1) 名前の部分一致（「山田」）
        $res = $this->get('/admin?q=山田');
        $res->assertStatus(200);
        $res->assertSee('山田 太郎');
        $res->assertDontSee('田中 花子');

        // 2) フルネーム（スペースあり）で検索
        $res = $this->get('/admin?q=田中 花子');
        $res->assertStatus(200);
        $res->assertSee('田中 花子');
        $res->assertDontSee('山田 太郎');

        // 3) メールアドレス部分一致
        $res = $this->get('/admin?q=hanako@');
        $res->assertStatus(200);
        $res->assertSee('田中 花子');
        $res->assertDontSee('山田 太郎');
    }

    public function test_admin_search_by_gender_category_and_date(): void
    {
        // 性別・カテゴリ・日付違いで3件用意
        $c1 = $this->createContact([
            'last_name'   => '山田',
            'gender'      => 1,
            'category_id' => $this->cat1->id,
        ]);
        $c1->created_at = '2026-01-20 10:00:00';
        $c1->save();

        $c2 = $this->createContact([
            'last_name'   => '佐藤',
            'gender'      => 2,
            'category_id' => $this->cat2->id,
        ]);
        $c2->created_at = '2026-01-21 10:00:00';
        $c2->save();

        $c3 = $this->createContact([
            'last_name'   => '鈴木',
            'gender'      => 3,
            'category_id' => $this->cat1->id,
        ]);
        $c3->created_at = '2026-01-22 10:00:00';
        $c3->save();

        $this->actingAs($this->admin);

        // 性別：女性(2)
        $res = $this->get('/admin?gender=2');
        $res->assertStatus(200);
        $res->assertSee('佐藤 太郎');
        $res->assertDontSee('山田 太郎');
        $res->assertDontSee('鈴木 太郎');

        // カテゴリ：cat1
        $res = $this->get('/admin?category_id=' . $this->cat1->id);
        $res->assertStatus(200);
        $res->assertSee('山田 太郎');
        $res->assertSee('鈴木 太郎');
        $res->assertDontSee('佐藤 太郎');

        // 日付：2026-01-21 のものだけ
        $res = $this->get('/admin?date=2026-01-21');
        $res->assertStatus(200);
        $res->assertSee('佐藤 太郎');
        $res->assertDontSee('山田 太郎');
        $res->assertDontSee('鈴木 太郎');
    }

    public function test_admin_show_returns_json_for_modal_detail(): void
    {
        $contact = $this->createContact([
            'last_name'   => '山田',
            'first_name'  => '太郎',
            'gender'      => 1,
            'email'       => 'taro@example.com',
            'tel'         => '08012345678',
            'address'     => '東京都渋谷区',
            'building'    => 'テストビル101',
            'category_id' => $this->cat2->id,
            'detail'      => 'モーダル表示テスト',
        ]);

        $this->actingAs($this->admin);

        $res = $this->getJson('/admin/' . $contact->id);

        $res->assertStatus(200)
            ->assertJson([
                'id'          => $contact->id,
                'name'        => '山田 太郎',
                'gender'      => 1,
                'gender_text' => '男性',
                'email'       => 'taro@example.com',
                'tel'         => '08012345678',
                'address'     => '東京都渋谷区',
                'building'    => 'テストビル101',
                'category'    => $this->cat2->content,
                'detail'      => 'モーダル表示テスト',
            ]);
    }

    public function test_admin_can_delete_contact(): void
    {
        $contact = $this->createContact([
            'email' => 'delete-me@example.com',
        ]);

        $this->actingAs($this->admin);

        $this->assertDatabaseHas('contacts', [
            'id'    => $contact->id,
            'email' => 'delete-me@example.com',
        ]);

        $res = $this->from('/admin')->post('/admin/delete', [
            'id' => $contact->id,
        ]);

        $res->assertRedirect('/admin');

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }

    public function test_admin_export_outputs_csv_with_current_filter(): void
    {
        // 山田太郎（ヒットさせる） / 田中花子（フィルタで除外）
        $hit = $this->createContact([
            'last_name'  => '山田',
            'first_name' => '太郎',
            'email'      => 'yamada@example.com',
            'detail'     => 'エクスポート対象',
        ]);

        $other = $this->createContact([
            'last_name'  => '田中',
            'first_name' => '花子',
            'email'      => 'tanaka@example.com',
            'detail'     => 'エクスポート対象外',
        ]);

        $this->actingAs($this->admin);

        // q=山田 でフィルタした状態でエクスポート
        $res = $this->get('/admin/export?q=山田');

        $res->assertStatus(200);
        $res->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // StreamedResponse なので streamedContent() で中身取得
        $csv = $res->streamedContent();

        // BOMの可能性があるのでそのまま検索
        $this->assertStringContainsString('お名前,性別,メールアドレス', $csv);
        $this->assertStringContainsString('山田 太郎', $csv);
        $this->assertStringContainsString('yamada@example.com', $csv);
        $this->assertStringNotContainsString('田中 花子', $csv);
        $this->assertStringNotContainsString('tanaka@example.com', $csv);
    }
}
