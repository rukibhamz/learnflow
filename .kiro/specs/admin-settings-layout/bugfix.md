# Bugfix Requirements Document

## Introduction

The admin settings page (`/admin/settings`) has two layout/behavior bugs. First, boolean feature toggle inputs (e.g. "Instructor Approvals", "Course Gamification") always save as `0` (off) regardless of the checkbox state, because a hidden `value="0"` input is placed *after* the checkbox in the DOM — PHP's form parsing uses the last value for duplicate field names, so the hidden field always wins. Second, the "Saved" confirmation text in the form footer uses an Alpine.js `session()` expression that does not exist, so the confirmation never appears after saving.

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN an admin checks a boolean feature toggle (e.g. "Instructor Approvals") and submits the settings form THEN the system saves the value as `0` (disabled) instead of `1` (enabled)

1.2 WHEN an admin submits the settings form successfully THEN the system does not display the inline "Saved" confirmation text in the form footer

### Expected Behavior (Correct)

2.1 WHEN an admin checks a boolean feature toggle and submits the settings form THEN the system SHALL save the value as `1` (enabled)

2.2 WHEN an admin submits the settings form successfully THEN the system SHALL display the inline "Saved" confirmation text in the form footer

### Unchanged Behavior (Regression Prevention)

3.1 WHEN an admin unchecks a boolean feature toggle and submits the settings form THEN the system SHALL CONTINUE TO save the value as `0` (disabled)

3.2 WHEN an admin updates non-boolean settings (site name, support email, SMTP fields, Stripe keys, etc.) and submits the form THEN the system SHALL CONTINUE TO save those values correctly

3.3 WHEN an admin navigates between settings tabs (General, Email, Payment, Enrollment, Notifications) THEN the system SHALL CONTINUE TO display the correct tab content without page reload

3.4 WHEN an admin submits the test email form THEN the system SHALL CONTINUE TO send the test email independently of the main settings form
