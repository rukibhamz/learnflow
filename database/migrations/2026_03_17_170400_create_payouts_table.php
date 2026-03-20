<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('platform_fee')->default(0);
            $table->string('status')->default('pending');
            $table->string('method')->default('manual');
            $table->string('stripe_transfer_id')->nullable();
            $table->text('notes')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['instructor_id', 'status']);
            $table->index('status');
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'revenue_share_percent')) {
                // Spatie roles are stored in pivot tables; `users.role` column does not exist.
                $table->unsignedTinyInteger('revenue_share_percent')->default(70);
            }
            if (!Schema::hasColumn('users', 'stripe_connect_id')) {
                $table->string('stripe_connect_id')->nullable()->after('revenue_share_percent');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['revenue_share_percent', 'stripe_connect_id']);
        });
    }
};
