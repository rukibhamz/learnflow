@extends('install.layout')

@section('title', 'Install')

@section('steps')
    <a href="{{ route('install.requirements') }}" class="text-slate-500 hover:text-slate-700">1. Requirements</a>
    <span class="text-slate-300">→</span>
    <a href="{{ route('install.database') }}" class="text-slate-500 hover:text-slate-700">2. Database</a>
    <span class="text-slate-300">→</span>
    <a href="{{ route('install.application') }}" class="text-slate-500 hover:text-slate-700">3. Application</a>
    <span class="text-slate-300">→</span>
    <span class="text-indigo-600 font-medium">4. Install</span>
@endsection

@section('content')
    <h2 class="text-xl font-semibold mb-2">Ready to install</h2>
    <p class="text-slate-600 text-sm mb-6">
        Everything is configured. Click the button below to run the installation.
    </p>

    <form method="POST" action="{{ route('install.execute') }}/" id="install-form">
        @csrf
        <button type="submit" id="install-btn"
                class="inline-flex items-center justify-center gap-2 w-full px-5 py-3 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
            <span class="install-text">Run installation</span>
            <span class="install-loading hidden">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Installing...
            </span>
        </button>
    </form>

    <script>
        document.getElementById('install-form').addEventListener('submit', function () {
            const btn = document.getElementById('install-btn');
            btn.disabled = true;
            btn.querySelector('.install-text').classList.add('hidden');
            btn.querySelector('.install-loading').classList.remove('hidden');
        });
    </script>
@endsection
