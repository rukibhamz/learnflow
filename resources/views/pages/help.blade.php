@extends('layouts.app')

@section('title', 'Help Center')

@section('content')
<div class="max-w-3xl mx-auto py-16 px-6">
    <h1 class="font-display font-extrabold text-3xl text-ink uppercase tracking-tight mb-8">Help Center</h1>
    <div class="space-y-6">
        <div class="bg-surface border border-rule rounded-card p-6">
            <h2 class="font-display font-bold text-base text-ink mb-2">Getting Started</h2>
            <p class="text-sm text-ink2 font-body">Create an account, browse our course catalog, and enroll in your first course. Your dashboard will track all your progress.</p>
        </div>
        <div class="bg-surface border border-rule rounded-card p-6">
            <h2 class="font-display font-bold text-base text-ink mb-2">Course Enrollment</h2>
            <p class="text-sm text-ink2 font-body">To enroll in a course, visit the course page and click "Enroll Now." Free courses provide instant access; paid courses require checkout via Stripe.</p>
        </div>
        <div class="bg-surface border border-rule rounded-card p-6">
            <h2 class="font-display font-bold text-base text-ink mb-2">Certificates</h2>
            <p class="text-sm text-ink2 font-body">Complete all lessons in a course to earn your certificate. Certificates can be downloaded as PDFs and verified online using the unique verification link.</p>
        </div>
        <div class="bg-surface border border-rule rounded-card p-6">
            <h2 class="font-display font-bold text-base text-ink mb-2">For Instructors</h2>
            <p class="text-sm text-ink2 font-body">Create courses from your instructor dashboard. Add sections, lessons, quizzes, and manage your curriculum with our intuitive builder tools.</p>
        </div>
        <div class="bg-surface border border-rule rounded-card p-6">
            <h2 class="font-display font-bold text-base text-ink mb-2">Need More Help?</h2>
            <p class="text-sm text-ink2 font-body">Contact our support team at <a href="mailto:support@learnflow.ai" class="text-accent hover:underline">support@learnflow.ai</a> and we'll get back to you within 24 hours.</p>
        </div>
    </div>
</div>
@endsection
