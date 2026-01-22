<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactConfirmDisplayTest extends TestCase
{
    use RefreshDatabase;

    private int $categoryId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryId = Category::create(['content' => 'その他'])->id;
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'last_name' => '山田',
            'first_name' => '太郎',
            'gender' => '1',
            'email' => 'test@example.com',
            'tel1' => '080',
            'tel2' => '1234',
            'tel3' => '5678',
            'address' => '東京都渋谷区',
            'building' => '',
            'category_id' => (string)$this->categoryId,
            'detail' => 'お問い合わせ内容テスト',
        ], $override);
    }

    /**
     * @dataProvider genderProvider
     */
    public function test_confirm_page_displays_required_fields_correctly(string $genderValue, string $expectedGenderText): void
    {
        $res = $this->post('/confirm', $this->payload([
            'gender' => $genderValue,
        ]));

        $res->assertStatus(200);

        // 1) お名前：姓名の間にスペース
        $res->assertSee('山田 太郎');

        // 2) 性別：男性/女性/その他の文字
        $res->assertSee($expectedGenderText);

        // 3) メール
        $res->assertSee('test@example.com');

        // 4) 電話番号：ハイフンなしで表示（08012345678）
        $res->assertSee('08012345678');

        // 5) 住所
        $res->assertSee('東京都渋谷区');

        // 7) お問い合わせの種類（categoriesのcontent）
        $res->assertSee('その他');

        // 8) お問い合わせ内容
        $res->assertSee('お問い合わせ内容テスト');
    }

    public static function genderProvider(): array
    {
        return [
            '男性' => ['1', '男性'],
            '女性' => ['2', '女性'],
            'その他' => ['3', 'その他'],
        ];
    }

    public function test_confirm_page_building_can_be_blank(): void
    {
        $res = $this->post('/confirm', $this->payload([
            'building' => '',
        ]));

        $res->assertStatus(200);

        // 「建物名」行のtdが空になることをHTMLで確認（エスケープ無効で検索）
        $res->assertSeeInOrder([
            '<th>建物名</th>',
            '<td></td>',
        ], false);
    }

    public function test_confirm_page_building_displays_when_filled(): void
    {
        $res = $this->post('/confirm', $this->payload([
            'building' => '千駄ヶ谷マンション101',
        ]));

        $res->assertStatus(200);
        $res->assertSee('千駄ヶ谷マンション101');
    }
}
