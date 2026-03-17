<?php

namespace Tests\Feature;

use App\Models\CertificateTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_template_auto_generates_slug()
    {
        $template = CertificateTemplate::create([
            'name' => 'Classic Blue',
            'html_template' => '<div>{{student_name}}</div>',
        ]);

        $this->assertEquals('classic-blue', $template->slug);
    }

    public function test_only_one_default_template_allowed()
    {
        $t1 = CertificateTemplate::create([
            'name' => 'Template A',
            'slug' => 'template-a',
            'html_template' => '<div>A</div>',
            'is_default' => true,
        ]);

        $t2 = CertificateTemplate::create([
            'name' => 'Template B',
            'slug' => 'template-b',
            'html_template' => '<div>B</div>',
            'is_default' => true,
        ]);

        $t1->refresh();
        $this->assertFalse($t1->is_default);
        $this->assertTrue($t2->is_default);
    }

    public function test_get_default_returns_active_default()
    {
        CertificateTemplate::create([
            'name' => 'Default',
            'slug' => 'default',
            'html_template' => '<div>default</div>',
            'is_default' => true,
            'is_active' => true,
        ]);

        $this->assertNotNull(CertificateTemplate::getDefault());
    }

    public function test_get_default_returns_null_when_inactive()
    {
        CertificateTemplate::create([
            'name' => 'Inactive Default',
            'slug' => 'inactive-default',
            'html_template' => '<div>inactive</div>',
            'is_default' => true,
            'is_active' => false,
        ]);

        $this->assertNull(CertificateTemplate::getDefault());
    }

    public function test_render_replaces_variables()
    {
        $template = CertificateTemplate::create([
            'name' => 'Render Test',
            'slug' => 'render-test',
            'html_template' => '<h1>{{student_name}} completed {{course_title}}</h1>',
        ]);

        $html = $template->render([
            'student_name' => 'John Doe',
            'course_title' => 'Laravel 101',
        ]);

        $this->assertStringContainsString('John Doe', $html);
        $this->assertStringContainsString('Laravel 101', $html);
    }

    public function test_render_escapes_html_in_variables()
    {
        $template = CertificateTemplate::create([
            'name' => 'Escape Test',
            'slug' => 'escape-test',
            'html_template' => '<p>{{student_name}}</p>',
        ]);

        $html = $template->render(['student_name' => '<script>alert("xss")</script>']);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }
}
