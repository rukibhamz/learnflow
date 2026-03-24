# Requirements Document

## Introduction

This feature adds a "coming soon" maintenance overlay to specific public-facing pages of the LMS when maintenance mode is enabled by an admin. When active, visitors to the course listing, individual course detail, mentor/instructor, and pricing pages will see a branded "coming soon" message instead of the normal page content. Admins and authenticated instructors are exempt from the overlay so they can continue working. The `maintenance_mode` setting already exists in the admin settings panel (`Setting::get('maintenance_mode')`); this feature wires that toggle to the affected views.

## Glossary

- **Maintenance_Mode**: A boolean platform setting stored in the `settings` table under the key `maintenance_mode`. Toggled by an admin via the Admin Settings panel.
- **Coming_Soon_Overlay**: A full-page UI component that replaces the normal page content when Maintenance_Mode is active.
- **Affected_Pages**: The four public-facing page types subject to the overlay: course listing, course detail, mentors, and pricing.
- **Exempt_User**: An authenticated user with the `admin` or `instructor` role who bypasses the overlay.
- **Setting**: The `App\Models\Setting` Eloquent model used to read and write platform configuration values.
- **Blade_Component**: A reusable Laravel Blade component (`<x-maintenance-overlay />`) that renders the Coming_Soon_Overlay.

## Requirements

### Requirement 1: Display Coming Soon Overlay on Affected Pages

**User Story:** As a visitor, I want to see a clear "coming soon" message when I navigate to a course, mentor, or pricing page during maintenance, so that I understand the content is temporarily unavailable.

#### Acceptance Criteria

1. WHEN Maintenance_Mode is enabled AND a visitor requests an Affected_Page, THE System SHALL render the Coming_Soon_Overlay in place of the normal page content.
2. WHEN Maintenance_Mode is disabled, THE System SHALL render the normal page content for all Affected_Pages.
3. THE Coming_Soon_Overlay SHALL display a heading, a short explanatory message, and the site name.
4. THE Coming_Soon_Overlay SHALL be visually consistent with the existing site branding (primary color, typography).

### Requirement 2: Exempt Admin and Instructor Users

**User Story:** As an admin or instructor, I want to see the real page content even when maintenance mode is on, so that I can review and prepare content without disabling maintenance mode.

#### Acceptance Criteria

1. WHEN Maintenance_Mode is enabled AND the authenticated user is an Exempt_User, THE System SHALL render the normal page content for all Affected_Pages.
2. WHEN Maintenance_Mode is enabled AND the request is unauthenticated, THE System SHALL render the Coming_Soon_Overlay.
3. WHEN Maintenance_Mode is enabled AND the authenticated user has the `student` role, THE System SHALL render the Coming_Soon_Overlay.

### Requirement 3: Affected Page Scope

**User Story:** As a product owner, I want maintenance mode to cover all course-related, mentor, and pricing pages, so that no incomplete content is accidentally exposed.

#### Acceptance Criteria

1. THE System SHALL apply the Coming_Soon_Overlay to the course listing page (`/courses`).
2. THE System SHALL apply the Coming_Soon_Overlay to individual course detail pages (`/courses/{slug}`).
3. THE System SHALL apply the Coming_Soon_Overlay to the mentors page (`/mentors`).
4. THE System SHALL apply the Coming_Soon_Overlay to the pricing page (`/pricing`).
5. THE System SHALL NOT apply the Coming_Soon_Overlay to pages outside the defined Affected_Pages scope (e.g., home, blog, auth pages, admin panel, student dashboard).

### Requirement 4: Admin Toggle Persists Correctly

**User Story:** As an admin, I want the maintenance mode toggle in the settings panel to reliably enable and disable the overlay, so that I have full control over when the overlay appears.

#### Acceptance Criteria

1. WHEN an admin saves the settings form with the Maintenance_Mode checkbox checked, THE Setting SHALL store the value `1` for the key `maintenance_mode`.
2. WHEN an admin saves the settings form with the Maintenance_Mode checkbox unchecked, THE Setting SHALL store the value `0` for the key `maintenance_mode`.
3. WHEN the Maintenance_Mode setting is updated, THE System SHALL reflect the change on the next page request without requiring a server restart.

### Requirement 5: Coming Soon Overlay Content

**User Story:** As a visitor, I want the coming soon page to give me enough context to understand the situation and know what to do next, so that I am not confused or frustrated.

#### Acceptance Criteria

1. THE Coming_Soon_Overlay SHALL display the site name retrieved from `Setting::get('site_name')`.
2. THE Coming_Soon_Overlay SHALL include a human-readable message indicating the section is under maintenance.
3. THE Coming_Soon_Overlay SHALL provide a link back to the home page.
4. IF the user is authenticated as a student, THE Coming_Soon_Overlay SHALL provide a link to the student dashboard so the student can access already-enrolled courses.
