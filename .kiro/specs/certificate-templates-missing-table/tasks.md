# Implementation Plan

- [x] 1. Write bug condition exploration test
  - **Property 1: Bug Condition** - Certificate Templates Table Absent
  - **CRITICAL**: This test MUST FAIL on unfixed code — failure confirms the bug exists
  - **DO NOT attempt to fix the test or the code when it fails**
  - **NOTE**: This test encodes the expected behavior — it will validate the fix when it passes after implementation
  - **GOAL**: Surface the `SQLSTATE[42S02]` / MySQL error 1146 counterexample that proves the table is missing
  - **Scoped PBT Approach**: The bug is deterministic (schema is either absent or not), so scope the property to the concrete failing cases below
  - Use `RefreshDatabase` but explicitly **skip** the certificate-templates migration so the table remains absent
  - Assert `Schema::hasTable('certificate_templates')` returns `false` (isBugCondition = true)
  - Assert `Schema::hasColumn('courses', 'certificate_template_id')` returns `false` (isBugCondition = true)
  - Assert `CertificateTemplate::latest()->paginate(10)` throws `Illuminate\Database\QueryException` with SQLSTATE 42S02
  - Assert `CertificateTemplate::getDefault()` throws `Illuminate\Database\QueryException`
  - Assert `GET /admin/certificate-templates` returns a 500 (or throws `QueryException`) rather than HTTP 200
  - Run test on UNFIXED code (before running `php artisan migrate`)
  - **EXPECTED OUTCOME**: Test FAILS — this is correct and proves the bug exists
  - Document counterexamples found (e.g., `QueryException: SQLSTATE[42S02] Table 'learnflow.certificate_templates' doesn't exist`)
  - Mark task complete when test is written, run, and failure is documented
  - _Requirements: 1.1, 1.2, 1.3_

- [x] 2. Write preservation property tests (BEFORE implementing fix)
  - **Property 2: Preservation** - Unrelated Schema and Behaviour Unchanged
  - **IMPORTANT**: Follow observation-first methodology — run UNFIXED code with non-buggy inputs first
  - Observe: `GET /admin/users` returns HTTP 200 on unfixed schema
  - Observe: `GET /admin/coupons` returns HTTP 200 on unfixed schema
  - Observe: `Course::factory()->create()` succeeds and all existing columns are stored correctly on unfixed schema
  - Observe: `Certificate::all()` executes without error on unfixed schema
  - Write property-based test: for all randomly-generated `Course` attribute sets, creating a course succeeds and all non-`certificate_template_id` columns round-trip correctly (preservation of existing course columns)
  - Write property-based test: for all non-certificate-template admin routes (`/admin/users`, `/admin/coupons`, `/admin/courses`), assert HTTP 200 is returned
  - Write unit assertion: `Certificate::all()` executes without error and returns the same rows before and after migration
  - Verify all these tests PASS on UNFIXED code (confirms baseline behavior to preserve)
  - **EXPECTED OUTCOME**: Tests PASS on unfixed code — this confirms the baseline
  - Mark task complete when tests are written, run, and passing on unfixed code
  - _Requirements: 3.1, 3.2, 3.4_

- [x] 3. Fix — run the pending migration

  - [x] 3.1 Execute the migration
    - Run `php artisan migrate` to execute `2026_03_17_170200_create_certificate_templates_table.php`
    - Confirm the `certificate_templates` table is created with columns: `id`, `name`, `slug`, `description`, `orientation`, `paper_size`, `html_template`, `variables`, `is_default`, `is_active`, `timestamps`
    - Confirm `certificate_template_id` nullable foreign key is added to `courses`
    - No PHP source files need to be modified — the migration file is already correct
    - _Bug_Condition: `NOT tableExists('certificate_templates') OR NOT columnExists('courses', 'certificate_template_id')`_
    - _Expected_Behavior: `Schema::hasTable('certificate_templates') = true` AND `Schema::hasColumn('courses', 'certificate_template_id') = true` AND all `CertificateTemplate` queries execute without error_
    - _Preservation: All other tables, routes, and model behaviour remain identical after migration runs_
    - _Requirements: 2.1, 2.2, 2.3, 3.1, 3.2, 3.3, 3.4_

  - [x] 3.2 Verify bug condition exploration test now passes
    - **Property 1: Expected Behavior** - Certificate Templates Table Exists After Migration
    - **IMPORTANT**: Re-run the SAME test from task 1 — do NOT write a new test
    - The test from task 1 encodes the expected behavior; when it passes the bug is fixed
    - Add schema-positive assertions to the same test file (or flip the test to run with migration applied):
      - `Schema::hasTable('certificate_templates')` returns `true`
      - `Schema::hasColumn('courses', 'certificate_template_id')` returns `true`
      - `CertificateTemplate::latest()->paginate(10)` returns a `LengthAwarePaginator` (zero items on empty table)
      - `CertificateTemplate::getDefault()` returns `null` on empty table
      - `GET /admin/certificate-templates` returns HTTP 200
    - **EXPECTED OUTCOME**: Test PASSES — confirms bug is fixed
    - _Requirements: 2.1, 2.2, 2.3_

  - [x] 3.3 Verify preservation tests still pass
    - **Property 2: Preservation** - Unrelated Schema and Behaviour Unchanged
    - **IMPORTANT**: Re-run the SAME tests from task 2 — do NOT write new tests
    - Run all preservation property tests from step 2 against the now-migrated schema
    - Confirm course property-based tests still pass (`certificate_template_id` is `null` by default, all other columns unaffected)
    - Confirm other admin route tests still return HTTP 200
    - Confirm `Certificate::all()` still executes without error
    - **EXPECTED OUTCOME**: Tests PASS — confirms no regressions
    - _Requirements: 3.1, 3.2, 3.4_

  - [x] 3.4 Write and run additional unit and integration tests
    - Assert `CertificateTemplate::create([…])` inserts a row and auto-generates a slug via the model boot hook
    - Assert `is_default` mutual-exclusivity: generate random sequences of `setDefault($id)` calls and assert only one template is default at a time (property-based)
    - Integration: `GET /admin/certificate-templates` renders the Livewire `AdminCertificateTemplates` component (HTTP 200)
    - Integration: Livewire `save()` action persists a new template and flashes a success message
    - Integration: `setDefault($id)` sets exactly one template as default and clears all others
    - Integration: `toggleActive($id)` flips `is_active` without affecting other templates
    - Integration: A `Course` can be updated to reference a `CertificateTemplate` via `certificate_template_id` and the relationship resolves correctly
    - _Requirements: 2.1, 2.2, 2.3, 3.2, 3.3_

- [x] 4. Checkpoint — Ensure all tests pass
  - Run the full test suite (`php artisan test`)
  - Confirm Property 1 (bug condition) test passes
  - Confirm Property 2 (preservation) tests pass
  - Confirm all unit and integration tests from 3.4 pass
  - Ensure all tests pass; ask the user if questions arise
