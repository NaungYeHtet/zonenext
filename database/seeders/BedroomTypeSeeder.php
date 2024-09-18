<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BedroomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'en' => 'Single',
                'my' => 'တစ်ယောက်ခန်း',
            ],
            [
                'en' => 'Double',
                'my' => 'နှစ်ယောက်ခန်း',
            ],
            [
                'en' => 'Master',
                'my' => 'မာစတာခန်း',
            ],
        ];

        foreach ($types as $type) {
            \App\Models\BedroomType::create([
                'name' => $type,
            ]);
        }
    }
}
