<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Admin & Judge Accounts for Filament Login
        User::updateOrCreate(
            ['email' => 'admin@camfestival.org'],
            [
                'name' => 'CAM Festival Executive Committee',
                'role' => 'admin',
                'judge_name' => null,
                'password' => bcrypt('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'judge1@camfestival.org'],
            [
                'name' => 'Judge 1 (Official Adjudicator)',
                'role' => 'judge',
                'judge_name' => 'Judge 1',
                'password' => bcrypt('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'judge2@camfestival.org'],
            [
                'name' => 'Judge 2 (Official Adjudicator)',
                'role' => 'judge',
                'judge_name' => 'Judge 2',
                'password' => bcrypt('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'judge3@camfestival.org'],
            [
                'name' => 'Judge 3 (Official Adjudicator)',
                'role' => 'judge',
                'judge_name' => 'Judge 3',
                'password' => bcrypt('password'),
            ]
        );

        // 2. Seed All CAM Festival Parishes, Categories & Schedule
        $this->call([
            CamFestivalSeeder::class,
        ]);
    }
}