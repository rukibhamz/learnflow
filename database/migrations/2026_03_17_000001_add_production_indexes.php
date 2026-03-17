<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->index(['user_id', 'course_id', 'completed_at'], 'enrollments_user_course_completed_at_idx');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'courses_status_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex('enrollments_user_course_completed_at_idx');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex('courses_status_created_at_idx');
        });
    }
};

