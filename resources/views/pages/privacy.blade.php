@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<div class="max-w-3xl mx-auto py-16 px-6">
    <h1 class="font-display font-extrabold text-3xl text-ink uppercase tracking-tight mb-8">Privacy Policy</h1>
    <p class="text-[11px] font-bold uppercase tracking-widest text-ink3 mb-6">Last updated: {{ now()->format('F d, Y') }}</p>
    <div class="prose prose-sm max-w-none text-ink2 font-body space-y-4">
        <h2 class="font-display font-bold text-lg text-ink mt-6">1. Information We Collect</h2>
        <p>We collect information you provide directly to us, such as when you create an account, enroll in a course, or contact us for support. This includes your name, email address, and payment information.</p>

        <h2 class="font-display font-bold text-lg text-ink mt-6">2. How We Use Your Information</h2>
        <p>We use the information we collect to provide, maintain, and improve our services, process transactions, send you notifications, and respond to your inquiries.</p>

        <h2 class="font-display font-bold text-lg text-ink mt-6">3. Information Sharing</h2>
        <p>We do not sell your personal information. We may share your information with service providers who help us operate our platform, and as required by law.</p>

        <h2 class="font-display font-bold text-lg text-ink mt-6">4. Data Security</h2>
        <p>We implement industry-standard security measures to protect your personal information from unauthorized access, alteration, or destruction.</p>

        <h2 class="font-display font-bold text-lg text-ink mt-6">5. Cookies</h2>
        <p>We use cookies and similar tracking technologies to enhance your browsing experience and analyze usage patterns on our platform.</p>

        <h2 class="font-display font-bold text-lg text-ink mt-6">6. Contact Us</h2>
        <p>If you have any questions about this Privacy Policy, please contact us at <a href="mailto:support@learnflow.ai" class="text-accent hover:underline">support@learnflow.ai</a>.</p>
    </div>
</div>
@endsection
