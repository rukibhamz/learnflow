@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<div class="max-w-3xl mx-auto py-16 px-6">
    <h1 class="font-display font-extrabold text-3xl text-ink uppercase tracking-tight mb-8">About LearnFlow</h1>
    <div class="prose prose-sm max-w-none text-ink2 font-body space-y-4">
        <p>LearnFlow is a modern learning management system built to help educators create engaging courses and help students learn at their own pace.</p>
        <p>Our mission is to make quality education accessible to everyone, everywhere. We provide powerful tools for course creation, student engagement, and progress tracking.</p>
        <h2 class="font-display font-bold text-lg text-ink mt-8">Our Vision</h2>
        <p>We believe that learning should be flexible, engaging, and rewarding. LearnFlow combines cutting-edge technology with proven educational methodologies to deliver an exceptional learning experience.</p>
        <h2 class="font-display font-bold text-lg text-ink mt-8">Contact Us</h2>
        <p>Have questions? Reach out to us at <a href="mailto:support@learnflow.ai" class="text-accent hover:underline">support@learnflow.ai</a> or visit our <a href="{{ route('pages.contact') }}" class="text-accent hover:underline">contact page</a>.</p>
    </div>
</div>
@endsection
