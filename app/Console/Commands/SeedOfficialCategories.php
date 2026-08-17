<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Services\OfficialCategoriesService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SeedOfficialCategories extends Command
{
    protected $signature = 'app:seed-categories
                            {--fresh : Drop and re-seed all categories}
                            {--force : Skip confirmation}';

    protected $description = 'Seed the 8 official CAM Festival 2026 competition categories with rubrics and rules';

    public function handle(): int
    {
        $this->info('──────────────────────────────────────────────────────────────────');
        $this->info('   CAM FESTIVAL 2026 — Official Category & Rubric Provisioning');
        $this->info('──────────────────────────────────────────────────────────────────');

        if ($this->option('fresh')) {
            if (!$this->option('force') && !$this->confirm('This will delete all existing categories and scores. Continue?', false)) {
                $this->warn('Aborted.');
                return Command::FAILURE;
            }
            Category::truncate();
            $this->warn('Existing categories cleared.');
        }

        $categories = OfficialCategoriesService::getOfficialCategories();
        $rows = [];

        foreach ($categories as $data) {
            $category = Category::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'type' => $data['type'],
                    'theme' => $data['theme'] ?? OfficialCategoriesService::THEME,
                    'description' => $data['description'],
                    'allocated_minutes' => $data['allocated_minutes'],
                    'prep_minutes' => $data['prep_minutes'],
                    'max_raw_score' => $data['max_raw_score'],
                    'judging_criteria' => $data['judging_criteria'],
                    'rules' => $data['rules'],
                ]
            );

            $rows[] = [
                $category->name,
                ucwords(str_replace('_', ' ', $category->type)),
                count($category->judging_criteria ?? []) . ' criteria',
                $category->max_raw_score . ' pts',
                $category->allocated_minutes > 0 ? $category->allocated_minutes . ' min' : 'Quiz',
            ];
        }

        $this->newLine();
        $this->table(
            ['Category Name', 'Type', 'Rubric Criteria', 'Max Score', 'Time'],
            $rows
        );
        $this->newLine();

        $this->components->info(count($categories) . ' official categories provisioned successfully.');
        $this->info('You can view and edit them in the Admin panel under: /admin/categories');
        $this->info('──────────────────────────────────────────────────────────────────');

        return Command::SUCCESS;
    }
}
