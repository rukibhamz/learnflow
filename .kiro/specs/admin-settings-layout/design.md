# Admin Settings Layout Bugfix Design

## Overview

Two independent bugs exist in `resources/views/admin/settings.blade.php`. First, boolean feature
toggle checkboxes always persist as `0` because a `<input type="hidden" name="..." value="0">` is
placed *after* the checkbox in the DOM — PHP reads the last value for duplicate field names, so the
hidden field always wins regardless of checkbox state. Second, the form footer shows a "Saved"
confirmation via `x-show="session('success')"` — `session()` is a PHP/Blade helper that does not
exist in Alpine.js scope, so the expression always evaluates falsy and the text never appears.

Both fixes are confined to the single Blade template. No controller or model changes are required.

## Glossary

- **Bug_Condition (C)**: The set of inputs that trigger either defect
- **Property (P)**: The desired correct behavior for those inputs
- **Preservation**: Existing behaviors that must remain unchanged after the fix
- **hidden-after-checkbox pattern**: Placing `<input type="hidden" value="0">` after a checkbox with the same `name` — PHP's `$_POST` parsing uses the last occurrence, so the hidden value always overwrites the checkbox value
- **Alpine.js `session()`**: An invalid expression — `session()` is a server-side Laravel/PHP helper unavailable in client-side Alpine.js data context
- **`Setting::set(key, value)`**: The model method in `app/Models/Setting.php` that persists a key/value pair to the database

## Bug Details

### Bug Condition

The defect has two independent manifestations, both in `resources/views/admin/settings.blade.php`.

**Bug A — Hidden input ordering (affects boolean toggles):**
The hidden fallback input for each boolean toggle is placed *after* the checkbox. When the checkbox
is checked, both `value="1"` (checkbox) and `value="0"` (hidden) are submitted; PHP uses the last
one (`0`). When unchecked, only the hidden `value="0"` is submitted — which is correct by accident.

**Bug B — Invalid Alpine.js expression (affects "Saved" confirmation):**
The footer uses `x-show="session('success')"`. Alpine.js evaluates this as a JavaScript expression
where `session` is undefined, so it throws silently and the element stays hidden permanently.

**Formal Specification:**
```
FUNCTION isBugCondition(input)
  INPUT: input — a form submission or page render event
  OUTPUT: boolean

  IF input.type == 'form_submit'
     AND input.fields contains a boolean toggle with value="1" (checkbox checked)
     AND hidden input with same name and value="0" appears AFTER checkbox in DOM
  THEN RETURN true   -- Bug A

  IF input.type == 'page_render_after_successful_save'
     AND footer contains x-show="session('success')"
  THEN RETURN true   -- Bug B

  RETURN false
END FUNCTION
```

### Examples

- Admin checks "Instructor Approvals" and saves → DB stores `feature_instructor_approvals = 0` (bug; expected `1`)
- Admin checks "Course Gamification" and saves → DB stores `feature_gamification = 0` (bug; expected `1`)
- Admin unchecks "Instructor Approvals" and saves → DB stores `feature_instructor_approvals = 0` (correct by accident)
- Admin saves any settings successfully → "Saved" text in footer never appears (bug; expected visible)
- Admin saves with `maintenance_mode` checked → DB stores `maintenance_mode = 0` (bug; expected `1`)

## Expected Behavior

### Preservation Requirements

**Unchanged Behaviors:**
- Unchecked boolean toggles must continue to save as `0`
- Non-boolean settings (site name, support email, timezone, SMTP fields, Stripe keys) must continue to save their submitted values without modification
- Tab navigation (General, Email, Payment, Enrollment, Notifications) must continue to switch content client-side without page reload
- The test email modal and its independent form submission must continue to work correctly
- The server-side `@if(session('success'))` flash banner above the form must continue to display on redirect

**Scope:**
All inputs that do NOT involve a checked boolean toggle or the footer "Saved" text rendering are
completely unaffected by this fix. This includes all text, email, password, and select inputs, as
well as all client-side Alpine.js tab and modal interactions.

## Hypothesized Root Cause

1. **Hidden input placed after checkbox (Bug A)**: The standard HTML pattern for checkbox fallback
   is to place the hidden `value="0"` input *before* the checkbox so the checkbox's `value="1"`
   overwrites it when checked. The template has this reversed — the hidden input comes after the
   checkbox — so PHP's last-value-wins rule always picks `0`.

2. **Wrong language context for `session()` (Bug B)**: The developer used a Blade/PHP helper
   (`session('success')`) inside an Alpine.js `x-show` attribute. Alpine evaluates attribute values
   as JavaScript expressions at runtime in the browser, where `session` is not defined. The fix is
   to render the session value server-side into a JavaScript-compatible boolean using Blade, e.g.
   `x-show="{{ session('success') ? 'true' : 'false' }}"`, or to replace the Alpine expression
   entirely with a server-side `@if` block.

3. **No controller involvement**: The `SettingsController::update()` method simply iterates
   `$request->except(['_token'])` and calls `Setting::set()` for each key. It correctly saves
   whatever PHP parsed from the request — the defect is entirely in what PHP receives from the
   malformed HTML, not in the controller logic.

## Correctness Properties

Property 1: Bug Condition A — Checked Boolean Toggle Saves as 1

_For any_ form submission where a boolean toggle checkbox is checked (value="1" submitted),
the fixed template SHALL result in the database storing `1` for that setting key, because the
hidden fallback input is moved before the checkbox so PHP's last-value-wins rule picks the
checkbox value.

**Validates: Requirements 2.1**

Property 2: Bug Condition B — Saved Confirmation Displays After Successful Save

_For any_ page render that follows a successful settings form submission (session 'success' is
set), the fixed template SHALL display the inline "Saved" confirmation text in the form footer,
because the Alpine.js expression is replaced with a valid server-rendered boolean.

**Validates: Requirements 2.2**

Property 3: Preservation — Unchecked Toggle and Non-Boolean Fields Unchanged

_For any_ form submission where boolean toggles are unchecked or where only non-boolean fields
are submitted, the fixed template SHALL produce exactly the same saved values as the original
template, preserving all existing correct behavior for those inputs.

**Validates: Requirements 3.1, 3.2**

## Fix Implementation

### Changes Required

**File**: `resources/views/admin/settings.blade.php`

**Change 1 — Move hidden inputs before checkboxes (Bug A)**

For every boolean toggle, move the `<input type="hidden" name="..." value="0">` to appear
*before* its corresponding `<input type="checkbox">`. This applies to:
- `feature_instructor_approvals` (Enrollment tab)
- `feature_gamification` (Enrollment tab)
- `maintenance_mode` (General tab)
- `mail_use_ssl` (Email tab)

Before (buggy):
```html
<input type="checkbox" name="feature_instructor_approvals" value="1" ...>
<input type="hidden"   name="feature_instructor_approvals" value="0">
```

After (fixed):
```html
<input type="hidden"   name="feature_instructor_approvals" value="0">
<input type="checkbox" name="feature_instructor_approvals" value="1" ...>
```

**Change 2 — Fix the Alpine.js "Saved" expression (Bug B)**

Replace the invalid `x-show="session('success')"` with a server-rendered boolean so Alpine
receives a valid JavaScript value:

Before (buggy):
```html
<div x-show="session('success')" x-transition ...>
```

After (fixed):
```html
<div x-show="{{ session('success') ? 'true' : 'false' }}" x-transition ...>
```

## Testing Strategy

### Validation Approach

Tests are PHP feature tests using Laravel's `TestCase` HTTP helpers. They POST to
`/admin/settings`, follow redirects, and assert database state or response content.
Property-based style is achieved by parameterizing over all four boolean toggle field names.

### Exploratory Bug Condition Checking

**Goal**: Demonstrate both bugs on the *unfixed* template before applying the fix.

**Test Plan**: Submit the settings form with checked boolean toggles and assert the saved value
is `1`. Also submit and follow the redirect, asserting the "Saved" text is present. Run on
unfixed code — both assertions will fail, confirming the root cause.

**Test Cases**:
1. **Checked toggle saves as 1** — POST `feature_instructor_approvals=1`, assert DB value is `1` (fails on unfixed code)
2. **Checked gamification saves as 1** — POST `feature_gamification=1`, assert DB value is `1` (fails on unfixed code)
3. **Saved text visible after submit** — POST valid settings, follow redirect, assert response contains "Saved" (fails on unfixed code)
4. **Maintenance mode checked saves as 1** — POST `maintenance_mode=1`, assert DB value is `1` (fails on unfixed code)

**Expected Counterexamples**:
- DB stores `0` for all checked boolean toggles — confirms hidden-after-checkbox ordering bug
- "Saved" text absent from response — confirms invalid Alpine.js `session()` expression

### Fix Checking

**Goal**: Verify that for all inputs where the bug condition holds, the fixed template produces correct behavior.

**Pseudocode:**
```
FOR ALL toggle IN [feature_instructor_approvals, feature_gamification, maintenance_mode, mail_use_ssl] DO
  result := POST /admin/settings WITH toggle=1 (checked)
  ASSERT Setting::get(toggle) == '1'
END FOR

result := POST /admin/settings WITH any valid data
ASSERT response after redirect contains "Saved" text
```

### Preservation Checking

**Goal**: Verify that unchecked toggles and non-boolean fields are unaffected by the fix.

**Pseudocode:**
```
FOR ALL toggle IN [feature_instructor_approvals, feature_gamification, maintenance_mode, mail_use_ssl] DO
  result_original := POST /admin/settings WITHOUT toggle (unchecked)
  result_fixed    := POST /admin/settings WITHOUT toggle (unchecked)
  ASSERT Setting::get(toggle) == '0'  -- same in both
END FOR

FOR ALL field IN [site_name, support_email, timezone, mail_mailer, stripe_publishable_key] DO
  ASSERT Setting::get(field) == submitted_value
END FOR
```

**Testing Approach**: Property-based testing by parameterizing over all boolean toggle names
and all non-boolean field names, generating representative values for each.

**Test Cases**:
1. **Unchecked toggle preservation** — POST without each boolean toggle, assert DB stores `0`
2. **Non-boolean field preservation** — POST text/select fields, assert DB stores submitted values
3. **Test email independence** — POST to `/admin/settings/test-email`, assert it does not affect settings

### Unit Tests

- Each boolean toggle: checked → saves `1`, unchecked → saves `0`
- Non-boolean fields (site_name, support_email, timezone) save submitted string values
- "Saved" confirmation text present in response after successful POST + redirect

### Property-Based Tests

- For all four boolean toggle names: `isBugCondition(checked=true)` → saved value is `1`
- For all four boolean toggle names: `isBugCondition(checked=false)` → saved value is `0` (preservation)
- For a set of representative non-boolean field names and values: saved value equals submitted value

### Integration Tests

- Full settings form submission with mixed boolean and non-boolean fields saves all values correctly
- Submitting the test email form does not interfere with the main settings form state
- Tab navigation Alpine.js state is unaffected (verified by asserting tab markup is present in rendered HTML)
