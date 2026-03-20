<?php

namespace Tests\Feature;

use App\Models\SearchLog;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_search_log_records_term()
    {
        SearchLog::log('laravel', 15, null);

        $this->assertDatabaseHas('search_logs', [
            'term' => 'laravel',
            'results_count' => 15,
        ]);
    }

    public function test_search_log_normalizes_term()
    {
        SearchLog::log('  Laravel  ', 10, null);

        $this->assertDatabaseHas('search_logs', ['term' => 'laravel']);
    }

    public function test_search_log_tracks_user()
    {
        $user = User::factory()->create();
        SearchLog::log('php', 5, $user->id);

        $this->assertDatabaseHas('search_logs', [
            'term' => 'php',
            'user_id' => $user->id,
        ]);
    }

    public function test_popular_terms_returns_top_results()
    {
        for ($i = 0; $i < 5; $i++) {
            SearchLog::log('laravel', 10);
        }
        for ($i = 0; $i < 3; $i++) {
            SearchLog::log('php', 8);
        }
        SearchLog::log('react', 2);

        $popular = SearchLog::popularTerms(2, 30);

        $this->assertCount(2, $popular);
        $this->assertEquals(5, $popular->first());
    }

    public function test_popular_terms_respects_date_range()
    {
        $old = SearchLog::create([
            'term' => 'old search',
            'results_count' => 10,
        ]);
        $old->forceFill(['created_at' => now()->subDays(60)])->save();

        SearchLog::log('recent search', 5);

        $popular = SearchLog::popularTerms(10, 30);

        $this->assertArrayNotHasKey('old search', $popular->toArray());
        $this->assertArrayHasKey('recent search', $popular->toArray());
    }

    public function test_admin_search_analytics_page_loads()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.search-analytics'));
        $response->assertOk();
    }
}
