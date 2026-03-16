<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Certificate Verification – {{ config('app.name', 'LearnFlow') }}</title>
    <meta name="description" content="Verify a LearnFlow certificate of completion." />

    {{-- LinkedIn / Open Graph --}}
    <meta property="og:title" content="Certificate Verified – {{ $certificate->course->title }}" />
    <meta property="og:description" content="{{ $maskedName }} completed {{ $certificate->course->title }} on {{ $certificate->issued_at->format('F j, Y') }}." />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ request()->url() }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-bg font-body text-ink antialiased flex items-center justify-center p-6">
    <div class="w-full max-w-[520px] bg-surface border border-rule rounded-card p-8 text-center">
        <span class="inline-block px-4 py-1.5 rounded-pill bg-success-bg text-success font-display font-bold text-sm mb-6">Verified</span>
        <h1 class="font-display font-extrabold text-2xl text-ink mb-2">{{ $certificate->course->title }}</h1>
        <p class="font-body text-lg text-ink">{{ $maskedName }}</p>
        <p class="text-[13px] text-ink3 mt-1">Completed on {{ $certificate->issued_at->format('F j, Y') }}</p>
        <hr class="border-t border-rule my-6">
        <p class="font-mono text-sm text-ink3">{{ strtoupper($certificate->uuid) }}</p>
        <div class="mt-8 pt-6 border-t border-rule">
            <a href="{{ route('home') }}" class="font-display font-extrabold text-ink">{{ config('app.name', 'LearnFlow') }}</a>
            <p class="text-[11px] text-ink3 mt-1">Issued by {{ config('app.name', 'LearnFlow') }}</p>
        </div>
    </div>
</body>
</html>
