# Implementation Plan

- [x] 1. Write bug condition exploration test
  - **Property 1: Bug Condition** - Hero Section Invisible Due to Late Alpine Registration
  - **CRITICAL**: This test MUST FAIL on unfixed code — failure confirms the bug exists
  - **DO NOT attempt to fix the test or the code when it fails**
  - **NOTE**: This test encodes the expected behavior — it will validate the fix when it passes after implementation
  - **GOAL**: Surface counterexamples that demonstrate the hero section is invisible on unfixed code
  - **Scoped PBT Approach**: Scope the property to the concrete failing case — homepage loaded with active slides where `heroSliderScriptPosition = "inline-in-content-section"`
  - Create `tests/Feature/HeroSectionBugConditionTest.php`
  - Test 1: Load homepage with seeded active slides; assert the hero `<section x-data="heroSlider">` contains visible slide content (title, description, CTA) — will FAIL on unfixed code because all slides are hidden by `x-show`
  - Test 2: Assert the rendered HTML does NOT contain `x-show="$root.activeSlide === 1"` on a hidden element — will FAIL on unfixed code
  - Test 3: Load homepage with no active slides; assert the fallback hero content is present and not hidden — will FAIL on unfixed code
  - Run tests on UNFIXED code
  - **EXPECTED OUTCOME**: Tests FAIL (this is correct — it proves the bug exists)
  - Document counterexamples found: e.g., "Hero section renders as empty min-h-[600px] block; slide content present in DOM but hidden because heroSlider component is unknown at Alpine init time"
  - Mark task complete when tests are written, run, and failure is documented
  - _Requirements: 1.1, 1.2, 1.3_

- [x] 2. Write preservation property tests (BEFORE implementing fix)
  - **Property 2: Preservation** - Slider Interactions, Navigation, Autoplay, and Fallback Unchanged
  - **IMPORTANT**: Follow observation-first methodology — run UNFIXED code with non-buggy inputs and record actual behavior
  - Create `tests/Feature/HeroSectionPreservationTest.php`
  - Observe on UNFIXED code: `next()` wraps from last slide back to slide 1; `prev()` wraps from slide 1 to last slide
  - Observe on UNFIXED code: `startAutoplay()` sets `autoplayInterval`; `stopAutoplay()` clears it to null
  - Observe on UNFIXED code: with 1 slide, navigation arrows and pagination dots are absent from rendered HTML
  - Observe on UNFIXED code: with multiple slides, pagination dots and navigation arrows are present in rendered HTML
  - Observe on UNFIXED code: with no slides, fallback static hero markup is present in rendered HTML
  - Write property-based tests:
    - For all slide counts N in [1..10]: `next()` always produces an index in [1, N] and wraps at boundary (from Preservation Requirements in design)
    - For all slide counts N in [1..10]: `prev()` always produces an index in [1, N] and wraps at boundary
    - For all autoplay speed values: `startAutoplay()` sets an interval; `stopAutoplay()` clears it, leaving `autoplayInterval` as null
  - Write example-based tests:
    - Single slide: no navigation arrows, no pagination dots in response HTML
    - Multiple slides: navigation arrows and pagination dots present in response HTML
    - No slides: fallback hero content present in response HTML
  - Verify all tests PASS on UNFIXED code
  - **EXPECTED OUTCOME**: Tests PASS (confirms baseline behavior to preserve)
  - Mark task complete when tests are written, run, and passing on unfixed code
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

- [x] 3. Fix for hero section invisible due to late Alpine component registration

  - [x] 3.1 Implement the fix in `resources/views/home.blade.php`
    - Remove the inline `<script>document.addEventListener('alpine:init', () => { Alpine.data('heroSlider', …) })</script>` block from the bottom of `@section('content')`
    - Add a `@push('head')` block at the top of `home.blade.php`, placed before `@section('content')`, containing the identical `Alpine.data('heroSlider', …)` registration wrapped in a `document.addEventListener('alpine:init', …)` listener
    - The `@stack('head')` directive in `resources/views/layouts/app.blade.php` is already inside `<head>`, so scripts pushed there are parsed before the deferred Vite bundle executes — the `alpine:init` listener is registered in time
    - No other files need to be modified
    - _Bug_Condition: isBugCondition(X) where X.alpineLoadedViaVite = true AND X.heroSliderScriptPosition = "inline-in-content-section"_
    - _Expected_Behavior: heroSectionVisible(result) = true AND activeSlide(result) = 1 for all X where isBugCondition(X)_
    - _Preservation: autoplay, hover pause/resume, arrow navigation, pagination dots, single-slide display, and fallback content rendering all unchanged_
    - _Requirements: 2.1, 2.2, 2.3, 3.1, 3.2, 3.3, 3.4, 3.5_

  - [x] 3.2 Verify bug condition exploration test now passes
    - **Property 1: Expected Behavior** - Hero Section Visible After Fix
    - **IMPORTANT**: Re-run the SAME tests from task 1 — do NOT write new tests
    - The tests from task 1 encode the expected behavior (hero section visible, first slide active, fallback content visible)
    - When these tests pass, it confirms the `heroSlider` component is registered before Alpine initializes
    - Run bug condition exploration tests from step 1
    - **EXPECTED OUTCOME**: Tests PASS (confirms bug is fixed)
    - _Requirements: 2.1, 2.2, 2.3_

  - [x] 3.3 Verify preservation tests still pass
    - **Property 2: Preservation** - Slider Interactions Unchanged After Fix
    - **IMPORTANT**: Re-run the SAME tests from task 2 — do NOT write new tests
    - Run preservation property tests from step 2
    - **EXPECTED OUTCOME**: Tests PASS (confirms no regressions in autoplay, navigation, hover pause, single-slide display, and fallback content)
    - Confirm all tests still pass after fix (no regressions)

- [x] 4. Checkpoint — Ensure all tests pass
  - Run the full test suite: `php artisan test --filter HeroSection`
  - Ensure all tests pass; ask the user if questions arise
