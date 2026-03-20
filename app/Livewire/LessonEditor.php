<?php

namespace App\Livewire;

use App\Enums\LessonType;
use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class LessonEditor extends Component
{
    use WithFileUploads;

    public Lesson $lesson;

    public bool $fromAdmin = false;

    public $title = '';
    public $type = '';
    public $content_url = '';
    public $content_body = '';
    public $duration_seconds = null;
    public $is_preview = false;
    public $unlock_after_days = null;

    public $pdfFile;
    public $existingPdf = null;

    public $uploadProgress = 0;
    public $isUploading = false;

    public function mount(Lesson $lesson)
    {
        $course = $lesson->section->course;
        $this->authorize('update', $course);

        $this->lesson = $lesson;
        $this->title = $lesson->title;
        $this->type = $lesson->type->value;
        $this->content_url = $lesson->content_url ?? '';
        $this->content_body = $lesson->content_body ?? '';
        $this->duration_seconds = $lesson->duration_seconds;
        $this->is_preview = $lesson->is_preview;
        $this->unlock_after_days = $lesson->unlock_after_days;

        if ($lesson->type === LessonType::Pdf) {
            $this->existingPdf = $lesson->getFirstMediaUrl('pdf');
        }
    }

    public function getPresignedUrl()
    {
        $this->authorize('update', $this->lesson->section->course);

        $filename = 'lessons/' . $this->lesson->id . '/' . Str::uuid() . '.mp4';
        
        $url = Storage::disk('s3')->temporaryUploadUrl(
            $filename,
            now()->addMinutes(30),
            ['ContentType' => 'video/mp4']
        );

        return [
            'url' => $url['url'],
            'headers' => $url['headers'] ?? [],
            'path' => $filename,
        ];
    }

    public function setVideoUrl($url)
    {
        $this->content_url = $url;
        $this->isUploading = false;
        $this->uploadProgress = 100;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'content_url' => 'nullable|string|max:2048',
            'content_body' => 'nullable|string',
            'duration_seconds' => 'nullable|integer|min:0',
            'is_preview' => 'boolean',
            'unlock_after_days' => 'nullable|integer|min:0',
            'pdfFile' => 'nullable|mimes:pdf|max:20480',
        ]);

        $this->lesson->update([
            'title' => $this->title,
            'content_url' => $this->content_url ?: null,
            'content_body' => $this->content_body ?: null,
            'duration_seconds' => $this->duration_seconds ?: null,
            'is_preview' => $this->is_preview,
            'unlock_after_days' => $this->unlock_after_days ?: null,
        ]);

        if ($this->pdfFile) {
            $this->lesson->clearMediaCollection('pdf');
            $this->lesson->addMedia($this->pdfFile->getRealPath())
                ->usingFileName(Str::uuid() . '.pdf')
                ->toMediaCollection('pdf');
            $this->existingPdf = $this->lesson->getFirstMediaUrl('pdf');
            $this->pdfFile = null;
        }

        session()->flash('success', 'Lesson saved successfully.');
    }

    public function saveAndReturn()
    {
        $this->save();
        return redirect()->route($this->fromAdmin ? 'admin.courses.curriculum' : 'instructor.courses.curriculum', $this->lesson->section->course);
    }

    public function removePdf()
    {
        $this->lesson->clearMediaCollection('pdf');
        $this->existingPdf = null;
    }

    public function render()
    {
        return view('livewire.lesson-editor');
    }
}
