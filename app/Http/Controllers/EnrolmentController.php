<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\EnrolmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EnrolmentController extends Controller
{
    public function __construct(
        protected EnrolmentService $enrolmentService
    ) {}

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        $course = Course::findOrFail($request->course_id);
        $user = $request->user();

        if ($this->enrolmentService->isAlreadyEnrolled($user, $course)) {
            return redirect()->route('learn.show', $course->slug)
                ->with('info', 'You are already enrolled in this course.');
        }

        if ($course->price > 0) {
            return redirect()->route('checkout.course', $course->slug)
                ->with('info', 'Please complete payment to enrol in this course.');
        }

        try {
            $this->enrolmentService->enrol($user, $course);

            return redirect()->route('learn.show', $course->slug)
                ->with('success', 'Successfully enrolled! Start learning now.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
