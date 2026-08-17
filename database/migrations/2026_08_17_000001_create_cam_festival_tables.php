<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('parishes', function (Blueprint $t) {
            $t->id(); $t->string('name')->unique(); $t->string('code')->unique();
            $t->string('deanery')->nullable(); $t->string('patron_matron_name')->nullable();
            $t->string('patron_contact')->nullable(); $t->integer('camp_contingent_count')->default(25);
            $t->boolean('camp_checked_in')->default(false);
            $t->timestamps();
        });
        Schema::create('categories', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('slug')->unique();
            $t->string('type'); $t->string('theme')->nullable();
            $t->text('description')->nullable();
            $t->integer('allocated_minutes')->default(0);
            $t->integer('prep_minutes')->default(0);
            $t->integer('max_raw_score')->default(100);
            $t->json('judging_criteria')->nullable();
            $t->json('rules')->nullable();
            $t->timestamps();
        });
        Schema::create('schedule_items', function (Blueprint $t) {
            $t->id(); $t->date('event_date'); $t->string('day_name');
            $t->time('scheduled_start_time'); $t->time('scheduled_end_time');
            $t->string('venue')->default('Main Stage'); $t->string('activity_title');
            $t->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('parish_id')->nullable()->constrained()->nullOnDelete();
            $t->integer('performance_order')->nullable();
            $t->string('status')->default('scheduled');
            $t->integer('actual_duration_seconds')->default(0);
            $t->integer('time_penalty_marks')->default(0);
            $t->timestamps();
        });
        Schema::create('adjudication_scores', function (Blueprint $t) {
            $t->id();
            $t->foreignId('category_id')->constrained()->cascadeOnDelete();
            $t->foreignId('parish_id')->constrained()->cascadeOnDelete();
            $t->string('adjudicator_name');
            $t->string('conductor_name')->nullable();
            $t->string('director_producer')->nullable();
            $t->string('composer_author')->nullable();
            $t->string('language_used')->nullable();
            $t->integer('participant_count')->nullable();
            $t->string('item_title')->nullable();
            $t->json('song_titles_breakdown')->nullable();
            $t->json('criteria_scores');
            $t->decimal('raw_total_score', 5, 2);
            $t->decimal('normalized_score', 5, 2);
            $t->text('comments')->nullable();
            $t->boolean('is_disqualified')->default(false);
            $t->timestamps();
            $t->unique(['category_id', 'parish_id', 'adjudicator_name']);
        });
        Schema::create('consolidated_results', function (Blueprint $t) {
            $t->id(); $t->foreignId('category_id')->constrained()->cascadeOnDelete();
            $t->foreignId('parish_id')->constrained()->cascadeOnDelete();
            $t->decimal('adjudicators_average', 5, 2)->default(0);
            $t->decimal('time_penalty', 5, 2)->default(0);
            $t->decimal('final_score', 5, 2)->default(0);
            $t->integer('rank')->nullable(); $t->integer('championship_points')->default(0);
            $t->boolean('is_finalized')->default(false); $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('consolidated_results');
        Schema::dropIfExists('adjudication_scores');
        Schema::dropIfExists('schedule_items');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('parishes');
    }
};