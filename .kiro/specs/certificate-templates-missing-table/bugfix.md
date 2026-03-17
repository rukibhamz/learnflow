# Bugfix Requirements Document

## Introduction

Visiting `GET /admin/certificate-templates` throws a fatal database error because the `certificate_templates` table does not exist in the `learnflow` MySQL database. The migration file `2026_03_17_170200_create_certificate_templates_table.php` is present in the codebase but has never been executed, so the Livewire component `AdminCertificateTemplates` fails at the point it queries the table (line 103 — the `paginate(10)` call in `render()`). This also means the related `certificate_template_id` foreign key column on the `courses` table is likely absent as well.

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN a user visits `GET /admin/certificate-templates` THEN the system throws `SQLSTATE[42S02]: Base table or view not found: 1146 Table 'learnflow.certificate_templates' doesn't exist`
1.2 WHEN the `AdminCertificateTemplates` Livewire component renders THEN the system fails to paginate `CertificateTemplate::latest()->paginate(10)` because the underlying table is missing
1.3 WHEN any operation (create, update, delete, toggle active, set default) is attempted on certificate templates THEN the system throws a database error because the `certificate_templates` table does not exist

### Expected Behavior (Correct)

2.1 WHEN a user visits `GET /admin/certificate-templates` THEN the system SHALL render the admin certificate templates page without error
2.2 WHEN the `AdminCertificateTemplates` Livewire component renders THEN the system SHALL successfully query the `certificate_templates` table and return a paginated result
2.3 WHEN CRUD operations are performed on certificate templates THEN the system SHALL persist changes to the `certificate_templates` table without error

### Unchanged Behavior (Regression Prevention)

3.1 WHEN a user visits any other admin page THEN the system SHALL CONTINUE TO load without database errors
3.2 WHEN courses are queried or managed THEN the system SHALL CONTINUE TO function correctly, with the `certificate_template_id` foreign key column present and nullable on the `courses` table
3.3 WHEN the `CertificateTemplate::getDefault()` method is called THEN the system SHALL CONTINUE TO return the active default template or null as expected
3.4 WHEN existing certificates are queried THEN the system SHALL CONTINUE TO load correctly from the `certificates` table (unrelated to `certificate_templates`)
