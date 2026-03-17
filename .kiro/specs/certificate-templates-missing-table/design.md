# Certificate Templates Missing Table — Bugfix Design

## Overview

The `certificate_templates` table was never created in the `learnflow` database because the
migration `2026_03_17_170200_create_certificate_templates_table.php` was never executed.
Every code path that touches `CertificateTemplate` (the Livewire admin component, CRUD
operations, and the `courses.certificate_template_id` foreign key) therefore throws
`SQLSTATE[42S02]: Base table or view not found: 1146`.

The fix is a single operation: run the pending migration. No application code needs to change.

## Glossary

- **Bug_Condition (C)**: The `certificate_templates` table (and the `courses.certificate_template_id` column) does not exist in the database.
- **Property (P)**: Any query against `certificate_templates` executes without a database error and returns the expected result.
- **Preservation**: All other database tables, routes, and model behaviour remain identical after the migration runs.
- **AdminCertificateTemplates**: The Livewire component in `app/Livewire/AdminCertificateTemplates.php` whose `render()` method calls `CertificateTemplate::latest()->paginate(10)`.
- **CertificateTemplate**: The Eloquent model in `app/Models/CertificateTemplate.php` backed by the missing table.
- **Migration**: `database/migrations/2026_03_17_170200_create_certificate_templates_table.php` — creates `certificate_templates` and adds `certificate_template_id` to `courses`.

## Bug Details

### Bug Condition

The bug manifests on every request that causes Eloquent to query `certificate_templates`.
The table is absent because the migration has never been run, so MySQL rejects the query
immediately with error 1146.

**Formal Specification:**
```
FUNCTION isBugCondition(environment)
  INPUT: environment — the state of the database schema
  OUTPUT: boolean

  RETURN NOT tableExists('certificate_templates')
         OR NOT columnExists('courses', 'certificate_template_id')
END FUNCTION
```

### Examples

- `GET /admin/certificate-templates` → `SQLSTATE[42S02] … 'learnflow.certificate_templates' doesn't exist` (expected: HTTP 200 with paginated template list)
- `CertificateTemplate::latest()->paginate(10)` → throws `QueryException` (expected: returns `LengthAwarePaginator`)
- `CertificateTemplate::create([…])` → throws `QueryException` (expected: inserts row and returns model)
- `CertificateTemplate::getDefault()` → throws `QueryException` (expected: returns `null` when no default exists)
- `Course::find(1)->certificateTemplate` → throws `QueryException` on eager-load (expected: returns `null` for courses with no template assigned)

## Expected Behavior

### Preservation Requirements

**Unchanged Behaviors:**
- All other admin pages (`/admin/users`, `/admin/courses`, `/admin/coupons`, etc.) must continue to load without errors.
- Course queries and management (create, update, delete, search) must continue to work; `certificate_template_id` is nullable so existing courses are unaffected.
- `CertificateTemplate::getDefault()` must return `null` when no default template exists and return the correct model when one is set.
- The `certificates` table and all certificate-issuance logic must remain completely unaffected.

**Scope:**
All inputs that do NOT involve the `certificate_templates` table or the
`courses.certificate_template_id` column are completely unaffected by running this migration.

## Hypothesized Root Cause

There is exactly one root cause:

1. **Migration never executed**: The migration file exists in `database/migrations/` but
   `php artisan migrate` was never run (or was run before the file was added). The
   `migrations` table therefore has no record of
   `2026_03_17_170200_create_certificate_templates_table`, so the schema is missing both
   the `certificate_templates` table and the `courses.certificate_template_id` foreign key
   column.

No application code is defective. The Livewire component, the model, and the route are all
correct — they simply cannot function without the underlying schema.

## Correctness Properties

Property 1: Bug Condition — Certificate Templates Table Exists After Migration

_For any_ database environment where `isBugCondition` returns true (table absent), running
the pending migration SHALL create the `certificate_templates` table and add the nullable
`certificate_template_id` foreign key to `courses`, so that subsequent queries against
`CertificateTemplate` execute without error and `GET /admin/certificate-templates` returns
HTTP 200.

**Validates: Requirements 2.1, 2.2, 2.3**

Property 2: Preservation — Unrelated Schema and Behaviour Unchanged

_For any_ database query or HTTP request that does NOT involve the `certificate_templates`
table or `courses.certificate_template_id`, the system SHALL produce exactly the same result
after the migration runs as it did before, preserving all existing admin pages, course
management, certificate issuance, and model behaviour.

**Validates: Requirements 3.1, 3.2, 3.3, 3.4**

## Fix Implementation

### Changes Required

**Action**: Run the existing, already-correct migration.

```bash
php artisan migrate
```

That single command executes `2026_03_17_170200_create_certificate_templates_table.php`,
which:

1. Creates the `certificate_templates` table with all required columns (`id`, `name`, `slug`,
   `description`, `orientation`, `paper_size`, `html_template`, `variables`, `is_default`,
   `is_active`, `timestamps`).
2. Adds `certificate_template_id` as a nullable foreign key on `courses` (guarded by
   `Schema::hasColumn` so it is idempotent).

No PHP source files need to be modified.

## Testing Strategy

### Validation Approach

Two phases: first confirm the bug is reproducible on the unfixed schema (table absent), then
verify the migration resolves it and leaves everything else intact.

### Exploratory Bug Condition Checking

**Goal**: Surface the `SQLSTATE[42S02]` exception on an environment where the migration has
not been run, confirming the root cause before applying the fix.

**Test Plan**: In a test environment that deliberately omits the migration, assert that
querying `CertificateTemplate` throws a `QueryException` with error code 1146.

**Test Cases**:
1. **Page load without migration**: `GET /admin/certificate-templates` returns a 500 / throws `QueryException` (will fail on unfixed schema — confirms bug).
2. **Paginate without migration**: `CertificateTemplate::latest()->paginate(10)` throws `QueryException` (will fail on unfixed schema).
3. **getDefault without migration**: `CertificateTemplate::getDefault()` throws `QueryException` (will fail on unfixed schema).
4. **courses.certificate_template_id absent**: `Schema::hasColumn('courses', 'certificate_template_id')` returns `false` (will fail on unfixed schema).

**Expected Counterexamples**:
- `Illuminate\Database\QueryException` with `SQLSTATE[42S02]` and MySQL error 1146 on any of the above calls.

### Fix Checking

**Goal**: Verify that after the migration runs, all previously-failing queries succeed.

**Pseudocode:**
```
RUN migration 2026_03_17_170200_create_certificate_templates_table

FOR ALL input WHERE isBugCondition(input) WAS true DO
  result := executeQuery(input)
  ASSERT NO QueryException thrown
  ASSERT expectedBehavior(result)
END FOR
```

### Preservation Checking

**Goal**: Verify that running the migration does not alter any existing table or break any
existing query.

**Pseudocode:**
```
FOR ALL query WHERE NOT isBugCondition(environment) DO
  ASSERT query_before_migration(query) = query_after_migration(query)
END FOR
```

**Testing Approach**: Property-based testing is appropriate here because the migration
touches two tables (`certificate_templates` created, `courses` altered). We want confidence
that the `courses` table change (adding a nullable column) does not affect any existing
course query across many possible course states.

**Test Cases**:
1. **Course query preservation**: Generate random `Course` records; assert that all existing
   columns are returned identically after migration and that `certificate_template_id` is
   `null` by default.
2. **Other admin pages**: Assert that routes like `/admin/users` and `/admin/coupons` return
   HTTP 200 both before and after migration.
3. **certificates table unaffected**: Assert that `Certificate::all()` executes without error
   and returns the same rows after migration.
4. **getDefault returns null on empty table**: After migration with no seeded templates,
   `CertificateTemplate::getDefault()` returns `null`.

### Unit Tests

- Assert `Schema::hasTable('certificate_templates')` is `true` after migration runs.
- Assert `Schema::hasColumn('courses', 'certificate_template_id')` is `true` after migration runs.
- Assert `CertificateTemplate::latest()->paginate(10)` returns a `LengthAwarePaginator` with zero items on a fresh (empty) table.
- Assert `CertificateTemplate::getDefault()` returns `null` when no default template exists.
- Assert `CertificateTemplate::create([…])` inserts a row and auto-generates a slug via the model boot hook.

### Property-Based Tests

- Generate random arrays of course attributes; assert that creating a `Course` with `certificate_template_id = null` succeeds and that all other attributes are stored correctly (preservation of existing course columns).
- Generate random `CertificateTemplate` data; assert that `is_default` mutual-exclusivity is maintained (only one template can be default at a time) across many create/update sequences.
- Generate random non-certificate-template admin routes; assert all return HTTP 200 after migration (preservation of other admin pages).

### Integration Tests

- `GET /admin/certificate-templates` returns HTTP 200 with the Livewire component rendered after migration.
- Creating a template via the Livewire `save()` action persists the record and flashes a success message.
- `setDefault($id)` sets exactly one template as default and clears all others.
- `toggleActive($id)` flips the `is_active` flag without affecting other templates.
- A `Course` can be updated to reference a `CertificateTemplate` via `certificate_template_id` and the relationship resolves correctly.
