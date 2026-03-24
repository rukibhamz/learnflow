# Hero Section Missing — Bugfix Design

## Overview

The hero slider on the homepage is invisible because the `heroSlider` Alpine.js component
is registered inside `@section('content')` in `resources/views/home.blade.php` via an
inline `<script>` that listens for `alpine:init`. Alpine.js is loaded through the Vite
bundle with `defer`, so Alpine fires `alpine:init` and processes the DOM before the inline
listener in the content section is ever attached. The `heroSlider` component is therefore
unknown at initialization time, `x-data="heroSlider"` fails silently, and every slide
remains hidden by its `x-show` directive.

The fix moves the `Alpine.data('heroSlider', …)` registration script into
`@push('head')` so it is emitted inside `<head>` — before Alpine initializes — using the
`@stack('head')` slot that already exists in `resources/views/layouts/app.blade.php`.

## Glossary

- **Bug_Condition (C)**: The condition where the `heroSlider` Alpine component registration
  script is positioned after Alpine's `alpine:init` event has already fired, causing the
  component to be unknown when Alpine processes the DOM.
- **Property (P)**: The desired behavior — `heroSlider` is registered before Alpine
  initializes, `x-data="heroSlider"` resolves correctly, and the first slide is visible.
- **Preservation**: All existing slider interactions (autoplay, navigation arrows,
  pagination dots, hover pause) and the fallback hero content must remain unchanged.
- **heroSlider**: The Alpine.js component defined in `resources/views/home.blade.php` that
  manages slide state, autoplay, and navigation.
- **alpine:init**: The event fired by Alpine.js before it walks the DOM; `Alpine.data()`
  registrations must be made during or before this event.
- **@stack('head') / @push('head')**: Laravel Blade stack mechanism; `@stack('head')` is
  already present in `resources/views/layouts/app.blade.php` inside `<head>`, making it
  the correct target for early script injection.

## Bug Details

### Bug Condition

The bug manifests on every homepage load where Alpine.js is loaded via the deferred Vite
bundle. The inline `<script>` at the bottom of `@section('content')` attaches a listener
for `alpine:init`, but by the time the browser parses that script block Alpine has already
fired `alpine:init` and begun walking the DOM. The `heroSlider` component is never
registered, so `x-data="heroSlider"` is treated as an unknown component and all
`x-show="$root.activeSlide === N"` expressions evaluate against an empty context, leaving
every slide hidden.

**Formal Specification:**
```
FUNCTION isBugCondition(X)
  INPUT: X of type PageLoadContext
  OUTPUT: boolean

  RETURN X.alpineLoadedViaVite = true
     AND X.heroSliderScriptPosition = "inline-in-content-section"
END FUNCTION
```

### Examples

- Homepage loaded with 3 active slides → hero section renders as a blank min-h-[600px]
  block; no slide content, no navigation arrows, no pagination dots are visible.
  (Expected: first slide is visible with title, description, image, and CTA buttons.)
- Homepage loaded with 1 active slide → same blank block.
  (Expected: single slide is visible, no navigation controls shown.)
- Homepage loaded with no active slides → fallback static hero content is also hidden
  because it lives inside the same `x-data="heroSlider"` section.
  (Expected: fallback content is visible.)
- Alpine DevTools shows `heroSlider` as "undefined component" on the `<section>` element.

## Expected Behavior

### Preservation Requirements

**Unchanged Behaviors:**
- Autoplay must continue to cycle slides at the configured `hero_autoplay_speed` setting
  when more than one slide exists.
- Hovering over the hero section must continue to pause autoplay; mouse leave must resume it.
- Clicking navigation arrows (`prev` / `next`) must continue to change the active slide and
  reset the autoplay timer.
- Clicking pagination dots must continue to jump to the selected slide and reset autoplay.
- When only one slide exists, navigation arrows and pagination dots must continue to be
  hidden (controlled by Blade `@if($slides->count() > 1)`).
- When no active slides exist, the static fallback hero content must continue to render.

**Scope:**
All inputs that do NOT involve the `heroSlider` Alpine component registration timing are
completely unaffected by this fix. This includes:
- All other Alpine components on the page (navigation, featured courses, etc.)
- All Livewire components (`featured-courses`, etc.)
- All other pages in the application

## Hypothesized Root Cause

There is exactly one root cause:

1. **Script position relative to Alpine initialization**: The `<script>` block containing
   `document.addEventListener('alpine:init', () => { Alpine.data('heroSlider', …) })`
   is placed at the bottom of `@section('content')`. The Vite bundle (`resources/js/app.js`)
   loads Alpine with `defer`, which means Alpine executes after the HTML is parsed — but
   the deferred bundle runs before inline scripts that appear later in the body are
   guaranteed to have registered their listeners. In practice, Alpine fires `alpine:init`
   before the browser reaches the inline `<script>` in the content section, so the
   `Alpine.data` call never executes during initialization.

No other code is defective. The `heroSlider` component logic itself is correct; it simply
needs to be registered before Alpine walks the DOM.

## Correctness Properties

Property 1: Bug Condition — Hero Slider Renders Visible First Slide

_For any_ homepage load where `isBugCondition` returns true (Alpine loaded via Vite and
registration script is inline in content), moving the `Alpine.data('heroSlider', …)` call
into `@push('head')` SHALL cause the `heroSlider` component to be registered before
Alpine initializes, so that `x-data="heroSlider"` resolves correctly and the first slide
is visible (`activeSlide === 1`) on page load.

**Validates: Requirements 2.1, 2.2, 2.3**

Property 2: Preservation — Existing Slider Interactions Unchanged

_For any_ user interaction or page state where the bug condition does NOT hold (component
registered correctly), the fixed code SHALL produce exactly the same behavior as the
original code, preserving autoplay cycling, hover pause/resume, arrow navigation,
pagination dot navigation, single-slide display, and fallback content rendering.

**Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5**

## Fix Implementation

### Changes Required

**File**: `resources/views/home.blade.php`

**Specific Changes**:

1. **Remove the inline `<script>` block** from the bottom of `@section('content')` — the
   entire `<script>document.addEventListener('alpine:init', …)</script>` block.

2. **Add `@push('head')` block** at the top of the file (before `@section('content')`)
   containing the identical `Alpine.data('heroSlider', …)` registration wrapped in a
   `document.addEventListener('alpine:init', …)` listener.

The `@stack('head')` directive in `resources/views/layouts/app.blade.php` is already
positioned inside `<head>`, after the Vite assets are declared but before `</head>`. Scripts
pushed there are parsed before the deferred Vite bundle executes, so the `alpine:init`
listener is registered in time.

No other files need to be modified.

**Before (bottom of `@section('content')`):**
```blade
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('heroSlider', () => ({ … }));
    });
</script>
```

**After (new `@push('head')` block, placed before `@section('content')`):**
```blade
@push('head')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('heroSlider', () => ({ … }));
    });
</script>
@endpush
```

## Testing Strategy

### Validation Approach

Two phases: first confirm the bug is reproducible with the script in its current position
(exploratory), then verify the fix resolves it and leaves all slider interactions intact
(fix checking + preservation checking).

### Exploratory Bug Condition Checking

**Goal**: Surface counterexamples that demonstrate the bug on the unfixed code. Confirm
that `heroSlider` is not registered when the script is inline in the content section.

**Test Plan**: Write a feature test that loads the homepage and asserts the hero section
content is visible. Run on the unfixed code to observe failure and confirm the root cause.

**Test Cases**:
1. **Hero section visible with slides**: Load homepage with seeded slides; assert the hero
   `<section>` contains visible slide content (will fail on unfixed code).
2. **First slide active**: Assert `activeSlide` initializes to 1 and the first slide is
   not hidden (will fail on unfixed code — all slides hidden).
3. **Fallback content visible**: Load homepage with no slides; assert fallback hero content
   is rendered and visible (will fail on unfixed code).
4. **Component registered**: Assert no Alpine "unknown component" error is present in the
   rendered HTML (will fail on unfixed code).

**Expected Counterexamples**:
- Hero section renders as an empty block; slide content is present in the DOM but hidden
  because `x-show` evaluates against an undefined context.
- Root cause confirmed: `heroSlider` not registered at Alpine init time.

### Fix Checking

**Goal**: Verify that after moving the script to `@push('head')`, all inputs where the
bug condition holds now produce the correct behavior.

**Pseudocode:**
```
FOR ALL X WHERE isBugCondition(X) DO
  result := renderHomepage_fixed(X)
  ASSERT heroSectionVisible(result) = true
     AND activeSlide(result) = 1
END FOR
```

### Preservation Checking

**Goal**: Verify that for all inputs where the bug condition does NOT hold, the fixed code
produces the same result as the original code.

**Pseudocode:**
```
FOR ALL X WHERE NOT isBugCondition(X) DO
  ASSERT renderHomepage_original(X) = renderHomepage_fixed(X)
END FOR
```

**Testing Approach**: Property-based testing is recommended for preservation checking
because slider interactions depend on state combinations (slide count, active index,
autoplay state) that are best explored with generated inputs.

**Test Cases**:
1. **Autoplay preservation**: Verify that with multiple slides the autoplay interval is
   started on `init()` and cycles slides correctly after the fix.
2. **Navigation preservation**: Verify `next()` and `prev()` wrap correctly at boundaries
   (slide 1 → last, last → slide 1) after the fix.
3. **Hover pause preservation**: Verify `stopAutoplay()` clears the interval and
   `startAutoplay()` restarts it, matching pre-fix behavior.
4. **Single-slide preservation**: Verify navigation controls are absent and autoplay is
   not started when `totalSlides === 1`.
5. **Fallback content preservation**: Verify the static fallback renders when no slides
   exist in the database.

### Unit Tests

- Assert that the homepage response contains the hero `<section>` element with
  `x-data="heroSlider"` and that slide content is present in the rendered HTML.
- Assert that with zero slides the fallback hero markup is present in the response.
- Assert that with one slide the navigation arrows and pagination dots are absent.
- Assert that with multiple slides the pagination dots and navigation arrows are present.

### Property-Based Tests

- Generate random slide counts (1–10); assert that `next()` always produces an index in
  `[1, totalSlides]` and wraps correctly at both boundaries.
- Generate random slide counts; assert that `prev()` always produces an index in
  `[1, totalSlides]` and wraps correctly at both boundaries.
- Generate random autoplay speed values; assert that `startAutoplay()` sets an interval
  and `stopAutoplay()` clears it, leaving `autoplayInterval` as `null`.

### Integration Tests

- Full page load with multiple slides: hero section is visible, first slide is shown,
  autoplay starts, navigation controls are present.
- Full page load with one slide: hero section is visible, single slide is shown, no
  navigation controls rendered.
- Full page load with no slides: fallback hero content is visible.
- Switching between slides via pagination dots updates `activeSlide` and resets autoplay.
- Hovering over the hero section pauses autoplay; mouse leave resumes it.
