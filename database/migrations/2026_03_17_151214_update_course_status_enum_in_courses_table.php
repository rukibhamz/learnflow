<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite doesn't support MODIFY COLUMN — the enum constraint is
            // enforced at the application layer (CourseStatus enum), so no-op.
            return;
        }

        DB::statement("ALTER TABLE courses MODIFY COLUMN status ENUM('draft', 'pending', 'review', 'published', 'rejected', 'archived') NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE courses MODIFY COLUMN status ENUM('draft', 'review', 'published', 'archived') NOT NULL DEFAULT 'draft'");
    }
};
