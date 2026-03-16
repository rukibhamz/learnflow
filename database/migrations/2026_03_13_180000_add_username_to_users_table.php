<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('name');
        });

        // Backfill existing users with unique usernames
        $users = \DB::table('users')->get();
        foreach ($users as $user) {
            $base = Str::slug(explode('@', $user->email)[0]);
            $username = $base ?: 'user' . $user->id;
            $original = $username;
            $i = 1;
            while (\DB::table('users')->where('username', $username)->where('id', '!=', $user->id)->exists()) {
                $username = $original . $i;
                $i++;
            }
            \DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
