<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactValidationTest extends TestCase
{
    use RefreshDatabase;

    private int $categoryId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryId = Category::create(['content' => 'その他'])->id;
    }

    private function validPayload(array $override = []): array
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
            'detail' => 'テストです',
        ], $override);
    }

    /**
     * @dataProvider requiredMessageProvider
     */
    public function test_required_messages(array $override, string $field, string $expectedMessage): void
    {
        $res = $this->from('/')->post('/confirm', $this->validPayload($override));

        $res->assertRedirect('/');
        $res->assertSessionHasErrors([$field]);
        $this->assertSame($expectedMessage, session('errors')->first($field));
    }

    public static function requiredMessageProvider(): array
    {
        return [
            '姓 required' => [['last_name' => ''], 'last_name', '姓を入力してください'],
            '名 required' => [['first_name' => ''], 'first_name', '名を入力してください'],
            '性別 required' => [['gender' => null], 'gender', '性別を選択してください'],
            'メール required' => [['email' => ''], 'email', 'メールアドレスを入力してください'],
            '電話 tel1 required' => [['tel1' => ''], 'tel1', '電話番号を入力してください'],
            '電話 tel2 required' => [['tel2' => ''], 'tel2', '電話番号を入力してください'],
            '電話 tel3 required' => [['tel3' => ''], 'tel3', '電話番号を入力してください'],
            '住所 required' => [['address' => ''], 'address', '住所を入力してください'],
            '種類 required' => [['category_id' => ''], 'category_id', 'お問い合わせの種類を選択してください'],
            '内容 required' => [['detail' => ''], 'detail', 'お問い合わせ内容を入力してください'],
        ];
    }

    public function test_email_format_message(): void
    {
        $res = $this->from('/')->post('/confirm', $this->validPayload([
            'email' => 'aaa',
        ]));

        $res->assertRedirect('/');
        $res->assertSessionHasErrors(['email']);
        $this->assertSame('メールアドレスはメール形式で入力してください', session('errors')->first('email'));
    }

    public function test_gender_invalid_message(): void
    {
        $res = $this->from('/')->post('/confirm', $this->validPayload([
            'gender' => '9',
        ]));

        $res->assertRedirect('/');
        $res->assertSessionHasErrors(['gender']);
        $this->assertSame('性別を選択してください', session('errors')->first('gender'));
    }

    public function test_category_not_exists_message(): void
    {
        $res = $this->from('/')->post('/confirm', $this->validPayload([
            'category_id' => '999999',
        ]));

        $res->assertRedirect('/');
        $res->assertSessionHasErrors(['category_id']);
        $this->assertSame('お問い合わせの種類を選択してください', session('errors')->first('category_id'));
    }

    /**
     * @dataProvider telRegexProvider
     */
    public function test_tel_regex_message(array $override, string $field): void
    {
        $res = $this->from('/')->post('/confirm', $this->validPayload($override));

        $res->assertRedirect('/');
        $res->assertSessionHasErrors([$field]);
        $this->assertSame('電話番号は 半角英数字で入力してください', session('errors')->first($field));
    }

    public static function telRegexProvider(): array
    {
        return [
            'tel1 全角' => [['tel1' => '１２３'], 'tel1'],
            'tel2 全角' => [['tel2' => '１２３'], 'tel2'],
            'tel3 全角' => [['tel3' => '１２３'], 'tel3'],
        ];
    }

    /**
     * @dataProvider telTooLongProvider
     */
    public function test_tel_digits_between_message(array $override, string $field): void
    {
        $res = $this->from('/')->post('/confirm', $this->validPayload($override));

        $res->assertRedirect('/');
        $res->assertSessionHasErrors([$field]);
        $this->assertSame('電話番号は 5桁まで数字で入力してください', session('errors')->first($field));
    }

    public static function telTooLongProvider(): array
    {
        return [
            'tel1 6桁' => [['tel1' => '123456'], 'tel1'],
            'tel2 6桁' => [['tel2' => '123456'], 'tel2'],
            'tel3 6桁' => [['tel3' => '123456'], 'tel3'],
        ];
    }

    public function test_detail_max_120_message(): void
    {
        $res = $this->from('/')->post('/confirm', $this->validPayload([
            'detail' => str_repeat('あ', 121),
        ]));

        $res->assertRedirect('/');
        $res->assertSessionHasErrors(['detail']);
        $this->assertSame('お問い合わせ内容は120文字以内で入力してください', session('errors')->first('detail'));
    }

    public function test_name_max_8_rule_is_applied(): void
    {
        $res = $this->from('/')->post('/confirm', $this->validPayload([
            'last_name' => 'あいうえおかきくけ', // 9文字
        ]));

        $res->assertRedirect('/');
        $res->assertSessionHasErrors(['last_name']);
    }

    public function test_building_is_optional(): void
    {
        $res = $this->post('/confirm', $this->validPayload([
            'building' => '',
        ]));

        $res->assertStatus(200);
        $res->assertSee('Confirm');
    }
}
