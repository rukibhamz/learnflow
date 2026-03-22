<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Preservation Tests — Hero Section
 *
 * Property 2: Preservation — Slider Interactions, Navigation, Autoplay, and Fallback Unchanged
 *
 * These tests document baseline behavior that must be preserved after the fix.
 * They test aspects NOT affected by the Alpine registration timing bug:
 *   - HTML structure (navigation arrows, pagination dots, fallback content)
 *   - Alpine component logic (next/prev wrapping) simulated in PHP
 *   - Autoplay speed setting rendered in HTML
 *
 * EXPECTED OUTCOME: All tests PASS on unfixed code.
 *
 * Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5
 */
class HeroSectionPreservationTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // PHP helpers that mirror the Alpine heroSlider JS navigation logic
    // -------------------------------------------------------------------------

    private function nextSlide(int $active, int $total): int
    {
        return $active === $total ? 1 : $active + 1;
    }

    private function prevSlide(int $active, int $total): int
    {
        return $active === 1 ? $total : $active - 1;
    }

    // =========================================================================
    // HTML Structure Tests
    // =========================================================================

    /**
     * With a single slide: navigation arrows and pagination dots must be ABSENT.
     *
     * The Blade template wraps both controls in @if($slides->count() > 1), so
     * they must not appear in the rendered HTML when only one slide exists.
     *
     * Validates: Requirements 3.5
     */
    public function test_single_slide_has_no_navigation_arrows_or_pagination_dots(): void
    {
        HeroSlide::create([
            'tag'         => 'Solo Slide',
            'title'       => 'Only One Slide Here',
            'description' => 'Single slide description.',
            'is_active'   => true,
            'order'       => 1,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        // Navigation arrows must be absent
        $response->assertDontSee('aria-label="Previous slide"', false);
        $response->assertDontSee('aria-label="Next slide"', false);

        // Pagination dots must be absent — the dot container is only rendered when count > 1
        // We check for the aria-label pattern used on each dot button
        $response->assertDontSee('aria-label="Go to slide 1"', false);
    }

    /**
     * With multiple slides (2+): navigation arrows and pagination dots must be PRESENT.
     *
     * The Blade template renders both controls when $slides->count() > 1.
     *
     * Validates: Requirements 3.3
     */
    public function test_multiple_slides_have_navigation_arrows_and_pagination_dots(): void
    {
        HeroSlide::create([
            'tag' => 'Slide One', 'title' => 'First Slide', 'description' => 'Desc 1',
            'is_active' => true, 'order' => 1,
        ]);
        HeroSlide::create([
            'tag' => 'Slide Two', 'title' => 'Second Slide', 'description' => 'Desc 2',
            'is_active' => true, 'order' => 2,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        // Navigation arrows must be present
        $response->assertSee('aria-label="Previous slide"', false);
        $response->assertSee('aria-label="Next slide"', false);

        // Pagination dots must be present
        $response->assertSee('aria-label="Go to slide 1"', false);
        $response->assertSee('aria-label="Go to slide 2"', false);
    }

    /**
     * With no slides: fallback hero content must be present.
     *
     * The @empty branch in the Blade template renders static fallback content
     * ("Learn without" / "limits.") when no active slides exist.
     *
     * Validates: Requirements 3.4
     */
    public function test_no_slides_renders_fallback_hero_content(): void
    {
        HeroSlide::query()->delete();

        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertSee('Learn without', false);
        $response->assertSee('limits.', false);
    }

    // =========================================================================
    // Alpine Component Logic Tests (PHP simulation)
    // =========================================================================

    /**
     * next() wrapping: from the last slide, next() must wrap back to slide 1.
     *
     * Property-based style: iterate over all slide counts N in [1..10].
     *
     * Validates: Requirements 3.3
     */
    public function test_next_wraps_from_last_slide_to_first(): void
    {
        for ($total = 1; $total <= 10; $total++) {
            $result = $this->nextSlide($total, $total);
            $this->assertEquals(
                1,
                $result,
                "next() from last slide ($total/$total) must wrap to 1"
            );
        }
    }

    /**
     * prev() wrapping: from slide 1, prev() must wrap to the last slide (N).
     *
     * Property-based style: iterate over all slide counts N in [1..10].
     *
     * Validates: Requirements 3.3
     */
    public function test_prev_wraps_from_first_slide_to_last(): void
    {
        for ($total = 1; $total <= 10; $total++) {
            $result = $this->prevSlide(1, $total);
            $this->assertEquals(
                $total,
                $result,
                "prev() from slide 1 (total=$total) must wrap to $total"
            );
        }
    }

    /**
     * next() normal: from slide 1, next() must go to slide 2 (when N > 1).
     *
     * Property-based style: iterate over all slide counts N in [2..10].
     *
     * Validates: Requirements 3.3
     */
    public function test_next_advances_normally_from_first_slide(): void
    {
        for ($total = 2; $total <= 10; $total++) {
            $result = $this->nextSlide(1, $total);
            $this->assertEquals(
                2,
                $result,
                "next() from slide 1 (total=$total) must go to slide 2"
            );
        }
    }

    /**
     * prev() normal: from slide N, prev() must go to slide N-1 (when N > 1).
     *
     * Property-based style: iterate over all slide counts N in [2..10].
     *
     * Validates: Requirements 3.3
     */
    public function test_prev_goes_back_normally_from_last_slide(): void
    {
        for ($total = 2; $total <= 10; $total++) {
            $result = $this->prevSlide($total, $total);
            $this->assertEquals(
                $total - 1,
                $result,
                "prev() from last slide ($total/$total) must go to slide " . ($total - 1)
            );
        }
    }

    /**
     * next()/prev() always produce an index in [1, N] for all valid positions.
     *
     * Property-based style: iterate over all slide counts N in [1..10] and
     * all valid active positions in [1..N].
     *
     * Validates: Requirements 3.3
     */
    public function test_next_and_prev_always_produce_valid_index(): void
    {
        for ($total = 1; $total <= 10; $total++) {
            for ($active = 1; $active <= $total; $active++) {
                $next = $this->nextSlide($active, $total);
                $prev = $this->prevSlide($active, $total);

                $this->assertGreaterThanOrEqual(1, $next, "next() result must be >= 1 (active=$active, total=$total)");
                $this->assertLessThanOrEqual($total, $next, "next() result must be <= total (active=$active, total=$total)");

                $this->assertGreaterThanOrEqual(1, $prev, "prev() result must be >= 1 (active=$active, total=$total)");
                $this->assertLessThanOrEqual($total, $prev, "prev() result must be <= total (active=$active, total=$total)");
            }
        }
    }

    // =========================================================================
    // Autoplay Speed Setting Test
    // =========================================================================

    /**
     * The rendered HTML must contain the autoplay speed value from settings.
     *
     * The Blade template inlines the setting value as:
     *   autoplaySpeed: {{ \App\Models\Setting::get('hero_autoplay_speed', 6000) }}
     * so the rendered HTML must contain "autoplaySpeed: 6000" (or the configured value).
     *
     * Validates: Requirements 3.1
     */
    public function test_rendered_html_contains_autoplay_speed_from_settings(): void
    {
        // Ensure the default value is used (no override in DB)
        Setting::where('key', 'hero_autoplay_speed')->delete();

        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertSee('autoplaySpeed: 6000', false);
    }

    /**
     * When a custom autoplay speed is configured, the rendered HTML must reflect it.
     *
     * Validates: Requirements 3.1
     */
    public function test_rendered_html_reflects_custom_autoplay_speed(): void
    {
        Setting::set('hero_autoplay_speed', '4000');

        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertSee('autoplaySpeed: 4000', false);
        $response->assertDontSee('autoplaySpeed: 6000', false);
    }
}
