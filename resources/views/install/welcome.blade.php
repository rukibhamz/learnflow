@extends('install.layout')

@section('title', 'Welcome')

@section('content')
    <h2 class="text-xl font-semibold mb-2">Welcome to LearnFlow</h2>
    <p class="text-slate-600 dark:text-slate-400 mb-6">
        This wizard will guide you through the installation process. It only takes a few minutes.
    </p>

    <div class="space-y-4 text-sm text-slate-600 dark:text-slate-400">
        <p>You will need:</p>
        <ul class="list-disc list-inside space-y-1 mx-auto max-w-fit">
            <li>PHP 8.2 or higher with required extensions</li>
            <li>MySQL, PostgreSQL, or SQLite database</li>
            <li>Writable storage and cache directories</li>
        </ul>
    </div>

    <div class="mt-8 flex justify-center">
        <a href="{{ route('install.requirements') }}"
           class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm transition">
            Let's get started
        </a>
    </div>
@endsection
