<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Certificate Verification – LearnFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500&family=Syne:wght@400;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-bg font-body text-ink antialiased flex items-center justify-center p-6">
    <div class="w-full max-w-[520px] bg-surface border border-rule rounded-card p-8 text-center">
        <span class="inline-block px-4 py-1.5 rounded-pill bg-success-bg text-success font-display font-bold text-sm mb-6">Verified</span>
        <h1 class="font-display font-extrabold text-2xl text-ink mb-2">Web Development Bootcamp</h1>
        <p class="font-body text-lg text-ink">John D.</p>
        <p class="text-[13px] text-ink3 mt-1">Completed on March 10, 2025</p>
        <hr class="border-t border-rule my-6">
        <p class="font-mono text-sm text-ink3">LF-8A2B-4C3D-9E1F</p>
        <div class="mt-8 pt-6 border-t border-rule">
            <a href="http://localhost/learnflow/" class="font-display font-extrabold text-ink">Learn<span class="text-accent">Flow</span></a>
            <p class="text-[11px] text-ink3 mt-1">Issued by LearnFlow</p>
        </div>
    </div>
</body>
</html>
