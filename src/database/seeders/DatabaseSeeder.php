<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. カテゴリ5件
        $this->call(CategorySeeder::class);

        // 2. contactsテーブルにダミーデータ35件
        Contact::factory()->count(35)->create();
    }
}
