# Implementation Plan: Maintenance Mode

## Overview

Wire the existing `maintenance_mode` setting to four public-facing routes via a dedicated middleware class, a Blade overlay component, and a suite of property-based tests.

## Tasks

- [x] 1. Create `MaintenanceModeMiddleware`
  - Create `app/Http/Middleware/MaintenanceModeMiddleware.php`
  - Read `config('settings.maintenance_mode')` (already loaded by `AppServiceProvider`)
  - If falsy, call `$next($request)` immediately
  - If truthy, check `$request->user()?->hasRole(['admin', 'instructor'])` — if exempt, call `$next($request)`
  - Otherwise return `response(view('components.maintenance-overlay'), 200)`
  - _Requirements: 1.1, 1.2, 2.1, 2.2, 2.3_

  - [ ]* 1.1 Write unit tests for middleware pass-through logic
    - Assert `$next` is called when `maintenance_mode` is `0`
    - Assert `$next` is called when user is admin or instructor and `maintenance_mode` is `1`
    - Assert overlay response is returned for guest when `maintenance_mode` is `1`
    - Assert overlay response is returned for student when `maintenance_mode` is `1`
    - _Requirements: 1.1, 1.2, 2.1, 2.2, 2.3_

- [x] 2. Register middleware alias in `bootstrap/app.php`
  - Add `'maintenance' => \App\Http\Middleware\MaintenanceModeMiddleware::class` to the existing `$middleware->alias([...])` block
  - _Requirements: 3.1, 3.2, 3.3, 3.4_

- [x] 3. Apply middleware to the four affected routes in `routes/web.php`
  - Append `->middleware('maintenance')` to `Route::get('courses', ...)` (`courses.index`)
  - Append `->middleware('maintenance')` to `Route::get('courses/{slug}', ...)` (`courses.show`)
  - Append `->middleware('maintenance')` to the `mentors` route closure (`pages.mentors`)
  - Append `->middleware('maintenance')` to `Route::get('pricing', ...)` (`pricing`)
  - Also append to the `plans` alias route (`pages.pricing`) if present
  - _Requirements: 3.1, 3.2, 3.3, 3.4_

- [x] 4. Create `resources/views/components/maintenance-overlay.blade.php`
  - Extend `layouts.app`
  - Display `$siteName` (shared by `AppServiceProvider` View composer)
  - Render a "Coming Soon" heading and a short maintenance message
  - Include a "Back to Home" link (`route('home')`)
  - Conditionally render a "Go to Dashboard" link (`route('dashboard')`) when `auth()->check() && auth()->user()->hasRole('student')`
  - Use existing Tailwind utility classes and primary color variable consistent with site branding
  - _Requirements: 1.3, 1.4, 5.1, 5.2, 5.3, 5.4_

  - [ ]* 4.1 Write unit tests for overlay view content
    - Render the view as a guest and assert it contains the site name, a maintenance keyword, and the home link
    - Render the view as an authenticated student and assert the dashboard link is present
    - Render the view as a guest and assert the dashboard link is absent
    - _Requirements: 1.3, 5.1, 5.2, 5.3, 5.4_

- [x] 5. Checkpoint — ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 6. Write property-based tests in `tests/Feature/MaintenanceModeTest.php`

  - [ ]* 6.1 Write property test for Property 1: Maintenance flag controls overlay visibility for non-exempt users
    - **Property 1: Maintenance flag controls overlay visibility for non-exempt users**
    - Iterate over all four affected URLs × `[guest, student]` × `[maintenance=0, maintenance=1]`
    - Assert overlay is present iff `maintenance=1`
    - **Validates: Requirements 1.1, 1.2, 2.2, 2.3**

  - [ ]* 6.2 Write property test for Property 2: Exempt users always see normal content
    - **Property 2: Exempt users always see normal content**
    - Iterate over all four affected URLs × `[admin, instructor]` × `[maintenance=0, maintenance=1]`
    - Assert overlay is never present
    - **Validates: Requirements 2.1**

  - [ ]* 6.3 Write property test for Property 3: All four affected pages show the overlay
    - **Property 3: All four affected pages show the overlay**
    - Iterate over all four affected URLs with `maintenance=1` and a guest requester
    - Assert overlay content is present for each URL
    - **Validates: Requirements 3.1, 3.2, 3.3, 3.4**

  - [ ]* 6.4 Write property test for Property 4: Non-affected pages never show the overlay
    - **Property 4: Non-affected pages never show the overlay**
    - Iterate over representative non-affected URLs (`/`, `/blog`, `/login`, `/register`) with `maintenance=1` and a guest
    - Assert overlay content is absent for each URL
    - **Validates: Requirements 3.5**

  - [ ]* 6.5 Write property test for Property 5: Overlay content includes site name and maintenance message
    - **Property 5: Overlay content includes site name and maintenance message**
    - Iterate over a set of arbitrary `site_name` strings stored via `Setting::set()`
    - Request an affected page as a guest with `maintenance=1`
    - Assert response contains the site name string and a maintenance keyword
    - **Validates: Requirements 1.3, 5.1, 5.2**

  - [ ]* 6.6 Write property test for Property 6: Setting persistence round-trip
    - **Property 6: Setting persistence round-trip**
    - For each of `['1', '0']`, call `Setting::set('maintenance_mode', $value)`, then read `Setting::get('maintenance_mode')` and assert equality
    - Also request an affected page and assert overlay visibility matches the stored value
    - **Validates: Requirements 4.1, 4.2, 4.3**

- [x] 7. Final checkpoint — ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for a faster MVP
- The middleware reads from `config('settings.maintenance_mode')` (no extra DB query per request) because `AppServiceProvider` already loads all settings into config on boot
- The overlay returns HTTP `200`, not `503`, so monitoring tools and search engines do not treat the pages as broken
- No new migrations or models are required
- Property tests use Pest datasets to cover all relevant input combinations
