<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\Project;
use App\Models\Property;
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

        $this->call([
            TagSeeder::class,
            BedroomTypeSeeder::class,
            ShieldSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
        ]);

        $this->command->info('Seeding Agent factory....');
        \App\Models\Admin::factory(rand(30, 80))
            ->role('Agent')
            ->create();
        $this->command->info('Seeding Lead factory....');
        Lead::factory(rand(500, 700))->create();
        $this->command->info('Seeding Property factory....');
        Property::factory(rand(200, 300))->create();
        // $this->call([
        //     PropertySeeder::class,
        // ]);
        $this->command->info('Seeding Project factory....');
        Project::factory(50)->create();

        $this->call([
            GroupSeeder::class,
        ]);
    }
}
