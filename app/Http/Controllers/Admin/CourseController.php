<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function show(Course $course)
    {
        $course->load(['instructor', 'sections.lessons', 'enrollments', 'reviews']);

        return view('admin.courses.show', compact('course'));
    }

    public function updateStatus(Request $request, Course $course): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'string', 'in:published,draft,pending,rejected,archived'],
        ]);

        $course->update(['status' => $request->status]);

        return back()->with('success', "Course status updated to {$request->status}.");
    }

    public function destroy(Course $course): RedirectResponse
    {
        $title = $course->title;
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', "Course \"{$title}\" has been deleted.");
    }
}
