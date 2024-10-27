<?php

namespace Database\Seeders;

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
            AgentSeeder::class,
        ]);

        $this->command->info('Seeding Agent factory....');
        \App\Models\Agent::factory(rand(30, 80))
            ->create();
        $this->command->info('Seeding Property factory....');
        Property::factory(200)->create();
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
