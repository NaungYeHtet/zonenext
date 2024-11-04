<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = json_decode(File::get(base_path('database/seeders/data/faqs.json')), true);

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
