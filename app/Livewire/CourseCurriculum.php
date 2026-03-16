<?php

namespace App\Livewire;

use App\Enums\LessonType;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Section;
use Livewire\Component;

class CourseCurriculum extends Component
{
    public Course $course;

    public $showSectionModal = false;
    public $editingSectionId = null;
    public $sectionTitle = '';
    public $sectionDescription = '';

    public $showLessonModal = false;
    public $editingLessonId = null;
    public $lessonSectionId = null;
    public $lessonTitle = '';
    public $lessonType = 'video';

    public $editingInlineId = null;
    public $editingInlineTitle = '';

    public function mount(Course $course)
    {
        $this->authorize('update', $course);
        $this->course = $course;
    }

    public function openAddSectionModal()
    {
        $this->reset(['editingSectionId', 'sectionTitle', 'sectionDescription']);
        $this->showSectionModal = true;
    }

    public function openEditSectionModal($sectionId)
    {
        $section = Section::findOrFail($sectionId);
        $this->editingSectionId = $section->id;
        $this->sectionTitle = $section->title;
        $this->sectionDescription = $section->description ?? '';
        $this->showSectionModal = true;
    }

    public function saveSection()
    {
        $this->validate([
            'sectionTitle' => 'required|string|max:255',
            'sectionDescription' => 'nullable|string|max:1000',
        ]);

        if ($this->editingSectionId) {
            $section = Section::findOrFail($this->editingSectionId);
            $section->update([
                'title' => $this->sectionTitle,
                'description' => $this->sectionDescription,
            ]);
            session()->flash('success', 'Section updated.');
        } else {
            $maxOrder = $this->course->sections()->max('order') ?? 0;
            Section::create([
                'course_id' => $this->course->id,
                'title' => $this->sectionTitle,
                'description' => $this->sectionDescription,
                'order' => $maxOrder + 1,
            ]);
            session()->flash('success', 'Section added.');
        }

        $this->showSectionModal = false;
        $this->reset(['editingSectionId', 'sectionTitle', 'sectionDescription']);
    }

    public function deleteSection($sectionId)
    {
        $section = Section::where('course_id', $this->course->id)->findOrFail($sectionId);
        $section->lessons()->delete();
        $section->delete();
        session()->flash('success', 'Section deleted.');
    }

    public function openAddLessonModal($sectionId)
    {
        $this->reset(['editingLessonId', 'lessonTitle', 'lessonType']);
        $this->lessonSectionId = $sectionId;
        $this->showLessonModal = true;
    }

    public function saveLesson()
    {
        $this->validate([
            'lessonTitle' => 'required|string|max:255',
            'lessonType' => 'required|in:video,text,pdf,embed',
            'lessonSectionId' => 'required|exists:sections,id',
        ]);

        $section = Section::where('course_id', $this->course->id)->findOrFail($this->lessonSectionId);
        $maxOrder = $section->lessons()->max('order') ?? 0;

        $lesson = Lesson::create([
            'section_id' => $this->lessonSectionId,
            'title' => $this->lessonTitle,
            'type' => LessonType::from($this->lessonType),
            'order' => $maxOrder + 1,
        ]);

        $this->showLessonModal = false;
        $this->reset(['lessonSectionId', 'lessonTitle', 'lessonType']);
        session()->flash('success', 'Lesson added.');

        return redirect()->route('instructor.lessons.edit', $lesson);
    }

    public function deleteLesson($lessonId)
    {
        $lesson = Lesson::whereHas('section', fn($q) => $q->where('course_id', $this->course->id))
            ->findOrFail($lessonId);
        $lesson->delete();
        session()->flash('success', 'Lesson deleted.');
    }

    public function startInlineEdit($lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);
        $this->editingInlineId = $lessonId;
        $this->editingInlineTitle = $lesson->title;
    }

    public function saveInlineEdit()
    {
        if (!$this->editingInlineId) return;

        $this->validate(['editingInlineTitle' => 'required|string|max:255']);

        $lesson = Lesson::whereHas('section', fn($q) => $q->where('course_id', $this->course->id))
            ->findOrFail($this->editingInlineId);
        $lesson->update(['title' => $this->editingInlineTitle]);

        $this->reset(['editingInlineId', 'editingInlineTitle']);
    }

    public function cancelInlineEdit()
    {
        $this->reset(['editingInlineId', 'editingInlineTitle']);
    }

    public function reorderSections($orderedIds)
    {
        foreach ($orderedIds as $index => $sectionId) {
            Section::where('id', $sectionId)
                ->where('course_id', $this->course->id)
                ->update(['order' => $index + 1]);
        }
    }

    public function reorderLessons($sectionId, $orderedIds)
    {
        $section = Section::where('course_id', $this->course->id)->findOrFail($sectionId);
        
        foreach ($orderedIds as $index => $lessonId) {
            Lesson::where('id', $lessonId)
                ->where('section_id', $section->id)
                ->update(['order' => $index + 1]);
        }
    }

    public function moveLessonToSection($lessonId, $newSectionId, $newOrder)
    {
        $lesson = Lesson::whereHas('section', fn($q) => $q->where('course_id', $this->course->id))
            ->findOrFail($lessonId);
        
        $newSection = Section::where('course_id', $this->course->id)->findOrFail($newSectionId);
        
        $lesson->update([
            'section_id' => $newSectionId,
            'order' => $newOrder,
        ]);
    }

    public function render()
    {
        $sections = $this->course->sections()
            ->withoutGlobalScope('ordered')
            ->orderBy('order')
            ->with(['lessons' => fn($q) => $q->withoutGlobalScope('ordered')->orderBy('order')])
            ->get();

        return view('livewire.course-curriculum', [
            'sections' => $sections,
        ]);
    }
}
