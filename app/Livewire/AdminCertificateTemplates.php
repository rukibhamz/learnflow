<?php

namespace App\Livewire;

use App\Models\CertificateTemplate;
use Livewire\Component;
use Livewire\WithPagination;

class AdminCertificateTemplates extends Component
{
    use WithPagination;

    public bool $showEditor = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $description = '';
    public string $orientation = 'landscape';
    public string $paper_size = 'a4';
    public string $html_template = '';
    public bool $is_default = false;
    public bool $is_active = true;

    protected array $rules = [
        'name' => 'required|string|max:255',
        'orientation' => 'required|in:landscape,portrait',
        'paper_size' => 'required|in:a4,letter',
        'html_template' => 'required|string',
    ];

    public function openEditor(?int $id = null): void
    {
        if ($id) {
            $t = CertificateTemplate::findOrFail($id);
            $this->editingId = $t->id;
            $this->name = $t->name;
            $this->description = $t->description ?? '';
            $this->orientation = $t->orientation;
            $this->paper_size = $t->paper_size;
            $this->html_template = $t->html_template;
            $this->is_default = $t->is_default;
            $this->is_active = $t->is_active;
        } else {
            $this->resetForm();
            $this->html_template = $this->defaultHtml();
        }
        $this->showEditor = true;
    }

    public function closeEditor(): void
    {
        $this->showEditor = false;
        $this->resetForm();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description ?: null,
            'orientation' => $this->orientation,
            'paper_size' => $this->paper_size,
            'html_template' => $this->html_template,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            CertificateTemplate::where('id', $this->editingId)->update($data);
            session()->flash('success', 'Template updated.');
        } else {
            CertificateTemplate::create($data);
            session()->flash('success', 'Template created.');
        }

        $this->closeEditor();
    }

    public function toggleActive(int $id): void
    {
        $t = CertificateTemplate::findOrFail($id);
        $t->update(['is_active' => !$t->is_active]);
    }

    public function setDefault(int $id): void
    {
        CertificateTemplate::query()->update(['is_default' => false]);
        CertificateTemplate::where('id', $id)->update(['is_default' => true]);
        session()->flash('success', 'Default template updated.');
    }

    public function delete(int $id): void
    {
        CertificateTemplate::destroy($id);
        session()->flash('success', 'Template deleted.');
    }

    public function render()
    {
        return view('livewire.admin-certificate-templates', [
            'templates' => CertificateTemplate::latest()->paginate(10),
        ]);
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'description', 'orientation', 'paper_size', 'html_template', 'is_default', 'is_active']);
        $this->is_active = true;
    }

    private function defaultHtml(): string
    {
        return <<<'HTML'
<div style="width:100%;height:100%;padding:60px;box-sizing:border-box;font-family:'Georgia',serif;text-align:center;border:8px double #1a1a2e;background:#fff;">
    <div style="margin-top:40px;">
        <h1 style="font-size:42px;color:#1a1a2e;margin:0;">Certificate of Completion</h1>
        <p style="font-size:16px;color:#666;margin-top:10px;">This certifies that</p>
        <p style="font-size:32px;color:#1a1a2e;font-weight:bold;margin:20px 0;">{{student_name}}</p>
        <p style="font-size:16px;color:#666;">has successfully completed the course</p>
        <p style="font-size:24px;color:#5046e5;font-weight:bold;margin:20px 0;">{{course_title}}</p>
        <p style="font-size:14px;color:#666;margin-top:30px;">Instructed by <strong>{{instructor_name}}</strong></p>
        <p style="font-size:14px;color:#999;margin-top:40px;">Issued on {{issued_date}} &middot; Certificate ID: {{certificate_uuid}}</p>
        <p style="font-size:12px;color:#aaa;margin-top:10px;">Verify at {{verify_url}}</p>
    </div>
</div>
HTML;
    }
}
