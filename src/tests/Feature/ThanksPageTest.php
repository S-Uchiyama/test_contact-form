<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThanksPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_thanks_page_can_be_opened(): void
    {
        $res = $this->get('/thanks');

        $res->assertStatus(200);
        $res->assertSee('お問い合わせありがとうございました');
        $res->assertSee('HOME');
    }

    public function test_thanks_page_home_link_goes_to_contact_form(): void
    {
        $res = $this->get('/thanks');

        // HOMEリンクが "/" を指していること（route('contact.create')）
        $res->assertSee('href="' . url('/') . '"', false);

        // 念のため、実際に "/" も 200 で開けることを確認
        $this->get('/')->assertStatus(200)->assertSee('Contact');
    }
}
