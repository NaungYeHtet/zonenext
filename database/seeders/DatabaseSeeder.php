<?php

namespace Database\Seeders;

use App\Models\State;
use App\Models\Township;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $states = json_decode(File::get(base_path('database/seeders/data/states.json')), true);

        foreach ($states as $state) {
            State::create($state);
        }

        $townships = json_decode(File::get(base_path('database/seeders/data/townships.json')), true);

        foreach ($townships as $township) {
            Township::create($township);
        }
    }
}
