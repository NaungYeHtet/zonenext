<?php

namespace Database\Seeders;

use App\Enums\GroupType;
use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            [
                'name' => [
                    'en' => 'Featured projects',
                    'my' => 'အိမ်ခြံမြေပရော့ဂျက်များ',
                ],
                'updatable' => true,
                'is_project' => true,
                'type' => GroupType::FeaturedProjects,
            ],
            [
                'name' => [
                    'en' => 'Top 10 projects',
                    'my' => 'ထိပ်တန်းပရော့ဂျက် ဆယ်ခု',
                ],
                'updatable' => true,
                'is_project' => true,
                'type' => GroupType::TopTenProjects,
            ],
            [
                'name' => [
                    'en' => 'Most popular properties',
                    'my' => 'လူကြိုက်များသေား အိမ်ခြံမြေအရောင်းများ',
                ],
                'updatable' => false,
                'is_project' => false,
                'type' => GroupType::MostPopularProperties,
            ],
            [
                'name' => [
                    'en' => 'Under construction',
                    'my' => 'ဆောက်လုပ်ဆဲ ပရော့ဂျက်များ',
                ],
                'updatable' => true,
                'is_project' => true,
                'type' => GroupType::UnderConstruction,
            ],
            [
                'name' => [
                    'en' => 'Top rated listings',
                    'my' => 'ထိပ်တန်းအဆင့် အိမ်ခြံမြေများ',
                ],
                'updatable' => true,
                'is_project' => false,
                'type' => GroupType::TopRatedListing,
            ],
        ];

        foreach ($groups as $group) {
            $exist = Group::where('name->en', $group['name']['en'])->first();

            if (! $exist) {
                if ($group['is_project'] && $group['updatable']) {
                    $projects = \App\Models\Project::inRandomOrder()->limit(rand(10, 15))->get();
                    Group::factory()->hasAttached($projects)->create([
                        'name' => $group['name'],
                        'updatable' => $group['updatable'],
                        'type' => $group['type'],
                    ]);
                } else {
                    $properties = \App\Models\Property::posted()->inRandomOrder()->limit(rand(10, 15))->get();
                    Group::factory()->hasAttached($properties)->create([
                        'name' => $group['name'],
                        'updatable' => $group['updatable'],
                        'type' => $group['type'],
                    ]);
                }
            }
        }
    }
}
