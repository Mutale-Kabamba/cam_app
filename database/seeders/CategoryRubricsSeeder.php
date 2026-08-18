<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Services\OfficialCategoriesService;

class CategoryRubricsSeeder extends Seeder
{
    public function run(): void
    {
        $categories = OfficialCategoriesService::getOfficialCategories();

        foreach ($categories as $cData) {
            $cat = Category::where('slug', $cData['slug'])
                ->orWhere('name', $cData['name'])
                ->first();

            if ($cat) {
                $cat->update([
                    'name' => $cData['name'],
                    'slug' => $cData['slug'],
                    'type' => $cData['type'],
                    'allocated_minutes' => $cData['allocated_minutes'],
                    'prep_minutes' => $cData['prep_minutes'],
                    'max_raw_score' => $cData['max_raw_score'],
                    'theme' => $cData['theme'],
                    'description' => $cData['description'],
                    'rules' => $cData['rules'],
                    'judging_criteria' => $cData['judging_criteria'],
                ]);
            } else {
                Category::create($cData);
            }
        }
    }
}
