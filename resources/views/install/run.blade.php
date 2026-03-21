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
        Review your configuration before proceeding. If anything looks incorrect, go back and fix it.
    </p>

    <div class="mb-8 p-4 bg-slate-50 border border-slate-200 rounded-lg text-sm">
        <h3 class="font-bold text-slate-800 mb-2 uppercase text-[10px] tracking-widest leading-none">Database Summary</h3>
        <ul class="space-y-1 text-slate-600">
            <li><span class="font-medium text-slate-900">Type:</span> {{ strtoupper($db['driver'] ?? 'unknown') }}</li>
            @if(($db['driver'] ?? '') !== 'sqlite')
                <li><span class="font-medium text-slate-900">Host:</span> {{ $db['host'] ?? '-' }}</li>
                <li><span class="font-medium text-slate-900">Database:</span> {{ $db['database'] ?? '-' }}</li>
                <li><span class="font-medium text-slate-900">User:</span> {{ $db['username'] ?? '-' }}</li>
            @else
                <li><span class="font-medium text-slate-900">Path:</span> {{ $db['database'] ?? '-' }}</li>
            @endif
        </ul>
        <div class="mt-4 pt-4 border-t border-slate-200">
            <a href="{{ route('install.database') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                Change database settings
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('install.run.execute') }}" id="install-form">
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
