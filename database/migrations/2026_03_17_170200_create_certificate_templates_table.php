<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('orientation')->default('landscape');
            $table->string('paper_size')->default('a4');
            $table->text('html_template');
            $table->json('variables')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'certificate_template_id')) {
                $table->foreignId('certificate_template_id')->nullable()->after('prerequisite_ids')
                    ->constrained('certificate_templates')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'certificate_template_id')) {
                $table->dropForeign(['certificate_template_id']);
                $table->dropColumn('certificate_template_id');
            }
        });
        Schema::dropIfExists('certificate_templates');
    }
};
