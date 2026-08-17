<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Parish;
use App\Models\ScheduleItem;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndJudgeSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_public_pages_are_accessible_and_show_single_login_button()
    {
        $response = $this->get('/program');
        $response->assertStatus(200);
        $response->assertSee('Official Login');
        $response->assertDontSee('Judge Workstation');

        $response = $this->get('/leaderboard');
        $response->assertStatus(200);
        $response->assertSee('Official Login');
    }

    public function test_parishes_page_displays_registered_parishes_and_filters_by_check_in_status()
    {
        $checkedInParish = Parish::create([
            'name' => "St. Theresa's Cathedral",
            'code' => 'STC',
            'deanery' => 'Livingstone Deanery',
            'camp_contingent_count' => 30,
            'camp_checked_in' => true,
        ]);

        $pendingParish = Parish::create([
            'name' => 'Kazungula Parish',
            'code' => 'KZP',
            'deanery' => 'Livingstone Deanery',
            'camp_contingent_count' => 25,
            'camp_checked_in' => false,
        ]);

        // Default: both appear with their respective status badges
        $response = $this->get('/registration');
        $response->assertStatus(200);
        $response->assertSee("St. Theresa's Cathedral");
        $response->assertSee("Kazungula Parish");
        $response->assertSee("Checked In");
        $response->assertSee("Pending Arrival");

        // Filter: checked_in only
        $checkedResponse = $this->get('/registration?status=checked_in');
        $checkedResponse->assertStatus(200);
        $checkedResponse->assertSee("St. Theresa's Cathedral");
        $checkedResponse->assertDontSee("Kazungula Parish");
    }

    public function test_judge_portal_is_not_public_and_redirects_guests_to_login()
    {
        $response = $this->get('/judge');
        $response->assertRedirect('/login');
    }

    public function test_main_login_panel_renders_filament_sign_in()
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertSee('CAM Festival 2026');
        $response->assertSee('Sign in');
    }

    public function test_judge_can_access_filament_dashboard_and_judge_workstation()
    {
        $judge = User::where('email', 'judge1@camfestival.org')->first();

        // Judge accessing Filament dashboard directly succeeds and sees Judge Widget
        $dashboardResponse = $this->actingAs($judge)->get('/admin');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Judge 1', false);
        $dashboardResponse->assertSee('Judge Workstation', false);

        // Judge accessing Filament Judge Workstation page succeeds
        $workstationResponse = $this->actingAs($judge)->get('/admin/judge-workstation');
        $workstationResponse->assertStatus(200);
        $workstationResponse->assertSee('Festival Adjudication & Scoring Console', false);
        $workstationResponse->assertDontSee('Audit Judge:');

        // Verify Judges cannot see or manage other admin resources
        $this->assertFalse(\App\Filament\Resources\ScheduleItemResource::canViewAny());
        $this->assertFalse(\App\Filament\Resources\ParishResource::canViewAny());
        $this->assertFalse(\App\Filament\Resources\CategoryResource::canViewAny());
        $this->assertFalse(\App\Filament\Resources\AdjudicationScoreResource::canViewAny());
        $this->assertFalse(\App\Filament\Resources\ConsolidatedResultResource::canViewAny());
        $this->assertFalse(\App\Filament\Resources\JudgeAssignmentResource::canViewAny());
    }

    public function test_admin_can_assign_judges_and_cannot_access_judge_workstation()
    {
        $admin = User::where('email', 'admin@camfestival.org')->first();
        $judge = User::where('email', 'judge1@camfestival.org')->first();

        // Admin accessing /admin dashboard succeeds
        $adminResponse = $this->actingAs($admin)->get('/admin');
        $adminResponse->assertStatus(200);

        // Admin can access Judge Assignments resource
        $assignmentsResponse = $this->actingAs($admin)->get('/admin/judge-assignments');
        $assignmentsResponse->assertStatus(200);
        $assignmentsResponse->assertSee('Judge 1', false);
        $assignmentsResponse->assertSee('Judge 2', false);
        // Admin is restricted from Judge Workstation scoring console (canAccess is false)
        auth()->login($admin);
        $this->assertFalse(\App\Filament\Pages\JudgeWorkstation::canAccess());

        // Judge can access Judge Workstation scoring console
        auth()->login($judge);
        $this->assertTrue(\App\Filament\Pages\JudgeWorkstation::canAccess());

        // Judge is restricted from Judge Assignments manager (canViewAny is false)
        $this->assertFalse(\App\Filament\Resources\JudgeAssignmentResource::canViewAny());
    }

    public function test_program_page_includes_monday_in_day_filter_and_shows_monday_events()
    {
        ScheduleItem::create([
            'event_date' => '2026-08-17',
            'day_name' => 'Monday',
            'scheduled_start_time' => '08:00:00',
            'scheduled_end_time' => '12:00:00',
            'venue' => 'Main Gate & Campsite Desk',
            'activity_title' => 'Parish Contingents Arrival, Accreditation & Campsite Check-In',
            'status' => 'completed',
        ]);

        $response = $this->get('/program?day_name=Monday');
        $response->assertStatus(200);
        $response->assertSee('Monday');
        $response->assertSee('Parish Contingents Arrival', false);
    }

    public function test_admin_parishes_create_page_includes_deaneries_and_parish_name_input()
    {
        $admin = User::where('email', 'admin@camfestival.org')->first();

        // Parishes list table
        $listResponse = $this->actingAs($admin)->get('/admin/parishes');
        $listResponse->assertStatus(200);

        // Parishes create page with datalist suggestions and categories selection
        $createResponse = $this->actingAs($admin)->get('/admin/parishes/create');
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Livingstone Deanery');
        $createResponse->assertSee('Sesheke Deanery');
        $createResponse->assertSee('Sioma Deanery');
        $createResponse->assertSee('St. Theresa', false);
        $createResponse->assertSee('Competition Participation');
        $createResponse->assertSee('Participating Categories');
    }

    public function test_admin_can_create_parish_with_selected_participating_categories()
    {
        $admin = User::where('email', 'admin@camfestival.org')->first();
        $category = Category::create([
            'name' => 'Choir Music',
            'slug' => 'choir-music',
            'type' => 'stage_performance',
            'max_raw_score' => 100,
        ]);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\ParishResource\Pages\CreateParish::class)
            ->fillForm([
                'name' => 'St. Jude Parish',
                'code' => 'SJP',
                'deanery' => 'Livingstone Deanery',
                'male_count' => 15,
                'female_count' => 10,
                'participating_categories' => [$category->id],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('parishes', [
            'name' => 'St. Jude Parish',
            'code' => 'SJP',
            'camp_contingent_count' => 25,
        ]);

        $createdParish = Parish::where('code', 'SJP')->first();
        $this->assertContains($category->id, $createdParish->participating_categories);
    }

    public function test_judge_workstation_filters_parishes_by_participating_categories()
    {
        $judge = User::where('email', 'judge1@camfestival.org')->first();

        $choirCat = Category::create([
            'name' => 'Choir Music',
            'slug' => 'choir',
            'type' => 'stage_performance',
            'max_raw_score' => 100,
        ]);

        $dramaCat = Category::create([
            'name' => 'Drama',
            'slug' => 'drama',
            'type' => 'stage_performance',
            'max_raw_score' => 100,
        ]);

        $poetryCat = Category::create([
            'name' => 'Poetry',
            'slug' => 'poetry',
            'type' => 'stage_performance',
            'max_raw_score' => 100,
        ]);

        // Parish 1 is only in Choir
        $choirParish = Parish::create([
            'name' => 'St. Theresa Choir Only',
            'code' => 'STC',
            'deanery' => 'Livingstone Deanery',
            'participating_categories' => [$choirCat->id],
        ]);

        // Parish 2 is only in Drama
        $dramaParish = Parish::create([
            'name' => 'Holy Cross Drama Only',
            'code' => 'HCD',
            'deanery' => 'Livingstone Deanery',
            'participating_categories' => [$dramaCat->id],
        ]);

        // Test Choir Category: only choirParish should appear
        $choirView = \Livewire\Livewire::actingAs($judge)
            ->test(\App\Filament\Pages\JudgeWorkstation::class)
            ->call('selectCategory', $choirCat->id)
            ->assertSee('St. Theresa Choir Only')
            ->assertDontSee('Holy Cross Drama Only');

        // Test Drama Category: only dramaParish should appear
        $dramaView = \Livewire\Livewire::actingAs($judge)
            ->test(\App\Filament\Pages\JudgeWorkstation::class)
            ->call('selectCategory', $dramaCat->id)
            ->assertSee('Holy Cross Drama Only')
            ->assertDontSee('St. Theresa Choir Only');

        // Test Poetry Category: neither should appear
        $poetryView = \Livewire\Livewire::actingAs($judge)
            ->test(\App\Filament\Pages\JudgeWorkstation::class)
            ->call('selectCategory', $poetryCat->id)
            ->assertDontSee('St. Theresa Choir Only')
            ->assertDontSee('Holy Cross Drama Only')
            ->assertSee('No Parishes Scheduled');
    }

    public function test_judge_can_submit_score_via_filament_workstation()
    {
        $judge = User::where('email', 'judge1@camfestival.org')->first();

        $category = Category::create([
            'name' => 'Choir Music (Melody)',
            'slug' => 'choir',
            'type' => 'stage_performance',
            'max_raw_score' => 100,
            'allocated_minutes' => 30,
            'prep_minutes' => 5,
        ]);

        $parish = Parish::create([
            'name' => "St. Theresa's Cathedral",
            'code' => 'STC',
            'deanery' => 'Livingstone Deanery',
            'camp_contingent_count' => 30,
            'camp_checked_in' => true,
        ]);

        \Livewire\Livewire::actingAs($judge)
            ->test(\App\Filament\Pages\JudgeWorkstation::class)
            ->call('selectCategory', $category->id)
            ->call('openScoreModal', $parish->id)
            ->set('itemTitle', 'Magnificat')
            ->set('conductorName', 'John Banda')
            ->set('criteriaScores.1', 5)
            ->set('criteriaScores.2', 10)
            ->set('criteriaScores.3', 10)
            ->set('criteriaScores.4', 10)
            ->set('criteriaScores.5', 10)
            ->set('criteriaScores.6', 10)
            ->set('criteriaScores.7', 5)
            ->set('criteriaScores.8', 5)
            ->set('criteriaScores.9', 10)
            ->set('criteriaScores.10', 5)
            ->call('saveScore')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('adjudication_scores', [
            'category_id' => $category->id,
            'parish_id' => $parish->id,
            'adjudicator_name' => 'Judge 1',
            'item_title' => 'Magnificat',
            'raw_total_score' => 80.00,
        ]);
    }

    public function test_parish_headcount_auto_calculates_total_from_male_and_female_counts()
    {
        $parish = Parish::create([
            'name' => 'Holy Cross Parish',
            'code' => 'HCP',
            'deanery' => 'Livingstone Deanery',
            'male_count' => 18,
            'female_count' => 14,
            'camp_checked_in' => true,
        ]);

        $this->assertEquals(32, $parish->camp_contingent_count);

        $response = $this->get('/registration');
        $response->assertStatus(200);
        $response->assertSee('Holy Cross Parish');
        $response->assertSee('32');
        $response->assertSee('18');
        $response->assertSee('14');
    }

    public function test_admin_can_create_another_admin_or_judge_account()
    {
        $admin = User::where('email', 'admin@camfestival.org')->first();

        // Create new Admin via Eloquent (or Filament)
        $newAdmin = User::create([
            'name' => 'Rev. Fr. Co-Admin',
            'email' => 'coadmin@camfestival.org',
            'role' => 'admin',
            'password' => 'secret123',
        ]);

        $this->assertTrue($newAdmin->isAdmin());
        $this->assertFalse($newAdmin->isJudge());

        // Create new Judge
        $newJudge = User::create([
            'name' => 'Mr. Senior Adjudicator',
            'email' => 'adjudicator@camfestival.org',
            'role' => 'judge',
            'judge_name' => 'Judge 2',
            'password' => 'secret123',
        ]);

        $this->assertTrue($newJudge->isJudge());
        $this->assertEquals('Judge 2', $newJudge->getJudgeName());

        $response = $this->actingAs($admin)->get('/admin/judge-assignments');
        $response->assertStatus(200);
        $response->assertSee('coadmin@camfestival.org');
        $response->assertSee('adjudicator@camfestival.org');
    }

    public function test_logout_redirects_to_program()
    {
        $judge = User::where('email', 'judge1@camfestival.org')->first();

        $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->actingAs($judge)
            ->post('/logout');
        $response->assertRedirect(route('program.index'));
        $this->assertGuest();
    }
}

