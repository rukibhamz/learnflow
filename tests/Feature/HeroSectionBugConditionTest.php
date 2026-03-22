<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Bug Condition Exploration Test
 *
 * Validates Requirements: 1.1, 1.2, 1.3
 *
 * This test confirms the bug condition where the `heroSlider` Alpine.js component
 * is registered via an inline <script> inside @section('content') in home.blade.php.
 * Because Alpine loads via a deferred Vite bundle, `alpine:init` fires before the
 * inline script is parsed, so `heroSlider` is never registered and all slides remain
 * hidden by their `x-show` directives.
 *
 * EXPECTED OUTCOME: Tests FAIL on unfixed code.
 * Failure confirms the bug exists — the hero section is invisible.
 *
 * isBugCondition(X):
 *   X.alpineLoadedViaVite = true
 *   AND X.heroSliderScriptPosition = "inline-in-content-section"
 */
class HeroSectionBugConditionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Homepage with active slides — hero section and slide content must be visible.
     *
     * Loads the homepage with seeded active HeroSlide records and asserts that:
     * - The hero <section x-data="heroSlider"> is present in the response
     * - Slide title and description content is present in the rendered HTML
     * - The Alpine component registration script is in @push('head'), NOT inline in content
     *
     * WILL FAIL on unfixed code because the heroSlider component is not registered
     * at Alpine init time, so all slides are hidden by x-show directives.
     *
     * Validates: Requirements 1.1, 1.2
     */
    public function test_hero_section_with_active_slides_is_visible(): void
    {
        HeroSlide::create([
            'tag'         => 'Online Learning',
            'title'       => 'Master New Skills Today',
            'description' => 'Expert-led courses for your career growth.',
            'is_active'   => true,
            'order'       => 1,
        ]);

        HeroSlide::create([
            'tag'         => 'Career Growth',
            'title'       => 'Advance Your Career Fast',
            'description' => 'Join thousands of successful learners.',
            'is_active'   => true,
            'order'       => 2,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);

        // The hero section element must be present
        $response->assertSee('x-data="heroSlider"', false);

        // Slide content must be visible in the rendered HTML
        $response->assertSee('Master New Skills Today', false);
        $response->assertSee('Expert-led courses for your career growth.', false);

        // The Alpine registration script must be in @push('head'), not inline in content.
        // On unfixed code the script is inline inside @section('content'), so this assertion
        // checks that @push('head') is used — the script should appear BEFORE @section('content').
        $content = $response->getContent();

        // Find positions: the heroSlider Alpine.data registration and the hero section element
        $scriptPos  = strpos($content, "Alpine.data('heroSlider'");
        $sectionPos = strpos($content, '<section x-data="heroSlider"');

        $this->assertNotFalse($scriptPos, 'Alpine.data(\'heroSlider\') registration script must be present in the rendered HTML');
        $this->assertNotFalse($sectionPos, 'Hero <section x-data="heroSlider"> must be present in the rendered HTML');

        // The registration script must appear BEFORE the hero section in the HTML
        // (i.e., it is in <head> via @push('head'), not inline after the section).
        // On unfixed code the script appears AFTER the section, so this assertion FAILS.
        $this->assertLessThan(
            $sectionPos,
            $scriptPos,
            'Bug condition detected: Alpine.data(\'heroSlider\') registration appears AFTER the hero section. ' .
            'The script must be in @push(\'head\') so it is parsed before Alpine initializes. ' .
            'isBugCondition: heroSliderScriptPosition = "inline-in-content-section"'
        );
    }

    /**
     * Test 2: The heroSlider registration script must NOT be inline in @section('content').
     *
     * Asserts that the rendered HTML does NOT contain the inline script pattern
     * (script block appearing after the hero section closing tag), which is the
     * exact bug condition described in the spec.
     *
     * WILL FAIL on unfixed code because the script is placed inline at the bottom
     * of @section('content'), after the hero <section> element.
     *
     * Validates: Requirements 1.1, 1.2
     */
    public function test_hero_slider_script_is_not_inline_in_content_section(): void
    {
        HeroSlide::create([
            'tag'         => 'Test Tag',
            'title'       => 'Test Slide Title',
            'description' => 'Test slide description text.',
            'is_active'   => true,
            'order'       => 1,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();

        // On unfixed code, the script block appears AFTER the closing </section> of the hero.
        // We detect the bug condition by checking that the script does NOT appear after
        // the hero section in the body — it should only appear in <head>.
        $headEnd    = strpos($content, '</head>');
        $scriptPos  = strpos($content, "Alpine.data('heroSlider'");

        $this->assertNotFalse($headEnd, '</head> tag must be present in the rendered HTML');
        $this->assertNotFalse($scriptPos, "Alpine.data('heroSlider') must be present in the rendered HTML");

        // The registration script must appear inside <head> (before </head>).
        // On unfixed code it appears in <body> after the hero section, so this FAILS.
        $this->assertLessThan(
            $headEnd,
            $scriptPos,
            'Bug condition detected: Alpine.data(\'heroSlider\') registration script is NOT inside <head>. ' .
            'It must be pushed via @push(\'head\') so it is available before Alpine initializes. ' .
            'isBugCondition: heroSliderScriptPosition = "inline-in-content-section"'
        );
    }

    /**
     * Test 3: Homepage with no active slides — fallback hero content must be visible.
     *
     * Loads the homepage with no active HeroSlide records and asserts that the
     * fallback static hero content ("Learn without") is present in the response.
     *
     * On unfixed code the fallback content lives inside the same
     * <section x-data="heroSlider"> scope. Because heroSlider is not registered,
     * the @empty branch content may still render in the HTML (it is not behind x-show),
     * so this test may or may not fail depending on Blade rendering.
     *
     * Validates: Requirements 1.3, 3.4
     */
    public function test_fallback_hero_content_is_visible_with_no_active_slides(): void
    {
        // Ensure no slides exist
        HeroSlide::query()->delete();

        $response = $this->get('/');
        $response->assertStatus(200);

        // The fallback hero content must be present in the rendered HTML
        $response->assertSee('Learn without', false);
        $response->assertSee('limits.', false);

        // The hero section element must still be present
        $response->assertSee('x-data="heroSlider"', false);

        // The Alpine registration script must be in <head> (before </head>)
        $content   = $response->getContent();
        $headEnd   = strpos($content, '</head>');
        $scriptPos = strpos($content, "Alpine.data('heroSlider'");

        $this->assertNotFalse($scriptPos, "Alpine.data('heroSlider') must be present even with no slides");

        // On unfixed code the script is in <body>, so this assertion FAILS.
        $this->assertLessThan(
            $headEnd,
            $scriptPos,
            'Bug condition detected: Alpine.data(\'heroSlider\') registration script is NOT inside <head> ' .
            'even when no slides exist. The fallback content is also affected by the missing component registration.'
        );
    }
}
