<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('parishes', function (Blueprint $table) {
            if (!Schema::hasColumn('parishes', 'male_count')) {
                $table->integer('male_count')->default(0)->after('patron_contact');
            }
            if (!Schema::hasColumn('parishes', 'female_count')) {
                $table->integer('female_count')->default(0)->after('male_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('parishes', function (Blueprint $table) {
            if (Schema::hasColumn('parishes', 'male_count')) {
                $table->dropColumn('male_count');
            }
            if (Schema::hasColumn('parishes', 'female_count')) {
                $table->dropColumn('female_count');
            }
        });
    }
};
