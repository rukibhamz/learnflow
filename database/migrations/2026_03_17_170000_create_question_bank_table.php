<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_bank', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('question', 2000);
            $table->string('type');
            $table->json('options')->nullable();
            $table->string('correct_answer');
            $table->text('explanation')->nullable();
            $table->string('category')->nullable();
            $table->integer('points')->default(1);
            $table->timestamps();

            $table->index(['course_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_bank');
    }
};
