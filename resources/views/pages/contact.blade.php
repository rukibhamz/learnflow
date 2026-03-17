@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="max-w-3xl mx-auto py-16 px-6">
    <h1 class="font-display font-extrabold text-3xl text-ink uppercase tracking-tight mb-8">Contact Us</h1>
    <div class="prose prose-sm max-w-none text-ink2 font-body space-y-4">
        <p>We'd love to hear from you. Whether you have a question about features, pricing, or anything else, our team is ready to help.</p>
        <div class="bg-surface border border-rule rounded-card p-8 mt-6 space-y-4 not-prose">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Email</p>
                <a href="mailto:support@learnflow.ai" class="text-accent text-sm hover:underline">support@learnflow.ai</a>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Response Time</p>
                <p class="text-sm text-ink2">We typically respond within 24 hours on business days.</p>
            </div>
        </div>
    </div>
</div>
@endsection
