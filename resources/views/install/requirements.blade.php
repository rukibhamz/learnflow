@extends('install.layout')

@section('title', 'Requirements')
@php
    $backHref = route('install.welcome');
    $continueHref = route('install.database');
    \Illuminate\Support\Facades\Log::info('[INSTALL_DEBUG] requirements view rendering', [
        'back_href' => $backHref,
        'continue_href' => $continueHref,
        'current_url' => request()->url(),
    ]);
@endphp

@section('steps')
    <span class="text-indigo-600 font-medium">1. Requirements</span>
    <span class="text-slate-300">→</span>
    <span class="text-slate-400">2. Database</span>
    <span class="text-slate-300">→</span>
    <span class="text-slate-400">3. Application</span>
    <span class="text-slate-300">→</span>
    <span class="text-slate-400">4. Install</span>
@endsection

@section('content')
    <h2 class="text-xl font-semibold mb-2">System requirements</h2>
    <p class="text-slate-600 text-sm mb-6">
        Before we begin, we need to verify your server meets the requirements.
    </p>

    <div class="space-y-2">
        @foreach ($requirements as $key => $req)
            <div class="flex items-center justify-between py-2 px-3 rounded-lg {{ $req['satisfied'] ? 'bg-slate-50' : 'bg-red-50' }}">
                <span class="text-sm {{ $req['satisfied'] ? 'text-slate-700' : 'text-red-700' }}">
                    {{ $req['label'] }}
                    @if (!empty($req['message']))
                        <span class="text-slate-500">({{ $req['message'] }})</span>
                    @endif
                </span>
                @if ($req['satisfied'])
                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                @else
                    <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-8 flex gap-3">
        <a href="{{ $backHref }}"
           class="inline-flex items-center px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-medium transition">
            Back
        </a>
        @if ($satisfied)
            <a href="{{ $continueHref }}"
               class="inline-flex items-center justify-center px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm transition">
                Continue
            </a>
        @else
            <button type="button" disabled
                    class="inline-flex items-center px-5 py-2 rounded-lg bg-slate-300 text-slate-500 font-medium text-sm cursor-not-allowed">
                Fix requirements to continue
            </button>
        @endif
    </div>
@endsection
