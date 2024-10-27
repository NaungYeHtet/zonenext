<?php

namespace Database\Seeders;

use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $propertiesWithType = json_decode(File::get(base_path('database/seeders/data/properties.json')), true);

        foreach ($propertiesWithType as $type => $properties) {
            $propertyType = PropertyType::from($type);

            foreach ($properties as $property) {
                Property::factory()->create([
                    'title' => $property['title'],
                    'description' => $property['description'],
                    'type' => $propertyType,
                ]);
            }
        }
    }
}
