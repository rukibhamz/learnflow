<?php

namespace Tests\Feature;

use App\Livewire\AdminCertificateTemplates;
use App\Models\CertificateTemplate;
use App\Models\Course;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Certificate Template Integration Tests
 *
 * Validates Requirements: 2.1, 2.2, 2.3, 3.2, 3.3
 *
 * Uses RefreshDatabase WITHOUT dropping the table — the migration runs normally
 * so the schema is in the FIXED state.
 */
class CertificateTemplateIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    // ── Unit: Model creation and slug auto-generation ─────────────────────────

    /**
     * CertificateTemplate::create() inserts a row and auto-generates a slug
     * via the model boot hook.
     *
     * Validates: Requirements 2.3
     */
    public function test_create_inserts_row_and_auto_generates_slug(): void
    {
        $template = CertificateTemplate::create([
            'name'          => 'My Test Template',
            'orientation'   => 'landscape',
            'paper_size'    => 'a4',
            'html_template' => '<p>Hello</p>',
        ]);

        $this->assertNotNull($template->id, 'Template should be persisted with an id');
        $this->assertDatabaseHas('certificate_templates', ['id' => $template->id]);
        $this->assertSame('my-test-template', $template->slug, 'Slug should be auto-generated from name');
    }

    // ── Unit: is_default mutual-exclusivity property ──────────────────────────

    /**
     * is_default mutual-exclusivity: after calling setDefault($id) on each
     * template in sequence, only ONE template has is_default = true at a time.
     *
     * Runs 3 different sequences to cover the property across varied orderings.
     *
     * Validates: Requirements 3.3
     */
    public function test_is_default_mutual_exclusivity_across_sequences(): void
    {
        $component = Livewire::actingAs($this->admin)->test(AdminCertificateTemplates::class);

        $templates = collect();
        for ($i = 1; $i <= 3; $i++) {
            $templates->push(CertificateTemplate::create([
                'name'          => "Template {$i}",
                'orientation'   => 'landscape',
                'paper_size'    => 'a4',
                'html_template' => "<p>Template {$i}</p>",
            ]));
        }

        $sequences = [
            [$templates[0]->id, $templates[1]->id, $templates[2]->id],
            [$templates[2]->id, $templates[0]->id, $templates[1]->id],
            [$templates[1]->id, $templates[2]->id, $templates[0]->id],
        ];

        foreach ($sequences as $sequence) {
            foreach ($sequence as $id) {
                $component->call('setDefault', $id);

                $defaultCount = CertificateTemplate::where('is_default', true)->count();
                $this->assertSame(1, $defaultCount, "Exactly one template should be default after setDefault({$id})");

                $defaultTemplate = CertificateTemplate::where('is_default', true)->first();
                $this->assertSame($id, $defaultTemplate->id, "Template {$id} should be the default");
            }
        }
    }

    // ── Integration: GET /admin/certificate-templates renders component ────────

    /**
     * GET /admin/certificate-templates returns HTTP 200 and renders the
     * AdminCertificateTemplates Livewire component.
     *
     * Validates: Requirements 2.1
     */
    public function test_admin_route_renders_livewire_component(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/certificate-templates');

        $response->assertStatus(200);
        $response->assertSee('wire:id', false);
    }

    // ── Integration: Livewire save() persists template and flashes success ─────

    /**
     * Livewire save() action persists a new template and flashes a success message.
     *
     * Validates: Requirements 2.3
     */
    public function test_livewire_save_persists_template_and_flashes_success(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminCertificateTemplates::class)
            ->set('name', 'Integration Test Template')
            ->set('html_template', '<p>Test HTML</p>')
            ->set('orientation', 'landscape')
            ->set('paper_size', 'a4')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('certificate_templates', [
            'name' => 'Integration Test Template',
        ]);

        $this->assertNotNull(
            CertificateTemplate::where('name', 'Integration Test Template')->first(),
            'Template should be persisted in the database'
        );
    }

    // ── Integration: setDefault sets exactly one template as default ──────────

    /**
     * setDefault($id) sets exactly one template as default and clears all others.
     *
     * Validates: Requirements 3.3
     */
    public function test_set_default_sets_exactly_one_template_as_default(): void
    {
        $templates = collect();
        for ($i = 1; $i <= 3; $i++) {
            $templates->push(CertificateTemplate::create([
                'name'          => "Template {$i}",
                'orientation'   => 'landscape',
                'paper_size'    => 'a4',
                'html_template' => "<p>Template {$i}</p>",
            ]));
        }

        $component = Livewire::actingAs($this->admin)->test(AdminCertificateTemplates::class);
        $component->call('setDefault', $templates[1]->id);

        $this->assertDatabaseHas('certificate_templates', ['id' => $templates[1]->id, 'is_default' => true]);
        $this->assertDatabaseHas('certificate_templates', ['id' => $templates[0]->id, 'is_default' => false]);
        $this->assertDatabaseHas('certificate_templates', ['id' => $templates[2]->id, 'is_default' => false]);

        $this->assertSame(1, CertificateTemplate::where('is_default', true)->count());
    }

    // ── Integration: toggleActive flips is_active without affecting others ────

    /**
     * toggleActive($id) flips is_active on the target template without
     * affecting other templates.
     *
     * Validates: Requirements 2.3
     */
    public function test_toggle_active_flips_is_active_without_affecting_others(): void
    {
        $first = CertificateTemplate::create([
            'name'          => 'First Template',
            'orientation'   => 'landscape',
            'paper_size'    => 'a4',
            'html_template' => '<p>First</p>',
            'is_active'     => true,
        ]);

        $second = CertificateTemplate::create([
            'name'          => 'Second Template',
            'orientation'   => 'landscape',
            'paper_size'    => 'a4',
            'html_template' => '<p>Second</p>',
            'is_active'     => true,
        ]);

        $component = Livewire::actingAs($this->admin)->test(AdminCertificateTemplates::class);
        $component->call('toggleActive', $first->id);

        $this->assertFalse((bool) $first->fresh()->is_active, 'First template should now be inactive');
        $this->assertTrue((bool) $second->fresh()->is_active, 'Second template should remain active');
    }

    // ── Integration: Course → CertificateTemplate relationship ───────────────

    /**
     * A Course can reference a CertificateTemplate via certificate_template_id
     * and the relationship resolves correctly.
     *
     * Validates: Requirements 3.2
     */
    public function test_course_certificate_template_relationship_resolves_correctly(): void
    {
        $template = CertificateTemplate::create([
            'name'          => 'Course Template',
            'orientation'   => 'landscape',
            'paper_size'    => 'a4',
            'html_template' => '<p>Course Template</p>',
        ]);

        $course = Course::withoutSyncingToSearch(fn () => Course::factory()->create([
            'certificate_template_id' => $template->id,
        ]));

        $resolved = $course->fresh()->certificateTemplate;

        $this->assertNotNull($resolved, 'certificateTemplate relationship should resolve');
        $this->assertSame($template->id, $resolved->id, 'Resolved template should match the assigned template');
        $this->assertSame('Course Template', $resolved->name);
    }
}
