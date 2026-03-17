# Admin Settings Layout Bugfix Tasks

- [x] 1 Write exploratory tests to confirm both bugs on unfixed code
  - [x] 1.1 Create `tests/Feature/AdminSettingsBugConditionTest.php` with tests that POST checked boolean toggles and assert saved value is `1` (expected to FAIL on unfixed code)
  - [x] 1.2 Add test that POSTs valid settings, follows redirect, and asserts "Saved" text is present in response (expected to FAIL on unfixed code)
  - [x] 1.3 Run the exploratory tests and confirm they fail, validating the root cause analysis

- [x] 2 Fix Bug A — move hidden inputs before checkboxes in `resources/views/admin/settings.blade.php`
  - [x] 2.1 Move `<input type="hidden" name="feature_instructor_approvals" value="0">` to appear before its checkbox
  - [x] 2.2 Move `<input type="hidden" name="feature_gamification" value="0">` to appear before its checkbox
  - [x] 2.3 Add missing hidden fallback input before `maintenance_mode` checkbox (currently has no hidden fallback — unchecked state sends nothing, controller receives no key)
  - [x] 2.4 Add missing hidden fallback input before `mail_use_ssl` checkbox (same issue as 2.3)

- [x] 3 Fix Bug B — replace invalid Alpine.js `session()` expression in form footer
  - [x] 3.1 Replace `x-show="session('success')"` with `x-show="{{ session('success') ? 'true' : 'false' }}"` in the footer "Saved" div

- [x] 4 Write fix-checking tests to verify correct behavior after the fix
  - [x] 4.1 Create `tests/Feature/AdminSettingsBugFixVerificationTest.php`
  - [x] 4.2 Add parameterized tests: for each boolean toggle (`feature_instructor_approvals`, `feature_gamification`, `maintenance_mode`, `mail_use_ssl`), POST with toggle checked and assert DB value is `1`
  - [x] 4.3 Add test: POST valid settings, follow redirect, assert response contains "Saved" text
  - [x] 4.4 Run fix-checking tests and confirm they all pass

- [x] 5 Write preservation tests to verify no regressions
  - [x] 5.1 Create `tests/Feature/AdminSettingsPreservationTest.php`
  - [x] 5.2 Add parameterized tests: for each boolean toggle, POST without the toggle (unchecked) and assert DB value is `0`
  - [x] 5.3 Add tests: POST non-boolean fields (`site_name`, `support_email`, `timezone`, `mail_mailer`, `stripe_publishable_key`) and assert each DB value matches the submitted value
  - [x] 5.4 Add test: POST to `/admin/settings/test-email` and assert it returns a redirect independently of the main settings form
  - [x] 5.5 Run preservation tests and confirm they all pass
