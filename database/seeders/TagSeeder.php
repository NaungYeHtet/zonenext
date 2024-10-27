<?php

namespace Database\Seeders;

use App\Enums\TagType;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tagList = json_decode(File::get(base_path('database/seeders/data/tags.json')), true);

        foreach ($tagList as $type => $tags) {
            foreach ($tags as $tag) {
                Tag::create([
                    'type' => TagType::from($type),
                    'name' => $tag['name'],
                    'icon' => "images/icons/{$tag['icon']}",
                ]);
            }
        }
    }
}
