@extends('layouts.app')

@section('title', 'Terms of Service')

@section('content')
<div class="max-w-3xl mx-auto py-16 px-6">
    <h1 class="font-display font-extrabold text-3xl text-ink uppercase tracking-tight mb-8">Terms of Service</h1>
    <p class="text-[11px] font-bold uppercase tracking-widest text-ink3 mb-6">Last updated: {{ now()->format('F d, Y') }}</p>
    <div class="prose prose-sm max-w-none text-ink2 font-body space-y-4">
        <h2 class="font-display font-bold text-lg text-ink mt-6">1. Acceptance of Terms</h2>
        <p>By accessing or using LearnFlow, you agree to be bound by these Terms of Service. If you do not agree, please do not use our platform.</p>

        <h2 class="font-display font-bold text-lg text-ink mt-6">2. Account Registration</h2>
        <p>You must provide accurate and complete information when creating an account. You are responsible for maintaining the confidentiality of your account credentials.</p>

        <h2 class="font-display font-bold text-lg text-ink mt-6">3. Course Content</h2>
        <p>Course content is protected by copyright and intellectual property laws. You may not reproduce, distribute, or create derivative works from course materials without authorization.</p>

        <h2 class="font-display font-bold text-lg text-ink mt-6">4. Payments & Refunds</h2>
        <p>All payments are processed securely through our payment providers. Refund requests are handled on a case-by-case basis in accordance with our refund policy.</p>

        <h2 class="font-display font-bold text-lg text-ink mt-6">5. User Conduct</h2>
        <p>You agree not to misuse the platform, share account credentials, engage in fraudulent activity, or violate any applicable laws while using our services.</p>

        <h2 class="font-display font-bold text-lg text-ink mt-6">6. Limitation of Liability</h2>
        <p>LearnFlow is provided "as is" without warranties of any kind. We shall not be liable for any indirect, incidental, or consequential damages arising from your use of the platform.</p>

        <h2 class="font-display font-bold text-lg text-ink mt-6">7. Changes to Terms</h2>
        <p>We reserve the right to modify these terms at any time. Continued use of the platform after changes constitutes acceptance of the updated terms.</p>
    </div>
</div>
@endsection
